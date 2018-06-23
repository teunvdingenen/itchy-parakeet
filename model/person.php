<?php
namespace model;
use DateTime;
include_once "dbconnection.php";
class Person {
	public $email, $firstname, $lastname, $birthdate, $street, $postal, $city, $gender, $phone, $familiar, $allow_email;

	public function __construct() {
	    
	}
	
	public static function findByEmail($email) {
	    $person = new Person();
	    $result = DBConnection::$con->query(sprintf("SELECT firstname, lastname, birthdate, street, postal, city, gender, phone, familiar, allow_email FROM person WHERE email = '%s'", DBConnection::$con->escape_string($email)));
		if($result === FALSE || $result->num_rows == 0) {
			return false;
		} else {
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$person->email = $email;
			$person->firstname = $row["firstname"];
			$person->lastname = $row["lastname"];
			$person->birthdate = new DateTime($row['birthdate']);
			$person->street = $row["street"];
			$person->postal = $row["postal"];
			$person->city = $row["city"];
			$person->gender = $row["gender"];
			$person->phone = $row["phone"];
			$person->familiar = $row["familiar"];
			$person->allow_email = $row["allow_email"] == 1;
		}
		return $person;
	}

   public function setValues($email, $firstname, $lastname, $birthdate, $street, $postal, $city, $gender, $phone, $familiar, $allow_email) { 
  	$this->email = $email;
  	$this->firstname = $firstname;
  	$this->lastname = $lastname;
  	$this->birthdate = $birthdate;
  	$this->street = $street;
  	$this->postal = $postal;
  	$this->city = $city;
  	$this->gender = $gender;
  	$this->phone = $phone;
  	$this->familiar = $familiar;
  	$this->allow_email = $allow_email;
  	return $this;
  }
  
  public function getFullName() {
      return $firstname." ".$lastname;
  }
  
  public static function exists($email) {
      $result = DBConnection::$con->query(sprintf("SELECT 1 FROM person WHERE email = '%s'", DBConnection::$con->escape_string($email)));
      return $result !== FALSE && $result->num_rows == 1;
  }

  public function save() {
      $result = null;
     if(!Person::exists($this->email)) {
  		$result = DBConnection::$con->query(sprintf("INSERT INTO person (email, firstname, lastname, birthdate, street, postal, city, gender, phone, familiar, allow_email) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %s);",
  		    DBConnection::$con->escape_string($this->email),
  		    DBConnection::$con->escape_string($this->firstname),
  		    DBConnection::$con->escape_string($this->lastname),
  		    DBConnection::$con->escape_string($this->birthdate->format('Y-m-d H:i:s')),
  		    DBConnection::$con->escape_string($this->street),
  		    DBConnection::$con->escape_string($this->postal),
  		    DBConnection::$con->escape_string($this->city),
  		    DBConnection::$con->escape_string($this->gender),
  		    DBConnection::$con->escape_string($this->phone),
  		    DBConnection::$con->escape_string($this->familiar),
  		    DBConnection::$con->escape_string($this->allow_email)));
  	} else {
  		$result = DBConnection::$con->query(sprintf("UPDATE person SET email = '%s', firstname = '%s', lastname = '%s', birthdate = '%s', street = '%s', postal = '%s', city = '%s', gender = '%s', phone = '%s', familiar = '%s', allow_email = %s WHERE email = '%s';",
  		    DBConnection::$con->escape_string($this->firstname),
  		    DBConnection::$con->escape_string($this->lastname),
  		    DBConnection::$con->escape_string($this->birthdate->format('Y-m-d H:i:s')),
  		    DBConnection::$con->escape_string($this->street),
  		    DBConnection::$con->escape_string($this->postal),
  		    DBConnection::$con->escape_string($this->city),
  		    DBConnection::$con->escape_string($this->gender),
  		    DBConnection::$con->escape_string($this->phone),
  		    DBConnection::$con->escape_string($this->familiar),
  		    DBConnection::$con->escape_string($this->allow_email),
  		    DBConnection::$con->escape_string($this->email)));
  	}
  	return $this->email;
  }
}

?>