<?php session_start();
include "functions.php";

if(!isset($_SESSION['loginuser'])) {
    header('Location: login.php');
}

$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info[$db_user_name];
$user_info_permissions = $user_info[$db_user_permissions];

$statistic_string = "";
$resultHTML = "<table>";
$resultHTML.="<tr>";
$resultHTML.="<th>Achternaam</th>"; //0
$resultHTML.="<th>Voornaam</th>"; //1
$resultHTML.="<th>Geboortedag</th>"; //2
$resultHTML.="<th>Geslacht</th>"; //3
$resultHTML.="<th>Woonplaats</th>"; //4
$resultHTML.="<th>Email</th>"; //5
$resultHTML.="<th>Telefoon</th>"; //6
$resultHTML.="<th>Voorgaande Edities</th>"; //8 (7=visists not displayed)
$resultHTML.="<th>Partner</th>"; //9
$resultHTML.="<th>Eerste keus</th>"; //10
$resultHTML.="<th></th>"; //11
$resultHTML.="<th></th>"; //12
$resultHTML.="<th>Tweede keus</th>"; //13
$resultHTML.="<th></th>"; //14
$resultHTML.="<th></th>"; //15
$resultHTML.="</th>";

//Statistics
$total=0;
$age_max=$age_tot=0;
$age_min = 9999;
$gender_m=$gender_f=0;
$cities = array();
$editions_tot=$editions_none=0;
$contrib0 = array();
$contrib1 = array();

if( $user_info_permissions & PERMISSION_DISPLAY ) {
    $sqlresult = get_signups();
    $mysqli = new mysqli();
    while($row = mysqli_fetch_array($sqlresult,MYSQLI_NUM))
    {
        $resultHTML.="<tr>";
        foreach($row as $key=>$value) {
            if( $key == 2 ) { //birthdate
                $age = (new DateTime($value))->diff(new DateTime('now'))->y;
                if( $age > $age_max) {
                    $age_max = $age;
                }
                if( $age < $age_min) {
                    $age_min = $age;
                }
                $age_tot += $age;
            } elseif( $key == 3 ) { //gender
                if( $value=='male') $gender_m += 1;
                elseif( $value=='female') $gender_f += 1;
            } elseif( $key == 4 ) { //city
                if(array_key_exists(strtolower($value), $cities)) {
                    $cities[strtolower($value)] += 1;
                } else {
                    $cities[strtolower($value)] = 1;
                }
            } elseif( $key == 7 ) { //nr visits
                $editions_tot += $value;
                if( $value == 0) {
                    $editions_none += 1;
                }
            } elseif( $key == 10) { //contrib0
                if( array_key_exists($value, $contrib0) ) {
                    $contrib0[$value] += 1;
                } else {
                    $contrib0[$value] = 1;
                }
            } elseif( $key == 13) { //contrib1
                if( array_key_exists($value, $contrib1) ) {
                    $contrib1[$value] += 1;
                } else {
                    $contrib1[$value] = 1;
                }
            }
            if( $key != 7) {
                $resultHTML.= "<td>" . $value . "</td>";
            }
        }
        $resultHTML.= "</tr>";
        $total += 1;
    }
    ksort($cities);
    ksort($contrib0);
    ksort($contrib1);
    $statistic_string .= "<ul>";
    $statistic_string .= "<li>Totaal aantal inschrijvingen: ". $total. "</li>";
    $statistic_string .= "<li>Leeftijd tussen ".$age_min." en ".$age_max." (Gemiddeld: ".round($age_tot/$total, 2).").</li>";
    $statistic_string .= "<li>Aantal Heren: ".$gender_m." (".round($gender_m/$total*100, 2) . "%)</li>";
    $statistic_string .= "<li>Aantal Dames: ".$gender_f." (".round($gender_f/$total*100,2) . "%)</li>"; 
    $statistic_string .= "<li>Woonplaatsen: ";
    foreach($cities as $key=>$value) {
        $statistic_string.= ucfirst($key)."(".$value.") ";
    }
    $statistic_string .= "</li>";
    $statistic_string .= "<li>Edities: Gemiddeld: ".round($editions_tot/$total,2).", waarvan ".$editions_none." geen edities opgegeven hebben.</li>";
    $statistic_string .= "<li>Eerste keuzes: ";
    foreach($contrib0 as $key=>$value) {
        $statistic_string.= $key."(".$value.") ";
    }
    $statistic_string.="</li>";
    $statistic_string .= "<li>Tweede keuzes: ";
    foreach($contrib1 as $key=>$value) {
        $statistic_string.= $key."(".$value.") ";
    }
    $statistic_string.="</li></ul>";
} else {
    $resultHTML="You do not have the necessary permissions to view this page";
}
$resultHTML.="</table>";
$resultHTML = $statistic_string . $resultHTML;
?>

<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <!-- Place favicon.ico in the root directory -->

        <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <div class="secure_content">
            <?php echo $resultHTML ?>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.3.min.js"><\/script>')</script>
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>

    </body>
</html>
