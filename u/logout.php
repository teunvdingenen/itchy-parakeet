<?php session_start();

include_once("../functions.php");

if( isset($_SESSION['email']) ) {
    logout($_SESSION['email']);
}
session_unset();
session_destroy();
header('Location: ../login');
?>
