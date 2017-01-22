<?php session_start();
include_once "initialize.php";
include('phpqrcode/qrlib.php');
include "../functions.php";

if(!isset($_SESSION['loginuser'])) {
    header('Location: ../login');
}

$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info[$db_user_name];
$user_info_permissions = $user_info[$db_user_permissions];

if( $user_info_permissions & PERMISSION_EDIT  != PERMISSION_EDIT ) {
    return false;
}

    if( !isset($_GET['hash'])) {
    	return false;
    }
     
    // outputs image directly into browser, as PNG stream 
    QRcode::png($_GET['hash'],FALSE,3);

?>