<?php session_start();
include "../functions.php";

if(!isset($_SESSION['loginuser'])) {
    header('Location: ../login.php');
}

$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info[$db_user_name];
$user_info_permissions = $user_info[$db_user_permissions];

if( $user_info_permissions & PERMISSION_ACTS != PERMISSION_ACTS || 
        $user_info_permissions & PERMISSION_VOLUNTEERS != PERMISSION_VOLUNTEERS) {
    return;
}

if( !isset($_POST['emails'])) {
    return;
}
if( !isset($_POST['numbers'])) {
    return;
}
if( !isset($_POST['tasks'])) {
    return;
}
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    return false;
}

$emails = $_POST['emails'];
$numbers = $_POST['numbers'];
$tasks = $_POST['tasks'];

foreach ($emails as $key => $value) { //TODO double check if key is in use
    $raffle_key = get_key($raffle_num + $added);
    $added += 1;

    $sqlquery = sprintf("INSERT INTO `%s` (`%s`, `%s`) VALUES ('%s', '%s')",
        $db_table_raffle,
        $db_raffle_code,
        $db_raffle_email,
        $mysqli->real_escape_string($raffle_key),
        $mysqli->real_escape_string($value));
    $result = $mysqli->query($sqlquery);
    if( !$result ) {
        //TODO handle error
    }
}

echo $added;
?>