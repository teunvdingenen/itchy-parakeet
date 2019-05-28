<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & (PERMISSION_DISPLAY | PERMISSION_BAR | PERMISSION_ACTS | PERMISSION_VOLUNTEERS)) == 0 ) {
  header('Location: oops.php');
}

$required_permissions = (PERMISSION_DISPLAY | PERMISSION_BAR | PERMISSION_ACTS | PERMISSION_VOLUNTEERS);
$request_for = 'buyer';

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
							<?php include("generic_filter.php"); ?>
							<?php include("stats.php"); ?>
							<?php include("pagination.php"); ?>
							<table class='table table-striped table-bordered table-hover table-condensed'>
								<thead>
									<tr class='header-row'><th>Ticket</th><th>Achternaam</th><th>Voornaam</th><th>Leeftijd</th><th>Woonplaats</th><th>Telefoon</th><th>Email</th><th>Transactie</th><th>Taak</th><th>Taak Start</th><th>Taak einde</th><th>Aantal edities</th><th>Eerste keus</th><th></th><th>Tweede keus</th><th></th><th>Voorgaande edities</th><th>Lieveling</th>
								</thead>
								<tbody>
							<?php
							while($row = mysqli_fetch_array($sqlresult,MYSQLI_ASSOC))
							{
								echo "<tr>";
								echo "<td><a class='btn btn-block btn-info' href='ticketpdf?ticket=".$row['ticket']."' target='_blank'>Ticket</a>";
								echo "<td>" . $row['lastname'] . "</td>";
								echo "<td>" . $row['firstname'] . "</td>";
								$age = (new DateTime($row['birthdate']))->diff(new DateTime('now'))->y;
								echo "<td>" . $age . "</td>";
								echo "<td>" . $row['city'] . "</td>";
								echo "<td>" . $row['phone'] . "</td>";
								echo "<td>" . $row['email'] . "</td>";
								echo "<td>" . $row['transactionid'] . "</td>";
								echo "<td>" . translate_task($row['task']) . "</td>";
								echo "<td>" . $row['startdate'] . "</td>";
								echo "<td>" . $row['enddate'] . "</td>";
								echo "<td>" . $row['visits'] . "</td>";
								echo "<td>" . translate_contrib($row['contrib0_type']) . "</td>";
								echo "<td>" . $row['contrib0_desc'] . "</td>";
								echo "<td>" . translate_contrib($row['contrib1_type']) . "</td>";
								echo "<td>" . $row['contrib1_desc'] . "</td>";
								$editions_arr = explode(",",$row['editions']);
								$editions_str = "";
								foreach( $editions_arr as $edition ) {
									$editions_str .= translate_edition($edition)."<br>";
								}
								echo "<td>" . $editions_str . "</td>";
								echo "<td>" . $row['partner'] . "</td>";
								echo "</tr>";
							}
							?>
								</tbody>
							</table>
						</div>
						<?php include("pagination.php"); ?>
					</div>
                </div>
            </div>
        </div>

        <?php include("form-js.html"); ?>
        <script src="js/generic_filter.js"></script>
        </body>
</html>
