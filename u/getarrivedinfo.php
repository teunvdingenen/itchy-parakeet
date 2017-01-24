<?php session_start();
include "../functions.php";
include "createmenu.php";

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

if( $user_permissions & PERMISSION_DISPLAY != PERMISSION_DISPLAY ) {
    echo "503";
    return 0;
}

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

$sqlresult = $mysqli->query("SELECT p.firstname, p.lastname, b.code, b.id, b.attending, b.ticket FROM buyer b join person p on p.email = b.email WHERE complete = 1 ORDER BY p.firstname");

if( !$sqlresult ) {
	echo $mysqli->error;
	return;
} 
$mysqli->close();

$result = array();
while($row = mysqli_fetch_array($sqlresult, MYSQLI_ASSOC)){
   $result[] = $row;
}
echo json_encode($result);
?>