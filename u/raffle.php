<?php session_start();
include "../functions.php";
include "createmenu.php";

$user_email = $user_firstname = $user_permissions = "";

if(!isset($_SESSION['email'])) {
    header('Location: ../login');
} else {
    $user_email = $_SESSION['email'];
}
if(!isset($_SESSION['firstname'])) {
    header('Location: ../login');
} else {
    $user_firstname = $_SESSION['firstname'];
}
if(!isset($_SESSION['permissions'])) {
    header('Location: ../login');
} else {
    $user_permissions = $_SESSION['permissions'];
}

if( $user_permissions & PERMISSION_RAFFLE != PERMISSION_RAFFLE ) {
    return false;
}

$menu_html = get_menu_html();

$resultHTML="<table class='table table-striped table-bordered table-hover table-condensed'>";
$resultHTML.="<thead><tr class='header-row'>";
$resultHTML.="<th><input type='checkbox' id='selectall' value='' >Inloten</th>";
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

$debug = "";

$cell_keys = ['lastname', 'firstname', 'birthdate', 'gender', 'city', 'email', 'phone', 'motivation', 'familiar', 'editions', 'partner', 'contrib0','type0','needs0', 'contrib1','type1','needs1', 'visits', 'preparations'];
$email = $firstname = $lastname = $gender = $contrib = $contribnr = $requestedage = $agetype = $visits = $visitstype = "";

$round = -1; //TODO get current round
$limit = 50;
$page = 0;

