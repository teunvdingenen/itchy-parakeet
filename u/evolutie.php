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
                            <p class='lead'>Onderzoekers, historici, geschiedschrijvers en archeologen,
                            </p>
                            <p>
                              Wij willen jullie graag uitnodigen voor een spiksplinternieuwe expositie in een zeer tijdelijk museum die gebouwd wordt op een prachtige archeologische vondst. Tien jaar lang heeft hier een proces plaatsgevonden dat wij samen willen gaan onderzoeken.
                            </p><p>
                              De raarste dingen worden hier opgegraven: piepkleine spiegeltjes waarvan we vermoeden deze ooit op een bol zaten. Een rare fascinatie voor echt heel hard geluid waarvan vermoed wordt dat hierop werd “gedanst”. Daarnaast allerlei teksten over een vermoedelijk fictief persoon genaamd “Stef Uijens”. DJ’s booths van het verleden en de toekomst, piramides, kastelen, watervallen, zeppelins, tipi’s en grijze stucloper. Ze hadden het hier allemaal, deden het samen: voor elkaar. Wij noemen dit fenomeen: de evolutie van Homo Familiaris.
                            </p><p>
                              Op 7 en 8 september openen wij onze deuren op deze super geheime locatie. Om toegang te verkrijgen tot het museum moet men zich inschrijven. Daarvoor gebruiken we deze historisch accurate website. Hier kun je helemaal in thema jezelf inschrijven voor het bezoek. Dat kan tot en met 9 mei van dit jaar waarna nog een tweede ronde zal volgen.
                            </p>
                            <p>
                              Dus schrijf je in, nodig je vrienden en familie uit en dan ontdekken we samen het verleden en vieren we 10 jaar gratis high fives, oprechte knuffels en oneindige liefde,
                            </p>
                            <p>
                              Familiar Forest
                            </p>
                            </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include("default-js.html"); ?>
        </body>
</html>
