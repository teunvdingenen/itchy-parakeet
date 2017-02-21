<?php
include "functions.php";
include "createhashes.php";

if ( !isset( $_POST['id'] ) || empty( $_POST['id'] ) ) {
    header("Location: index");
}

try
{
    include "u/mollie_api_init.php";

    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

    $payment_id = $_POST["id"];
    $payment = $mollie->payments->get($payment_id);
    $code = $payment->metadata->raffle;

    if( $payment->isRefunded() ) {
        if( isHalfTicket($mysqli, $code) ) {
            send_confirmation_refund_half($mysqli, $payment_id);
        } else {
            database_setpayed($mysqli, $payment_id, 3);
            if( !send_confirmation_refund($mysqli, $payment_id) ) {
                email_error("Failed to send confimation refund for payment: ".$payment_id);
            }
        }
    } else if ($payment->isPaid()) {
        database_setpayed($mysqli, $payment_id, 1);
        if( !set_hash($mysqli, $payment_id, $code) ) {
            email_error("Unable to create ticket for payment: ".$payment_id);
        }
        if( !send_confirmation($mysqli, $payment_id) ) {
            email_error("Failed to send confirmation for payment: ".$payment_id);
        }
    } else { 
        database_setpayed($mysqli, $payment_id, 0);
    }
    $mysqli->close();
}
catch (Mollie_API_Exception $e) {
    //email error
    email_error("Transaction: ".$payment_id." "."API call failed: " . htmlspecialchars($e->getMessage()));
}

function send_confirmation($mysqli, $payment_id) {
    global $current_table;
    $query = sprintf("SELECT p.firstname, p.lastname, p.email, s.rafflecode, s.ticket
        FROM person p join $current_table s on s.email = p.email
        WHERE s.transactionid = '%s'", $mysqli->real_escape_string($payment_id));
    $sqlresult = $mysqli->query($query);
    if( $sqlresult->num_rows != 1 ) {
        return FALSE;
    }
    $row = $sqlresult->fetch_array(MYSQLI_ASSOC);
    $fullname = $row['firstname']." ".$row['lastname'];
    $ticketurl = "http://stichtingfamiliarforest.nl/ticket.php?ticket=".$row['ticket'];
    $content = get_email_header();
    $content .= "<p>Lieve ".$row['firstname'].",</p>";
    $content .= "<p>We hebben al je gegevens ontvangen en de betaling is rond dus dat betekent dat we samen naar Familiar Voorjaar 2017 kunnen!</p>";
    $content .= "<p>Meer informatie over Familiar Voorjaar volgt nog maar houd alvast 5 tot en met 7 mei 2017 vrij te maken in je agenda. Houd onze <a href='https://www.facebook.com/events/1428059363879439/'>Facebook</a> in de gaten voor meer nieuws.</p>";
    $content .= "<p>Bewaar ook de volgende informatie nog even goed:</p>";
    $content .= "<p>Je deelname code is: " . $row['rafflecode'] . "</p>";
    $content .= "<p>Je transactienummer is: " . $payment_id . "</p>";
    $content .= "<p>Binnenkort zal het mogelijk zijn om je ticket te downloaden van onze website. Zodra we die voor je klaar hebben ontvang je nog een email daarover.</p>";
    //$content .= "<p>Je kunt je ticket downloaden en printen door op deze link te klikken <a href='".$ticketurl."'>".$ticketurl."</a></p>";
    //$content .= "<p>Een allerlaatst hebben we wat <a href='http://stichtingfamiliarforest.nl/info.html'>informatie</a> voor je klaar gezet</p>";

    $content .= get_email_footer();

    send_mail($row['email'], $fullname, "Familiar Voorjaar 2017 Deelname bevestiging", $content);
    return true;
}

function send_confirmation_refund($mysqli, $payment_id) {
    global $current_table;
    $query = sprintf("SELECT p.firstname, p.lastname, p.email
        FROM person p join $current_table s on s.email = p.email
        WHERE s.transactionid = '%s'", $mysqli->real_escape_string($payment_id));
    $sqlresult = $mysqli->query($query);
    if( $sqlresult->num_rows != 1 ) {
        return FALSE;
    }
    $row = $sqlresult->fetch_array(MYSQLI_ASSOC);
    $fullname = $row['firstname']." ".$row['lastname'];
    $content = get_email_header();
    $content .= "<p>Lieve ".$row['firstname'].",</p>";
    $content .= "<p>Je verzoek voor een refund op je ticketgeld hebben we in goede orde ontvangen. Normaal gesproken ontvang je het geld na een werkdag terug op je rekening.</p>";
    $content .= "<p>We vinden het erg jammer dat we je er niet bij hebben op Familiar Forest. Hopelijk zien we je bij de volgende editie!<p>";
    $content .= "<p>Als je nog vragen, opmerkingen of andere zorgen hebt kun je een reply sturen naar deze email.";

    $content .= get_email_footer();

    send_mail($row['email'], $fullname, "Familiar Voorjaar 2017 Refund bevestiging", $content);
    return true;
}

function send_confirmation_refund_half($mysqli, $payment_id) {
    global $current_table;
    $query = sprintf("SELECT p.firstname, p.lastname, p.email
        FROM person p join $current_table s on s.email = p.email
        WHERE s.transactionid = '%s'", $mysqli->real_escape_string($payment_id));
    $sqlresult = $mysqli->query($query);
    if( $sqlresult->num_rows != 1 ) {
        return FALSE;
    }
    $row = $sqlresult->fetch_array(MYSQLI_ASSOC);
    $fullname = $row['firstname']." ".$row['lastname'];
    $content = get_email_header();
    $content .= "<p>Lieve ".$row['firstname'].",</p>";
    $content .= "<p>We hebben gezien dat jij dit jaar (weer) komt opbouwen, afbouwen of iets anders fantastisch doet bij Familiar Forest. Alvast super bedankt daarvoor! Naast lieve woorden ontvang je ook nog eens een half ticket!</p>";
    $content .= "<p>We hebben opgemerkt dat je al een ticket had gekocht en hebben daarom 60EUR naar je terug overgemaakt. Normaal gesproken wordt dat na een werkdag verwerkt.<p>";
    $content .= "<p>Als je nog vragen, opmerkingen of andere zorgen hebt kun je een reply sturen naar deze email. Tot snel!";

    $content .= get_email_footer();

    send_mail($row['email'], $fullname, "Familiar Voorjaar 2017 Half ticket", $content);
    return true;
}

function isHalfTicket($mysqli, $code) {
    global $current_table;
    $sqlresult = $mysqli->query(sprintf("SELECT share FROM $current_table WHERE rafflecode = '%s'", 
        $mysqli->real_escape_string($code)));
    if( $sqlresult === FALSE) {
        //log error
        return FALSE;
    }
    if( $sqlresult->num_rows != 1 ) {
        //log error
        return false;
    }
    $row = $sqlresult->fetch_array(MYSQLI_ASSOC);
    return ($row['share'] == "HALF");
}

function database_setpayed($mysqli, $payment_id, $payed) {
    global $current_table;
    $sqlquery = sprintf("UPDATE $current_table set complete=$payed WHERE transactionid = '%s';",
        $mysqli->real_escape_string($payment_id));
    $sqlresult = $mysqli->query($sqlquery);
    if( $sqlresult === FALSE ) {
        email_error("Failed to set payed for transaction: ".$payment_id);
        return FALSE;
    }
    return true;
}

?>
