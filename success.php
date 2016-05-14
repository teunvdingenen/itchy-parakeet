<?php session_start(); 
include "functions.php";
include "initialize.php";

$email = "";
$content = "";
$weberror = "";
if( !empty($_SESSION["success_email"]) ){
    $email = $_SESSION["success_email"];
}

$signup = get_signup($email);
if( $signup === FALSE ) {
    $weberror = "Er is helaas iets fout gegaan met het verwerken van je inschrijving. Probeer het nogmaals of stuur een mailtje naar ".$mailtolink.".";
} else {
    $contrib0 = get_contrib($signup["contrib0"]);
    $contrib1 = get_contrib($signup["contrib1"]);

    $firstname = $signup["firstname"];

    $fullname = $signup["firstname"] ." ". $signup["lastname"];
    $subject = "Inschrijving Familiar Forest 2016";

    $content = "<html>".get_email_header();
    $content .= "<p>Lieve ".$firstname.",</p>";
    $content .= "<p>Bedankt voor je inschrijving voor Familiar Forest 2016! Hieronder vind je de informatie die we van jou ontvangen hebben. Zie je iets vreemds? Reply op deze mail en dan kunnen we er samen even naar kijken.</p>";
    $content .= "<table>";
    $content .= "<tr><td>Voornaam</td><td>".$firstname."</td></tr>";
    $content .= "<tr><td>Achternaam</td><td>".$signup["lastname"]."</td></tr>";
    $content .= "<tr><td>Woonplaats</td><td>".$signup["city"]."</td></tr>";
    $content .= "<tr><td>Geboortedatum</td><td>".$signup["birthdate"]."</td></tr>";
    $content .= "<tr><td>Geslacht</td><td>".translate_gender($signup["gender"])."</td></tr>";
    $content .= "<tr><td>Email</td><td>".$signup["email"]."</td></tr>";
    $content .= "<tr><td>Telefoonnummer</td><td>".$signup["phone"]."</td></tr>";
    $content .= "<tr><td>Lieveling</td><td>".$signup["partner"]."</td></tr>";
    $content .= "<tr><td>Motivatie</td><td>".$signup["motivation"]."</td></tr>";
    $content .= "<tr><td>Hoe ken je Familiar?</td><td>".$signup["familiar"]."</td></tr>";

    $content .= "<tr><td>Voorgaande edities</td><td>";
    $editions = explode(",", $signup["editions"]);
    foreach($editions as $edition) {
        $content .= translate_edition($edition) . "<br>";
    }
    $content .= "</td></tr>";

    $content .= "<tr><td>Eerste Keus</td><td>".translate_contrib($contrib0["type"])."</td></tr>";
    $content .= "<tr><td></td><td>".$contrib0["description"]."</td></tr>";
    if( $contrib0["needs"] != "" ) {
        $content .= "<tr><td></td><td>".$contrib0["needs"]."</td></tr>";
    }
    $content .= "<tr><td>Tweede Keus</td><td>".translate_contrib($contrib1["type"])."</td></tr>";
    $content .= "<tr><td></td><td>".$contrib1["description"]."</td></tr>";
    if( $contrib1["needs"] != "" ) {
        $content .= "<tr><td></td><td>".$contrib1["needs"]."</td></tr>";
    }
    $content .= "<tr><td>Voorbereidingen</td><td>".$signup["preparations"]."</td></tr>";
    $content .= "<tr><td>Datum inschrijving</td><td>".$signup["signupdate"]."</td></tr>";
    $content .= "</table><br><br>";
    $content .= get_email_footer();
    $content .= "</html>";

    send_mail($email, $fullname, $subject, $content);
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

    <title>Familiar Forest 2016</title>

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
                <?php if( $weberror == "" ) {
                    echo "
                    <h1>Inschrijving compleet!</h1>
                    <div>
                    <p>Lieve ".$firstname.",</p>
                    <p>
                        Bedankt voor je inschrijving! We hebben al je gegevens in goede orde ontvangen en je ontvangt ook nog een email ter bevestiging. 14 mei 2016 zal de loting plaatsvinden en word je gebeld als jouw naam hieruit rolt. Je kunt onze <a href='https://www.facebook.com/events/591534081011159/'>Facebook</a> pagina in de gaten houden voor het laatste nieuws!
                    </p>
                    <p>
                        Als je zorgen, vragen of iets anders kwijt wilt, kan je mailen naar ".$mailtolink.".
                    <p>
                    De high fives zijn gratis, de knuffels oprecht en de liefde oneindig.
                    </p>
                     <p>
                    Familiar Forest
                    <br>
                    <img src='img/logo_small.png' alt='Stichting Familiar Forest'>
                </p>
            </div>";
                } else {
                    echo "<div class='alert alert-danger' role='alert'>".$weberror."</div>";
                }
                ?>
        </div>

        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.3.min.js"><\/script>')</script>
        <script src="js/vendor/bootstrap.min.js"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    </body>
</html>