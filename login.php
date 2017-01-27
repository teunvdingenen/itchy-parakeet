<?php session_start(); 
$username=$password=$rememberme=$returnVal="";
$error = FALSE;
include "initialize.php";
include "functions.php";
include "fields.php";

rememberMe();

if( $_SERVER["REQUEST_METHOD"] == "POST") {

    if( !empty($_POST["username"]) ) {
        $username = test_input($_POST["username"]);
    } else {
        $username = "";
        $error = TRUE;
    }

    if( !empty($_POST["password"]) ) {
        $password = test_input($_POST["password"]);
    } else {
        $password = "";
        $error = TRUE;
    }

    if( !empty($_POST["rememberme"]) ) {
        $rememberme = test_input($_POST["rememberme"]);
    } else {
        $rememberme = "";
    }

    if(!$error) { //SO FAR SO GOOD
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        $username = $mysqli->real_escape_string($username);
        if( $mysqli->connect_errno ) {
            $returnVal = "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error. " ";
        } else {
            $result = $mysqli->query("SELECT * FROM `users` WHERE 
                (`username` = '$username')");
            if( $result && $result->num_rows == 1 ) {
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $db_hash = $row["password"];
                if( password_verify($password, $db_hash)) {
                    if( $rememberme == "rememberme" ) {
                        setRememberMe($username);
                    }
                    if( login($username) ) {
                        header('Location: u/index');
                    }
                } else {
                    $error = TRUE;
                }
            } else {
                $error = TRUE;
            }
        }
        $mysqli->close();
    }
}

if($error) {
    $returnVal = "Ongeldige gebruikersnaam of paswoord.";
}

?>
<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Familiar Forest</title>
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
            <?php if($returnVal != "") {
                echo '<div class="alert alert-danger" role="alert">'.$returnVal.'</div>';
            } ?>
            <form class="form-small" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>">
                <h2 class="form-small-heading">Inloggen</h2>
                <label for="username" class="sr-only">Gebruikersnaam</label>
                <input type="text" id="username" class="form-control" placeholder="Gebruikersnaam" name="username" required autofocus>
                <label for="password" class="sr-only">Wachtwoord</label>
                <input type="password" id="password" class="form-control" placeholder="Wachtwoord" name="password" required>
                <div class="checkbox">
                    <label><input type="checkbox" name="rememberme" value="rememberme">Ingelogd blijven</label>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Inloggen</button>
                <a href="create">Ik heb nog geen account</a><br>
                <a href="wachtwoordvergeten">Ik ben mijn wachtwoord vergeten</a>
            </form>
        </div> <!-- /container -->

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.3.min.js"><\/script>')</script>
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>
    </body>
</html>
