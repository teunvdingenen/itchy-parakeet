<?php session_start();
include "../functions.php";

$user_email = $user_firstname = $user_permissions = "";

if(!isset($_SESSION['email'])) {
    header('Location: ../login');
} else {
    $user_email = $_SESSION['email'];
}
if(!isset($_SESSION['firstname'])) {
    header('Location: ../login');
} else {
    $user_firstname = $_SESSION['firstname'];
}
if(!isset($_SESSION['permissions'])) {
    header('Location: ../login');
} else {
    $user_permissions = $_SESSION['permissions'];
}

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