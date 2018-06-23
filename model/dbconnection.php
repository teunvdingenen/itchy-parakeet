<?php
namespace model;
include_once __DIR__ ."/../initialize.php";

class DBConnection {
    public static $con;
}
DBConnection::$con = new \mysqli($dbhost, $dbuser, $dbpass, $dbname);
?>