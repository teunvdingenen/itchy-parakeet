<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_PARTICIPANT) != PERMISSION_PARTICIPANT ) {
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
                    <div class="col-xs-13 col-sm-10"> 
                        <div class="jumbotron">
                            <h2>Back to the FFFuture: '95</h2>
                            <p class='lead'> Daar zit je dan.. Tas gepakt met alle nostalgie, buurman doei gezegd, die toch wel een beetje vreemd keek toen je zei dat je een tijdreis ging maken. Maanden voorbereiding: Is de ruimte voor het jaartal in je tijdmachine maar twee cijfers groot. Lekker dan: millennium bug ten top.
                            </p>
                            <p>Maar goed, een beetje avontuur gaan we al lang niet meer uit de weg en "of je nu wel of niet terug komt met die super speciale limited edition tamagotchi, je vrienden en familie gaan mee dus gezellig wordt het sowieso", aldus Stef Uijens.</p>
                            <p>Ga je tas dus maar opnieuw inpakken voor het onbekende, het is ieders gok waar we precies uit gaan komen. Inschrijven voor de tijdreis doe je binnenkort op onze website en de tijdmachine is klaar met configureren op vrijdagochtend 27 april '18, hier in ieder geval wel echt te lezen als twee-duizend-achtien.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include("default-js.html"); ?>
        </body>
</html>
