<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_DISPLAY) != PERMISSION_DISPLAY ) {
    header('Location: oops.php');
}

$returnVal = "";
$infoBlock = "";

$tableHTML="<table class='table table-striped table-bordered table-hover table-condensed'>";
$tableHTML.="<thead><tr class='header-row'>";
$tableHTML.="<th>ID</th>";
$tableHTML.="<th>Code</th>";
$tableHTML.="<th>Email</th>";
$tableHTML.="<th>Status (extern)</th>";
$tableHTML.="<th>Status (intern)</th>";
$tableHTML.="<th>Bedrag</th>";
$tableHTML.="</th></thead>";

try {
    include "../mollie_api_init.php";
} catch (Mollie_API_Exception $e) {
    addError("Er is iets fout gegaan met de Mollie link");
    _exit();
}
$offset = 0;
$requested = 50;
$total = 999999999;
$nr_paid = 0;
$complete_amount = 0;
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
while( $offset < $total ) {
    $payments = $mollie->payments->all($offset,$requested);
    $total = $payments->totalCount;
    foreach ($payments->data as $payment ) {
        if( $payment->status != 'paid' ) {
            continue;
        }
        $nr_paid += 1;
        $complete_amount += $payment->amount;
        $query = sprintf("SELECT * FROM $current_table WHERE transactionid = '%s'", $mysqli->real_escape_string($payment->id));
        $sqlresult = $mysqli->query($query);
        if( !$sqlresult ) {
            addError("Unable to query database");
        } else if ( $sqlresult->num_rows == 0) {
            addError(sprintf("Transactie met id: %s en code: %s staat niet in de database", $payment->id, $payment->metadata->raffle));
        } else {
            $string = "";
            $err = false;
            if( $sqlresult->num_rows > 1 ) {
                $err = true;
                $string .= "Meerdere entries voor: ".$payment->id."<br>";
            }
            while( $row = $sqlresult->fetch_array(MYSQLI_ASSOC) ) {                
                if( $payment->status == 'paid' && $row['complete'] != 1 ) {
                    $err = true;
                    $string .= sprintf("Betaling met id: %s, code %s is niet als betaald gemarkeerd! Status is: %s", $payment->id, $payment->metadata->raffle, $row['complete']);
                }
                addToTable($row['id'], $row['code'], $row['email'], $payment->status, $row['complete'], $payment->amount);
            }
            if( $err ) {
                addInfo($string);
            } 
        }
    }
    $offset += $requested;
}
$tableHTML.="</table>";

function addError($value) {
    global $returnVal;
    $returnVal .= "<div class='alert alert-danger'><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span> ".$value."</div>";
}

function addInfo($value) {
    global $infoBlock;
    $infoBlock .= "<div class='alert alert-info'><span class='glyphicon glyphicon-exclamation-remove' aria-hidden='true'></span> ".$value."</div>";
}

function addtoTable($id, $code, $email, $mollie_status, $complete, $amount) {
    global $tableHTML;
    $tableHTML .= "<tr>";
    $tableHTML .= "<td>".$id."</td>";
    $tableHTML .= "<td>".$code."</td>";
    $tableHTML .= "<td>".$email."</td>";
    $tableHTML .= "<td>".$mollie_status."</td>";
    $tableHTML .= "<td>".$complete."</td>";
    $tableHTML .= "<td>".$amount."</td>";
    $tableHTML .= "</tr>";
}

function addOK($value) {
    global $infoBlock;
    $infoBlock .= "<div class='alert alert-success'><span class='glyphicon glyphicon-exclamation-ok' aria-hidden='true'></span> ".$value."</div>";
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
                <?=$returnVal?>
                <div class='alert alert-success'><span class='glyphicon glyphicon-exclamation-ok' aria-hidden='true'></span> Totaal: <?=$nr_paid?> successvolle transacties (€ <?=$complete_amount?>) </div>
                <?=$infoBlock?>
                <?=$tableHTML?>
            </div>
        </div>
    </div>
</div>

    	<?php include("default-js.html"); ?>
    </body>
</html>
