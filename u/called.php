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

if( $user_permissions & PERMISSION_CALLER != PERMISSION_CALLER ) {
    return false;
}

$email = $_POST['email'];
echo $email;

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    return false;
} else {
    $query = sprintf("UPDATE $current_table set called=1 WHERE email = '%s';", $email);
    echo $query;
    $sqlresult = $mysqli->query($query);
    echo $sqlresult;
    if( $sqlresult === FALSE ) {
         //error
        echo $sqlresult->error;
        return false;
    }
}
$mysqli->close();

?>
