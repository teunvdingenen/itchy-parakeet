<?php
include "../functions.php";

include("checklogin.php");

if( $user_permissions & PERMISSION_CALLER != PERMISSION_CALLER ) {
    header('Location: oops.php');
}

$statistic_string = "";
$resultHTML="<table class='table table-striped table-bordered table-hover table-condensed'>";
$resultHTML.="<thead><tr class='header-row'>";
$resultHTML.="<th>Gebeld</th>";
$resultHTML.="<th>Niet opgenomen</th>";
$resultHTML.="<th>Voornaam</th>";
$resultHTML.="<th>Achternaam</th>";
$resultHTML.="<th>Email</th>";
$resultHTML.="<th>Telefoon</th>";
$resultHTML.="<th>Code</th>";
$resultHTML.="<th>Status</th>";
$resultHTML.="<th>Motivatie</th>";
$resultHTML.="</th></thead>";

//Statistics

$email = $firstname = $lastname = $rafflecode = "";
$notcontacted = "Y";
$filtersql = array();
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

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
    if( !empty($_POST["rafflecode"]) ) {
        $rafflecode = test_input($_POST["rafflecode"]);
        if( $rafflecode != "" ) {
            $filtersql[] = "s.rafflecode = '" . $mysqli->real_escape_string($rafflecode)."'";
        }
    }
    if( !empty($_POST["notcontacted"]) ) {
        $notcontacted = test_input($_POST["notcontacted"]);
    }
}
if( $notcontacted == 'Y') {
    $filtersql[] = "r.called != 1 AND r.called != 4";
}

$filterstr = "1";
foreach($filtersql as $filter) {
    $filterstr .= " AND " . $filter;
}

$sqlresult = "";
$query = "SELECT p.firstname, p.lastname, p.email, p.phone, s.rafflecode, s.called, s.motivation FROM person p join $current_table s on s.email = p.email WHERE r.valid = 1 AND ".$filterstr;

if( $mysqli->connect_errno ) {
    return false;
} else {
    $sqlresult = $mysqli->query($query);
}
$mysqli->close();

while($row = mysqli_fetch_array($sqlresult,MYSQLI_NUM))
{
    $code = $row[4];
    $resultHTML.="<tr>";
    $resultHTML.="<td>";
    if( $row[5] != 1 ) {
        $resultHTML.="<button class='btn btn-sm btn-success btn-block' id='noanswer' onclick=called('".$code."');>Gebeld!</button>";
    }
    $resultHTML.="</td>";
    $resultHTML.="<td>";
    if( $row[5] != 1 ) {
        $resultHTML.="<button class='btn btn-sm btn-info btn-block' id='noanswer' onclick=called('".$code."');>Geen antwoord </button>";
    }
    $resultHTML.="</td>";
    foreach($row as $key=>$value) {
        if( $key == 2 ) {
            $resultHTML.= "<td><div id='email' class='table-cell'>" . $value . "</div></td>";
        } else if ($key == 4 ) {
            $resultHTML.= "<td><div id='code' class='table-cell'>" . $value . "</div></td>";
        } else if( $key == 5 ) {
            $Bvalue = '';
            if( $value == 0 ) $Bvalue = 'Niet gecontact';
            else if( $value == 1 ) $Bvalue = 'Gebeld';
            else if( $value == 2 ) $Bvalue = 'In behandeling'; 
            else if( $value == 3 ) $Bvalue = 'Niet opgenomen'; 
            else if( $value == 4 ) $Bvalue = 'Mailen'; 
            $resultHTML.= "<td><div class='table-cell'>" . $Bvalue . "</div></td>";
        } else {
            $resultHTML.= "<td><div class='table-cell'>" . $value . "</div></td>";
        }
    }
    $resultHTML.= "</tr>";
}

$resultHTML.="</table>";

?>

<!doctype html>
<html class="no-js" lang="">
    <?php include("head.html"); ?>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <?php include("header.php"); ?>
        <div class="page-container">
            <?php include("header.php"); ?>
            
            <div class="container">
                <div class="row row-offcanvas row-offcanvas-left">

                <?php include("navigation.php");?>

                <div class="col-xs-12 col-sm-9"> 
                <div id='statcontent' class="container-fluid">

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
                        <label for="rafflecode" class="col-sm-2 form-control-label">Loting Code</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" id="rafflecode" placeholder="Loting Code" value="<?php echo $rafflecode;?>" name="rafflecode">
                        </div>
                    </div>
                    <div class = "form-group row">
                        <label for = "notcontacted" class="col-sm-2 form-control-label">Gecontact?</label>
                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="notcontacted" id="notcontacted" value="Y" <?php if($notcontacted == 'Y') echo( "checked"); ?> >
                                    Nog niet bereikt
                                </label>
                            </div>
                        </div>
                    <button class="btn btn-sm btn-primary" type="submit">Filteren</button>
                </form>
                <div><?php echo $resultHTML ?></div>
            </div>
            </div>
        </div>

	<?php include("default-js.html"); ?>
        <script src="js/called.js"></script>
    </body>
</html>

