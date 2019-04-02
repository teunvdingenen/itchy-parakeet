<?php
include_once "functions.php";

try {
    include "u/mollie_api_init.php";
} catch (Mollie_API_Exception $e) {
    addError("Er is iets fout gegaan met de iDeal link");
    _exit();
}
$message = "";

if ( !isset( $_GET['raffle'] ) || empty( $_GET['raffle'] ) ) {
    header("Location: index");
}

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
$code = $_GET["raffle"];
$recevied = false;
$result = $mysqli->query(sprintf("SELECT p.firstname, p.email from $current_table s join person p on s.email = p.email WHERE rafflecode ='%s'", $mysqli->real_escape_string($code)));
if( !$result || $result->num_rows != 1 ) {
    $result = $mysqli->query(sprintf("SELECT p.firstname, p.email from person p join swap s on p.email = s.buyer WHERE s.code = '%s'", $mysqli->real_escape_string($code)));
    if( !$result || $result->num_rows != 1 ) {
        email_error("Kon email en naam niet ophalen voor code: ".$code." ".$mysqli->error. " ".$result->num_rows);
    } else {
        $received = true;
    }
} else {
    $received = true;
}

if($received) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $firstname = $row['firstname'];
    $email = $row['email'];
    $paid = get_paid($mysqli, $email);

    if( $paid === FALSE ) {
        email_error("redirect.php: paid returned FALSE for: ".$firstname. " with code: ". $code);
        $message = "<p>Er is een fout opgetreden in het betalingsysteem. <br> Voor mail informatie of geruststellingen kun je mailen naar: ".$mailtolink.".</p>";
    } else if( $paid == 0 ) {
        $message = "<p>Het lijkt erop dat de betaling niet gelukt is of je hebt deze afgebroken.<p>";
        $message .= "<p>Als je per ongeluk iets fout gedaan hebt kun het je nogmaals proberen door naar het <a href='u/deelname'>betalingscherm</a> te gaan.</p>";
        $message .= "<p>Bij zorgen, voor vragen of je wilt iets anders kwijt, dan kun je altijd mailen naar: ". $mailtolink.".</p>";
    } else {
        $message = "<p>De betaling is helemaal rond! We hebben erg veel zin om met jou de het onderzoek aan te gaan!</p>";
        $message .= "<p>Ter bevestiging ontvang je ook nog een email met wat aanvullende gegevens.</p>";
        $message .= "<p>Als je zorgen, vragen of je wilt iets anders kwijt kun je altijd mailen naar: ". $mailtolink.".</p>";
    }
    $message .= "<p>De high fives zijn gratis, de knuffels oprecht en de liefde oneindig,<br><br>Familiar Forest</p>";
} else {
    $message = "<p>We hebben je betalingsgegevens niet kunnen achterhalen. Als je het idee hebt dat er iets fout is gegaan, stuur dan even een mailtje naar: ".$mailtolink."</p>";
}

$mysqli->close();

function get_paid($mysqli, $email) {
    global $mollie, $current_table;
    $sqlquery = sprintf("SELECT s.transactionid FROM $current_table s WHERE s.email = '%s'",
        $mysqli->real_escape_string($email));
    $sqlresult = $mysqli->query($sqlquery);
    if( $sqlresult === FALSE) {
        email_error("Fout bij ophalen transaction id voor: ".$email." ".$mysqli->error);
        return FALSE;
        //log error
    }
    if( $sqlresult->num_rows != 1 ) {
        return FALSE;
    }
    $row = $sqlresult->fetch_array(MYSQLI_ASSOC);
    $id = $row["transactionid"];
    if( $mollie->payments->get($id)->isPaid()) {
        return 1;
    }
    return 0;
}

?>
<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Familiar Forest</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <link rel="icon" href="favicon.ico">
        <!-- Place favicon.ico in the root directory -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/bootstrap-datepicker3.css">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" type="text/css" media="all"
            href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/smoothness/jquery-ui.css"/>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        <div class="page-container">
        <?php include("header.php"); ?>
            <div class="container">
                <div class="jumbotron">
                    <h2>Familiar Forest 2019 : evolutie van de homo familiaris</h2>
                    <p class="lead">
                        Lieve <? echo $firstname ?>,
                    </p>
                    <p><? echo $message ?><p>
                </div>
            </div>
        </div>

        <?php include("default-js.html"); ?>
        </body>
</html>
