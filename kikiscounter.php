<?php session_start();
include "initialize.php";
$allow_count = FALSE;

$html = "<form class='form-small' method='post'><h2 class='form-small-heading'>Paswoord</h2><input type='password' id='password' class='form-control' placeholder='Paswoord' name='password' required><button class='btn btn-lg btn-primary btn-block' type='submit'>Sign in</button></form>";

if( $_SERVER["REQUEST_METHOD"] == "POST") {
    if( !empty($_POST["password"]) ) {
        if( $_POST["password"] == "mediamanteldraad") {
            $allow_count = TRUE;
        }
    }

    if( $allow_count ) {
        $count = 0;
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        if( $mysqli->connect_errno ) {
            $count = "Error01";
        } else {
            $query = "SELECT COUNT(*) FROM person;";
            $result = $mysqli->query($query);
            if( $count === FALSE ) {
                $count = "Error02";
            }
            $data = $result->fetch_array(MYSQLI_NUM);
            $count = $data[0];
        }
        $html = "<h1>Er zijn nu " . $count . " aanmeldingen Kiki..</h1>";
        $mysqli->close();
    }
}
?>

<!doctype html>
<html class="no-js" lang="">
    <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>Kiki's Aanmeldingen counter</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <!-- <link href="../../assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet"> -->

    <!-- Custom styles for this template -->
    <link href="css/main.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->

        <div class="container">
            <?php echo $html ?>
        </div>

        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.3.min.js"><\/script>')</script>
        <script src="js/vendor/bootstrap.min.js"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->

    </body>
</html>
