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
    $menu_html .= "<li><a class='menulink' id='displaybuyers' href='buyers'>Verkochte tickets tonen</a></li>";
    $menu_html .= "</ul>";
}
if( $user_info_permissions & PERMISSION_RAFFLE ) {
    $menu_html .= "<ul class='nav nav-sidebar'>";
    $menu_html .= "<li><a class='menulink' id='raffle' href='raffle'>Loting <span class='sr-only'>(current)</span></a></li>";
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
    $menu_html .= "<li><a class='menulink' id='usermanage' href='#''>Gebruikers</a></li>";
    $menu_html .= "</ul>";
}

$resultHTML="<table class='table table-striped table-bordered table-hover table-condensed'>";
$resultHTML.="<thead><tr class='header-row'>";
$resultHTML.="<th>Inloten</th>";
$resultHTML.="<th>Achternaam</th>";
$resultHTML.="<th>Voornaam</th>";
$resultHTML.="<th>Geboortedag</th>";
$resultHTML.="<th>Geslacht</th>";
$resultHTML.="<th>Woonplaats</th>";
$resultHTML.="<th>Email</th>";
$resultHTML.="<th>Telefoon</th>";
$resultHTML.="<th>Motivatie</th>";
$resultHTML.="<th>Bekend door</th>";
$resultHTML.="<th>Voorgaande Edities</th>";
$resultHTML.="<th>Partner</th>";
$resultHTML.="<th>Eerste keus</th>";
$resultHTML.="<th></th>";
$resultHTML.="<th></th>";
$resultHTML.="<th>Tweede keus</th>";
$resultHTML.="<th></th>";
$resultHTML.="<th></th>";
$resultHTML.="<th>Voorbereiding</th>";
$resultHTML.="<th>Aantal bezoeken</th>";
$resultHTML.="<th>Leeftijd</th>";
$resultHTML.="</tr></thead>";
$resultHTML.="<tbody>";

$cell_keys = ['lastname', 'firstname', 'birthdate', 'gender', 'city', 'email', 'phone', 'motivation', 'familiar', 'editions', 'partner', 'contrib0','type0','needs0', 'contrib1','type1','needs1', 'visits', 'preparations'];

if( $user_info_permissions & PERMISSION_DISPLAY ) {
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if( $mysqli->connect_errno ) {
        return false;
    } else {
        $query = "SELECT p.lastname, p.firstname, p.birthdate, p.gender, p.city, p.email, p.phone, p.motivation, p.familiar, p.editions, p.partner, c0.type, c0.description, c0.needs, c1.type, c1.description, c1.needs, p.preparations, p.visits
            FROM person p join contribution c0 on p.contrib0 = c0.id join contribution c1 on p.contrib1 = c1.id
            WHERE  NOT EXISTS (SELECT 1 FROM $db_table_raffle as r WHERE  p.email = r.email)";
        $sqlresult = $mysqli->query($query);
        if( $sqlresult === FALSE ) {
             //error
        }
    }
    $mysqli->close();

    while($row = mysqli_fetch_array($sqlresult,MYSQLI_NUM))
    {
        $resultHTML.="<tr>";
        $resultHTML.="<td><input type='checkbox' id='' value='' ></td>";
        $i = 0;
        foreach($row as $value) {
            $resultHTML .= "<td><div id='".$cell_keys[$i]."' class='table-cell'>".$value."</div></td>";
            $i++;
        }
        $age = (new DateTime($row[2]))->diff(new DateTime('now'))->y;
        $resultHTML .= "<td><div id='age' class='table-cell'>".$age."</div></td>";
        $resultHTML.= "</tr>";
    }
    $resultHTML.="</tbody></table>";
} else {
    $resultHTML="You do not have the necessary permissions to view this page";
}

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
                <div id="filter_bar" class="filter_bar">
                    <div class="fieldpair">
                        <label class="filterlabel" for"genderfilter">Geslacht:</label>
                        <select name="genderfilter" id="genderfilter">
                            <option value="both">Beide</option>
                            <option value="Male">Man</option>
                            <option value="Female">Vrouw</option>
                        </select>
                    </div>
                    <div class="fieldpair">
                        <label class="filterlabel" for"agefilter">Leeftijd:</label>
                        <input name="agefilter" id="agefilter" type="text"/>
                    </div>
                    <div class="fieldpair">
                        <label class="filterlabel" for"city">Woonplaats:</label>
                        <input name="city" type="city" id="cityfilter"/>
                    </div>
                    <div class="fieldpair">
                        <label class="filterlabel" for="visits">Aantal edities</label>
                        <input name="visits" type="text" id="visitsfilter"/>
                    </div>
                    <div class="fieldpair">
                        <label class="filterlabel" for"editions">Was aanwezig bij:</label>
                        <input type="text" name="editions" id="editionsfilter"/>
                    </div>
                    <div class="fieldpair">
                        <label class="filterlabel" for"contrib">Bijdrage:</label>
                        <input type="text" name="contrib" id="contribfilter"/>
                    </div>
                    <div id="error" class="error"></div>
                </div>
                <?php echo $resultHTML ?>
                <div><button id='confirm' onclick="storeWinners();">Inloten</button></div>
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
        <script src="js/raffle.js"></script>
    </body>
</html>
