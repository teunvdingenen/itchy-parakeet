<?php
namespace model;
include_once "dbconnection.php";
use DateTime;
class Event {
    public $id, $shorthand, $name, $price, $start, $end;
    
    public static function findById($id) {
        $event = new Event();
        $result = DBConnection::$con->query(sprintf("SELECT name, shorthand, price, date_start, date_end FROM event WHERE id = %s;", DBConnection::$con->escape_string($id)));
        if($result === FALSE || $result->num_rows == 0) {
            return false;
        } else {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $event->id = $id;
            $event->name = $row["name"];
            $event->shorthand = $row["shorthand"];
            $event->price = $row["price"];
            $event->start = new DateTime($row["date_start"]);
            $event->end = new DateTime($row["date_end"]);
        }
        return $event;
    }
    
    function setValues($name, $shorthand, $price, $start, $end)
    {
        $this->id = null;
        $this->name = $name;
        $this->shorthand = $shorthand;
        $this->price = $price;
        $this->start = $start;
        $this->end = $end;
        return $this;
    }
    
    public static function findByShorthand($shorthand) {
        $result = DBConnection::$con->query(printf("SELECT id FROM event where shorthand = '%s'",
            DBConnection::$con->escape_string($shorthand)));
        if( $result === FALSE || $result->num_rows != 1 ) { 
            return false;
        } else {
            return Event::findById($result->fetch_array(MYSQLI_ASSOC)['id']);
        }
        return false;
    }
    
    public function save() {
        $signup->save();
        if($id == null) {
            DBConnection::$con->query(sprintf("INSERT INTO event (name, shorthand, price, start_date, end_date) VALUES ('%s', %s, '%s', '%s');",
                DBConnection::$con->escape_string($name),
                DBConnection::$con->escape_string($shorthand),
                DBConnection::$con->escape_string($price),
                DBConnection::$con->escape_string($start->format('Y-m-d H:i:s')),
                DBConnection::$con->escape_string($end->format('Y-m-d H:i:s'))));
            $this->id = DBConnection::$con->insert_id;
        } else {
            DBConnection::$con->query(sprintf("UPDATE event SET name = '%s', shorthand = '%s', price = %s, start_date = '%'s, end_date = '%s' WHERE id = %s;",
                DBConnection::$con->escape_string($name),
                DBConnection::$con->escape_string($shorthand),
                DBConnection::$con->escape_string($price),
                DBConnection::$con->escape_string($start->format('Y-m-d H:i:s')),
                DBConnection::$con->escape_string($end->format('Y-m-d H:i:s')),
                DBConnection::$con->escape_string($id)));
        }
        return $id;
    }
    
    public static function getCurrentEvent() {
        $result = DBConnection::$con->query(printf("SELECT MAX(id) FROM event",
            DBConnection::$con->escape_string($shorthand)));
        if( $result === FALSE || $result->num_rows != 1 ) {
            return false;
        } else {
            return Event::findById($result->fetch_array(MYSQLI_ASSOC)['id']);
        }
        return false;
    }
}

?>