<?php session_start();


//TODO update dologin function

$user_email = $user_firstname = $user_permissions = "";

if( !isset($_SESSION['LAST_ACTIVITY']) ) {
    header('Location: ../login');
} else if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    session_unset();
    session_destroy();
    header('Location: ../login');
} else {
    $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
    $_SESSION['email'] = $_SESSION['email'];
    $_SESSION['firstname'] = $_SESSION['firstname'];
    $_SESSION['permissions'] = $_SESSION['permissions'];
    $user_email = $_SESSION['email'];
    $user_firstname = $_SESSION['firstname'];
    $user_permissions = $_SESSION['permissions'];


    if (!isset($_SESSION['CREATED'])) {
        $_SESSION['CREATED'] = time();
    } else if (time() - $_SESSION['CREATED'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['CREATED'] = time();
    }
}

?>
