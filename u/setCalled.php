<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_CALLER) != PERMISSION_CALLER ) {
    header('Location: oops.php');
}
$onlyprogress = false;

if( !isset($_POST['code'])) {
    echo 1;
}
if( !isset($_POST['value'])) {
    echo 2;
}
if( !empty($_POST['onlyinprogress'])) {
    $onlyprogress = $_POST['onlyinprogress'];
}

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    echo 3;
}

$code = $_POST['code'];
$value = $_POST['value'];

$result = $mysqli->query(
    sprintf("UPDATE %s SET `%s` = '%s' WHERE `%s` = '%s';", 
        $db_table_raffle, 
        $db_raffle_called,
        $mysqli->real_escape_string($value),
        $db_raffle_code, 
        $mysqli->real_escape_string($code)
    ));
if( $result === FALSE ) {
    echo 4;
} else {
    echo 0;
}
?>