<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_VOLUNTEERS) != PERMISSION_VOLUNTEERS ) {
	echo 1;
	return;
}
$name = "";
if( !isset($_POST['name'])) {
    echo 2;
    return;
}
$set = "";
if( isset($_POST['nrrequired'])) {
	$set .= sprintf("`nrrequired` = %s",$_POST['nrrequired']);
}
if( isset($_POST['startdate'])) {
	if( strlen($set) > 0 ) {
		$set.=", ";
	}
	$startdate = $_POST["startdate"];
    $sdate = DateTime::createFromFormat('D, d/m/Y G:i', $startdate);
    if( $sdate === FALSE ) {
    	echo 3;
    	return;
    } else {
        $startdate_output = $sdate->format('Y-m-d H:i:s');
    }
	$set .= sprintf("`startdate` = '%s'",$startdate_output);
}
if( isset($_POST['enddate'])) {
	if( strlen($set) > 0 ) {
		$set.=", ";
	}
	$enddate = $_POST["enddate"];
    $edate = DateTime::createFromFormat('D, d/m/Y G:i', $enddate);
    if( $edate === FALSE ) {
    	echo 4;
    	return;
    } else {
        $enddate_output = $edate->format('Y-m-d H:i:s');
    }
	$set .= sprintf("`enddate` = '%s'",$enddate_output);
}
$name = $_POST['name'];
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
	echo 5;
    return;
}

$query = sprintf("UPDATE `shifts` SET %s WHERE `name` = '%s'",
        $set,
        $mysqli->real_escape_string($name));
$result = $mysqli->query( $query );
if( $result === FALSE ) {
	echo 6;
    return;
}
$mysqli->close();
echo 0;
return;
?>