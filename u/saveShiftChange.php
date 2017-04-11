<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_VOLUNTEERS) != PERMISSION_VOLUNTEERS ) {
	echo 1;
	return;
}
$name = $nrrequired = "";
if( !isset($_POST['name'])) {
    echo 2;
    return;
}
if( !isset($_POST['nrrequired'])) {
	echo 3;
	return;
}
$nrrequired = $_POST['nrrequired'];
$name = $_POST['name'];
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
	echo 4;
    return;
}

$result = $mysqli->query( sprintf("UPDATE `shifts` SET `nrrequired` = %s WHERE `name` = '%s'",
        $mysqli->real_escape_string($nrrequired),
        $mysqli->real_escape_string($name)));
$mysqli->close();
if( $result === FALSE ) {
	echo 5;
    return;
}
echo 0;
return;
?>