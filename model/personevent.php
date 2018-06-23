<?php
namespace model;
include_once "dbconnection.php";
include_once "event.php";
include_once "person.php";
class PersonEvent {
    public $id, $event, $person;
    
    public static function findById($id) {
        $personevent = new PersonEvent();
        $result = DBConnection::$con->query(sprintf("SELECT event_id, person_email FROM personevent WHERE id = %s", DBConnection::$con->escape_string($id)));
        if($result === FALSE || $result->num_rows == 0) {
            return false;
        } else {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $personevent->id = $id;
            $personevent->event = Event::findById($row["event_id"]);
            $personevent->person = Person::findByEmail($row["person_email"]);
        }
        return $personevent;
    }
    
    function setValues($event, $person)
    {
        $this->id = null;
        $this->event = $event;
        $this->person = $person;
        return $this;
    }
    
    public function save() {
        if($id == null) {
            DBConnection::$con->query(sprintf("INSERT INTO personevent (event_id, person_email) VALUES (%s, '%s');",
                DBConnection::$con->escape_string($event->id),
                DBConnection::$con->escape_string($person->email)));
            $this->id = DBConnection::$con->insert_id;
        } else {
            DBConnection::$con->query(sprintf("UPDATE shifttype SET event_id = '%s', person_email = '%s' WHERE id = %s;",
                DBConnection::$con->escape_string($event->id),
                DBConnection::$con->escape_string($person_email),
                DBConnection::$con->escape_string($id)));
        }
        return $id;
    }
}

?>