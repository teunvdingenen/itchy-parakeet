<?php
namespace model;
use DateTime;
include_once "dbconnection.php";
class User {
	public $email, $password, $permissions, $token, $expire, $last_login;
	
	public function __construct() {
	    
	}

	public static function findByEmail($email) {
	    $user = new User();
		$result = DBConnection::$con->query(sprintf("SELECT password, permissions, token, expire, last_login FROM user WHERE email = '%s'", DBConnection::$con->escape_string($email)));
		if($result === FALSE || $result->num_rows == 0) {
			return false;
		} else {
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$user->email = $email;
			$user->password = $row["password"];
			$user->permissions = $row["permissions"];
			$user->token = $row["token"];
			$user->expire = new DateTime($row['expire']);
			$user->last_login = new DateTime($row['last_login']);
		}
		return $user;
	}

	public function setValues($email, $password, $token, $permisions, $expire, $last_login) { 
      	$this->email = $email;
      	$this->password = $password;
      	$this->token = $token;
      	$this->permissions = $permisions;
      	$this->expire = $expire;
      	$this->last_login = $last_login;
      	return $this;
    }
  
  public static function exists($email) {
      $result = DBConnection::$con->query(sprintf("SELECT 1 FROM user WHERE email = '%s'", DBConnection::$con->escape_string($email)));
      return $result !== FALSE && $result->num_rows == 1;
  }

  public function save() {
    $result = null;
  	if(!User::exists($this->email)) {
  		$result = DBConnection::$con->query(sprintf("INSERT INTO user (email, password, permissions, token, expire, last_login) VALUES ('%s', '%s', '%s', '%s', '%s', '%s');",
	  		DBConnection::$con->escape_string($this->email),
  		    DBConnection::$con->escape_string($this->password),
  		    DBConnection::$con->escape_string($this->permissions),
  		    DBConnection::$con->escape_string($this->token),
  		    DBConnection::$con->escape_string($this->expire->format('Y-m-d H:i:s')),
  		    DBConnection::$con->escape_string($this->last_login->format('Y-m-d H:i:s'))));
  	} else {
  		$result = DBConnection::$con->query(sprintf("UPDATE user SET password = '%s', permissions = '%s', token = '%s', expire = '%s', last_login = '%s' WHERE email = '%s';",
  		    DBConnection::$con->escape_string($this->password),
  		    DBConnection::$con->escape_string($this->permissions),
  		    DBConnection::$con->escape_string($this->token),
  		    DBConnection::$con->escape_string($this->expire->format('Y-m-d H:i:s')),
  		    DBConnection::$con->escape_string($this->last_login->format('Y-m-d H:i:s')),
  		    DBConnection::$con->escape_string($this->email)));
  	}
  	return $this->email;
  }
}
?>