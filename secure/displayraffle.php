<?php session_start();
include "../functions.php";

if(!isset($_SESSION['loginuser'])) {
    header('Location: ../login.php');
}

$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info[$db_user_name];
$user_info_permissions = $user_info[$db_user_permissions];



if( $user_info_permissions & PERMISSION_DISPLAY != PERMISSION_DISPLAY) {
    return;
}

function get_raffle() {
    global $db_host, $db_user, $db_pass, $db_name;
    global $db_table_raffle;
    
}

$statistic_string = "";
$resultHTML = "<table class='called-table'>";
$resultHTML.="<tr class='header-row'>";
$resultHTML.="<th>Voornaam</th>";
$resultHTML.="<th>Achternaam</th>";
$resultHTML.="<th>Email</th>";
$resultHTML.="<th>Telefoon</th>";
$resultHTML.="<th>Code</th>";
$resultHTML.="<th>Gebeld</th>";
$resultHTML.="</th>";

//Statistics

$sqlresult = "";
$query = "SELECT p.firstname, p.lastname, r.email, p.phone, r.code, r.called FROM person p join raffle r on r.email = p.email";
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    return false;
} else {
    $sqlresult = $mysqli->query($query);
}
$mysqli->close();


while($row = mysqli_fetch_array($sqlresult,MYSQLI_NUM))
{
    $resultHTML.="<tr>";
    foreach($row as $key=>$value) {
        if( $key == 2 ) {
            $resultHTML.= "<td><div id='email' class='table-cell'>" . $value . "</div></td>";
        } else if( $key == 5 ) {
            $Bvalue = ($value == 0 ? 'N' : 'J'); 
            $resultHTML.= "<td><div class='table-cell'>" . $Bvalue . "</div></td>";
        } else {
            $resultHTML.= "<td><div class='table-cell'>" . $value . "</div></td>";
        }
    }
    $resultHTML.= "</tr>";
}

$resultHTML.="</table>";

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

        <script src="../js/vendor/modernizr-2.8.3.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <div class="secure_content">
            <span class="raffle_table">
                <?php echo $resultHTML ?>
            </span>
            <span class="person_info">

            </span>
        </div>



        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.3.min.js"><\/script>')</script>
        <script src="../js/plugins.js"></script>
        <script src="../js/main.js"></script>
        <script src="js/called.js"></script>

    </body>
</html>
