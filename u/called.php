<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_CALLER) != PERMISSION_CALLER ) {
    header('Location: oops.php');
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
