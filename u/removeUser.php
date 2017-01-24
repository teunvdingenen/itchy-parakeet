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

if( $user_permissions & PERMISSION_USER != PERMISSION_USER ) {
    echo "503";
    return 0;
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