<?php session_start();
include "../functions.php";
include "../createhashes.php";

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

if( $user_permissions & PERMISSION_RAFFLE != PERMISSION_RAFFLE ) {
    header('Location: oops.php');
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