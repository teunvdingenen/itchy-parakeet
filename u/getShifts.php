<?php

include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_VOLUNTEERS) != PERMISSION_VOLUNTEERS &&
	( $user_permissions & PERMISSION_ACTS) != PERMISSION_ACTS ) {
	return;
}

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

$shift = "";

if( isset($_POST['shift'])) {
    $shift = $_POST['shift'];
} else {
	echo "noshift";
	$mysqli->close();
	return;
}
$result = $mysqli->query(sprintf("SELECT name, startdate, enddate, nrrequired FROM shifts WHERE task = '%s' ORDER BY startdate ASC",$mysqli->real_escape_string($shift)));
if( !$result ) {
	$mysqli->close();
	return;
}
$volunteers = array();
while( $row = mysqli_fetch_array($result,MYSQLI_ASSOC) ) {
	$vshift = array();
	$vshift['num'] = $row['nrrequired'];
	$vshift['startdate'] = $row['startdate'];
	$vshift['enddate'] = $row['enddate'];
	$vresult = $mysqli->query(sprintf("SELECT p.email, p.lastname, p.firstname, s.contrib0_desc FROM person p join $current_table s on p.email = s.email WHERE task = '%s'",$row["name"]));
	if( $vresult != FALSE ) {
		$varray = array();
		while ($vrow = mysqli_fetch_array($vresult,MYSQLI_ASSOC)) {
			$varray[] = $vrow;
		}
		$vshift['volunteers'] = $varray;
	} else {
		echo "err";
	}
	$volunteers[$row['name']] = $vshift;
}

$mysqli->close();

echo json_encode($volunteers);

?>