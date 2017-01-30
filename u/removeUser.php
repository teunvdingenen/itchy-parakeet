<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_USER) != PERMISSION_USER ) {
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
    sprintf("DELETE FROM `%s` WHERE `%s` = '%s';", 
        $db_table_users, 
        $db_user_username, 
        $mysqli->real_escape_string($toremove)
    ));
return $result;
?>