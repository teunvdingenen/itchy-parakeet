<?php
namespace model;
use DateTime;
include_once "dbconnection.php";
include_once "person.php";
include_once "event.php";
class Signup {
    public $id, $person, $event, $partner, $motivation, $question, $preparations, $terms, $date, $contrib0, $contrib1;
    
    public static function findByPersonAndEvent($person, $event) {
        if(!$person || !$event) { 
            return false;
        }
        $signup = new Signup();
        $result = DBConnection::$con->query(sprintf("SELECT id WHERE person_email = '%s' and event_id = %s", DBConnection::$con->escape_string($person->email), DBConnection::$con->escape_string($event->id)));
        if($result === FALSE || $result->num_rows == 0) {
            return false;
        } else {
            $signup = findById(row["id"]);
        }
        return $signup;
    }
    
    public static function findById($id) {
        $signup = new Signup();
        $result = DBConnection::$con->query(sprintf("SELECT person_email, event_id, partner_email, motivation, question, preparations, terms, date, contrib0_id, contrib1_id FROM signup WHERE id = %s", DBConnection::$con->escape_string($id)));
        if($result === FALSE || $result->num_rows == 0) {
            return false;
        } else {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $signup->id = $id;
            $signup->person = Person::findByEmail($row["person_email"]);
            $signup->event = Event::findById($row["event_id"]);
            $signup->partner = Person::findByEmail($row["partner_email"]);
            $signup->motivation = $row["motivation"];
            $signup->question = $row["question"];
            $signup->preparations = $row["preparations"];
            $signup->terms = $row["terms"];
            $signup->contrib0 = Contribution::findById(row["contrib0_id"]);
            $signup->contrib1 = Contribution::findById(row["contrib1_id"]);
            $signup->date = new DateTime($row["date"]);
        }
        return $signup;
    }
    
    function setValues($person, $event, $partner, $motivation, $question, $preparations, $terms, $contrib0, $contrib1, $date)
    {
        $this->person = $person;
        $this->event = $event;
        $this->partner = $partner;
        $this->motivation = $motivation;
        $this->question = $question;
        $this->preparations = $preparations;
        $this->terms = $terms;
        $this->contrib0 = $contrib0;
        $this->contrib1 = $contrib1;
        $this->date = $date;
        return $this;
    }
    
    public function save() {
        $signup->save();
        if($id == null) {
            DBConnection::$con->query(sprintf("INSERT INTO signup (person_email, event_id, partner_email, motivation, question, preparations, terms, date, contrib0, contrib1) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', %s, '%s', %s, %s);",
                DBConnection::$con->escape_string($person->email),
                DBConnection::$con->escape_string($event->id),
                DBConnection::$con->escape_string($partner->email),
                DBConnection::$con->escape_string($motivation),
                DBConnection::$con->escape_string($question),
                DBConnection::$con->escape_string($preparations),
                DBConnection::$con->escape_string($terms),
                DBConnection::$con->escape_string($date->format('Y-m-d H:i:s')),
                DBConnection::$con->escape_string($conrtrib0->id),
                DBConnection::$con->escape_string($conrtrib1->id)));
            $this->id = DBConnection::$con->insert_id;
        } else {
            DBConnection::$con->query(sprintf("UPDATE contribution SET person_email = '%s', event_id = '%s', partner_email = '%s', motivation = '%s', question = '%s', preparations = '%s', terms = %s, date = '%s', contrib0_id = %s, contrib1_id = %s WHERE id = %s;",
                DBConnection::$con->escape_string($person->email),
                DBConnection::$con->escape_string($event->id),
                DBConnection::$con->escape_string($partner->email),
                DBConnection::$con->escape_string($motivation),
                DBConnection::$con->escape_string($question),
                DBConnection::$con->escape_string($preparations),
                DBConnection::$con->escape_string($terms),
                DBConnection::$con->escape_string($date->format('Y-m-d H:i:s')),
                DBConnection::$con->escape_string($conrtrib0->id),
                DBConnection::$con->escape_string($conrtrib1->id),
                DBConnection::$con->escape_string($id)));
        }
        return $id;
    }
}

?>