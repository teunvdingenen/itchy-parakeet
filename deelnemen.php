<?php session_start();
include_once "functions.php";

if(!isset($_SESSION['loginuser'])) {
    header('Location: login');
}
$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info['username'];
$user_info_permissions = $user_info['permissions'];

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Familiar Forest Deelnemen</title>
        <meta name="description" content="">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <link rel="icon" href="favicon.ico">
        <!-- Place favicon.ico in the root directory -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" type="text/css" media="all"
            href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/smoothness/jquery-ui.css"/> 
  </head>

  <body>
        <!-- TODO : Insert into login frame -->
        <div class="container">
            <div class="default-text">
                <h2>Familiar Forest 2016</h2>
                <p class="lead">
                   Lieve <?=get_firstname($user_info_name)?>,
                </p>
                <p>
                    Hieronder vindt je de mogelijkheden die je op dit moment hebt tot deelnemen aan de geplande Familiar Forest edities.
                </p>
                <p> De high fives zijn gratis, de knuffels oprecht en de liefde oneindig.<br>Familiar Forest</p>
                </div>

                <?php if( strtotime('now') < strtotime('2017-02-16 00:00') ) {
                    echo "<p><a class='btn btn-primary btn-lg' href='signup' role='button'>Inschrijven voor Familiar Voorjaar 2017</a></p>";
                }
                ?>
                <!--
                <p><a id="togglebutton" class="btn btn-info btn-lg" role="button" data-toggle="collapse" data-target="#signup-panel">Ik wil me inschrijven voor de tweede ronde <i class="glyphicon glyphicon-chevron-right"></i></a></p>
                <div class="row">
                    <div id="signup-panel" class="collapse signup-panel">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <form method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top" role="form">
                                    <div class="form-group">
                                        <div class="radio form-inline">
                                            <label for="true">
                                                <input type="radio" name="previous" id="true" value="true">
                                               Ik heb me voor de eerste ronde ingeschreven, mijn email adres is:
                                            </label>
                                            <input type="email" class="form-control input-sm" id="signup-email" name="email">
                                        </div>
                                        <div class="radio">
                                            <label for="false">
                                                <input type="radio" name="previous" id="false" value="false">
                                                Ik heb me niet ingeschreven voor de eerste ronde
                                            </label>
                                        </div>
                                    </div>
                                    <button style="margin-top:5px;"type="submit" class="btn btn-primary">
                                        <span class="glyphicon glyphicon-pencil"></span> Inschrijven
                                    </button> 
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            -->
            </div>

        </div><!-- /.container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.3.min.js"><\/script>')</script>
    <script src="js/vendor/bootstrap.min.js"></script>
    <script>
    $('#togglebutton').on('click', function(){
        $(this).children().closest('.glyphicon').toggleClass('glyphicon-chevron-right glyphicon-chevron-down');
    });
    </script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
  </body>
</html>
