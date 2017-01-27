<?php
include "../functions.php";

include("checklogin.php");

if( $user_permissions & PERMISSION_RAFFLE != PERMISSION_RAFFLE ) {
    header('Location: oops.php');
}

if( !isset($_POST['remove'])) {
    return 1;
}
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    return false;
}

$toremove = $_POST['remove'];

$result = $mysqli->query(
    sprintf("UPDATE raffle set valid = 0 WHERE `%s` = '%s';",  
        $db_raffle_code, 
        $mysqli->real_escape_string($toremove)
    ));
if( $result === FALSE ) {
    return false;
}
$mysqli->close();
return true;
?>