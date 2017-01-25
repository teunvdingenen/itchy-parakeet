<?php session_start();
include "../functions.php";

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

if( $user_permissions & PERMISSION_VOLUNTEERS != PERMISSION_VOLUNTEERS ) {
    header('Location: oops.php');
}

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
                <div class="col-xs-12 col-sm-9"> 
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
    </div>
</div>

    	<?php include("default-js.html"); ?>
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
