<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_EDIT) != PERMISSION_EDIT ) {
    exit;
}

if( !isset($_POST['email'])) {
    return 1;
}
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    return 1;
}

$email = $_POST['email'];

$result = $mysqli->query(sprintf("INSERT INTO $current_table (`email`, `contrib0_type`, `contrib0_desc`, `contrib0_need`, `contrib1_type`, `contrib1_desc`, `contrib1_need`, `terms0`, `terms1`, `terms2`, `terms3`, `valid`, `round`, `motivation`, `question`, `partner`) VALUES ('%s', 'iv','Handmatig ingeschreven, info bij Teun', '', 'iv', '','','J','J','J','J',0,0, '','','')",
	$mysqli->real_escape_string($email)));
if( !$result ) {
	return 1;
}

return 0;
?>