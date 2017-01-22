<?php
include_once "functions.php";
include_once "fields.php";
include_once "sendmail.php";
$email=$returnVal="";
$code = "";
$firstname = $fullname = "";
$error = FALSE;
$rafflestatus = 0;

if( $_SERVER["REQUEST_METHOD"] == "POST") {

    if( !empty($_POST["email"]) ) {
        $email = test_input($_POST["email"]);
    } else {
        $email = "";
        $error = TRUE;
    }
    if($error === FALSE ) { //SO FAR SO GOOD
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        $email = $mysqli->real_escape_string($email);
        if( $mysqli->connect_errno ) {
            email_error("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error. " ");
        } else {
            $result = $mysqli->query("SELECT p.firstname, p.lastname, s.code, s.email FROM $current_table s join person p on p.email = s.email WHERE 
                (s.email = '$email') AND s.valid = 1");
            if( $result->num_rows == 1 ) {
                $rafflestatus = 1;
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $code = $row['code'];
                $firstname = $row['firstname'];
                $fullname = $firstname." ".$row['lastname'];
                $email = $row['email'];
            } else if ( $result->num_rows == 0 ) {
                $rafflestatus = 2;
            } else {
                $error = TRUE;
                email_error("Found more then one person in raffle table with email: ".$email);
            }
        }
        $mysqli->close();
    }
    if( $error === FALSE && $rafflestatus == 1 && $code != "" ) {
        $content = "<html>".get_email_header();
        $content .= "<p>Lieve ".$firstname.",<p>";
        $content .= "<p>De code die je onlangs vergeten bent is: ".$code.".</p>";
        $content .= "<p>Heb je niet je code opgevraagd? Reply dan even op deze email.</p><br><br>";
        $content .= get_email_footer();
        $content .= "</html>";
        send_mail($email, $fullname, "Code vergeten", $content);
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
        <meta name="author" content="Teun van Dingenen">
        <link rel="icon" href="favicon.ico">

        <title>Code vergeten</title>

        <!-- Bootstrap core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

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
            <?php if($error) {
                echo '<div class="alert alert-danger" role="alert">Er is iets fout gegaan met het ophalen van je code. Probeer het later nog eens of mail naar: '.$mailtolink.'</div>';
            } else if ($rafflestatus == 2) {
                echo '<div class="alert alert-info" role="alert">We hebben geen ticketcode kunnen vinden bij dit email adres. Verrast je dit? Stuur dan een email naar: '.$mailtolink.'</div>';
            } else if($rafflestatus == 1) {
                echo '<div class="alert alert-success" role="alert">We hebben je een email gestuurd met je code. Ontvang je deze niet? Stuur dan een email naar: '.$mailtolink.'</div>';
            }?>
            <form class="form-small" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>">
                <h2 class="form-small-heading">Code ophalen</h2>
                <label for="email" class="sr-only">Email</label>
                <input type="text" id="forgetemail" class="form-control" placeholder="Email" name="email" required autofocus>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Ophalen</button>
            </form>
        </div> <!-- /container -->

        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
        <script src="js/vendor/bootstrap.min.js"></script>
        <script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
        <scirpt src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/localization/messages_nl.js"></script>

        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>
    </body>
</html>
