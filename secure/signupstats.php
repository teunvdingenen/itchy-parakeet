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

$displayname = "";
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $statistic_type == 'signup') {
	$displayname = "ingeschreven";
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
	$displayname = "ingelood";
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

$total = 0;
$age_total = 0;

while($row = mysqli_fetch_array($sqlresult,MYSQLI_NUM))
{
	$total += 1;
	foreach($row as $key=>$value) {
		if( $key == 0 ) { //birthdate
			$age = (new DateTime($value))->diff(new DateTime('now'))->y;
			$age_total += $age;
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
		} elseif( $key == 5) { //contrib0
			$storevalue = $value;
			if( $value != 'iv' && $value != 'bar' && $value != 'keuken' && $value != 'afb') {
				$storevalue = 'act';
			}
		    if( array_key_exists($storevalue, $contrib0) ) {
		        $contrib0[$storevalue] += 1;
		    } else {
		        $contrib0[$storevalue] = 1;
		    }
		} elseif( $key == 6) { //contrib1
		    $storevalue = $value;
			if( $value != 'iv' && $value != 'bar' && $value != 'keuken' && $value != 'afb') {
				$storevalue = 'act';
			}
		    if( array_key_exists($storevalue, $contrib1) ) {
		        $contrib1[$storevalue] += 1;
		    } else {
		        $contrib1[$storevalue] = 1;
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

asort($cities);
asort($contrib0);
asort($contrib1);
//ksort($signupdates);
asort($ages);
asort($visits);

reset($cities);
reset($visits);
reset($ages);
reset($contrib0);
reset($contrib1);

$max_columns = 5;

$resultHTML="<table class='table table-sm table-bordered table-hover table-condensed'>";

$resultHTML.="<tr>";
$resultHTML.="<th>Totaal " . $displayname . "</th>";
$resultHTML.="<td>".$total."</td>";
$resultHTML.="</tr>";

$resultHTML.="<tr>";
$resultHTML.="<th>Leeftijd</th>";
$resultHTML.="<td>Gemiddeld: ". round($age_total / $total)."</td>";
for($i = 0; $i < $max_columns-1; $i++) {
	$resultHTML.="<td>".key($ages)." : ".current($ages)." (".round(current($ages)/$total * 100) . "%)</td>";
	if( next($ages) === FALSE ) {
		break;
	}
}
$resultHTML.="</tr>";

$resultHTML.="<tr>";
$resultHTML.="<th>Geslacht</th>";
$resultHTML.="<td>Man: ".$gender_m." (".round($gender_m/$total * 100) . "%)</td>";
$resultHTML.="<td>Vrouw: ".$gender_f." (".round($gender_f/$total * 100) . "%)</td>";
$resultHTML.="</tr>";

$resultHTML.="<tr>";
$resultHTML.="<th>Eerste keus</th>";
for($i = 0; $i < $max_columns; $i++) {
	$resultHTML.="<td>".key($contrib0)." : ".current($contrib0)." (".round(current($contrib0)/$total * 100) . "%)</td>";
	if( next($contrib0) === FALSE ) {
		break;
	}
}
$resultHTML.="</tr>";

$resultHTML.="<tr>";
$resultHTML.="<th>Tweede keus</th>";
for($i = 0; $i < $max_columns; $i++) {
	$resultHTML.="<td>".key($contrib1)." : ".current($contrib1)." (". round(current($contrib1)/$total * 100) . "%)</td>";
	if( next($contrib1) === FALSE ) {
		break;
	}
}
$resultHTML.="</tr>";

$resultHTML.="<tr>";
$resultHTML.="<th>Steden</th>";
for($i = 0; $i < $max_columns; $i++) {
	$resultHTML.="<td>".key($cities)." : ".current($cities)." (". round(current($cities)/$total * 100) . "%)</td>";
	if( next($cities) === FALSE ) {
		break;
	}
}
$resultHTML.="</tr>";

$resultHTML.="<tr>";
$resultHTML.="<th>Bezoeken</th>";
$resultHTML.="<td>Nooit: ".$visits[0]. " (" . round($visits[0] / $total * 100) .")</td>";
for($i = 0; $i < $max_columns-1; $i++) {
	$resultHTML.="<td>".key($visits)." : ".current($visits)." (". round(current($visits)/$total * 100) . "%)</td>";
	if( next($visits) === FALSE ) {
		break;
	}
}
$resultHTML.="</tr>";

$resultHTML.="</tbody></table>";




//age a = 18-21, b=22-26, c=27-30, d=31-34, e=34+
//$visits_tot=$visits_one=$visits_two=$visits_three=$visits_four=$visits_more=$visits_none=0;

//$result["total"] = $total;
//$result["ages"] = $ages;
//$result["gender"] = array('Male' => $gender_m, 'Female' => $gender_f);
//$result["city"] = $cities;
//$result["visits"] = $visits;
//$result["contrib0"] = $contrib0;
//$result["contrib1"] = $contrib1;
//$result["signupdates"] = $signupdates;

?>

<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="Teun van Dingenen">
        <link rel="icon" href="../favicon.ico">

        <title>Familiar Forest Dashboard</title>

        <!-- Bootstrap core CSS -->
        <link href="../css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="../css/main.css" rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        
        <div id="content" class="container-fluid">
        	<?=$resultHTML?>
        </div>

   		<!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
        <script src="../js/vendor/bootstrap.min.js"></script>

        <script src="../js/plugins.js"></script>
        <script src="../js/main.js"></script>
        <script src="js/secure.js"></script>
    </body>
</html>