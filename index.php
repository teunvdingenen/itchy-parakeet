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

        <title>Stichting Familiar Forest</title>

        <!-- Bootstrap core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap social CSS -->
        <link href="css/bootstrap-social.css" rel="stylesheet">
        <link href="fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet">


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
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Familiar Forest is in volle gang!</h4>
              </div>
              <div class="modal-body">
                Vanaf 5 september worden de eerste ontdekkingen gedaan in Nieuw Babylon. Het kan zijn dat we minder goed bereikbaar zijn dan je van ons gewend bent. We zullen tot 10 september onze mail blijven lezen op <a href="mailto:info@stichtingfamiliarforest.nl">info@stichtingfamiliarforest.nl</a>. <br>
                <?php if( strtotime('now') > strtotime('2016-09-09 00:00') ) { 
                    echo "Voor noodgevallen zijn we te bereiken op het telefoonnummer: +32 476 627 294.";
                } else {
                    echo "Vanaf 10 september zal hier een telefoonnummer beschikbaar zijn voor noodgevallen.";
                }
                ?>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Afsluiten</button>
              </div>
            </div>
          </div>
        </div>


        <div id="header" class="text-center">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">

                    </div>
                    <div class="col-md-6">
                        <h1 class="">Stichting Familiar Forest</h1>
                    </div>
                    <div class="col-md-3">

                    </div>
                </div>
            </div>
        </div>
        <?php if( strtotime('now') < strtotime('2016-06-29 00:00') ) {
        echo '<div id="signup-button" class="container">
            <div class="row">
                <div class="col-md-12">
                    <p><a class="btn btn-lg btn-primary btn-block" role="button" href="deelnemen">Deelnemen</a></p>
                </div>
            </div>
        </div>';
        } ?>

        <!--parallax 1 -->
        <section class="bg-1">
            <!--<p class="lead"></p>-->
        </section>
        <!--/parallax 1-->
        <div class="container">
            <hr class="">
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-3">
                        <h3 class="">Participatie</h3>
                        <div>Stichting Familiar Forest organiseert evenementen waarbij iedereen zijn steentje bij kan dragen. Co-creatie staat voorop en iedereen is deelnemer! Dit vertaalt zich niet alleen in kunstenaars, artiesten en muzikanten want er is voor iedereen plaats. Bijvoorbeeld op het gebied van gastvrijheid, koken, chaufferen, afwassen, opbouwen of afbouwen.</div>
                    </div>
                    <div class="col-md-3">
                        <h3 class="">Veiligheid</h3>
                        <div>Familiar Forest probeert een plaats neer te zetten waar je helemaal jezelf kan zijn. Los van conventies en met een eigen beeldtaal. Iedereen is welkom op een manier die je zelf kiest. Niets is te gek en alles is normaal.</div>
                    </div>
                    <div class="col-md-3">
                        <h3 class="">Samen op reis</h3>
                        <div>Familiar Forest is een reis. Iedereen vertrekt op hetzelfde moment per bus naar onze geheime locatie en vertrekt een dag later weer terug naar huis. Op deze manier kun je samen, op gelijke voet, het bos ontdekken.</div>
                    </div>
                    <div class="col-md-1"></div>
                </div>
            <!--/row-->
            <hr class="">
        </div>
        <!--/container-->
        <div class="divider"></div>
        <section class="bg-2">
            <div class="col-sm-10 col-sm-offset-1 text-center">
                <h1 class=""></h1>
            </div>
        </section>
        <div class="divider"></div>
        <div class="container">
            <div class="row">
                
                <div class="col-md-12">
                    <h3 class="text-center">De high fives zijn gratis, de knuffels oprecht en de liefde oneindig</h3>
                </div>

            </div>
        </div>
        <!--/container-->
        <div class="divider"></div>
        <!--parallax 2 -->
            <section class="bg-3 text-center">
            <h1 class=""></h1>
                <p class="lead"></p>
            </section>
        <!--/parallax 2-->
        <div id="footer" class="">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <p class="text-muted">Foto's van <a target="_blank" href="http://www.fotografiegroen.nl/">Jan Willem Groen</a> en <a target="_blank" href="https://www.facebook.com/studiogrinnitch/">Grinnitch Photography</a>.
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p class="text-muted">
                            Stichting Familiar Forest <br> Sint Antoniesbreestraat 108 <br> 1011HB Amsterdam <br>
                            Tel: +316 46 37 30 69<br>
                            KVK: 608 735 66 <br>
                            BTW: NL 854 097 739 B01 <br>
                            <a href='mailto:info@stichtingfamiliarforest.nl' target='_top'>info@stichtingfamiliarforest.nl</a>
                        </p>
                    </div>
                    <div class="col-md-5">
                    </div>
                    <div class=".col-xs-11 col-md-1">
                        <a href="https://www.facebook.com/FamiliarForest" target="_blank" class="btn btn-lg btn-social-icon btn-facebook text-right">
                            <span class="fa fa-facebook"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    	<!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
        <script src="js/vendor/bootstrap.min.js"></script>

        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>
        <script>
        $(document).ready(function() {
            setTimeout(
                function() {
                  $('#myModal').modal('toggle');
                }, 3000);
        });
        </script>
    </body>
</html>
