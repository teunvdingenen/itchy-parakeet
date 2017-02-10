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

$result = $mysqli->query( sprintf("SELECT partner FROM $current_table WHERE `email` = '%s';",
        $mysqli->real_escape_string($email)));
if( $result === FALSE ) {
	$mysqli->close();
    return false;
}
$partner = mysqli_fetch_array($result,MYSQLI_ASSOC)['partner'];
$result = $mysqli->query( sprintf("SELECT p.email, p.firstname, p.lastname, s.partner FROM $current_table s join person p on p.email = s.email WHERE p.email = '%s';",
        $mysqli->real_escape_string($partner)));
if( $result === FALSE ) {
	echo $mysqli->error;
	$mysqli->close();
    return false;
}
$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
if( $row['partner'] == $email ) {
	$row['success'] = TRUE;
	echo json_encode($row);
} else {
	echo '{"success": false}';
}
$mysqli->close();
return true;
?>