<?php session_start();
include "../functions.php";

if(!isset($_SESSION['loginuser'])) {
    header('Location: ../login.php');
}

$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info[$db_user_name];
$user_info_permissions = $user_info[$db_user_permissions];

if( $user_info_permissions & PERMISSION_USERS != PERMISSION_USERS ) {
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