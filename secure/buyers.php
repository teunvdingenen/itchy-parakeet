<?php session_start();
include "../functions.php";

if(!isset($_SESSION['loginuser'])) {
    header('Location: ../login');
}
$menu_html = "";
$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info[$db_user_name];
$user_info_permissions = $user_info[$db_user_permissions];

// Assemble menu:
if( $user_info_permissions & PERMISSION_DISPLAY ) {
    $menu_html .= "<ul class='nav nav-sidebar'>";
    $menu_html .= "<li><a class='menulink' id ='showstats' href='index'>Main</a></li>";
    $menu_html .= "<li><a class='menulink' id='displaysignup' href='signups'>Inschrijvingen tonen</a></li>";
    $menu_html .= "<li><a class='menulink' id='displayraffle' href='displayraffle'>Loting tonen</a></li>";
    $menu_html .= "<li><a class='menulink' id='displaybuyers' href='buyers'>Verkochte tickets tonen <span class='sr-only'>(current)</span></a></li>";
    $menu_html .= "</ul>";
}
if( $user_info_permissions & PERMISSION_RAFFLE ) {
    $menu_html .= "<ul class='nav nav-sidebar'>";
    $menu_html .= "<li><a class='menulink' id='raffle' href='raffle'>Loting</a></li>";
    $menu_html .= "</ul>";
}
if( $user_info_permissions & PERMISSION_EDIT ) {
    $menu_html .= "<ul class='nav nav-sidebar'>";
    $menu_html .= "<li><a class='menulink' id='editsignup' href='#''>Wijzigingen</a></li>";
    $menu_html .= "<li><a class='menulink' id='removesignup' href='#''>Verwijderen</a></li>";
    $menu_html .= "</ul>";
}
if( $user_info_permissions & PERMISSION_USER) {
    $menu_html .= "<ul class='nav nav-sidebar'>";
    $menu_html .= "<li><a class='menulink' id='usermanage' href='users''>Gebruikers</a></li>";
    $menu_html .= "</ul>";
}

function get_raffle() {
    global $db_host, $db_user, $db_pass, $db_name;
    global $db_table_raffle;
    
}

$statistic_string = "";
$resultHTML="<table class='table table-striped table-bordered table-hover table-condensed'>";
$resultHTML.="<thead><tr class='header-row'>";
$resultHTML.="<th>Voornaam</th>";
$resultHTML.="<th>Achternaam</th>";
$resultHTML.="<th>Email</th>";
$resultHTML.="<th>Telefoon</th>";
$resultHTML.="<th>Code</th>";
$resultHTML.="<th>Transactie ID</th>";
$resultHTML.="</th></thead>";

//Statistics

$sqlresult = "";
$query = "SELECT p.firstname, p.lastname, p.email, p.phone, b.code, b.id FROM person p join buyer b on p.email = b.email";
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    return false;
} else {
    $sqlresult = $mysqli->query($query);
}

if( $sqlresult === FALSE ) {
    echo $mysqli->error;
}
$mysqli->close();


while($row = mysqli_fetch_array($sqlresult,MYSQLI_ASSOC))
{
    $resultHTML.="<tr>";
    foreach($row as $key=>$value) {
        $resultHTML.= "<td><div id='".$key."' class='table-cell'>" . $value . "</div></td>";
    }
    $resultHTML.= "</tr>";
}

$resultHTML.="</table>";

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
            <div id="content" class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <?php echo $resultHTML ?>
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
    </body>
</html>