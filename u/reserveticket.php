<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_PARTICIPANT) != PERMISSION_PARTICIPANT ) {
	echo 1;
	return;
}
if( !isset($_POST['code'])) {
    echo 2;
    return;
}
$code = $_POST['code'];
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
	echo 4;
    return;
}

$result = $mysqli->query(sprintf("UPDATE `swap` set `buyer` = '%s', `lock_expire` = date_add(now(), INTERVAL 1 day) where `code` = '%s' and `lock_expire` < now()",
	$mysqli->real_escape_string($user_email),
	$mysqli->real_escape_string($code)));
$affect = $mysqli->affected_rows;
$mysqli->close();
if( $affect != 1 ) {
	echo 5;
	return;	
}
if( $result === FALSE ) {
	echo 6;
    return;
}
echo 0;
return;
?>