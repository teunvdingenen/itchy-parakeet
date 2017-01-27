<?php
include "../functions.php";

include("checklogin.php");

if( $user_permissions & PERMISSION_DISPLAY != PERMISSION_DISPLAY ) {
    header('Location: oops.php');
}

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

$sqlresult = $mysqli->query("SELECT p.firstname, p.lastname, b.code, b.id, b.attending, b.ticket FROM buyer b join person p on p.email = b.email WHERE complete = 1 ORDER BY p.firstname");

if( !$sqlresult ) {
	echo $mysqli->error;
	return;
} 
$mysqli->close();

$result = array();
while($row = mysqli_fetch_array($sqlresult, MYSQLI_ASSOC)){
   $result[] = $row;
}
echo json_encode($result);
?>
