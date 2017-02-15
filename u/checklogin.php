<?php session_start();

$user_email = $user_firstname = $user_permissions = "";

if( !isset($_SESSION['email'])) { 
//	rememberMe();
}

if(!isset($_SESSION['email'])) {
    header('Location: ../login');
} else {
    $user_email = $_SESSION['email'];
    $_SESSION['email'] = $_SESSION['email'];
}
if(!isset($_SESSION['firstname'])) {
    header('Location: ../login');
} else {
    $user_firstname = $_SESSION['firstname'];
    $_SESSION['firstname'] = $_SESSION['firstname'];
}
if(!isset($_SESSION['permissions'])) {
    header('Location: ../login');
} else {
    $user_permissions = $_SESSION['permissions'];
    $_SESSION['permissions'] = $_SESSION['permissions'];
}

?>
