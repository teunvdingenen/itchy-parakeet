<?php session_start();
include "../functions.php";
if(!isset($_SESSION['loginuser'])) {
    header('Location: login');
    return;
}
$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info[$db_user_name];
$user_info_permissions = $user_info[$db_user_permissions];
if( !($user_info_permissions & PERMISSION_DISPLAY) ) {
	return;
}

$statistic_type = $_POST["type"];

$result = array();
$total=0;
$ages=array();
$gender_m=$gender_f=0;
$cities = array();
$visits = array();
//$has_partner=$no_partner=0;
$contrib0 = array();
$contrib1 = array();
$signupdates = array();
$sqlresult = 0;

$result = "";
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $statistic_type == 'signup') {
	if( $mysqli->connect_errno ) {
	  return false;
	} else {
	    $query = "SELECT p.birthdate, p.gender, p.city, p.visits, p.partner, c0.type, c1.type, p.signupdate 
	    	FROM person p join contribution c0 on p.contrib0 = c0.id join contribution c1 on p.contrib1 = c1.id";
	    $sqlresult = $mysqli->query($query);
	    if( $sqlresult === FALSE ) {
	        echo $mysqli->error;
	    }
	}
} else if ( $statistic_type == 'raffle' ) {
    if( $mysqli->connect_errno ) {
        return false;
    } else {
        $query = "SELECT p.birthdate, p.gender, p.city, p.visits, p.partner, c0.type, c1.type, p.signupdate
            FROM person p join contribution c0 on p.contrib0 = c0.id join contribution c1 on p.contrib1 = c1.id
            WHERE EXISTS (SELECT 1 FROM $db_table_raffle as r WHERE  p.email = r.email) ";
        $sqlresult = $mysqli->query($query);
        if( $sqlresult === FALSE ) {
            echo $mysqli->error;
        }
    }
}
$mysqli->close();

while($row = mysqli_fetch_array($sqlresult,MYSQLI_NUM))
{
	foreach($row as $key=>$value) {
		if( $key == 0 ) { //birthdate
			$age = (new DateTime($value))->diff(new DateTime('now'))->y;
		    if(array_key_exists($age, $ages)) {
		    	$ages[$age] += 1;
		    } else {
		    	$ages[$age] = 1;
		    }
		} elseif( $key == 1 ) { //gender
		    if( $value=='male' || $value=='Male') $gender_m += 1;
		    elseif( $value=='female' || $value=='Female') $gender_f += 1;
		} elseif( $key == 2 ) { //city
		    if(array_key_exists(strtolower($value), $cities)) {
		        $cities[strtolower($value)] += 1;
		    } else {
		        $cities[strtolower($value)] = 1;
		    }
		} elseif( $key == 3 ) {  //visits
		    if(array_key_exists(strtolower($value), $visits)) {
		        $visits[$value] += 1;
		    } else {
		        $visits[$value] = 1;
		    }
//		} elseif( $key == 4 ) { //partner
//			if( $value != '' && $value != NULL) {
//				$has_partner += 1;
//			} else {
//				$no_partner += 1;
//			}
		} elseif( $key == 5) { //contrib0
		    if( array_key_exists($value, $contrib0) ) {
		        $contrib0[$value] += 1;
		    } else {
		        $contrib0[$value] = 1;
		    }
		} elseif( $key == 6) { //contrib1
		    if( array_key_exists($value, $contrib1) ) {
		        $contrib1[$value] += 1;
		    } else {
		        $contrib1[$value] = 1;
		    }
		} elseif( $key == 7) { //signupdate
			$datetime = new DateTime($value);
			$datestr = $datetime->format('Y-m-d');
			if(array_key_exists($datestr, $signupdates)) {
				$signupdates[$datestr] += 1;
			} else {
				$signupdates[$datestr] = 1;
			}
		}
	}
}
//age a = 18-21, b=22-26, c=27-30, d=31-34, e=34+
//$visits_tot=$visits_one=$visits_two=$visits_three=$visits_four=$visits_more=$visits_none=0;
arsort($cities);
ksort($contrib0);
ksort($contrib1);
ksort($signupdates);
ksort($ages);
ksort($visits);
//$result["total"] = $total;
$result["ages"] = $ages;
$result["gender"] = array('Male' => $gender_m, 'Female' => $gender_f);
$result["city"] = $cities;
$result["visits"] = $visits;
$result["contrib0"] = $contrib0;
$result["contrib1"] = $contrib1;
$result["signupdates"] = $signupdates;

echo json_encode($result);
?>