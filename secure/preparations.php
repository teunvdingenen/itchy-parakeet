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

if( $user_info_permissions & PERMISSION_DISPLAY != PERMISSION_DISPLAY ) {
    return false;
}

$menu_html = get_menu_html();

$resultHTML="<table class='table table-striped table-bordered table-hover table-condensed'>";
$resultHTML.="<thead><tr class='header-row'>";
$resultHTML.="<th>Voornaam</th>";
$resultHTML.="<th>Achternaam</th>";
$resultHTML.="<th>Email</th>";
$resultHTML.="<th>Voorbereidingen</th>";
$resultHTML.="</tr></thead>";

$query = "SELECT p.firstname, p.lastname, p.email, p.preparations from buyer b join person p on b.email = p.email where p.preparations != 'N' and b.complete = 1";

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    return false;
} else {
    $sqlresult = $mysqli->query($query);
}
if( !$sqlresult ) {
    echo $mysqli->error;
}
$mysqli->close();
$email_adr = "";
while($row = mysqli_fetch_array($sqlresult,MYSQLI_NUM))
{
    $resultHTML.="<tr>";
    foreach($row as $key=>$value) {
        if( $key == 2) {
        	$email_adr .= $value . ", ";
            $resultHTML.= "<td><div class='table-cell email'>" . $value . "</div></td>";
        } else {
            $resultHTML.= "<td><div class='table-cell ".$key."'>" . $value . "</div></td>";
        }
    }
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
