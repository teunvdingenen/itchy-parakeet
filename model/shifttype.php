<?php
namespace model;
include_once "dbconnection.php";
class ShiftType {
    public $id, $name, $description, $isAct;
    
    static function findById($id) {
        $shifttype = new Shifttype();
        $result = DBConnection::$con->query(sprintf("SELECT name, description, is_act FROM shifttype WHERE id = %s", DBConnection::$con->escape_string($id)));
        if($result === FALSE || $result->num_rows == 0) {
            return false;
        } else {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $shifttype->id = $id;
            $shifttype->name = $row["name"];
            $shifttype->description = $row["description"];
            $shifttype->isAct = $row['is_act'];
        }
        return $shifttype;
    }
    
    function setValues($name, $description, $isAct)
    {
        $this->id = null;
        $this->name = $name;
        $this->description = $description;
        $this->isAct = $isAct;
        return $this;
    }
    
    public function save() {
        $signup->save();
        if($id == null) {
            DBConnection::$con->query(sprintf("INSERT INTO shifttype (name, description, is_act) VALUES ('%s', '%s', %s);",
                DBConnection::$con->escape_string($name),
                DBConnection::$con->escape_string($description),
                DBConnection::$con->escape_string($isAct)));
            $this->id = DBConnection::$con->insert_id;
        } else {
            DBConnection::$con->query(sprintf("UPDATE shifttype SET name = '%s', description = '%s', is_act = %s WHERE id = %s;",
                DBConnection::$con->escape_string($name),
                DBConnection::$con->escape_string($description),
                DBConnection::$con->escape_string($isAct),
                DBConnection::$con->escape_string($id)));
        }
        return $id;
    }
    
    private static function getShifts($type){ 
        $query = "SELECT id FROM shifttype WHERE ";
        if($type == 'BOTH') {
            $query .= "1";
        } else if( $type == "ACT" ) {
            $query .= "is_act = 1";
        } else if( $type == "NONACT") {
            $query .= "is_act = 0";
        }
        $result = DBConnection::$con->query($query);
        if(!$result) {
            return [];
        } else {
            $acts = array();
            while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
                $acts[] = ShiftType::findById($row['id']);
            }
            return $acts;
        }
        return false;
    }
    
    public static function getActs() {
        return getShifts("ACT");
    }
    
    public static function getNonActs() {
        return getShifts("NONACT");
    }
    
    public static function getAll() {
        return getShifts("BOTH");
    }
    
}

?>