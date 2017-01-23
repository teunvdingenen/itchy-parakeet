<?php
include_once "initialize.php";
include_once "fields.php";
include_once "sendmail.php";

function generateRandomToken($length = 250) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function setRememberMe($user) {
    $token = generateRandomToken(); // generate a token, should be 128 - 256 bit
    store_user_token($user, $token);
    $cookie = $user . ':' . $token;
    $mac = hash_hmac('sha256', $cookie, SECRET_KEY);
    $cookie .= ':' . $mac;
    setcookie('ff_rememberme', $cookie);
}

function rememberMe() {
    $cookie = isset($_COOKIE['ff_rememberme']) ? $_COOKIE['ff_rememberme'] : '';
    if ($cookie) {
        list ($user, $token, $mac) = explode(':', $cookie);
        if (!hash_equals(hash_hmac('sha256', $user . ':' . $token, SECRET_KEY), $mac)) {
            return false;
        }
        $usertoken = get_user_token($user);
        if (hash_equals($usertoken, $token)) {
            $_SESSION['loginuser'] = $user;
        }
    }
}

function email_error($message) {
    send_mail('info@stichtingfamiliarforest.nl', 'Web Familiar Forest', 'Found ERROR!', $message);  
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function store_user_token($username, $token) {
  global $db_host, $db_user, $db_pass, $db_name;
  $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
  if( $mysqli->connect_errno ) {
    return false;
  } else {
    $query = sprintf("UPDATE `users` u SET u.token = '%s' WHERE u.username = '%s'", 
      $mysqli->real_escape_string($token),
      $mysqli->real_escape_string($username));
    $result = $mysqli->query($query);
    $mysqli->close();
    if( $result === FALSE ) {
      return FALSE;
    }
  }
  return true;
}

function get_user_token($username) {
  global $db_host, $db_user, $db_pass, $db_name;
  $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
  if( $mysqli->connect_errno ) {
    return "false";
  } else {
    $query = sprintf("SELECT u.token FROM `users` WHERE (`username` = '%s')",
        $mysqli->real_escape_string($username));
    $result = $mysqli->query($query);
    $mysqli->close();
    if( $result === FALSE ) {
      return "FALSE";
    } elseif( $result->num_rows == 1 ) {
      $row = $result->fetch_array(MYSQLI_ASSOC);
      return $row['token'];
    } else {
      return "false";
    }
  }
  return "false";
}

function get_user_info($username) {
 	global $db_host, $db_user, $db_pass, $db_name;
 	$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
 	$row = array();
 	if( $mysqli->connect_errno ) {
  	return false;
  } else {
  	$query = sprintf("SELECT * FROM `users` WHERE (`username` = '%s')",
      $mysqli->real_escape_string($username));
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

function get_firstname($username) {
  global $db_host, $db_user, $db_pass, $db_name;
  $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
  if( $mysqli->connect_errno ) {
    return false;
  } else {
    $query = sprintf("SELECT * FROM `person` WHERE (`email` = '%s')",
      $mysqli->real_escape_string($username));
    $result = $mysqli->query($query);
    $mysqli->close();
    if( $result === FALSE ) {
      return FALSE;
    } elseif( $result->num_rows == 1 ) {
      $row = $result->fetch_array(MYSQLI_ASSOC);
      return $row['firstname'];
    } else {
      return false;
    }
  }
  return false;
}

function get_signups() {
	global $db_host, $db_user, $db_pass, $db_name;
	$result = "";
	$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
	if( $mysqli->connect_errno ) {
  		return false;
  	} else {
  		$query = "SELECT p.lastname, p.firstname, p.birthdate, p.gender, p.city, p.email, p.phone, s.motivation, s.familiar, p.editions, s.partner, s.contrib0_type, s.contrib0_desc, s.contrib0_need, s.contrib1_type, s.contrib1_desc, s.contrib1_need, s.preparations, p.visits
            FROM person p join $current_table s on p.email = s.email";
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
  $result = "";
  $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
  if( $mysqli->connect_errno ) {
      return false;
    } else {
        $query = "SELECT p.birthdate, p.gender, p.city, p.visits, s.partner, s.contrib0_type, s.contrib1_type FROM person p join $current_table s on p.email = s.email";
        $result = $mysqli->query($query);
        if( $result === FALSE ) {
            //echo $mysqli->error;
        }
    }
    $mysqli->close();
    return $result;
}

function get_buyers() {
	global $db_host, $db_user, $db_pass, $db_name;
	$result = "";
	$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
	if( $mysqli->connect_errno ) {
  		return false;
  	} else {
  		$result = $mysqli->query("SELECT * FROM $current_table WHERE complete = 1");
  	}
  $mysqli->close();
  return $result;
}

function get_person($email) {
  global $db_host, $db_user, $db_pass, $db_name;
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
      //echo $mysqli->error;
      return FALSE;
    } elseif( $result->num_rows == 1 ) {
      $row = $result->fetch_array(MYSQLI_ASSOC);
    } else {
      return false;
    }
  }
  return $row;
}

function get_signup($email) {
  global $db_host, $db_user, $db_pass, $db_name;
  $result = "";
  $row = array();
  $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
  if( $mysqli->connect_errno) {
    return false;
  } else {
    $query = sprintf("SELECT * FROM $current_table WHERE email = '%s'", $mysqli->real_escape_string($email));
    $result = $mysqli->query($query);
    $mysqli->close();
    if( $result === FALSE ) {
      //echo $mysqli->error;
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
    return $type;
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
  } else if( $edition == "fff2016" ) {
    return "Familiar Forest Festival 2016";
  } else if( $edition == "" ) {
    return "";
  } else {
    return "Onbekend";
  }
}

function translate_gender($gender) {
  if( $gender == "male" ) { 
    return "Jongeman";
  } else if( $gender == "female" ) {
    return "Jongedame";
  } else {
    return "Onbekend";
  }
}

function translate_task($task) {
  if( $task == "" ) {
    return "Niet ingedeeld";
  } else if( $task == "keuken") { 
    return "Keuken";
  } else if( $task == "bar" ) {
    return "Bar";
  } else if( $task == "other" ) {
    return "Anders";
  } else if ($task == "interiour" ) {
    return "Interieur Verzorging";
  } else if ( $task == "thee") {
    return "Theetent";
  } else if( $task == "camping") {
    return "Campingwinkel";
  } else if ($task == "afbouw" ) {
    return "Afbouw";
  } else if ($task == "act" ) {
    return "Act, niet ingedeeld";
  } else if ( $task == "game" ) {
    return "Game";
  } else if ( $task == "lecture" ) {
    return "Lezing";
  } else if ( $task == "schmink" ) {
    return "Schmink";
  } else if ( $task == "other_act" ) {
    return "Act anders";
  } else if ( $task == "perform" ) {
    return "Performance";
  } else if ( $task == "install" ) {
    return "Installatie";
  }
  return "Onbekend (Error)";
}
?>
