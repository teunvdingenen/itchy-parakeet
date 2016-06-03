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
if ( count($emails) == count($numbers) && count($emails) == count($tasks) ) {
    for ($i = 0; $i < count($emails); $i++) {
        $email = $mysqli->real_escape_string($emails[$i]);
        $number = $mysqli->real_escape_string(intval($numbers[$i]));
        $task = $mysqli->real_escape_string($tasks[$i]);

        $sqlquery = sprintf("UPDATE buyer b SET b.number = %s, b.task = '%s' WHERE b.email = '%s'",
            $number, $task, $email);
        $result = $mysqli->query($sqlquery);
        if( !$result ) {
            echo $mysqli->error;
            echo 1;
        }
    }
} else {
    echo 1;
}

echo 0;
?>