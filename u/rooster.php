<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_VOLUNTEERS) != PERMISSION_VOLUNTEERS || 
    ($user_permissions & PERMISSION_ACTS) != PERMISSION_ACTS ) {
    header('oops');
}

$task = "";
if( !isset($_GET['t'])) {
    header('indelen');
}
$task = $_GET['t'];

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
$shifts_result = $mysqli->query(sprintf("SELECT name, startdate, enddate FROM shifts where task = '%s' ORDER BY startdate ASC;",$mysqli->real_escape_string($task)));
if( !$shifts_result ) {
	header('indelen');
}

?>

<!doctype html>
<html class="no-js" lang="">
    <?php include("head.html"); ?>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div class="page-container">
        	<h1><?php echo translate_task($task); ?></h1>
        	<table class='table table-striped table-bordered table-hover table-condensed'>
        		<?php
        		$lastday = DateTime::createFromFormat('Y-m-d',"1970-1-1");
        		while($row = $shifts_result->fetch_array(MYSQLI_ASSOC)) {
        			$startdate = DateTime::createFromFormat('Y-m-d H:i:s', $row['startdate']);
                    $enddate = DateTime::createFromFormat('Y-m-d H:i:s', $row['enddate']);
					if ( $startdate->format('Y-m-d') != $lastday->format('Y-m-d') ) {
						echo "<tr><th>".$startdate->format('d-m-Y')."</th><td></td></tr>";
						$lastday = $startdate;
					}
        			echo "<tr>";
                    echo "<td>".$startdate->format('H:i')." tot ".$enddate->format('H:i')."</td>";
        			$result = $mysqli->query(sprintf("SELECT p.firstname, p.lastname FROM person p join $current_table s on p.email = s.email WHERE s.task = '%s'",$row['name']));
        			echo "<td>";
        			while($name_row = $result->fetch_array(MYSQLI_ASSOC)) {
        				echo $name_row['firstname']." ".$name_row['lastname'];
        				echo "<br>";
        			}
        			echo "</td></tr>";
        		}
        		$mysqli->close();
        		?>
        	</table>
        </div>

        <?php include("default-js.html"); ?>
        </body>
</html>
