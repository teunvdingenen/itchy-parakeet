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
    return false;
}

$email = $_POST['email'];

$result = $mysqli->query("SELECT 1 FROM $current_table s WHERE s.email = '%s'",
	$mysqli->real_escape_string($email));
if( !$result || $result->num_rows < 1 ) {
	$result = $mysqli->query("SELECT 1 FROM person p WHERE p.email = '%s'",
		$mysqli->real_escape_string($email));
	if( !$result || $result->num_rows < 1 ) {
		$mysqli->close();
		exit;
	}
	$result = $mysqli->query("INSERT INTO $current_table (`email`, `contrib0_type`, `contrib0_desc`, `contrib0_need`, `contrib1_type`, `contrib1_desc`, `contrib1_need`, `round`, `terms0`, `terms1`, `terms2`, `terms3` VALUES ('%s' '', '','', '', '', '', 0, 'N', 'N', 'N', 'N')", $mysqli->real_escape_string($email);
}
$add_comma = FALSE;
$query = "UPDATE $current_table SET ";
if( isset($_POST['note'])) {
	$add_comma = TRUE;
	$query .= sprintf("`note` = '%s'", $mysqli->real_escape_string($_POST['note']);
}
if( isset( $_POST['share'])) { 
	if( $add_comma) { 
		$query .= ", ";
	}
	$add_comma = TRUE;
	$query .= sprintf("`share` = '%s'", $mysqli->real_escape_string($_POST['share']);
}
if( isset($_POST['task'])) {
	if( $add_comma) { 
		$query .= ", ";
	}
	$add_comma = TRUE;
	$query .= sprintf("`task` = '%s'", $mysqli->real_escape_string($_POST['task']);
}
$query .= sprintf(" WHERE `email` = '%s'", $mysqli->real_escape_string($email));
$result = $mysqli->query($query);

if( $result === FALSE ) {
	$mysqli->close();
    return false;
}
$mysqli->close();
return true;
?>