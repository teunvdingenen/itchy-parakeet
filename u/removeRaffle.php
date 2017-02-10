<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_RAFFLE) != PERMISSION_RAFFLE ) {
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

$result = $mysqli->query( sprintf("UPDATE $current_table set valid = 0 WHERE `email` = '%s';",
        $mysqli->real_escape_string($email)));
if( $result === FALSE ) {
	$mysqli->close();
    return false;
}
$mysqli->close();
return true;
?>