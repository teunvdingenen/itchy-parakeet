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

$result = $mysqli->query(sprintf("SELECT 1 FROM $current_table s WHERE s.email = '%s'",
	$mysqli->real_escape_string($email)));
if( !$result || $result->num_rows < 1 ) {
	return 1;
}

$add_comma = FALSE;
$query = "UPDATE $current_table SET ";
if( isset($_POST['note'])) {
	if( $add_comma) { 
		$query .= ", ";
	}
	$add_comma = TRUE;
	$query .= sprintf("`note` = '%s'", $mysqli->real_escape_string($_POST['note']));
}
if( isset( $_POST['share'])) { 
	if( $add_comma) { 
		$query .= ", ";
	}
	$add_comma = TRUE;
	$query .= sprintf("`share` = '%s'", $mysqli->real_escape_string($_POST['share']));
}
if( isset($_POST['task'])) {
	if( $add_comma) { 
		$query .= ", ";
	}
	$add_comma = TRUE;
	$query .= sprintf("`task` = '%s'", $mysqli->real_escape_string($_POST['task']));
}
$query .= sprintf(" WHERE `email` = '%s'", $mysqli->real_escape_string($email));
$result = $mysqli->query($query);
$mysqli->close();
if( $result === FALSE ) {
    return 1;
}

return 0;
?>