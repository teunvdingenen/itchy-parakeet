<?php session_start();
include "../functions.php";
include "../createhashes.php";

if(!isset($_SESSION['loginuser'])) {
    header('Location: ../login.php');
}

$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info[$db_user_name];
$user_info_permissions = $user_info[$db_user_permissions];

if( $user_info_permissions & PERMISSION_RAFFLE != PERMISSION_RAFFLE ) {
    echo "503";
    return 0;
}

if( !isset($_POST['gift'])) {
    return 1;
}
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    return false;
}

$code = $_POST['gift'];

$result = $mysqli->query(sprintf("SELECT email from raffle where code = '%s'",$mysqli->real_escape_string($code)));
if( $result->num_rows != 1 ) {
    return false;
}
$email = mysqli_fetch_array($result,MYSQLI_ASSOC)['email'];
echo $email;

$result = $mysqli->query(
    sprintf("INSERT INTO buyer (id,code,email,complete,share,ticket) VALUES ('%s','%s','%s',1,0,'%s')",  
        $mysqli->real_escape_string($code),
        $mysqli->real_escape_string($code),
        $mysqli->real_escape_string($email),
        get_hash($code,$code)
    ));
if( $result === FALSE ) {
    echo $mysqli->error;
    return false;
}
$mysqli->close();
return true;
?>