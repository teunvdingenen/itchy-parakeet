<?php
namespace model;
use DateTime;
include_once "dbconnection.php";
include_once "user.php";
class PasswordReset {
    public $user, $token, $expire;
    
    public static function findByEmail($email) {
        $passwordreset = new PasswordReset();
        $result = DBConnection::$con->query(sprintf("SELECT user_email, token, expire FROM pwreset WHERE user_email = '%s';", DBConnection::$con->escape_string($email)));
        if($result === FALSE || $result->num_rows == 0) {
            return false;
        } else {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $passwordreset->user = User::findByEmail($email);
            $passwordreset->token = $row["token"];
            $passwordreset->expire = new DateTime($row["expire"]);
        }
        return $passwordreset;
    }
    
    public static function findByToken($token) {
        $passwordreset = new PasswordReset();
        $result = DBConnection::$con->query(sprintf("SELECT user_email, token, expire FROM pwreset WHERE token = '%s';", DBConnection::$con->escape_string($token)));
        if($result === FALSE || $result->num_rows == 0) {
            echo DBConnection::$con->error;
            return false;
        } else {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $passwordreset->user = User::findByEmail($row['user_email']);
            $passwordreset->token = $row["token"];
            $passwordreset->expire = new DateTime($row["expire"]);
        }
        return $passwordreset;
    }
    
    function setValues($user, $token, $expire)
    {
        $this->user = $user;
        $this->token = $token;
        $this->expire = $expire;
        return $this;
    }
    
    public static function exists($email) {
        $result = DBConnection::$con->query(sprintf("SELECT 1 FROM pwreset WHERE email = '%s'", DBConnection::$con->escape_string($email)));
        return $result !== FALSE && $result->num_rows == 1;
    }
    
    public function save() {
        $result = null;
        if(!PasswordReset::exists($this->user->email)) {
            $result = DBConnection::$con->query(sprintf("INSERT INTO pwreset (user_email, token, expire) VALUES ('%s', '%s', '%s');",
                DBConnection::$con->escape_string($this->user->email),
                DBConnection::$con->escape_string($this->token),
                DBConnection::$con->escape_string($this->expire->format('Y-m-d H:i:s'))));
                
        } else {
            $result = DBConnection::$con->query(sprintf("UPDATE pwreset SET token = '%s', expire = '%s' WHERE user_email = '%s';",
                DBConnection::$con->escape_string($this->token),
                DBConnection::$con->escape_string($this->expire->format('Y-m-d H:i:s')),
                DBConnection::$con->escape_string($this->user->email)));
        }
        echo DBConnection::$con->error;
        return $this->user->email;
    }
    
    public function delete() {
        DBConnection::$con->query(sprintf("DELETE FROM `pwreset` WHERE `email` = '%s'",
            DBConnection::$con->escape_string($this->user->email)));
    }
}

?>