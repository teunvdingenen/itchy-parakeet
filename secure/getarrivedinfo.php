<?php session_start();
include "../functions.php";
include "createmenu.php";

if(!isset($_SESSION['loginuser'])) {
    header('Location: ../login');
}
$menu_html = "";
$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info[$db_user_name];
$user_info_permissions = $user_info[$db_user_permissions];

if( $user_info_permissions & PERMISSION_DISPLAY != PERMISSION_DISPLAY ) {
    echo "503";
    return 0;
}

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

$sqlresult = $mysqli->query("SELECT p.firstname, p.lastname, b.code, b.id, b.attending, b.ticket FROM buyer b join person p on p.email = b.email WHERE complete = 1");

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