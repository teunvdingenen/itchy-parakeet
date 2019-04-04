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
                            <h2>Familiar Forest 2019 : Evolutie van de homo familiaris</h2>
                            <p class='lead'>
                              Ja ja, echt vandaag! Waarschijnlijk rond 20u
                            </p>
                            <!--
                            <p class='lead'>We gaan een revolutie starten, een revolutie van kleur! Komende editie wordt de kleurrijkste Familiar tot nu toe. We gaan de lucht roze verven, het bos blauw, het gras paars en de sterren goud. We zullen zoveel kleur in het bos gooien, dat je aan het einde van het weekend alleen nog maar regenboog poept.
                            </p>
                            <p>
                            Je gaat kleuren zien die je niet eens kende. Het geelste geel, het turquoiseste turquoise. Wist je dat je oranje kan ruiken? Heb je weleens rood gevoeld?
                            </p><p>
                            Samen zullen we het weekend inkleuren met een palet aan muziek, acts, grappen en grollen, gratis high-fives, oprechte knuffels, heel veel glitters en nog meer liefde. Speciaal voor deze editie is Stef Uijens lid geworden van de Blue Man Group. Wat draag jij bij?
                            </p><p>
                            Broeders en zusters, verenig jullie. Het is tijd voor de kleurenrevolutie!
                            </p>
                            <blockquote class="blockquote">
                              <p class="mb-0">‘Van Afrika tot in Amerika, ja we zijn zoveel mooier als we samen zijn. Hand in hand, oog in oog, alle kleuren van de regenboog’</p>
                              <footer class="blockquote-footer">Kristel Verbeke, 2001. <cite title="Source Title">2001</cite></footer>
                            </blockquote>
                          -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include("default-js.html"); ?>
        </body>
</html>
