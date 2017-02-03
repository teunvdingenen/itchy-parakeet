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

if( !isset($_POST['winners'])) {
    return;
}
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    return false;
}

$result = $mysqli->query(sprintf("SELECT COUNT(*) FROM `%s` WHERE 1", $db_table_raffle));
$raffle_num = mysqli_fetch_array($result,MYSQLI_NUM)[0];

$winners = $_POST['winners'];

$added = 0;
foreach ($winners as $key => $value) { //TODO double check if key is in use
    $raffle_key = get_key($raffle_num + $added);
    $added += 1;

    $sqlquery = sprintf("SELECT 1 FROM raffle r WHERE r.email = '%s'", $mysqli->real_escape_string($value));
    $result = $mysqli->query($sqlquery);
    if( $result->num_rows != 0 ) {
        $sqlquery = sprintf("UPDATE raffle SET code = '%s', valid = 1 WHERE email = '%s'",
            $mysqli->real_escape_string($raffle_key),
            $mysqli->real_escape_string($value));
    } else {
        $sqlquery = sprintf("INSERT INTO `%s` (`%s`, `%s`, `%s`) VALUES ('%s', '%s', 1)",
            $db_table_raffle,
            $db_raffle_code,
            $db_raffle_email,
            $db_raffle_valid,
            $mysqli->real_escape_string($raffle_key),
            $mysqli->real_escape_string($value));
    }
    $result = $mysqli->query($sqlquery);
    if( !$result ) {
        email_error("Failed to add to raffle: email ".$value." code: ".$raffle_key);
    }
}
?>