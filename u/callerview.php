<?php session_start();
include "../functions.php";


$user_email = $user_firstname = $user_permissions = "";

if(!isset($_SESSION['email'])) {
    header('Location: ../login');
} else {
    $user_email = $_SESSION['email'];
}
if(!isset($_SESSION['firstname'])) {
    header('Location: ../login');
} else {
    $user_firstname = $_SESSION['firstname'];
}
if(!isset($_SESSION['permissions'])) {
    header('Location: ../login');
} else {
    $user_permissions = $_SESSION['permissions'];
}

if( $user_permissions & PERMISSION_CALLER != PERMISSION_CALLER ) {
    header('Location: oops.php');
}

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    echo "De website is kapot! Bel Teun!";
    return false;
}

$fullname = $firstname = $lastname = $contrib0 = $motivation = $phone = $code = $familiar = "";
$headertext = "Bellen maar!";
$query = "SELECT p.firstname, p.lastname, s.contrib0_type, s.familiar, s.motivation, p.email, p.phone, s.rafflecode, s.called FROM person p join $current_table s on s.email = p.email WHERE s.called = 0 AND s.valid = 1 ORDER BY RAND() LIMIT 1";
$result = $mysqli->query($query);
if( $result === FALSE ) {
    //error
} else if( $result->num_rows != 1 ) {
    $headertext = "Er is niemand meer om te bellen!";
} else {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $firstname = $row['firstname'];
    $lastname = $row['lastname'];
    $email = $row['email'];
    $phone = $row['phone'];
    $code = $row['code'];
    $contrib0 = translate_contrib($row['type']);
    $motivation = $row['motivation'];
    $familiar = $row['familiar'];
    $fullname = $firstname . " " . $lastname;

    //$query = sprintf("UPDATE raffle SET called = 2 WHERE code = '%s'", $mysqli->real_escape_string($code));
    //$mysqli->query($query);
    //if( $mysqli->affected_rows != 1 ) {
        //error
    //}


}

?>

<!doctype html>
<html class="no-js" lang="">
    <?php include("head.html"); ?>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <?php include("header.php"); ?>
        <div class="page-container">
            <?php include("header.php"); ?>
            <div class="container">
                <div class="row row-offcanvas row-offcanvas-left">
                <?php include("navigation.php");?>
                <div class="col-xs-12 col-sm-9"> 
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="text-center"><?=$headertext?></h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        Naam:
                    </div>
                    <div class="col-md-4">
                        <?=$fullname?>
                    </div>
                    <div class="col-md-2">
                        Bijdrage:
                    </div>
                    <div class="col-md-4">
                        <?=$contrib0?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        Telefoonnummer:
                    </div>
                    <div class="col-md-4">
                        <?=$phone?>
                    </div>
                    <div class="col-md-2">
                        Motivatie:
                    </div>
                    <div class="col-md-4">
                        <?=$motivation?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        Code:
                    </div>
                    <div class="col-md-4">
                        <?=$code?>
                    </div>
                    <div class="col-md-2">
                        Bekend door:
                    </div>
                    <div class="col-md-4">
                        <?=$familiar?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <button class='btn btn-lg btn-info btn-block' id='noanswer' onclick=<?php echo "notAnswered('".$code."');" ?>>Geen antwoord </button>
                    </div>
                    <div class="col-md-6">
                        <button class='btn btn-lg btn-success btn-block' id='answered' onclick=<?php echo "called('".$code."');" ?>>Gebeld!</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class='btn btn-lg btn-danger btn-block' id='stalk' onclick=<?php echo "window.open('https://www.facebook.com/search/top/?q=".str_replace(' ', '%20', $fullname)."')" ?>>STALK</button>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div id="code" style="display:none"><?=$code?></div>
    	<?php include("default-js.html"); ?>
        <script src="js/called.js"></script>
    </body>
</html>
