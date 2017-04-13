<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_VOLUNTEERS) != PERMISSION_VOLUNTEERS ) {
	echo 1;
	return;
}

if( !isset($_POST['name'])) {
    echo 1;
    return;
}
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
	echo 1;
    return;
}

$name = $_POST['name'];

$result = $mysqli->query(sprintf("UPDATE $current_table SET `task` = '' WHERE `task` = '%s'",
	$mysqli->real_escape_string($name)));
if( !$result ) {
	echo 1;
	$mysqli->close();
	return;
}

$result = $mysqli->query( sprintf("DELETE FROM `shifts` WHERE `name` = '%s'",
        $mysqli->real_escape_string($name)));
if( $result === FALSE ) {
	echo 1;
	$mysqli->close();
    return;
}
if( $mysqli->affected_rows != 1 ) {
	echo 1;
	$mysqli->close();
	return;
}
$mysqli->close();
echo 0;
return;
?>