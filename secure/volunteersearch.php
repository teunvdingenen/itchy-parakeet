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

$filtersql = array();
$firstname = $lastname = $contrib = $gender = $contribnr = $email_adr = "";

if( $_SERVER["REQUEST_METHOD"] == "POST") {
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if( !empty($_POST["firstname"]) ) {
        $firstname = test_input($_POST["firstname"]);
        if( $firstname != "" ) {
            $filtersql[] = "p.firstname = '" . $mysqli->real_escape_string($firstname)."'";
        }
    }
    if( !empty($_POST["lastname"]) ) {
        $lastname = test_input($_POST["lastname"]);
        if( $lastname != "" ) {
            $filtersql[] = "p.lastname = '" . $mysqli->real_escape_string($lastname)."'";
        }
    }
    if( !empty($_POST["gender"]) ) {
        if( $_POST["gender"] == 'male') {
            $filtersql[] = "p.gender = 'male'";    
        } else if( $_POST["gender"] == 'female') {
            $filtersql[] = "p.gender = 'female'";
        }
        $gender = $_POST["gender"];
    }
    if( !empty($_POST["contrib"]) ) {
        $contrib = $_POST["contrib"];
        $contribselector = "c0";
        if( !empty($_POST["contribnr"])) {
            $contribnr = $_POST["contribnr"];
            if( $contribnr == 'contrib0') {
                $contribselector = 'c0';
            } else if ( $contribnr == 'contrib1') {
                $contribselector = 'c1';
            }
        }
        if( $contrib == '' || $contrib == 'all') {
            //nothing
        } else if( $contrib == 'act') {
            $filtersql[] = $contribselector.".type IN ('workshop', 'game', 'lecture', 'schmink', 'other', 'perform', 'install')";    
        } else {
            $filtersql[] = $contribselector.".type = '" . $mysqli->real_escape_string($contrib)."'";
        }
    }

    $filterstr = "1";
    foreach($filtersql as $filter) {
        $filterstr .= " AND " . $filter;
    }

    $query = "SELECT p.firstname, p.lastname, p.email, p.phone, c0.type, c0.description, c0.needs, c1.type, c1.description, c1.needs, b.number, b.note, b.task 
        FROM buyer b join person p on p.email = b.email join contribution c0 on c0.id = p.contrib0 join contribution c1 on c1.id = p.contrib1 
        WHERE b.complete = 1 AND " . $filterstr;

    if( $mysqli->connect_errno ) {
        return false;
    } else {
        $sqlresult = $mysqli->query($query);
    }
    $mysqli->close();

    while($row = mysqli_fetch_array($sqlresult,MYSQLI_NUM))
    {
        $resultHTML.="<tr>";
        foreach($row as $key=>$value) {
            if( $key == 2) {
                $resultHTML.= "<td><div class='table-cell email'>" . $value . "</div></td>";
                $email_adr .= $value . ", ";
            } else if( $key == 4 || $key == 7 ) {
                $resultHTML.= "<td><div class='table-cell ".$key."'>" . translate_contrib($value) . "</div></td>";
            } else if ($key == 12 ) {
                $resultHTML .= "<td><div class='table-cell ".$key."'>" . translate_task($value) . "</div></td>";
            } else {
                $resultHTML.= "<td><div class='table-cell ".$key."'>" . $value . "</div></td>";
            }
        }
        $resultHTML.="</tr>";
    }
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
                <form method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">
                    <div class="form-group row">
                        <label for="firstname" class="col-sm-2 form-control-label">Voornaam</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" id="firstname" placeholder="Voornaam" value="<?php echo $firstname;?>" name="firstname">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="lastname" class="col-sm-2 form-control-label">Achternaam</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" id="lastname" placeholder="Achternaam" value="<?php echo $lastname;?>" name="lastname">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2">Geslacht</label>
                        <div class="col-sm-10">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="gender" id="both" value="both" <?php if($gender == "both") echo( "checked"); ?> >
                                    Beide
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="gender" id="male" value="male" <?php if($gender == "male") echo( "checked"); ?>>
                                    Jongeman
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="gender" id="female" value="female" <?php if($gender == "female") echo( "checked"); ?> >
                                    Jongedame
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="contrib" class="col-sm-2 form-control-label">Bijdrage</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="contrib" id="contrib">
                                <option value="all" <?= $contrib == 'all' ? ' selected="selected"' : '';?>>Alles</option>
                                <option value="iv" <?= $contrib == 'iv' ? ' selected="selected"' : '';?>>Interieur verzorging</option>
                                <option value="bar" <?= $contrib == 'bar' ? ' selected="selected"' : '';?>>Bar</option>
                                <option value="keuken" <?= $contrib == 'keuken' ? ' selected="selected"' : '';?>>Keuken</option>
                                <option value="act" <?= $contrib == 'act' ? ' selected="selected"' : '';?>>Act of Performance</option>
                                <option value="afb" <?= $contrib == 'afb' ? ' selected="selected"' : '';?>>Afbouw</option>
                            </select>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="contribnr" id="contrib0" value="contrib0" <?php if($contribnr == "contrib0") echo( "checked"); ?>>
                                    Eerste keus
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="contribnr" id="contrib1" value="contrib1" <?php if($contribnr == "contrib1") echo( "checked"); ?> >
                                    Tweede keus
                                </label>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-primary" type="submit">Zoeken</button>
                </form>
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
        $(document).ready(function() {
            $('#togglebutton').on('click', function(){
                $(this).children().closest('.glyphicon').toggleClass('glyphicon-chevron-right glyphicon-chevron-down');
            });
            $('#togglebutton_email').on('click', function(){
                $(this).children().closest('.glyphicon').toggleClass('glyphicon-chevron-right glyphicon-chevron-down');
            });
        });
        </script>
    </body>
</html>
