<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_DISPLAY) != PERMISSION_DISPLAY ) {
        header('Location: oops.php');
}

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

$query = "SELECT sw.code, sw.date_sold, sw.lock_expire, seller.email, seller.firstname, seller.lastname, buyer.email, buyer.firstname, buyer.lastname FROM `swap` sw left join person seller on seller.email = sw.seller left join person buyer on sw.buyer = buyer.email WHERE 1 order by sw.date_sold asc;";

$sqlresult = $mysqli->query($query);

if( !$sqlresult ) {
	echo $mysqli->error;
}
$mysqli->close();
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
                    <div class="col-xs-13 col-sm-10"> 
                    	<div style='margin-top: 5px;'>
							<table class='table table-striped table-bordered table-hover table-condensed'>
								<thead>
									<tr class='header-row'><th>Code</th><th>Te koop sinds</th><th>Reservering eindigd</th><th>Verkoper Email</th><th>Verkoper naam</th><th></th><th>Koper Email</th><th>Koper naam</th><th></th>
								</thead>
								<tbody>
							<?php
							while($row = mysqli_fetch_array($sqlresult,MYSQLI_NUM))
							{
								echo "<tr>";
								for( $i = 0; $i < 9; $i++ ) {
									echo "<td>" . $row[$i] . "</td>";
								}
								echo "</tr>";
							}
							?>
								</tbody>
							</table>
						</div>
					</div>
                </div>
            </div>
        </div>

        <?php include("form-js.html"); ?>
        <script src="js/generic_filter.js"></script>
        </body>
</html>
