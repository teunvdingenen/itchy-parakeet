<?php session_start();
if( isset($_SESSION['email'])) {
    unset($_SESSION['email']);
}
if( isset($_SESSION['firstname'])) {
    unset($_SESSION['firstname']);
}
if( isset($_SESSION['permissions'])) {
    unset($_SESSION['permissions']);
}
header('Location: ../login');
?>
