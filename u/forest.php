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
                            <h2>Familiar Forest en de Magiefabriek</h2>
                            <p>
                            Stef wist niet wat hij meemaakte. Hij had gewoon een kaartje gekocht, op aanraden van een vriend, voor een leuk festival in een bos. Je weet wel, beetje feesten enzo. Stef was dan wel een ervaren feestganger, dit overtrof toch al zijn ervaringen. Hij stond midden in de natuur naast een enorme machine, versierd met discoballen, waar wolken uitkwamen puffen. Wolken in alle vormen die je kon bedenken. 
                            </p><p>
                            ‘Hier worden dromen gemaakt’ zei een meisje, gehuld in een trouwjurk met elvenvleugeltjes. Ze gaf Stef een high-five. ‘Kom, dan laat ik je de rest zien’. 
                            </p><p>
                            Het meisje met de trouwjurk en elvenvleugeltjes nam Stef bij de hand. Ze wandelen door het bos langs allerlei machines en apparaten. Ze stopten bij een lopende band, waar verschillende leggings met alle kleuren van de regenboog uit kwamen rollen. ‘Pak er maar één!’ riep het meisje blij, en Stef deed een roze legging met unicorn printjes aan. Een klassieker. Ze liepen dieper het bos in naar een verlicht gebouw dat tussen de bomen en de struiken verstopt was. Boven hun hoofd was het een warboel van draden die door vogelkooien naar ingewikkelde systemen gingen. ‘Dit is de Burlesque Bohemian kamer’ zei het meisje, ‘hier wordt alle decoratie gemaakt’. Vanaf boven vielen er glitters op Stef zijn hoofd en zijn schouders. ‘Ja, het regent hier soms glitters, die gebruiken we weer voor andere plekken in het bos, we recyclen alles’. Stef z’n gezicht zat er direct vol mee. Het meisje pakte een apparaat van de grond dat er uitzag als een haarföhn, waarop ze hem volschoot met schmink. ‘Kom dan laat ik je nu ons pronkstuk zien’ riep ze trots, terwijl ze wat vers geperste lampenkappen meenam. 
                            </p><p>
                            Ze liepen verder langs betoverende bomen, geheime plekjes en magische paden. Ze kwamen langs de Tepel Tape-automaat, naast het discobollenvoertuig en tegenover de rookmachine-robot. Ze stopten even voor de verkleedmachine. ‘Ga maar’ glimlachte het meisje. De machine werkte een beetje als een wasstraat voor auto’s: Stef moest op een band gaan staan, werd langzaam een ruimte binnengereden en via automatische armen en haken werd hij verkleedt. Getransformeerd als een piratentijger liep hij verder. Bij een groot open veld, omringt door hoge bomen, stopten ze. ‘Kijk’, zei het meisje met een extra twinkeling in haar ogen, ‘dit is de muziekmachine’. Stef wist niet wat hij zag. Voor hem dansten honderden mensen, met hun voeten diep in de grond geworteld en hun vuisten hoog in de lucht gegooid. En boven hen allen stak een enorme machine de hemel in. Gigantische ronde mechanismen draaiden heen en weer. Er kwam vuur en rook uit, lasers schenen de nacht in. Stef wist niet of het nou een wolkenkrabber was, of een vuurspuwende draak. In het midden stond een vrouw met blonde lange haren en een koptelefoon op, draaiend aan de knoppen. ‘Hier worden de beats gemaakt’ zei het meisje. Vanuit enorme speakers werden ze het bos ingeblazen. Stef stampte zijn voeten in de grond en gooide zijn vuist de lucht in. Familiar Forest 2017, de Magiefabriek. Hij was erbij. Jij toch ook?
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include("default-js.html"); ?>
        </body>
</html>