if( $user_info_permissions & PERMISSION_DISPLAY ) {
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    $filtersql = array();
    if( !empty($_GET['p'])) {
        $page = $mysqli->real_escape_string($_GET['p']);
    }
    if( $_SERVER["REQUEST_METHOD"] == "POST") {
        if( !empty($_POST["email"]) ) {
            $email = test_input($_POST["email"]);
            if( $email != "" ) {
                $filtersql[] = "p.email = '" . $mysqli->real_escape_string($email)."'";
            }
        }
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
        if( !empty($_POST["requestedage"]) && !empty($_POST["agetype"])) {
            $requestedage = test_input($_POST["requestedage"]);
            $agetype = test_input($_POST["agetype"]);
            $operator = "";
            if( $agetype == "min") { 
                $operator = ">=";
            } else if( $agetype == "max") {
                $operator = "<=";
            } else if( $agetype == "exact") {
                $operator = "=";
            }
            $filtersql[] = "FLOOR(DATEDIFF (NOW(), p.birthdate)/365) ".$operator." '".$mysqli->real_escape_string($requestedage)."'";
        }
        if( !empty($_POST["visits"])) {
            $visits = test_input($_POST["visits"]);
            $visitstype = test_input($_POST["visitstype"]);
            $operator = "";
            if( $visitstype == "min") { 
                $operator = ">=";
            } else if( $visitstype == "max") {
                $operator = "<=";
            } else if( $visitstype == "exact") {
                $operator = "=";
            }
            $filtersql[] = "p.visits ".$operator." '".$mysqli->real_escape_string($visits)."'";
        }
        if( !empty($_POST["round"])) {
            $roundstr = test_input($_POST["round"]);
            if( $roundstr == "all") {
                $round = -1;
            } else if ($roundstr == "first") {
                $round = 0;
            } else if ($roundstr == "second") {
                $round = 1;
            } else if ($roundstr == "third") {
                $round = 2;
            }
        } 
    }
    if( $round != -1 ) {
        $filtersql[] = sprintf("p.round = %s", $mysqli->real_escape_string($round));
    }
    $filterstr = "";
    foreach($filtersql as $filter) {
        $filterstr .= " AND " . $filter;
    }

    $query = "SELECT COUNT(*) FROM person p join contribution c0 on p.contrib0 = c0.id join contribution c1 on p.contrib1 = c1.id
            WHERE NOT EXISTS (SELECT 1 FROM $db_table_raffle as r WHERE p.email = r.email and r.valid = 1)" . $filterstr;

    $sqlresult = $mysqli->query($query);
    if( $sqlresult === FALSE ) {
        echo $mysqli->error;
    }
    $row = mysqli_fetch_array($sqlresult, MYSQLI_NUM);
    $entries = $row[0];

    $pages = $entries / $limit;
    if( $page >= $pages ) {
        $page = $pages - 1;
    }
    if( $page < 0 ) {
        $page = 0;
    }
    $offset = $page * $limit;

    if( $mysqli->connect_errno ) {
        return false;
    } else {
        $query = sprintf("SELECT p.lastname, p.firstname, p.birthdate, p.gender, p.city, p.email, p.phone, p.motivation, p.familiar, p.editions, p.partner, c0.type, c0.description, c0.needs, c1.type, c1.description, c1.needs, p.preparations, p.visits
            FROM person p join contribution c0 on p.contrib0 = c0.id join contribution c1 on p.contrib1 = c1.id
            WHERE NOT EXISTS (SELECT 1 FROM $db_table_raffle as r WHERE  p.email = r.email AND r.valid = 1)" . $filterstr . " LIMIT %s OFFSET %s", $mysqli->real_escape_string($limit), $mysqli->real_escape_string($offset));
        $sqlresult = $mysqli->query($query);
        if( $sqlresult === FALSE ) {
             //error
        }
    }
    $pulled_partners = array();
    $nr_showing = 0;
    while($row = mysqli_fetch_array($sqlresult,MYSQLI_NUM))
    {
        $partneremail = $row[10];
        if( !in_array($row[5], $pulled_partners) ) {
            $nr_showing += 1;
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

            if( $partneremail != "" && $partneremail != $row[5] ) {
                $query = sprintf("SELECT p.lastname, p.firstname, p.birthdate, p.gender, p.city, p.email, p.phone, p.motivation, p.familiar, p.editions, p.partner, c0.type, c0.description, c0.needs, c1.type, c1.description, c1.needs, p.preparations, p.visits
                FROM person p join contribution c0 on p.contrib0 = c0.id join contribution c1 on p.contrib1 = c1.id
                WHERE p.email = '%s' AND NOT EXISTS (SELECT 1 FROM $db_table_raffle as r WHERE  p.email = r.email)", $mysqli->real_escape_string($partneremail));
                $partnersqlresult = $mysqli->query($query);
                if( $partnersqlresult === FALSE ) {
                    //error
                } else if( $partnerrow = mysqli_fetch_array($partnersqlresult, MYSQLI_NUM) ) {
                    if( $partnerrow[10] == $row[5]) {
                        $nr_showing += 1;
                        $resultHTML.="<tr class='info'>";
                        $resultHTML.="<td><input type='checkbox' id='' value='' ></td>";
                        $i = 0;

                        foreach($partnerrow as $value) {
                            $resultHTML .= "<td><div id='".$cell_keys[$i]."' class='table-cell'>".$value."</div></td>";
                            $i++;
                        }
                        $age = (new DateTime($partnerrow[2]))->diff(new DateTime('now'))->y;
                        $resultHTML .= "<td><div id='age' class='table-cell'>".$age."</div></td>";
                        $resultHTML.= "</tr>";

                        $pulled_partners[] = $partneremail;
                    }
                }
            }
        }
    }
    $resultHTML.="</tbody></table>";
    $resultHTML.="<p>".$nr_showing." voldoen aan de filter</p>";

    $mysqli->close();
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
                <a id="togglebutton" class="btn btn-info btn-sm btn-block" role="button" data-toggle="collapse" data-target="#stat-panel"><span class='glyphicon glyphicon-refresh spinning'></span></a>
                <div class="row">
                    <div id="stat-panel" class="collapse stat-panel">
                        <div class="panel panel-default">
                            <div id="statcontent" class="panel-body">
                                
                            </div>
                        </div>
                    </div>
                </div>
                <form id="user-form" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">
                    <div class="form-group row">
                        <label for="email" class="col-sm-2 form-control-label">Email</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="email" id="email" placeholder="Email" value="<?php echo $email;?>" name="email">
                        </div>
                    </div>
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
                        <label for="requestedage" class="col-sm-2 form-control-label">Leeftijd</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="agetype" id="agetype">
                                <option value="min" <?= $agetype == 'min' ? ' selected="selected"' : '';?>>Minimaal</option>
                                <option value="max" <?= $agetype == 'max' ? ' selected="selected"' : '';?>>Maximaal</option>
                                <option value="exact" <?= $agetype == 'exact' ? ' selected="selected"' : '';?>>Precies</option>
                            </select>
                            <input class="form-control" type="text" id="requestedage" placeholder="Leeftijd" value="<?php echo $requestedage;?>" name="requestedage">
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
                    <div class="form-group row">
                        <label for="visits" class="col-sm-2 form-control-label">Aantal Bezoeken</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="visitstype" id="visitstype">
                                <option value="min" <?= $visitstype == 'min' ? ' selected="selected"' : '';?>>Minimaal</option>
                                <option value="max" <?= $visitstype == 'max' ? ' selected="selected"' : '';?>>Maximaal</option>
                                <option value="exact" <?= $visitstype == 'exact' ? ' selected="selected"' : '';?>>Precies</option>
                            </select>
                            <input class="form-control" type="text" id="visits" placeholder="Bezoeken" value="<?php echo $visits;?>" name="visits">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="contrib" class="col-sm-2 form-control-label">Ronde</label>
                        <div class="col-sm-10">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="round" value="all" <?php if($round == -1) echo( "checked"); ?>>
                                    Alles
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="round" value="first" <?php if($round == 0) echo( "checked"); ?>>
                                    Eerste Ronde
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="round" value="second" <?php if($round == 1) echo( "checked"); ?> >
                                    Tweede Ronde
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="round" value="third" <?php if($round == 2) echo( "checked"); ?> >
                                    Derde Ronde
                                </label>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-primary" type="submit">Filteren</button>
                </form>
                <nav>
                    <ul class="pagination">
                        <li>
                            <a href=<?php echo "?p=".($page-1) ?> aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php 
                            for($i = 0; $i < $pages; $i++ ) {
                                printf("<li><a href='?p=%s''>%s</a></li>",$i,$i+1);
                            }
                        ?>
                        <li>
                            <a href=<?php echo "?p=".($page+1) ?> aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <div style='margin-top: 5px;'>
                    <?php echo $resultHTML ?>
                </div>
                <nav>
                    <ul class="pagination">
                        <li>
                            <a href=<?php echo "?p=".($page-1) ?> aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php 
                            for($i = 0; $i < $pages; $i++ ) {
                                printf("<li><a href='?p=%s''>%s</a></li>",$i,$i+1);
                            }
                        ?>
                        <li>
                            <a href=<?php echo "?p=".($page+1) ?> aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class='btn btn-lg btn-primary btn-block' id='confirm' onclick="storeWinners();">Inloten</div>
                <?=$debug?>
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
        <script>
        $(document).ready(function() {
            $.post("signupstats.php", {"type":"secondraffle"}, function(response){
                $("#statcontent").html($(response).find('table'));
                $("#togglebutton").html("Statistieken <i class='glyphicon glyphicon-chevron-right'>");
            });
            $('#togglebutton').on('click', function(){
                $(this).children().closest('.glyphicon').toggleClass('glyphicon-chevron-right glyphicon-chevron-down');
            });
        });
        </script>
    </body>
</html>
