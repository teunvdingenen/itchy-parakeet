<?php session_start();
include_once "functions.php";
date_default_timezone_set('Europe/Amsterdam');
$returnVal = "";
$email = ""; 

if( $_SERVER["REQUEST_METHOD"] == "GET") {
    if(!empty($_GET["t"]) ) {
        $token = $_GET["t"];
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        $query = sprintf("SELECT * FROM `pwreset` WHERE `token` = '%s'",
            $mysqli->real_escape_string($token));
        $sqlresult = $mysqli->query($query);
        if( $sqlresult->num_rows < 1 ) {
            $mysqli->close();
            header('Location: index'); 
        }
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $expire = new DateTime($row['expire']);
        if( new Datetime() < $expire ) {
            $email = $row['email'];
        } else {
            addError("Je link is verlopen. Ga naar <a href='wachtwoordvergeten'>wachtwoord vergeten</a> om het nogmaals te proberen");
        }
        $mysqli->close();
    } else {
        header('Location: index');
    }
}

if( $_SERVER["REQUEST_METHOD"] == "POST") {
    if( !empty($_POST["password"]) ) {
        $password = test_input($_POST["password"]);
    } else {
        $password = "";
        addError("Je hebt je wachtwoord niet opgegeven.");
    }
    if( !empty($_POST["repeat"]) ) {
        $repeat = test_input($_POST["repeat"]);
    } else {
        $repeat = "";
        addError("Je hebt je herhaling niet opgegeven.");
    }

    if( $repeat != $password ) {
        addError( "De opgegeven wachtwoorden komen niet overeen");
    }
    
    if( $returnVal == "" ) {
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        $query = sprintf("SELECT 1 FROM `pwreset` WHERE `email` = '%s'",
            $mysqli->real_escape_string($email));
        $sqlresult = $mysqli->query($query);
        if( $sqlresult === FALSE ) {
            email_error("Error looking for pwreset: ".$mysqli->error);
        } else if( $sqlresult->num_rows != 0 ) {
            $query = sprintf("DELETE FROM `pwreset` WHERE `email` = '%s'",
                $mysqli->real_escape_string($email));
            $mysqli->query($query);
        }

        $pw_hash = password_hash($password, PASSWORD_DEFAULT);
        $query = sprintf(
            "UPDATE `users` set `password` = '%s' WHERE `username` = '%s'",
            $mysqli->real_escape_string($pw_hash),
            $mysqli->real_escape_string($email)
        );
        $mysqli->query($query);
        if( $mysqli->affected_rows > 1 ) {
            email_error("Multiple effected rows (".$mysqli->affected_rows.") for password update on email: ".$email);
        } else if ( $mysqli->affected_rows == 0 ) {
            addError("Er is iets fout gegaan met het opslaan van je wachtwoord. Stuur een email naar ".$mailtolink." voor hulp.");
        } else {
            $resultVal = '<div class="alert alert-success" role="alert">Je wachtwoord is ingesteld. Ga naar de <a href="login">login</a> pagina om verder te gaan.</div>';
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
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Familiar Forest wachtwoord</title>
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

        <!-- Add your site or application content here -->
        <div id="header" class="text-center">
            <div class="container">
                <div class="row">
                    <div class="col-xs-3">

                    </div>
                    <div class="col-xs-6">
                        <h1 class="">Stichting Familiar Forest</h1>
                    </div>
                    <div class="col-xs-3">
                        <a class='login-button' href="login">Inloggen</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="form-intro-text">
                <h1>Familiar Forest wachtwoord instellen</h1>
                <p>
                    Je kunt hier je wachtwoord opnieuw instellen:
                </p>
            </div>
            <?php echo $returnVal; ?>
            <form class='form-small' id="reset-form" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">
                <div class="form-group row">
                    <label for="password" class="col-sm-2 form-control-label">Wachtwoord</label>
                    <div class="col-sm-10">
                        <input type="password" id="password" class="form-control" placeholder="Paswoord" name="password">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="passwordrepeat" class="col-sm-2 form-control-label">Wachtwoord Herhalen</label>
                    <div class="col-sm-10">
                        <input type="password" id="repeat" class="form-control" placeholder="Paswoord Herhalen" name="repeat">
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
        <script src="js/reset.js"></script>
        <script src="js/main.js"></script>
    </body>
</html>
