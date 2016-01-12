<?php
include "initialize.php";
try
{
    include "mollie_api_init.php";

    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

    $payment_id = $_POST["id"];
    $payment = $mollie->payments->get($payment_id);
    $code = $payment->metadata->raffle;

    if ($payment->isPaid()) {
        if( database_getpayed($mysqli, $payment_id) != 1) {
            database_setpayed($mysqli, $payment_id, 1);
            //send email to buyer
        }
    }
    elseif (!$payment->isOpen()) {
        $remove_success = database_remove($mysqli, $payment_id);
        echo "removed ".$remove_success;
    }
    $mysqli->close();
}
catch (Mollie_API_Exception $e) {
    //email error
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}

function database_getpayed($mysqli, $payment_id) {
    $sqlquery = sprintf("SELECT b.complete FROM `buyer` b WHERE `b.id` = '%s'",
        $mysqli->real_escape_string($payment_id));
    $sqlresult = $mysqli->query($sqlquery);
    if( $sqlresult === FALSE) {
        return FALSE;
        //log error
    }
    if( $sqlresult->num_rows != 1 ) {
        return FALSE;
    }
    $row = $sqlresult->fetch_array(MYSQLI_ASSOC);
    return $row['complete'];
}

function database_remove($mysqli, $payment_id) {
    $sqlquery = sprintf("DELETE FROM `buyer` WHERE id = '%s'", $mysqli->real_escape_string($payment_id));
    $sqlresult = $mysqli->query($sqlquery);
    if( $sqlresult === FALSE) {
        echo $sqlresult->error;
        return FALSE;
        //log error
    }
    return true;
}

function database_setpayed($mysqli, $payment_id, $payed) {
    $sqlquery = sprintf("UPDATE buyer set complete='%s' WHERE id = '%s';",
        $mysqli->real_escape_string($payed),
        $mysqli->real_escape_string($payment_id));
    $sqlresult = $mysqli->query($sqlquery);
    if( $sqlresult === FALSE ) {
        //log error
        return FALSE;
    }
    return true;
}

?>