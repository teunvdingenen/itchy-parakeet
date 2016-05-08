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
    $menu_html .= "<li><a class='menulink' id='usermanage' href='users''>Gebruikers</a></li>";
    $menu_html .= "</ul>";
}

$firstname = $lastname = $gender = $contrib = $requestedage = $optionalnotempty = $agetype = $visits = $visitstype = "";

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
                <form id="user-form" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">
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
                                <option value="iv" <?= $contrib == 'iv' ? ' selected="selected"' : '';?>>Interieur verzorging</option>
                                <option value="bar" <?= $contrib == 'bar' ? ' selected="selected"' : '';?>>Bar</option>
                                <option value="keuken" <?= $contrib == 'keuken' ? ' selected="selected"' : '';?>>Keuken</option>
                                <option value="act" <?= $contrib == 'act' ? ' selected="selected"' : '';?>>Act of Performance</option>
                                <option value="afb" <?= $contrib == 'afb' ? ' selected="selected"' : '';?>>Afbouw</option>
                            </select>
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
                        <label for="optionalnotempty" class="col-sm-2 form-control-label">Optionele velden</label>
                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input class="checkbox" type="checkbox" id="optionalnotempty" name="optionalnotempty" value="J" <?php echo ($optionalnotempty=='J' ? 'checked' : '');?>>
                                    Niet leeg
                                </label>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-primary" type="submit">Filteren</button>
                </form>
                <div style='margin-top: 5px;'>
                    <?php echo $resultHTML ?>
                </div>
                <div><button class='btn btn-lg btn-primary btn-block' id='confirm' onclick="storeWinners();">Inloten</button></div>
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
