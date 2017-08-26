<?php
include_once("../functions.php");
include_once("checklogin.php");

if( !isset($required_permissions) ) {
    exit;
}
if( !isset($request_for) ) {
    exit;
}

if( ($user_permissions & $required_permissions) != $required_permissions ) {
    exit;
}

$result = array();
$ages=array();
$gender_m=$gender_f=0;
$cities = array();
$visits = array();
$contrib0 = array();
$contrib1 = array();
$tickets_half = $tickets_free = $tickets_full_pay = 0;

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

$countresult = $mysqli->query("SELECT COUNT(*) as 'count' from $current_table where complete = 1 and share = 'FULL'");
if(!$result ) {
	$tickets_full_pay = $countresult->fetch_array(MYSQLI_ASSOC)['count'];
}

$displayname = "";

$statsrestriction = "1";
if( $request_for == 'raffle' || $request_for == 'showraffle' ) {
    $statsrestriction = "(s.complete = 1 or s.valid = 1) AND s.task != 'crew'";
    $displayname = "ingeloot & verkocht";
} else if( $request_for == 'buyer' ) {
    $statsrestriction = 's.complete = 1';
    $displayname = "verkocht";
} else if( $request_for == 'signups' ) {
    $statsrestriction = "1 AND EXISTS (SELECT 1 FROM $current_table s WHERE s.email = p.email)";
    $displayname = "ingeschreven";
} else if( $request_for == 'caller' ) {
    $statsrestriction = "s.valid = 1 AND s.called = 0 AND s.task != 'crew'";
} else if( $request_for == 'called_done' ) {
    $statsrestriction = 's.valid = 1 AND s.called != 0';
} else if( $request_for == 'people' ) {
    $statsrestriction = '1';
} else {
	exit;
}

$statsresult = $mysqli->query("SELECT p.birthdate, p.gender, p.city, p.visits, s.contrib0_type, s.contrib1_type, s.task, s.share 
        FROM person p left join $current_table s on s.email = p.email
        WHERE $statsrestriction");
$mysqli->close();
if( $statsresult === FALSE ) {
	echo $mysqli->error;
	return false;
} else if( $statsresult->num_rows == 0 ) {
	return;
}

$total = 0;
$age_total = 0;
$total_crew = 0;

while($row = mysqli_fetch_array($statsresult,MYSQLI_ASSOC))
{
	$total += 1;
	foreach($row as $key=>$value) {
		if( $key == 'birthdate' ) { 
			$age = (new DateTime($value))->diff(new DateTime('now'))->y;
			$age_total += $age;
		    if(array_key_exists($age, $ages)) {
		    	$ages[$age] += 1;
		    } else {
		    	$ages[$age] = 1;
		    }
		} elseif( $key == 'gender' ) { 
		    if( $value=='male' || $value=='Male') $gender_m += 1;
		    elseif( $value=='female' || $value=='Female') $gender_f += 1;
		} elseif( $key == 'city' ) { //city
		    if(array_key_exists(strtolower($value), $cities)) {
		        $cities[strtolower($value)] += 1;
		    } else {
		        $cities[strtolower($value)] = 1;
		    }
		} elseif( $key == 'visits' ) { 
		    if(array_key_exists(strtolower($value), $visits)) {
		        $visits[$value] += 1;
		    } else {
		        $visits[$value] = 1;
		    }
		} elseif( $key == 'contrib0_type') { 
			$storevalue = $value;
			if( $value != 'iv' && $value != 'bar' && $value != 'keuken' && $value != 'afb') {
				$storevalue = 'act';
			}
		    if( array_key_exists($storevalue, $contrib0) ) {
		        $contrib0[$storevalue] += 1;
		    } else {
		        $contrib0[$storevalue] = 1;
		    }
		} elseif( $key == 'contrib1_type') {
		    $storevalue = $value;
			if( $value != 'iv' && $value != 'bar' && $value != 'keuken' && $value != 'afb') {
				$storevalue = 'act';
			}
		    if( array_key_exists($storevalue, $contrib1) ) {
		        $contrib1[$storevalue] += 1;
		    } else {
		        $contrib1[$storevalue] = 1;
		    }
		} elseif( $key == 'task' ) {
			if( $value == 'crew' ) {
				$total_crew += 1;
			}
		}
	}
}
arsort($cities);
arsort($contrib0);
arsort($contrib1);
//ksort($signupdates);
arsort($ages);
arsort($visits);
reset($cities);
reset($visits);
reset($ages);
reset($contrib0);
reset($contrib1);
$max_columns = 5;


echo "<table class='table table-sm table-bordered table-hover table-condensed'>";
echo "<tr>";
echo "<th>Totaal " . $displayname . "</th>";
echo "<td>".$total."</td>";
echo "<th>Waarvan crew: </th>";
echo "<td>".$total_crew."</td>";
echo "<th>Volle prijs tickets:</th>";
echo "<td>".$tickets_full_pay."</td>";

echo "<tr>";
echo "<th>Leeftijd</th>";
echo "<td>Gemiddeld: ". round($age_total / $total)."</td>";
for($i = 0; $i < $max_columns-1; $i++) {
	echo "<td>".key($ages)." : ".current($ages)." (".round(current($ages)/$total * 100) . "%)</td>";
	if( next($ages) === FALSE ) {
		break;
	}
}
echo "</tr>";
echo "<tr>";
echo "<th>Geslacht</th>";
echo "<td>Man: ".$gender_m." (".round($gender_m/$total * 100) . "%)</td>";
echo "<td>Vrouw: ".$gender_f." (".round($gender_f/$total * 100) . "%)</td>";
echo "</tr>";
echo "<tr>";
echo "<th>Eerste keus</th>";
for($i = 0; $i < $max_columns; $i++) {
	echo "<td>".key($contrib0)." : ".current($contrib0)." (".round(current($contrib0)/$total * 100) . "%)</td>";
	if( next($contrib0) === FALSE ) {
		break;
	}
}
echo "</tr>";
echo "<tr>";
echo "<th>Tweede keus</th>";
for($i = 0; $i < $max_columns; $i++) {
	echo "<td>".key($contrib1)." : ".current($contrib1)." (". round(current($contrib1)/$total * 100) . "%)</td>";
	if( next($contrib1) === FALSE ) {
		break;
	}
}
echo "</tr>";
echo "<tr>";
echo "<th>Steden</th>";
for($i = 0; $i < $max_columns; $i++) {
	echo "<td>".key($cities)." : ".current($cities)." (". round(current($cities)/$total * 100) . "%)</td>";
	if( next($cities) === FALSE ) {
		break;
	}
}
echo "</tr>";
echo "<tr>";
echo "<th>Bezoeken</th>";
for($i = 0; $i < $max_columns; $i++) {
	echo "<td>".key($visits)." : ".current($visits)." (". round(current($visits)/$total * 100) . "%)</td>";
	if( next($visits) === FALSE ) {
		break;
	}
}
echo "</tr>";
echo "</tbody></table>";

?>
