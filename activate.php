<?php session_start();
include_once "functions.php";
date_default_timezone_set('Europe/Amsterdam');
$returnVal = "";
$email = ""; 


if( $_SERVER["REQUEST_METHOD"] == "POST") {
    if( !empty($_POST["email"]) ) {
        $email = test_input($_POST["email"]);
        if( !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            addError("Het email adres wat je hebt opgegeven lijkt niet te kloppen.");
        }
    } else {
        $email = "";
        addError("Je hebt geen email adres opgegeven");
    }
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $sqlresult = $mysqli->query(sprintf("SELECT 1 FROM `users` WHERE `email` = '%s'",
        $mysqli->real_escape_string($email)));
    if( $sqlresult->num_rows != 0 ) {
        addError("Je hebt al een account aangemaakt. In je email staat een email waarmee je een wachtwoord kunt instellen. Is die verlopen of hebt je die niet ontvangen? Dan kun je doen alsof je je <a href='wachtwoordvergeten'>wachtwoord vergeten</a> bent. Mocht het dan nog niet lukken kun je mailen naar: ".$mailtolink);
    }
    if( $returnVal == "" ) {
        $query = sprintf("DELETE FROM `pwreset` WHERE `email` = '%s'",
            $mysqli->real_escape_string($email));
        if( !$mysqli->query($query) ) {
            //email_error("Error removing from pwreset ".$mysqli->error);
        }
        $query = sprintf("SELECT * FROM `person` WHERE `email` = '%s'",
            $mysqli->real_escape_string($email));
        $sqlresult = $mysqli->query($query);
        if( $sqlresult === FALSE || $sqlresult->num_rows != 1 ) {
            addError("Het lijkt erop dat je niet jezelf had ingeschreven voor Familiar Forest 2016. Je kunt een account aanmaken op <a href='create'> deze pagina</a>. Mocht het dan nog niet lukken kun je mailen naar: ".$mailtolink);
        } else {
            $row = $sqlresult->fetch_array(MYSQLI_ASSOC);
            $fullname = $row['firstname']." ".$row['lastname'];
            $token = generateRandomToken(128);
            $now = new DateTime();
            $pw_reset_query = sprintf(
                "INSERT INTO `pwreset` (`email`, `token`, `expire`) VALUES ('%s', '%s', '%s')",
                $mysqli->real_escape_string($email),
                $mysqli->real_escape_string($token),
                $now->add(new DateInterval('P1W'))->format('Y-m-d H:i:s')
            );
            $user_add_query = sprintf(
                "INSERT INTO `users` (`email`, `permissions`) VALUES ('%s', '%s')",
                $mysqli->real_escape_string($email),
                $mysqli->real_escape_string(PERMISSION_PARTICIPANT)
            );
            $link = "https://stichtingfamiliarforest.nl/pw?t=".$token;
            if( $mysqli->query($user_add_query) ) {
                if( $mysqli->query($pw_reset_query) ) {
                    $subject = "Familiar Forest wachtwoord";
                    $content = "<html>".get_email_header();
                    $content .= "<p>Lieve ".$row['firstname'].",</p>";
                    $content .= "<p>Bedankt voor je aanmelding bij Familiar Forest. Je kunt een wachtwoord instellen door op de onderstaande link te klikken, of deze in de adresbalk van je browser te plakken:</p>";
                    $content .= "<p><a href='".$link."'>".$link."</a>";
                    $content .= "<p>De link blijft een week geldig, mocht het niet lukken voor die tijd kun je het opnieuw proberen via de <a href='https://stichtingfamiliarforest.nl/wachtwoordvergeten'>wachtwoord vergeten</a> pagina.</p>";
                    $content .= get_email_footer();
                    $content .= "</html>";
                    send_mail($email, $fullname, $subject, $content);
                    $returnVal .= '<div class="alert alert-success" role="alert">We hebben je een email verstuurd waarmee je een wachtwoord kunt instellen.</div>';
                } else {
                    addError("Helaas konden we op dit moment geen account voor je aanmaken, probeer het later nog eens of mail naar: ".$mailtolink);
                    email_error("Error resetting password on activate: ".$mysqli->error);
                }
            } else {
                addError("Helaas konden we geen account voor je aanmaken. Een veel voorkomend probleem is dat je afgelopen jaar een ander email adres hebt gebruikt. Voor hulp en informatie kun je mailen naar: ".$mailtolink);
            }
        }
    } else {
        //try again..
    }
    $mysqli->close();
} //End POST
function addError($value) {
    global $returnVal;
    $returnVal .= '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ' . $value . '</div>';
}
?>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Familiar Forest Account Activeren</title>
        <meta name="description" content="">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <link rel="icon" href="favicon.ico">
        <!-- Place favicon.ico in the root directory -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" type="text/css" media="all"
            href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/smoothness/jquery-ui.css"/>        
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <?php include("header.php"); ?>
        <div class="container">
            <div class="form-intro-text">
                <h2>Familiar Forest account activeren</h2>
                <p>Als je jezelf vorig jaar hebt opgegeven voor Familiar Forest 2016 kun je gemakkelijk gebruik maken van de gegevens die je destijds al aan ons hebt verstuurd. Het enige wat je daarvoor hoeft te doen is je email adres invullen en wij versturen je een link waarmee je een wachtwoord kunt instellen.
                </p>
            </div>
            <?php echo $returnVal; ?>
            <form class='form-small' id="create-form" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">
                <div class="form-group row">
                    <label for="email" class="col-sm-2 form-control-label">Email*</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="email" id="email" placeholder="Email" value="<?php echo $email;?>" name="email">
                    </div>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Versturen</button>
            </form>
        </div>
        <script src="js/vendor/modernizr-2.8.3.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
        <scirpt src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/localization/messages_nl.js"></script>
        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.3.min.js"><\/script>')</script>
        <script src="js/vendor/bootstrap.min.js"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>
    </body>
</html>
