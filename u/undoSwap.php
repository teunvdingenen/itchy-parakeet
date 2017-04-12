<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_PARTICIPANT) != PERMISSION_PARTICIPANT ) {
	echo 1;
	return;
}

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
	echo 2;
    return;
}

$result = $mysqli->query(sprintf("DELETE FROM `swap` where `seller` = '%s' and lock_expire < now()",
	$mysqli->real_escape_string($user_email)));
$affect = $mysqli->affected_rows;
$mysqli->close();
if( $affect != 1 ) {
	echo 3;
	return;	
}
if( $result === FALSE ) {
	echo 4;
    return;
}
echo 0;
return;
?>