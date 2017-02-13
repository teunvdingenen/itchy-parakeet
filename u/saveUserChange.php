<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_EDIT) != PERMISSION_EDIT ) {
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

$result = $mysqli->query(sprintf("SELECT 1 FROM users u WHERE u.email = '%s'",
	$mysqli->real_escape_string($email)));
if( !$result || $result->num_rows < 1 ) {
	return 1;
}

$query = "UPDATE users SET ";
if( isset($_POST['permission_add'])) {
	$query .= sprintf("`permissions` = permissions | %s", $mysqli->real_escape_string($_POST['permission_add']));
} else if( isset( $_POST['permission_remove'])) { 
	$query .= sprintf("`permissions` = permissions ~ %s", $mysqli->real_escape_string($_POST['permission_remove']));
} else if( isset( $_POST['permission_set'])) {
	$query .= sprintf("`permissions` = %s", $mysqli->real_escape_string($_POST['permission_set']));
} else {
	return 1;
}
$query .= sprintf(" WHERE `email` = '%s'", $mysqli->real_escape_string($email));
$result = $mysqli->query($query);
$mysqli->close();
if( $result === FALSE ) {	
    return 1;
}
return 0;
?>