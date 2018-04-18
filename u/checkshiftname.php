<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_VOLUNTEERS) != PERMISSION_VOLUNTEERS ) {
    exit;
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

$result = $mysqli->query(sprintf("SELECT 1 FROM `shifts` s WHERE s.name = '%s'",
	$mysqli->real_escape_string($name)));
$mysqli->close();
if( !$result || $result->num_rows >= 1 ) {
	echo 1;
    return;
}
echo 0;
return;
?>