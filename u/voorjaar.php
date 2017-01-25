<?php session_start();
include "../functions.php";

$user_email = $user_firstname = $user_permissions = "";

if(!isset($_SESSION['email'])) {
    header('Location: ../login');
} else {
    $user_email = $_SESSION['email'];
}
if(!isset($_SESSION['firstname'])) {
    header('Location: ../login');
} else {
    $user_firstname = $_SESSION['firstname'];
}
if(!isset($_SESSION['permissions'])) {
    header('Location: ../login');
} else {
    $user_permissions = $_SESSION['permissions'];
}

if( $user_permissions & PERMISSION_PARTICIPANT != PERMISSION_PARTICIPANT ) {
    header('Location: oops.php');
}

?>

<!doctype html>
<html class="no-js" lang="">
    <?php include("head.html"); ?>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div class="page-container">
        <?php include("header.php"); ?>
            <div class="container">
                <div class="row row-offcanvas row-offcanvas-left">
                    <?php include("navigation.php");?>
                    <div class="col-xs-12 col-sm-9">
                        <div class="form-intro-text">
                            <h2>Familiar Voorjaar</h2>
                            <p class='lead'> We zijn in de zomer op avontuur gegaan door magische bossen. We hebben samen kastelen veroverd. We hebben in een intergalactisch schip de zee bevaren. We hebben elkaar warm gehouden tijdens de winter. We hebben zelfs een keer met de hele familie gezellig gepicknickt. Nu is het tijd voor iets nieuws. Nu is het tijd voor Familiar Voorjaar.
                            </p>
                            <p>Ja, Familiar Voorjaar. Het beste van de winter en de zomer in één. Dansen tot diep in de nacht en dik chillen overdag. Op avontuur buiten in de natuur en springend in een rave-kelder indoor. Het voorjaar: de tijd om verliefd te worden. Daarom worden we het weekend van 5, 6 en 7 mei graag weer verliefd op jou. We beginnen op Bevrijdingsdag, want is er een betere plek om je vrijheid te vieren dan samen met je familie op Familiar? Familiar Freedom, baby. Voel je vrij om te zijn wie je wilt zijn en om samen met ons dansend, spelend en ravottend het voorjaar te bejubelen. Denk liefde, denk Stef Uijens met een bloemenkrans op z’n hoofd, denk geheime plekjes binnen en buiten. En denk natuurlijk aan een overweldigende portie oprechte voorjaarsknuffels, gratis high-fives en oneindige liefde. </p>
                            <div role="separator" class="divider"></div>
                            <p>Voor Familiar Voorjaar al er weer gebruik gemaakt worden van twee rondes waarin iedereen zich kan inschrijven en kans maakt op deelname. De eerste inschrijfronde loopt tot en met 15 februari 2017. Kort daarna is de loting en kun je een ticket kopen, of je aanmelden voor de tweede ronde. Tickets kopen voor de eerste ronde kan tot en met 9 maart 2017, inschrijven voor de tweede ronde kan tot en met 15 maart 2017. De laatste kaarten verkopen we op 6 april 2017 en dan hebben we nog een maandje voor we kunnen genieten van de aller eerste Familiar Voorjaar!
                        </div>
                        <a href='signup' class='btn btn-lg btn-block btn-primary'>Inschrijven</a>
                    </div>
                </div>
            </div>
        </div>

        <?php include("default-js.html"); ?>
        </body>
</html>
