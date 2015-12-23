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
$age_max=$age_tot=0;
$age_min = 9999;
$age_a=$age_b=$age_c=$age_d=$age_e=0;
$gender_m=$gender_f=0;
$cities = array();
$visits_tot=$visits_one=$visits_two=$visits_three=$visits_four=$visits_more=$visits_none=0;
$has_partner=$no_partner=0;
$contrib0 = array();
$contrib1 = array();
$signupdates = array();
$sqlresult = 0;

$result = "";
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
  return false;
} else {
    $query = "SELECT p.birthdate, p.gender, p.city, p.visits, p.partner, c0.type, c1.type, p.signupdate FROM person p join contribution c0 on p.contrib0 = c0.id join contribution c1 on p.contrib1 = c1.id";
    $sqlresult = $mysqli->query($query);
    if( $sqlresult === FALSE ) {
        echo $mysqli->error;
    }
}
$mysqli->close();

while($row = mysqli_fetch_array($sqlresult,MYSQLI_NUM))
{
	foreach($row as $key=>$value) {
		if( $key == 0 ) { //birthdate
		    $age = (new DateTime($value))->diff(new DateTime('now'))->y;
		    if( $age > $age_max) {
		        $age_max = $age;
		    }
		    if( $age < $age_min) {
		        $age_min = $age;
		    }
		    if( $age < 22) {
		    	$age_a+=1;
		    } elseif( $age < 27) {
		    	$age_b+=1;
		    } elseif( $age < 31) {
		    	$age_c+=1;
		    } elseif( $age < 34) {
		    	$age_d+=1;
		    } else {
		    	$age_e+=1;
		    }
		    if(array_key_exists($age, $ages)) {
		    	$ages[$age] += 1;
		    } else {
		    	$ages[$age] = 1;
		    }
		    $age_tot += $age;
		} elseif( $key == 1 ) { //gender
		    if( $value=='male') $gender_m += 1;
		    elseif( $value=='female') $gender_f += 1;
		} elseif( $key == 2 ) { //city
		    if(array_key_exists(strtolower($value), $cities)) {
		        $cities[strtolower($value)] += 1;
		    } else {
		        $cities[strtolower($value)] = 1;
		    }
		} elseif( $key == 3 ) {  //visits
		    $visits_tot += $value;
		    if( $value == 0) {
		        $visits_none += 1;
		    } elseif( $value == 1) {
		    	$visits_one += 1;
		    } elseif( $value == 2) {
		    	$visits_two += 1;
		    } elseif( $value == 3) {
		    	$visits_three += 1;
		    } elseif( $value == 4) {
		    	$visits_four += 1;
		    } elseif( $value >= 5) {
		    	$visits_more += 1;
		    }
		} elseif( $key == 4 ) { //partner
			if( $value != '' && $value != NULL) {
				$has_partner += 1;
			} else {
				$no_partner += 1;
			}
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
ksort($cities);
ksort($contrib0);
ksort($contrib1);
ksort($signupdates);
ksort($ages);
$result["total"] = $total;
$result["age"] = array('min'=>$age_min, 'max'=>$age_max, '1821'=>$age_a, '2226'=>$age_b, '2730'=>$age_c,
	'3134'=>$age_d, '35' => $age_e, 'tot' => $age_tot);
$result["ages"] = $ages;
$result["gender"] = array('male' => $gender_m, 'female' => $gender_f);
$result["city"] = $cities;
$result["visits"] = array('tot'=>$visits_tot, 'none'=>$visits_none, 'one'=>$visits_one, 'two'=>$visits_two,
	'three'=>$visits_three, 'four'=>$visits_four, 'more'=>$visits_more);
$result["contrib0"] = $contrib0;
$result["contrib1"] = $contrib1;
$result["signupdates"] = $signupdates;

echo json_encode($result);
?>