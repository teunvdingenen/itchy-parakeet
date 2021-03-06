<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_CALLER) != PERMISSION_CALLER ) {
    exit;
}

if( !isset($_POST['email'])) {
    return 1;
}
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    return 1;
}

$email = $_POST['email'];

$result = $mysqli->query(sprintf("SELECT 1 FROM $current_table s WHERE s.email = '%s'",
	$mysqli->real_escape_string($email)));
if( !$result || $result->num_rows < 1 ) {
	return 1;
}

$query = sprintf("UPDATE $current_table SET called = %s WHERE `email` = '%s'", 
	$mysqli->real_escape_string($_POST['called']),
	$mysqli->real_escape_string($email));

$result = $mysqli->query($query);
$mysqli->close();
if( $result === FALSE ) {
    return 1;
}
return 0;
?>