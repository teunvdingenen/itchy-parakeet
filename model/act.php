<?php
namespace model;
include_once "dbconnection.php";
include_once "shifttype.php";
class Act {
	public $id, $shifttype, $description, $needs;
	
	public function __construct() {
	    
	}

	public static function findById($id) {
	    $act = new Act();
	    $result = DBConnection::$con->query(sprintf("SELECT shifttype_id, description, needs FROM act WHERE id = %s", DBConnection::$con->escape_string($id)));
		if($result === FALSE || $result->num_rows == 0) {
			return false;
		} else {
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$act->id = $id;
			$act->shifttype = ShiftType::findById($row["shifttype_id"]);
			$act->description = $row["description"];
			$act->needs = $row["needs"];
		}
		return $act;
	}

	function setValues($shifttype, $description, $needs) 
  { 
  	$this->id = null;
  	$this->shifttype = $shifttype;
  	$this->description = $description;
  	$this->needs = $needs;
  }

  public function save() {
  	if($id == null) {
  		DBConnection::$con->query(sprintf("INSERT INTO act (shifttype_id, description, needs) VALUES (%s, '%s', '%s');",
	  		DBConnection::$con->escape_string($shifttype->id),
	  		DBConnection::$con->escape_string($description),
	  		DBConnection::$con->escape_string($needs)));
  		$this->id = DBConnection::$con->insert_id;
  	} else {
  		DBConnection::$con->query(sprintf("UPDATE act SET shifttype_id = %s, description = '%s', needs = '%s' WHERE id = %s;",
	  		DBConnection::$con->escape_string($shifttype->id),
	  		DBConnection::$con->escape_string($description),
	  		DBConnection::$con->escape_string($needs),
	  		DBConnection::$con->escape_string($id)));
  	}
  	return $id;
  }
}

?>