<?php
include_once "initialize.php";
include_once "fields.php";
include_once "sendmail.php";

function generateRandomToken($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function store_user_token($username, $token) {
  global $db_host, $db_user, $db_pass, $db_name;
  $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
  if( $mysqli->connect_errno ) {
    $mysqli->close();
    return false;
  } else {
    $now = new DateTime();
    $oneweek = $now->add(new DateInterval('P1W'))->format('Y-m-d H:i:s');
    $now = new DateTime();
    $query = sprintf("UPDATE `users` u SET u.token = '%s', u.expire = '%s', u.lastlogin = '%s' WHERE u.email = '%s'", 
      $mysqli->real_escape_string($token),
      $oneweek,
      $now->format('Y-m-d H:i:s'),
      $mysqli->real_escape_string($username));
    $result = $mysqli->query($query);
    $mysqli->close();
    if( $result === FALSE ) {
      email_error("Error bij store user token: ".$mysqli->error);
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
    $query = sprintf("SELECT token FROM `users` WHERE (`email` = '%s')",
        $mysqli->real_escape_string($username));
    $result = $mysqli->query($query);
    $mysqli->close();
    if( $result === FALSE ) {
      return "FALSE";
    } elseif( $result->num_rows == 1 ) {
      $row = $result->fetch_array(MYSQLI_ASSOC);
      return $row['token'];
    } else {
      email_error(sprintf("Unable to get Token for: %s", $username));
      return "false";
    }
  }
  return "false";
}

function updateSession($username, $permissions, $firstname) {
  $_SESSION['email'] = $username;
  $_SESSION['permissions'] = $permissions;
  $_SESSION['firstname'] = $firstname;
  $_SESSION['LAST_ACTIVITY'] = time();
  $_SESSION['CREATED'] = time();
}

function login($username, $remember) {
    global $db_host, $db_user, $db_pass, $db_name;
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if( $mysqli->connect_errno ) {
      $mysqli->close();
      return false;
    }
    $query = sprintf("SELECT `permissions` FROM users WHERE `email` = '%s'", 
      $mysqli->real_escape_string($username));
    $result = $mysqli->query($query);
    if( $result === FALSE ) {
      $mysqli->close();
      return FALSE;
    } else if( $result->num_rows != 1 ) {
      $mysqli->close();
      return FALSE;
    }
    $permissions = $result->fetch_array(MYSQLI_ASSOC)['permissions'];
    $query = sprintf("SELECT `firstname` FROM person WHERE `email` = '%s'", 
      $mysqli->real_escape_string($username));
    $result = $mysqli->query($query);
    if( $result === FALSE ) {
      $mysqli->close();
      return FALSE;
    } else if( $result->num_rows != 1 ) {
      $mysqli->close();
      return FALSE;
    }
    $firstname = $result->fetch_array(MYSQLI_ASSOC)['firstname'];
    $mysqli->close();
    updateSession($username, $permissions, $firstname);
    $token = generateRandomToken(128);
    store_user_token($username, $token);
    if( $remember ) {
      setRememberMe($username, $token);
    }
    return true;
}

function logout($user) {
  global $db_host, $db_user, $db_pass, $db_name;
  $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
  if( $mysqli->connect_errno ) {
    $mysqli->close();
    return false;
  } else {
    $now = new DateTime();
    $query = sprintf("UPDATE `users` u SET u.expire = '%s' WHERE u.email = '%s'", 
      $now->format('Y-m-d H:i:s'),
      $mysqli->real_escape_string($user));
    $result = $mysqli->query($query);
    $mysqli->close();
    if( $result === FALSE ) {
      email_error("Error bij store user token: ".$mysqli->error);
      return FALSE;
    }
  }
}

function setRememberMe($user, $token) {
  $cookie = $user . ':' . $token;
  $mac = hash_hmac('sha256', $cookie, SECRET_KEY);
  $cookie .= ':' . $mac;
  setcookie('ff_rememberme', $cookie, time()+604800, '/u');
}

function rememberMe() {
    $cookie = isset($_COOKIE['ff_rememberme']) ? $_COOKIE['ff_rememberme'] : '';
    if ($cookie) {
        list ($user, $token, $mac) = explode(':', $cookie);
        if (!hash_equals(hash_hmac('sha256', $user . ':' . $token, SECRET_KEY), $mac)) {
            email_error(sprintf("No rememberme match for: %s, token: %s, mac: %s", $user, $token, $mac));
            return false;
        }
        $usertoken = get_user_token($user);
        if (hash_equals($usertoken, $token)) {
            updateSession($user, get_permissions($user), get_firstname($user));
        } else {
          email_error(sprintf("No hash match for user: %s, usertoken: %s, token: %s", $user, $usertoken, $token));
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

function get_user_info($username) {
 	global $db_host, $db_user, $db_pass, $db_name;
 	$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
 	$row = array();
 	if( $mysqli->connect_errno ) {
  	return false;
  } else {
  	$query = sprintf("SELECT * FROM `users` WHERE (`email` = '%s')",
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

function get_permissions($username) {
  global $db_host, $db_user, $db_pass, $db_name;
  $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
  if( $mysqli->connect_errno ) {
    return false;
  } else {
    $query = sprintf("SELECT `permissions` FROM users WHERE `email` = '%s'", 
      $mysqli->real_escape_string($username));
    $result = $mysqli->query($query);
    if( $result === FALSE ) {
      $mysqli->close();
      return FALSE;
    } else if( $result->num_rows != 1 ) {
      $mysqli->close();
      return FALSE;
    }
    return $result->fetch_array(MYSQLI_ASSOC)['permissions'];
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
    return "Interieurverzorging";
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
  } else if( $type == "opb" ) {
    return "Opbouw";
  } else if( $type == "ontw" ) {
    return "Ontwerpen en/of bouw decoraties, podia etc.";
  } else if( $type == "" ) {
    return "";
  } else if( $type == "other_act" ) {
    return "Act Overig";
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
  } else if( $edition == "fv2017" ) {
    return "Familiar Voorjaar 2017";
  } else if ( $edition == "fff2017") {
    return "Familiar Forest 2017";
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
    return "Niet ingedeeld: ".$task;
  } else if( $task == "keuken") { 
    return "Keuken";
  } else if( $task == "bar" ) {
    return "Bar";
  } else if( $task == "other" ) {
    return "Anders";
  } else if ($task == "iv" ) {
    return "Interieur Verzorging";
  } else if ( $task == "thee") {
    return "Theetent";
  } else if( $task == "camping") {
    return "Campingwinkel";
  } else if ($task == "afb" ) {
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
  } else if( $task == "workshop" ) {
    return "Workshop";
  } else if( $task == "crew" ) {
    return "Crew";
  } else if( $task == "other_act") {
    return "Act Overig";
  } else if( $task == "jip" ) {
    return "Jips hoekje";
  } else if( $task = "silent" ) {
    return "Silent Disco";
  } else if( $task == "vuur") { 
    return "Vuurmeester";
  }
  return "Onbekend: ".$task;
}

function is_act($task) {
  return in_array($task, ['act','game','lecture','schmink','other_act','perform','install','workshop']);
}
?>
