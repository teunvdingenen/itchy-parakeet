<?php session_start();
include "../functions.php";

if(!isset($_SESSION['loginuser'])) {
    header('Location: ../login.php');
}

$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info[$db_user_name];
$user_info_permissions = $user_info[$db_user_permissions];

if( $user_info_permissions & PERMISSION_CALLER != PERMISSION_CALLER ) {
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
