<?php session_start(); 
$username=$password=$returnVal="";
$error = FALSE;
include "initialize.php";
include "functions.php";
include "fields.php";


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

    if(!$error) { //SO FAR SO GOOD
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        $username = $mysqli->real_escape_string($username);
        if( $mysqli->connect_errno ) {
            $returnVal = "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error. " ";
        } else {
            $result = $mysqli->query("SELECT * FROM $db_table_users WHERE 
                (`username` = '$username')");
            if( $result->num_rows == 1 ) {
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $db_hash = $row["password"];
                if( password_verify($password, $db_hash)) {
                    $_SESSION['loginuser'] = $username;
                    header('Location: secure/index.php');
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

        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/main.css">
        <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <h1 class="header">Lorem ipsum dolor sit amet</h1>
        <div class="content">
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut eget elementum purus. Ut consectetur varius vehicula. Pellentesque imperdiet ex nisl, eget porta mi venenatis et. Nulla facilisi. Fusce sodales justo at felis mollis semper. Suspendisse ut nulla eu lectus sollicitudin congue vel ac arcu. Integer dictum turpis vulputate urna tincidunt, vel pulvinar mi imperdiet. Praesent egestas, mauris in molestie facilisis, metus mauris sollicitudin enim, ut eleifend enim augue quis erat. Nullam ullamcorper tristique sodales. Maecenas et nisi vel tortor vestibulum blandit ac ut tellus. </p> 
        </div>
        <div class="content">
        <div class="error"><?php echo $returnVal; ?></div>
        <form id="signup-form" class="signup-form" method="post" 
            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" target="_top">
            <fieldset class="login">
                <legend>Login</legend>
                <ul>
                    <li>
                        <div>
                            <label for="username">Username</label>
                            <input class="field text full" type="text" name="username" value="">
                        </div>
                        <div>
                            <label for="password">Password</label>
                            <input class="field pass full" type="password" name="password" value="">
                        </div>
                    </li>
                    <li>
                        <input class="submit" type="submit" name="submit" value="Login"/>
                    </li>
                </ul>
            </fieldset>
        </form>
        </div>

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
