<?php

include "../functions.php";

include("checklogin.php");

try {
    include "mollie_api_init.php";
} catch (Mollie_API_Exception $e) {
    addError("Er is iets fout gegaan met de iDeal link");
    _exit();
}
$message = "";


$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

$paid = get_paid($mysqli, $user_email);

if( $paid === FALSE ) {
    email_error("redirect.php: paid returned FALSE for: ".$user_firstname. " with code: ". $code);
    $message = "<p>Er is een fout opgetreden in het betalingsysteem. <br> Voor mail informatie of geruststellingen kun je mailen naar: ".$mailtolink.".</p>";
} else if( $paid == 0 ) {
    $message = "<p>Het lijkt erop dat de betaling niet gelukt is of je hebt deze afgebroken.<p>";
    $message .= "<p>Als je per ongeluk iets fout gedaan hebt kun het je nogmaals proberen door naar het <a href='buyer'>betalingscherm</a> te gaan.</p>";
    $message .= "<p>Bij zorgen, voor vragen of je wilt iets anders kwijt, dan kun je altijd mailen naar: ". $mailtolink.".</p>";
} else {
    $message = "<p>De betaling is helemaal rond! We hebben erg veel zin om met jou het voorjaar te gaan vieren.</p>";
    $message .= "<p>Ter bevestiging ontvang je ook nog een email met wat aanvullende gegevens.</p>";
    $message .= "<p>Als je zorgen, vragen of je wilt iets anders kwijt wilt kun je altijd mailen naar: ". $mailtolink.".</p>";
}
$message .= "<p>De high fives zijn gratis, de knuffels oprecht en de liefde oneindig,<br><br>Familiar Forest</p>";
$mysqli->close();

function get_paid($mysqli, $email) {
    global $mollie;
    $sqlquery = sprintf("SELECT s.transactionid FROM $current_table s WHERE s.email = '%s'",
        $mysqli->real_escape_string($email));
    $sqlresult = $mysqli->query($sqlquery);
    if( $sqlresult === FALSE) {
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
    <?php include("head.html"); ?>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div class="page-container">
        <?php include("header.php"); ?>
            <div class="container">
                <div class="row row-offcanvas row-offcanvas-left">
                    <?php include("navigation.php");?>
                    <div class="col-xs-12 col-sm-9"> 

                        <h2>Familiar Forest 2016</h2>
                        <p class="lead">
                            Lieve <? echo $user_firstname ?>,
                        </p>
                        <? echo $message ?>
                    </div>
                </div>
            </div>
        </div>

        <?php include("default-js.html"); ?>
        </body>
</html>
