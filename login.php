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
        $error = TRUE;
    }

    if(!$error) { //SO FAR SO GOOD
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        $username = $mysqli->real_escape_string($username);
        if( $mysqli->connect_errno ) {
            $returnVal = "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error. " ";
        } else {
            $result = $mysqli->query("SELECT * FROM `users` WHERE 
                (`username` = '$username')");
            if( $result->num_rows == 1 ) {
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $db_hash = $row["password"];
                if( password_verify($password, $db_hash)) {
                    if( $rememberme == "rememberme" ) {
                        setRememberMe($username);
                    }
                    $_SESSION['loginuser'] = $username;
                    header('Location: secure/');
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
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <!-- Place favicon.ico in the root directory -->

        <link href="css/bootstrap.min.css" rel="stylesheet">

        <link href="css/main.css" rel="stylesheet">
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <div class="container">
            <?php if($returnVal != "") {
                echo '<div class="alert alert-danger" role="alert">'.$returnVal.'</div>';
            } ?>
            <form class="form-small" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>">
                <h2 class="form-small-heading">Inloggen</h2>
                <label for="username" class="sr-only">Gebruikersnaam</label>
                <input type="text" id="username" class="form-control" placeholder="Gebruikersnaam" name="username" required autofocus>
                <label for="password" class="sr-only">Paswoord</label>
                <input type="password" id="password" class="form-control" placeholder="Paswoord" name="password" required>
                <div class="checkbox">
                <label for="rememberme">
                    <input type="checkbox" class="form-control" name="rememberme" id="rememberme" value="rememberme">
                        Onthoud mij
                </label>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Inloggen</button>
                <a href="create">Ik heb nog geen account</a><br>
                <a href="password">Ik ben mijn wachtwoord vergeten</a>
            </form>
        </div> <!-- /container -->

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.3.min.js"><\/script>')</script>
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>

        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='https://www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-XXXXX-X','auto');ga('send','pageview');
        </script>
    </body>
</html>
