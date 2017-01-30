<?php
include_once "initialize.php";
include('phpqrcode/qrlib.php');
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_EDIT)  != PERMISSION_EDIT ) {
    header('Location: oops.php');
}

if( !isset($_GET['hash'])) {
	return false;
}
 
// outputs image directly into browser, as PNG stream 
QRcode::png($_GET['hash'],FALSE,3);

?>