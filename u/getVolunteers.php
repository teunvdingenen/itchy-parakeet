<?php

include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_VOLUNTEERS) != PERMISSION_VOLUNTEERS ) {
	return;
}

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

$task = $type = "";

if( isset($_POST['task'])) {
    $task = $_POST['task'];
}
if( isset($_POST['type'])) {
    $type = $_POST['type'];
}
$filter = "s.complete = 1 AND s.task = ''";
/**if( $task != "" ) {
	$filter .= " AND s.task LIKE '".$mysqli->real_escape_string($task)."'";
} else {
	$filter .= " AND s.task != ''";
}
**/
if( $type == "contrib0") {
	$filter .= " AND s.contrib0_type = '".$mysqli->real_escape_string($task)."'";
} else if( $type == "contrib1" ) {
	$filter .= " AND s.contrib1_type = '".$mysqli->real_escape_string($task)."' AND s.contrib0_type NOT IN ('act','game','lecture','schmink','other','perform','install','workshop')";
} else {
	$filter .= " AND s.contrib0_type NOT IN ('act','game','lecture','schmink','other','perform','install','workshop')";
}
$result = $mysqli->query("SELECT p.email, p.lastname, p.firstname FROM person p join $current_table s on p.email = s.email WHERE ".$filter);
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