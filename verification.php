<?php
include "initialize.php";
include "functions.php";
include "createhashes.php";

try
{
    include "mollie_api_init.php";

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
    email_error("API call failed: " . htmlspecialchars($e->getMessage()));
}

function send_confirmation($mysqli, $payment_id) {
    $query = sprintf("SELECT p.firstname, p.lastname, p.email, r.code, b.ticket
        FROM person p join raffle r on r.email = p.email join buyer b on b.email = p.email
        WHERE b.id = '%s'", $mysqli->real_escape_string($payment_id));
    $sqlresult = $mysqli->query($query);
    if( $sqlresult->num_rows != 1 ) {
        return FALSE;
    }
    $row = $sqlresult->fetch_array(MYSQLI_ASSOC);
    $fullname = $row['firstname']." ".$row['lastname'];
    $ticketurl = "http://stichtingfamiliarforest.nl/ticket.php?ticket=".$row['ticket'];
    $content = get_email_header();
    $content .= "<p>Lieve ".$row['firstname'].",</p>";
    $content .= "<p>We hebben al je gegevens ontvangen en de betaling is rond dus dat betekent dat we samen naar Familiar Forest 2016 kunnen!</p>";
    $content .= "<p>Meer informatie over Familiar Forest volgt nog maar het is goed om alvast 10 en 11 september 2016 vrij te maken in je agenda. Houd onze <a href'https://www.facebook.com/events/591534081011159/'>Facebook</a> in de gaten voor meer nieuws.</p>";
    $content .= "<p>Het is goed om de volgende informatie nog even goed te bewaren:</p>";
    $content .= "<p>Je deelname code is: " . $row['code'] . "</p>";
    $content .= "<p>Je transactienummer is: " . $payment_id . "</p>";
    $content .= "<p>Je kunt je ticket downloaden en printen door op deze link te klikken <a href='".$ticketurl."'>".$ticketurl."</a></p>";
    $content .= "<p>Een allerlaatst hebben we wat <a href='http://stichtingfamiliarforest.nl/info.html'>informatie</a> voor je klaar gezet</p>";

    $content .= get_email_footer();

    send_mail($row['email'], $fullname, "Familiar Forest 2016 Deelname bevestiging", $content);
    return true;
}

function send_confirmation_refund($mysqli, $payment_id) {
    $query = sprintf("SELECT p.firstname, p.lastname, p.email
        FROM person p join buyer b on b.email = p.email
        WHERE b.id = '%s'", $mysqli->real_escape_string($payment_id));
    $sqlresult = $mysqli->query($query);
    if( $sqlresult->num_rows != 1 ) {
        return FALSE;
    }
    $row = $sqlresult->fetch_array(MYSQLI_ASSOC);
    $fullname = $row['firstname']." ".$row['lastname'];
    $content = get_email_header();
    $content .= "<p>Lieve ".$row['firstname'].",</p>";
    $content .= "<p>Je verzoek voor een refund op je ticketgeld hebben we in goede orde ontvangen. Normaal gesproken ontvang je het geld na een werkdag terug op je rekening.</p>";
    $content .= "<p>We vinden het erg jammer dat we je er niet bij hebben op Familiar Forest. Hopelijk zien we volgend jaar!<p>";
    $content .= "<p>Als je nog vragen, opmerkingen of andere zorgen hebt kun je een reply sturen naar deze email.";

    $content .= get_email_footer();

    send_mail($row['email'], $fullname, "Familiar Forest 2016 Refund bevestiging", $content);
    return true;
}

function send_confirmation_refund_half($mysqli, $payment_id) {
    $query = sprintf("SELECT p.firstname, p.lastname, p.email
        FROM person p join buyer b on b.email = p.email
        WHERE b.id = '%s'", $mysqli->real_escape_string($payment_id));
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

    send_mail($row['email'], $fullname, "Familiar Forest 2016 Half ticket", $content);
    return true;
}

function isHalfTicket($mysqli, $code) {
    $sqlresult = $mysqli->query(sprintf("SELECT * FROM halfticket WHERE code = '%s'", 
        $mysqli->real_escape_string($code)));
    if( $sqlresult === FALSE) {
        //log error
        return FALSE;
    }
    if( $sqlresult->num_rows != 1 ) {
        //log error
        return false;
    }
    return true;
}

function database_setpayed($mysqli, $payment_id, $payed) {
    $sqlquery = sprintf("UPDATE buyer set complete=$payed WHERE id = '%s';",
        $mysqli->real_escape_string($payment_id));
    $sqlresult = $mysqli->query($sqlquery);
    if( $sqlresult === FALSE ) {
        email_error("Failed to set payed for transaction: ".$payment_id);
        return FALSE;
    }
    return true;
}

?>