<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_ACTS) != PERMISSION_ACTS || 
        ($user_permissions & PERMISSION_VOLUNTEERS) != PERMISSION_VOLUNTEERS) {
    header('Location: oops.php');
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
if( !isset($_POST['notes'])) {
    return;
}
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    return false;
}

$emails = $_POST['emails'];
$numbers = $_POST['numbers'];
$tasks = $_POST['tasks'];
$notes = $_POST['notes'];
if ( count($emails) == count($numbers) && count($emails) == count($tasks) ) {
    for ($i = 0; $i < count($emails); $i++) {
        $email = $mysqli->real_escape_string($emails[$i]);
        $number = $mysqli->real_escape_string(intval($numbers[$i]));
        $task = $mysqli->real_escape_string($tasks[$i]);
        $note = $mysqli->real_escape_string($notes[$i]);
        if( strlen($note) >= 1024 ) {
            continue;
        }

        $sqlquery = sprintf("UPDATE buyer b SET b.number = %s, b.task = '%s', b.note = '%s' WHERE b.email = '%s'",
            $number, $task, $note, $email);
        $result = $mysqli->query($sqlquery);
        if( !$result ) {
            email_error("Bij slaan vrijwilligers: ".$mysqli->error);
            echo 1;
        }
    }
} else {
    echo 1;
}

echo 0;
?>