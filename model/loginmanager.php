<?php
namespace model;
session_start();
use DateTime;
include_once __DIR__."/../fields.php";
include_once "person.php";
include_once "util.php";
include_once "user.php";

class LoginManager
{
    public $user;
    public $person;
    
    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new LoginManager();
            if( isset($_SESSION["USER"])) {
                $inst->setUser(User::findByEmail($_SESSION["USER"]));
            }
        }
        return $inst;
    }
    
    public function getPermissions() {
        return $this->user->permissions;
    }
    
    public function isLoggedIn() {
        $this->rememberMe();
        if( !isset($_SESSION['LAST_ACTIVITY']) || $this->user == NULL) {
            session_unset();
            session_destroy();
            header('Location: ../login');
        } else if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 18000)) {
            session_unset();
            session_destroy();
            header('Location: ../login');
        } else {
            $this->updateSession();
        }
        return true;
    }
    
    private function __construct()
    {
        
    }
    
    public function setUser($user) {
        $this->user = $user;
        $this->person = Person::findByEmail($this->user->email);
    }
    
    public function updateSession() {
        $_SESSION['LAST_ACTIVITY'] = time();
        $_SESSION['CREATED'] = time();
        $_SESSION['USER'] = $this->user->email;
    }
    
    private function setRememberMe() {
        $cookie = $this->user->email . ':' . $this->user->token;
        $mac = hash_hmac('sha256', $cookie, SECRET_KEY);
        $cookie .= ':' . $mac;
        setcookie('ff_rememberme', $cookie, time()+604800, '/u');
    }
    
    public function rememberMe() {
        $cookie = isset($_COOKIE['ff_rememberme']) ? $_COOKIE['ff_rememberme'] : '';
        if ($cookie) {
            list ($user, $token, $mac) = explode(':', $cookie);
            if (!hash_equals(hash_hmac('sha256', $user . ':' . $token, SECRET_KEY), $mac)) {
                //email_error(sprintf("No rememberme match for: %s, token: %s, mac: %s", $user, $token, $mac));
                return false;
            }
            if (hash_equals($this->user->token, $token)) {
                $this->updateSession();
            } else {
                //email_error(sprintf("No hash match for user: %s, usertoken: %s, token: %s", $user, $usertoken, $token));
            }
        }
    }
    
    public function login($user, $remember) {
        $this->user = $user;
        $this->updateSession();
        $this->user->token = Utilities::generateRandomToken(128);
        $this->user->save();
        if( $remember ) {
            $this->setRememberMe();
        }
        return true;
    }
    
    function logout($user) {
        $this->user->expire = new DateTime();
        $this->user->save();
    }
    
}
?>