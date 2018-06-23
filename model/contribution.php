<?php
namespace model;
include_once "dbconnection.php";
include_once "shifttype.php";
include_once "signup.php";
class Contribution {
    public $id, $shifttype, $description;
    
    public static function findById($id) {
        $contribution = new Contribution();
        $result = DBConnection::$con->query(sprintf("SELECT shifttype_id, description FROM contribution WHERE id = %s", DBConnection::$con->escape_string($id)));
        if($result === FALSE || $result->num_rows == 0) {
            return false;
        } else {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $contribution->id = $id;
            $contribution->shifttype = ShiftType::findById($row["shifttype_id"]);
            $contribution->description = $row["description"];
        }
        return $contribution;
    }
    
    function setValues($shifttype, $description)
    {
        $this->id = null;
        $this->shifttype = $shifttype;
        $this->description = $description;
        return $this;
    }
    
    public function save() {
        if($id == null) {
            DBConnection::$con->query(sprintf("INSERT INTO contribution (shifttype_id, description) VALUES (%s, '%s', %s);",
                DBConnection::$con->escape_string($shifttype->id),
                DBConnection::$con->escape_string($description)));
            $this->id = DBConnection::$con->insert_id;
        } else {
            DBConnection::$con->query(sprintf("UPDATE contribution SET shifttype_id = %s, description = '%s' WHERE id = %s;",
                DBConnection::$con->escape_string($shifttype->id),
                DBConnection::$con->escape_string($description),
                DBConnection::$con->escape_string($id)));
        }
        return $id;
    }
}

?>