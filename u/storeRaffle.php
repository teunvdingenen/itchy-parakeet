<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_RAFFLE) != PERMISSION_RAFFLE ) {
    header('Location: oops.php');
}

function get_key($i) {
    //key = aannbbmm
    $aa = $bb = "";
    $mm = $i % 100;
    $nn = floor($i / 100) % 100;
    $aa = randomChar().randomChar();
    $bb = randomChar().randomChar();
    $nn_str = $mm_str = '';
    if($nn < 10) {
        $nn_str .= "0".$nn;
    } else {
        $nn_str .= $nn;
    }
    if($mm < 10) {
        $mm_str .= "0".$mm;
    } else {
        $mm_str .= $mm;
    }
    return $aa.$nn_str.$bb.$mm_str;
}

function randomChar() {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return $alphabet[rand(0, strlen($alphabet)-1)];
}

if( !isset($_POST['email'])) {
    return;
}
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    $mysqli->close();
    return false;
}

$result = $mysqli->query(sprintf("SELECT COUNT(*) FROM $current_table WHERE valid=1"));
$raffle_num = mysqli_fetch_array($result,MYSQLI_NUM)[0];

$email = $_POST['email'];

$raffle_key = get_key($raffle_num);

$sqlquery = sprintf("UPDATE $current_table SET rafflecode = '%s', valid = 1 WHERE email = '%s'",
    $mysqli->real_escape_string($raffle_key),
    $mysqli->real_escape_string($email));

$result = $mysqli->query($sqlquery);
if( !$result ) {
    email_error("Failed to add to raffle: email ".$email." code: ".$raffle_key . "<br>".$mysqli->error);
}
$mysqli->close();

?>