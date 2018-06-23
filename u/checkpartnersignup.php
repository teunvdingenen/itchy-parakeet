<?php
include_once "loginmanager.php";
include_once "signup.php";
model\LoginManager::Instance()->isLoggedIn();
if( model\LoginManager::Instance()->getPermissions() & PERMISSION_PARICIPANT != PERMISSION_PARICIPANT ) {
    echo '{"success": false}';
    return;
}

if( !isset($_POST['email'])) {
    echo '{"success": false}';
    return;
}

$email = $_POST['email'];
$signup = model\Signup::findByPersonAndEvent(model\Person::findByEmail($email), model\Event::getCurrentEvent());
if($signup == false) {
    echo '{"success": true, "status": 2}';
    return;
}
if( strcasecmp($signup->partner->email, model\LoginManager::Instance()->user->email) == 0 ) {
    echo '{"success": true, "status": 0}';
} else {
    echo '{"success": true, "status": 1}';
}
?>