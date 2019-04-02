<?php

include "../functions.php";

include("checklogin.php");

$tasks_act = array("act", "game", "schmink", "other_act", "perform", "install", "workshop", "acteren");
$tasks_vol = array("keuken", "bar", "other", "iv", "thee", "camping", "afb");
$default_task = '';
if( ($user_permissions & PERMISSION_VOLUNTEERS) != PERMISSION_VOLUNTEERS && 
	($user_permissions & PERMISSION_ACTS) != PERMISSION_ACTS ) {
	return;
}

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

$task = $type = $contrib = "";

if( isset($_POST['contrib'])) {
    $contrib = $_POST['contrib'];
}
if( isset($_POST['type'])) {
    $type = $_POST['type'];
}
if( in_array($contrib, $tasks_act)) {
	$default_task = 'act';
}
$filter = sprintf("s.complete = 1 AND s.task = '%s'", $mysqli->real_escape_string($default_task));

if( $type == "contrib0") {
	$filter .= " AND s.contrib0_type = '".$mysqli->real_escape_string($contrib)."'";
} else if( $type == "contrib1" ) {
	$filter .= " AND s.contrib1_type = '".$mysqli->real_escape_string($contrib)."'";
} else {
	
}
$result = $mysqli->query("SELECT p.email, p.lastname, p.firstname, s.contrib0_desc FROM person p join $current_table s on p.email = s.email WHERE ".$filter);
$mysqli->close();
if( !$result ) {
	//echo $mysqli->error;
	echo "{}";
} else {
	$volunteers = array();
	while( $row = mysqli_fetch_array($result,MYSQLI_ASSOC) ) {
		$volunteers[] = $row;
	}
	echo json_encode($volunteers);
}

?>