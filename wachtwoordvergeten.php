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
    if( $returnVal == "" ) {
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        $query = sprintf("DELETE FROM `pwreset` WHERE `email` = '%s'",
            $mysqli->real_escape_string($email));
        if( !$mysqli->query($query) ) {
            email_error("Error removing from pwreset ".$mysqli->error);
        }
        $query = sprintf("SELECT * FROM `person` WHERE `email` = '%s'",
            $mysqli->real_escape_string($email));
        $personresult = $mysqli->query($query);
        $userresult = $mysqli->query(sprintf("SELECT 1 FROM `users` WHERE `email` = '%s'",
            $mysqli->real_escape_string($email)));
        if( $personresult === FALSE ) {
            addError("Helaas konden we je gegevens niet ophalen, probeer het later nog eens of mail naar: ".$mailtolink);
            email_error("Error looking for person: ".$mysqli->error);
        } else if ($userresult->num_rows != 1) {
            addError("Het lijkt erop dat je nog geen account hebt. <a href='create'>Klik hier</a> om deze aan te maken");
        } else {
            $row = $personresult->fetch_array(MYSQLI_ASSOC);
            $fullname = $row['firstname']." ".$row['lastname'];
            $token = generateRandomToken(128);
            $now = new DateTime();
            $pw_reset_query = sprintf(
                "INSERT INTO `pwreset` (`email`, `token`, `expire`) VALUES ('%s', '%s', '%s')",
                $mysqli->real_escape_string($email),
                $mysqli->real_escape_string($token),
                $now->add(new DateInterval('P1W'))->format('Y-m-d H:i:s')
            );
            $link = "https://stichtingfamiliarforest.nl/pw?t=".$token;
            if( $mysqli->query($pw_reset_query) ) {
                $subject = "Familiar Forest wachtwoord";
                $content = "<html>".get_email_header();
                $content .= "<p>Lieve ".$row['firstname'].",</p>";
                $content .= "<p>Je kunt een wachtwoord instellen door op de onderstaande link te klikken, of deze in de adresbalk van je browser te plakken:</p>";
                $content .= "<p><a href='".$link."'>".$link."</a>";
                $content .= "<p>Deze link blijft een week geldig.</p>";
                $content .= "<p>Je ontvangt deze email omdat we een verzoek ontvangen hebben om je wachtwoord opnieuw in te stellen. Weet je hier niets van? Stuur dan even een reply op deze email.</p>";
                $content .= get_email_footer();
                $content .= "</html>";
                send_mail($email, $fullname, $subject, $content);
                $returnVal .= '<div class="alert alert-success" role="alert">We hebben je een email verstuurd waarmee je een wachtwoord kunt instellen.</div>';
            } else {
                addError("Helaas konden we op dit moment niet je wachtwoord resetten, probeer het later nog eens of mail naar: ".$mailtolink);
                email_error("Error resetting password on forgot: ".$mysqli->error);
            }
        }
        $mysqli->close();
        
    } else {
        //try again..
    }
} //End POST
function addError($value) {
    global $returnVal;
    $returnVal .= '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ' . $value . '</div>';
}
?>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Familiar Forest wachtwoord vergeten</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <link rel="icon" href="favicon.ico">
        <!-- Place favicon.ico in the root directory -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/bootstrap-datepicker3.css">
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
                <?php echo $returnVal; ?>
                <h1>Familiar Forest wachtwoord vergeten</h1>
                <p>
                    Voer hier je email adres, je ontvangt dan een email waarmee je opnieuw je wachtwoord kunt instellen.
                </p>
            </div>
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
