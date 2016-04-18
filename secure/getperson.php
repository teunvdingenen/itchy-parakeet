<?php session_start();
include "../functions.php";

if(!isset($_SESSION['loginuser'])) {
    header('Location: ../login.php');
}

$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info[$db_user_name];
$user_info_permissions = $user_info[$db_user_permissions];


if( $user_info_permissions & PERMISSION_DISPLAY != PERMISSION_DISPLAY ) {
    return false;
}

$email = $_POST['email'];
$level = $_POST['level'];

echo $level == 'raffle';

$resultHTML = '';

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    return false;
} else {
    $query = "";
    if( $level == 'buyer') {
        $query = sprintf("SELECT p.lastname, p.firstname, p.birthdate, p.gender, p.city, p.email, p.phone, p.editions, p.partner, p.motivation, p.familiar, c0.type, c0.description, c0.needs, c1.type, c1.description, c1.needs, p.visits, r.code, r.called, b.id
            FROM person p join contribution c0 on p.contrib0 = c0.id join contribution c1 on p.contrib1 = c1.id join raffle r on p.email = r.email join buyer b on p.email = b.email
            WHERE  p.email = '%s'", $mysqli->real_escape_string($email));
    } else if( $level == 'raffle' ) {
        $query = sprintf("SELECT p.lastname, p.firstname, p.birthdate, p.gender, p.city, p.email, p.phone, p.editions, p.partner, p.motivation, p.familiar, c0.type, c0.description, c0.needs, c1.type, c1.description, c1.needs, p.visits, r.code, r.called
        FROM person p join contribution c0 on p.contrib0 = c0.id join contribution c1 on p.contrib1 = c1.id join raffle r on p.email = r.email
        WHERE  p.email = '%s'", $mysqli->real_escape_string($email));
    }
    $sqlresult = $mysqli->query($query);
    if( $sqlresult === FALSE ) {
         //error
        return false;
    }
    if( $sqlresult->num_rows != 1 ) {
        //error
        return false;
    }
    $row = $sqlresult->fetch_array(MYSQLI_NUM);
    $firstname = $row[1];
    $lastname = $row[0];
    $birthdate = $row[2];
    $gender = $row[3];
    $city = $row[4];
    $email = $row[5];
    $phone = $row[6];
    $editions = $row[7];
    $partner = $row[8];
    //TODO add motivation & familiar
    $contrib0_type = $row[9];
    $contrib0_desc = $row[10];
    $contrib0_need = $row[11];
    $contrib1_type = $row[12];
    $contrib1_desc = $row[13];
    $contrib1_need = $row[14];
    $visits = $row[15];
    $raffle_code = $row[16];
    $raffle_called = ($row[17] == 0 ? 'N' : 'J');
    $buyer_id = "";
    if( $level == 'buyer') {
        $buyer_id = $row[18];
    }
    $age = $age = (new DateTime($birthdate))->diff(new DateTime('now'))->y;
    $buttonHTML = "";
    if( $raffle_called == 'N' ) {
        $buttonHTML = "<div><button id='called-button'>Gebeld</button></div>";
    } else {
        $buttonHTML = "";
    }
}
$mysqli->close();

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
            <div>
                <span>Voornaam:</span><span><?php echo $firstname; ?></span>
            </div>
            <div>
                <span>Achternaam:</span><span><?php echo $lastname; ?></span>
            </div>
            <div>
                <span>Geboortedatum:</span><span><?php echo $birthdate; ?> (<?php echo $age; ?> jaar)</span>
            </div>
            <div>
                <span>Geslacht:</span><span><?php echo $gender ?></span>
            </div>
            <div>
                <span>Woonplaats:</span><span><?php echo $city ?></span>
            </div>
            <div>
                <span>Email:</span><span id='email'><?php echo $email ?></span>
            </div>
            <div>
                <span>Telefoon:</span><span><?php echo $phone ?></span>
            </div>
            <div>
                <span>Edities:</span><span><?php echo $editions ?></span>
            </div>
            <div>
                <span>Partner:</span><span><?php echo $partner ?></span>
            </div>
            <div>
                <span>Bijdrage:</span><span><?php echo $contrib0_type ?></span>
            </div>
            <div>
                <?php echo $contrib0_desc ?>
            </div>
            <div>
                <?php echo $contrib0_need ?>
            </div>
            <div>
                <span>Tweede keus:</span><span><?php echo $contrib1_type ?></span>
            </div>
             <div>
                <?php echo $contrib1_desc ?>
            </div>
            <div>
                <?php echo $contrib1_need ?>
            </div>
            <div>
                <span>Loting code:</span><span><?php echo $raffle_code ?></span>
            </div>
            <div>
                <span>Gebeld:</span><span><?php echo $raffle_called ?></span>
            </div>
            <div>
                <span>Transactie code:</span><span><?php echo $buyer_id ?></span>
            </div>
            <?php echo $buttonHTML ?>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.3.min.js"><\/script>')</script>
        <script src="../js/plugins.js"></script>
        <script src="../js/main.js"></script>

    </body>
</html>
