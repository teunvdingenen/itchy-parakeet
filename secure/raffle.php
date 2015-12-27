<?php session_start();
include "../functions.php";

if(!isset($_SESSION['loginuser'])) {
    header('Location: login');
}

$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info[$db_user_name];
$user_info_permissions = $user_info[$db_user_permissions];

$resultHTML="<table class='raffle-table'>";
$resultHTML.="<tr class='header-row'>";
$resultHTML.="<th>Achternaam</th>";
$resultHTML.="<th>Voornaam</th>";
$resultHTML.="<th>Geboortedag</th>";
$resultHTML.="<th>Geslacht</th>";
$resultHTML.="<th>Woonplaats</th>";
$resultHTML.="<th>Email</th>";
$resultHTML.="<th>Telefoon</th>";
$resultHTML.="<th>Voorgaande Edities</th>";
$resultHTML.="<th>Partner</th>";
$resultHTML.="<th>Eerste keus</th>";
$resultHTML.="<th></th>";
$resultHTML.="<th></th>";
$resultHTML.="<th>Tweede keus</th>";
$resultHTML.="<th></th>";
$resultHTML.="<th></th>";
$resultHTML.="<th>Aantal bezoeken</th>";
$resultHTML.="</th>";

$cell_keys = ['lastname', 'firstname', 'birthdate', 'gender', 'city', 'email', 'phone', 'editions', 'partner', 'contrib0','type0','needs0', 'contrib1','type1','needs1', 'visits'];

if( $user_info_permissions & PERMISSION_DISPLAY ) {
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if( $mysqli->connect_errno ) {
        return false;
    } else {
        $query = "SELECT p.lastname, p.firstname, p.birthdate, p.gender, p.city, p.email, p.phone, p.editions, p.partner, c0.type, c0.description, c0.needs, c1.type, c1.description, c1.needs, p.visits
            FROM person p join contribution c0 on p.contrib0 = c0.id join contribution c1 on p.contrib1 = c1.id
            WHERE  NOT EXISTS (SELECT 1 FROM $db_table_raffle as r WHERE  p.email = r.email)";
        $sqlresult = $mysqli->query($query);
        if( $sqlresult === FALSE ) {
             //error
        }
    }
    $mysqli->close();

    while($row = mysqli_fetch_array($sqlresult,MYSQLI_NUM))
    {
        $resultHTML.="<tr>";
        $i = 0;
        foreach($row as $value) {
            $resultHTML .= "<td><div id='".$cell_keys[$i]."' class='table-cell'>".$value."</div></td>";
            $i++;
        }
        $resultHTML.= "</tr>";
    }
    $resultHTML.="</table>";
} else {
    $resultHTML="You do not have the necessary permissions to view this page";
}

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

        <div id="statsbar">
            <div class="statistics_content">
                <canvas class='forth' id='genderchart'></canvas>
                <canvas class='forth' id='ageschart'></canvas>
                <canvas class='forth' id='visitschart'></canvas>
                <canvas class='forth' id='citieschart'></canvas>
            </div>        
        </div>
        <div id="secure_content" class="secure_content">
            <button id='confirm'>Inloten</button>
            <?php echo $resultHTML ?>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.3.min.js"><\/script>')</script>
        <script src="../js/plugins.js"></script>
        <script src="../js/main.js"></script>
        <script src="js/Chart.js"></script>
        <script src="js/chartfunctions.js"></script>
        <script src="js/raffle.js"></script>

    </body>
</html>
