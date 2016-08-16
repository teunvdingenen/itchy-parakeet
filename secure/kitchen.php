<?php session_start();
include "../functions.php";
include "createmenu.php";

if(!isset($_SESSION['loginuser'])) {
    header('Location: ../login');
}

$menu_html = "";
$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info[$db_user_name];
$user_info_permissions = $user_info[$db_user_permissions];

if( $user_info_permissions & PERMISSION_VOLUNTEERS != PERMISSION_VOLUNTEERS ) {
    return false;
}

$menu_html = get_menu_html();

$resultHTML="<table class='table table-striped table-bordered table-hover table-condensed'>";
$resultHTML.="<thead><tr class='header-row'>";
$resultHTML.="<th>Voornaam</th>";
$resultHTML.="<th>Achternaam</th>";
$resultHTML.="<th>Email</th>";
$resultHTML.="<th>Telefoon</th>";
$resultHTML.="<th>Keuze</th>";
$resultHTML.="<th>Omschrijving</th>";
$resultHTML.="<th>Benodigdheden</th>";
$resultHTML.="<th>Keuze</th>";
$resultHTML.="<th>Omschrijving</th>";
$resultHTML.="<th>Benodigdheden</th>";
$resultHTML.="<th>Nummer</th>";
$resultHTML.="<th>Aantekening</th>";
$resultHTML.="<th>Taak</th>";
$resultHTML.="</tr></thead>";

$email_adr = "";

$query = "SELECT p.firstname, p.lastname, p.email, p.phone, c0.type, c0.description, c0.needs, c1.type, c1.description, c1.needs, b.number, b.note FROM buyer b join person p on p.email = b.email join contribution c0 on c0.id = p.contrib0 join contribution c1 on c1.id = p.contrib1 WHERE b.task = 'keuken' AND b.complete = 1 ORDER BY b.number";

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    return false;
} else {
    $sqlresult = $mysqli->query($query);
}
$mysqli->close();
$count=0;
while($row = mysqli_fetch_array($sqlresult,MYSQLI_NUM))
{
    $count+=1;
    $resultHTML.="<tr>";
    foreach($row as $key=>$value) {
        if( $key == 2) {
            $resultHTML.= "<td><div class='table-cell email'>" . $value . "</div></td>";
            $email_adr .= $value . ", ";
        } else if( $key == 10) {
            $resultHTML.= "<td><div class='table-cell'><input class='form-control' type='text' id='number' value=".$value."></div></td>";
        } else if( $key == 4 || $key == 7 ) {
            $resultHTML.= "<td><div class='table-cell ".$key."'>" . translate_contrib($value) . "</div></td>";
        } else if( $key == 11 ) { 
            $resultHTML .= "<td><textarea cols='60' rows='4'>".$value."</textarea></td>";
        } else {
            $resultHTML.= "<td><div class='table-cell ".$key."'>" . $value . "</div></td>";
        }
    }
    $resultHTML.="<td><div class='table-cell'><select class='form-control'>
                                <option value=''>Naar vrijwilligers..</option>
                                <option value='keuken' selected>Keuken</option>
                                <option value='bar'>Bar</option>
                                <option value='other'>Anders</option>
                                <option value='interiour'>Interieur</option>
                                <option value='thee'>Theetent</option>
                                <option value='camping'>Campingwinkel</option>
                                <option value='afbouw'>Afbouw</option>
                                <option value='act'>Naar Acts..</option>
                            </select></div></td>";
    $resultHTML.="<td class='changed' style='display:none;'>0</div>";
    $resultHTML.="</tr>";
}
$resultHTML .= "</table>";
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
        <link rel="icon" href="../favicon.ico">

        <title>Familiar Forest Dashboard</title>

        <!-- Bootstrap core CSS -->
        <link href="../css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="../css/main.css" rel="stylesheet">

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
        <nav class="navbar navbar-inverse navbar-fixed-top">
          <div class="container-fluid">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="#">Familiar Forest Festival</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
              <ul class="nav navbar-nav navbar-right">
                <li><a class='menulink' href='logout.php'>Logout</a></li>
              </ul>
            </div>
          </div>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-3 col-md-2 sidebar">
                  <?php echo $menu_html ?>
                </div>
            </div>
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <div class='btn btn-primary btn-lg btn-block' id='save' onclick="saveVolunteerChanges();">Opslaan 
                    <i class='glyphicon glyphicon-floppy-disk'></i>
                </div>
                <div class='alert alert-info'><span class='glyphicon glyphicon-user' aria-hidden='true'></span> <?=$count?> sous-chefs</div>
                <div style='margin: 5px;'>
                    <?php echo $resultHTML ?>
                </div>
                <a id="togglebutton_email" class="btn btn-info btn-sm btn-block" role="button" data-toggle="collapse" data-target="#email-panel">Email adressen voor deze selectie <i class='glyphicon glyphicon-chevron-right'></i></a>
                <div class="row">
                    <div id="email-panel" class="collapse email-panel">
                        <div class="panel panel-default">
                            <div id="emailcontent" class="panel-body">
                                <textarea cols='60' rows='4' readonly><?= $email_adr ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
        <script src="../js/vendor/bootstrap.min.js"></script>

        <script src="../js/plugins.js"></script>
        <script src="../js/main.js"></script>
        <script src="js/secure.js"></script>
        <script src="js/volunteer.js"></script>
        <script>
        $('#togglebutton_email').on('click', function(){
            $(this).children().closest('.glyphicon').toggleClass('glyphicon-chevron-right glyphicon-chevron-down');
        });
        </script>
    </body>
</html>
