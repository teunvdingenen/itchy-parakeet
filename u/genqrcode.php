<?php session_start();
include_once "initialize.php";
include('phpqrcode/qrlib.php');
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

if( $user_permissions & PERMISSION_EDIT  != PERMISSION_EDIT ) {
    return false;
}

    if( !isset($_GET['hash'])) {
    	return false;
    }
     
    // outputs image directly into browser, as PNG stream 
    QRcode::png($_GET['hash'],FALSE,3);

?>