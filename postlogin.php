<?php session_start(); 
include "functions.php";

rememberMe();

if(!isset($_SESSION['loginuser'])) {
    header('Location: login');
}
$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info[$db_user_name];
$user_info_permissions = $user_info[$db_user_permissions];
  header('Location: deelnemen');
/*
if( $user_info_permissions == PERMISSION_PARTICIPANT ) {
    header('Location: deelnemen');
} else {
    header('Location: secure/index');
}
*/
?>
