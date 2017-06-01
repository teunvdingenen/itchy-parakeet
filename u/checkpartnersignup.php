<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_PARTICIPANT) != PERMISSION_PARTICIPANT ) {
    echo '{"success": false}';
    exit;
}

if( !isset($_POST['email'])) {
    echo '{"success": false}';
    return;
}
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    echo '{"success": false}';
    $mysqli->close();
    return;
}

$email = $_POST['email'];

$result = $mysqli->query( sprintf("SELECT partner FROM $current_table WHERE `email` = '%s';",
        $mysqli->real_escape_string($email)));
$mysqli->close();
if( $result === FALSE ) {
    echo '{"success": false}';
    return;
}
if( $result->num_rows == 0 ) {
    echo '{"success": true, "status": 2}';
    return;
}
$partner = mysqli_fetch_array($result,MYSQLI_ASSOC)['partner'];
if( strcasecmp($partner, $user_email) == 0 ) {
    echo '{"success": true, "status": 0}';
} else {
    echo '{"success": true, "status": 1}';
}
?>