<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_DISPLAY) != PERMISSION_DISPLAY ) {
    header('Location: oops.php');
}

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
        $('#togglebutton_email').on('click', function(){
            $(this).children().closest('.glyphicon').toggleClass('glyphicon-chevron-right glyphicon-chevron-down');
        });
        </script>
    </body>
</html>
