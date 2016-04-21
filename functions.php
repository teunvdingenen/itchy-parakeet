<?php
include "initialize.php";
include "fields.php";

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
 }

 function get_user_info($username) {
 	global $db_host, $db_user, $db_pass, $db_name;
 	global $db_table_users, $db_user_username, $db_user_name;
 	$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
 	$row = array();
 	if( $mysqli->connect_errno ) {
  		return false;
    } else {
    	$query = "SELECT * FROM `$db_table_users` WHERE (`$db_user_username` = '$username')";
	 	$result = $mysqli->query($query);
	 	$mysqli->close();
	 	if( $result === FALSE ) {
	 		return FALSE;
	 	} elseif( $result->num_rows == 1 ) {
            $row = $result->fetch_array(MYSQLI_ASSOC);
	 	} else {
	 		return false;
	 	}
	}
 	return $row;
}

function get_signups() {
	global $db_host, $db_user, $db_pass, $db_name;
	global $db_table_person, $db_table_contrib;
	$result = "";
	$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
	if( $mysqli->connect_errno ) {
  		return false;
  	} else {
  		$query = "SELECT p.lastname, p.firstname, p.birthdate, p.gender, p.city, p.email, p.phone, p.editions, p.partner, c0.type, c0.description, c0.needs, c1.type, c1.description, c1.needs FROM person p join contribution c0 on p.contrib0 = c0.id join contribution c1 on p.contrib1 = c1.id";
  		$result = $mysqli->query($query);
  		if( $result === FALSE ) {
  			 //error
  		}
  	}
  	$mysqli->close();
  	return $result;
}

function get_signup_statistics() {
  global $db_host, $db_user, $db_pass, $db_name;
  global $db_table_person, $db_table_contrib;
  $result = "";
  $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
  if( $mysqli->connect_errno ) {
      return false;
    } else {
        $query = "SELECT p.birthdate, p.gender, p.city, p.visits, p.partner, c0.type, c1.type FROM person p join contribution c0 on p.contrib0 = c0.id join contribution c1 on p.contrib1 = c1.id";
        $result = $mysqli->query($query);
        if( $result === FALSE ) {
            echo $mysqli->error;
        }
    }
    $mysqli->close();
    return $result;
}

function get_buyers() {
	global $db_host, $db_user, $db_pass, $db_name;
	global $db_table_buyer;
	$result = "";
	$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
	if( $mysqli->connect_errno ) {
  		return false;
  	} else {
  		$result = $mysqli->query("SELECT * FROM `$db_table_buyer` WHERE 1");
  	}
  $mysqli->close();
  return $result;
}

function get_signup($email) {
  global $db_host, $db_user, $db_pass, $db_name;
  global $db_table_person, $db_table_contrib;
  $result = "";
  $row = array();
  $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
  if( $mysqli->connect_errno) {
    return false;
  } else {
    $query = sprintf("SELECT * FROM person WHERE email = '%s'", $mysqli->real_escape_string($email));
    $result = $mysqli->query($query);
    $mysqli->close();
    if( $result === FALSE ) {
      echo $mysqli->error;
      return FALSE;
    } elseif( $result->num_rows == 1 ) {
      $row = $result->fetch_array(MYSQLI_ASSOC);
    } else {
      return false;
    }
  }
  return $row;
}

function get_contrib($id) {
  global $db_host, $db_user, $db_pass, $db_name;
  global $db_table_person, $db_table_contrib;
  $result = "";
  $row = array();
  $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
  if( $mysqli->connect_errno) {
    return false;
  } else {
    $query = sprintf("SELECT * FROM contribution WHERE id = '%s'", $mysqli->real_escape_string($id));
    $result = $mysqli->query($query);
    $mysqli->close();
    if( $result === FALSE ) {
      echo $mysqli->error;
      return FALSE;
    } elseif( $result->num_rows == 1 ) {
      $row = $result->fetch_array(MYSQLI_ASSOC);
    } else {
      return false;
    }
  }
  return $row;
}

function translate_contrib($type) {
  if( $type == "iv") {
    return "Interieur verzorging";
  } else if( $type == "bar" ) {
    return "Bar";
  } else if( $type == "keuken" ) {
    return "Keuken";
  } else if( $type == "workshop" ) {
    return "Workshop of Cursus";
  } else if( $type == "game" ) { 
    return "Ervaring of Game";
  } else if ( $type == "lecture" ) {
    return "Lezing";
  } else if( $type == "other" ) { 
    return "Anders";
  } else if ( $type == "perform" ) {
    return "Performance";
  } else if( $type == "install" ) {
    return "Installatie of Beeldend";
  } else if( $type == "afb" ) {
    return "Afbouw";
  } else if( $type == "ontw" ) {
    return "Ontwerpen en/of bouw decoraties, podia etc.";
  } else if( $type == "" ) {
    return "";
  } else {
    return "Onbekend";
  }
}

function translate_edition($edition) {
  if( $edition == "fff2010" ) {
    return "Familiar Forest Festival 2010";
  } else if( $edition == "fff2011" ) {
    return "Familiar Forest Festival 2011";
  } else if( $edition == "ffcastle" ) {
    return "Familiar Castle Festival";
  } else if( $edition == "fwf2012" ) {
    return "Familiar Winter Festival 2012";
  } else if( $edition == "fh2012" ) {
    return "Familiar Hemelvaartsnacht 2012";
  } else if( $edition == "fff2012" ) {
    return "Familiar Forest Festival 2012";
  } else if( $edition == "fh2013" ) {
    return "Familiar Hemelvaartsnacht 2013";
  } else if( $edition == "fwf2013" ) {
    return "Familiar Winter Festival 2013";
  } else if( $edition == "fff2013" ) {
    return "Familiar Forest Festival 2013";
  } else if( $edition == "fwf2014" ) {
    return "Familiar Winter Festival 2014";
  } else if( $edition == "fff2014" ) {
    return "Familiar Forest Festival 2014";
  } else if( $edition == "fwf2015" ) {
    return "Familiar Winter Festival 2015";
  } else if( $edition == "fff2015" ) {
    return "Familiar Forest Festival 2015";
  } else if( $edition == "" ) {
    return "";
  } else {
    return "Onbekend";
  }
}

 ?>