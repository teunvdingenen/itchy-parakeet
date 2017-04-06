<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_VOLUNTEERS) != PERMISSION_VOLUNTEERS ) {
	echo 1;
	return;
}
$name = $email = "";
if( isset($_POST['name'])) {
    $name = $_POST['name'];
}
if( !isset($_POST['email'])) {
	echo 1;
	return;
}
$email = $_POST['email'];
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
	echo 1;
    return;
}

$result = $mysqli->query( sprintf("UPDATE $current_table SET `task` = '%s' WHERE `email` = '%s'",
        $mysqli->real_escape_string($name),
        $mysqli->real_escape_string($email)));
$mysqli->close();
if( $result === FALSE ) {
	echo 1;
    return;
}
echo 0;
return;
?>