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
        if( !isFullTicket($mysqli, $code) ) {
            //send_confirmation_refund_half($mysqli, $payment_id);
        } else {
            $result = $mysqli->query(sprintf("UPDATE $current_table SET `task` = '', `complete` = 3, `valid` = 0, `ticket` = '', `rafflecode` = '' WHERE transactionid = '%s'",$mysqli->real_escape_string($payment_id)));
            if( !$result || $mysqli->affected_rows != 1 ) {
                email_error("Something may have gone wrong with refund update transaction: ".$payment_id);
            }
            if( !send_confirmation_refund($mysqli, $payment_id) ) {
                email_error("Failed to send confimation refund for payment: ".$payment_id);
            }
        }
    } else if ($payment->isPaid()) {
        database_setpayed($mysqli, $payment_id, 1);
        $result = $mysqli->query("SELECT seller, buyer FROM `swap` WHERE `code` = '%s'",$mysqli->real_escape_string($code));
        if( !$result || $mysqli->num_rows != 1 ) {
            //do nothing
        } else {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $buyer_email = $row['buyer'];
            $seller_email = $row['seller'];
            $task = "";
            if( !is_null($seller_email) ) {
                $result = $mysqli->query("SELECT transactionid, share, task FROM $current_table WHERE `email` = '%s'",
                    $mysqli->real_escape_string($seller_email));
                if( !$result || $result->num_rows != 1 ) {
                    email_error("Unable to do refund for seller: ".$seller_email);
                } else {
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $seller_payment = $row['transactionid'];
                    $share = $row['share'];
                    $task = $row['task'];
                    $amount = 120;
                    if( $share == "FREE" ) {
                        email_error("Somehow I just wanted to refund a free ticket?!, code: ".$code);
                        $mysqli->close();
                        exit;
                    } else if( $share == "HALF")  {
                        $amount = 60;
                        email_error("Refunded half ticket for code: ".$code);
                    }
                    $amount -= 0.19;
                    $payment = $mollie->payments->get($seller_payment);
                    $refund = $mollie->payments->refund($payment, $amount);
                }
                if( $task != "" ) {
                    $taskresult = $mysqli->query(sprintf("SELECT task FROM `shifts` WHERE `name` = '%s'",
                        $mysqli->real_escape_string($task)));
                    if( !$taskresult ) {
                        email_error("Couldn't get task for name: ".$task);
                    } else {
                        $tasktype = $taskresult->fetch_array(MYSQLI_ASSOC)['task'];
                        $result = $mysqli->query(sprintf("SELECT firstname, lastname FROM person WHERE email = '%s'",
                            $mysqli->real_escape_string($seller_email)));
                        if(!$result || $result->num_rows != 1 ) {
                            email_error("Unable to send acts/volunteers an email about sale of ticket for: ".$seller_email);
                        } else {
                            $row = $result->fetch_array(MYSQLI_ASSOC);
                            if( is_act($tasktype) ) {
                                $task = "";
                                send_mail('acts@stichtingfamiliarforest.nl', 'Team Acts', "Automatische email: Ticketruil", "Hoi lieve team acts! <br> Even ter info: ".$row['firstname']. " ".$row['lastname']." heeft zijn of haar ticket verkocht en zal dus geen act meer doen.");
                            } else {
                                $sellername = $row['firstname']." ".$row['lastname'];
                                $result = $mysqli->query(sprintf("SELECT firstname, lastname FROM person WHERE email = '%s'",
                                    $mysqli->real_escape_string($buyer_email)));
                                if(!$result || $result->num_rows != 1 ) {
                                    email_error("Unable to send volunteers an email about sale of ticket for: ".$seller_email);
                                } else {
                                    $row = $result->fetch_array(MYSQLI_ASSOC);
                                    $buyername = $row['firstname']." ".$row['lastname'];
                                    send_mail("vrijwilligers@stichtingfamiliarforest.nl","Team Vrijwilligers","Automatische email: Ticketruil","Hoi lieverds! <br>Ter info: ".$sellername." heeft zijn of haar ticket verkocht aan: ".$buyername."<br>Taak nummer: ".$task." is automatisch overgezet.");
                                }
                            }
                        }
                    }
                }
            } else {
                email_error("Sold extra ticket");
            }
            $result = $mysqli->query("UPDATE $current_table SET `rafflecode` = '%s', `task` = '%s' WHERE `email` = '%s'",
                $mysqli->real_escape_string($code),
                $mysqli->real_escape_string($task),
                $mysqli->real_escape_string($buyer_email));
            if( !$result || $mysqli->affected_rows != 1 ) {
                email_error("Unable to set ticket code to: ".$code." for swap to: ".$buyer_email. "affected_rows = ".$mysqli->affected_rows);
            }

            $result = $mysqli->query("DELETE FROM `swap` WHERE `code` = '%s'",
                $mysqli->real_escape_string($code));
            if( !$result || $mysqli->affected_rows != 1 ) {
                email_error("Swap for code: ".$code." might not have been deleted. Affected rows: ".$mysqli->affected_rows);
            }
        }
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
    //$content .= "<p>En tot slot hebben we wat <a href='http://stichtingfamiliarforest.nl/info.html'>informatie</a> voor je klaar gezet</p>";

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
    $content .= "<p>Met een beetje een dubbel gevoel versturen we deze email omdat we jou ticket opnieuw hebben kunnen verkopen. We vinden het erg fijn dat het gelukt is om iemand anders blij te maken jou Familiar Voorjaar deelname maar we hadden natuurlijk erg graag ook jou erbij gehad in mei.</p>";
    $content .= "<p>We gaan er vanuit dat je vast hele goede redenen had om af te zien van ons weekendje weg en we hopen dat we bij de volgende editie (weer) van je aanwezigheid mogen genieten!<p>";
    $content .= "<p>Als je nog vragen, opmerkingen of andere zorgen hebt kun je een reply sturen op deze email.";

    $content .= get_email_footer();

    send_mail($row['email'], $fullname, "Familiar Voorjaar 2017 ticketruil bevestiging", $content);
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
    $content .= "<p>We hebben gezien dat jij dit jaar (weer) komt opbouwen, afbouwen of iets anders fantastisch doet bij Familiar Forest. Alvast super bedankt daarvoor! Naast lieve woorden ontvang je ook nog eens een half of heel ticket!</p>";
    $content .= "<p>We hebben opgemerkt dat je al een ticket had gekocht en hebben daarom een deel of volledig naar je terug overgemaakt. Normaal gesproken wordt dat na een werkdag verwerkt.<p>";
    $content .= "<p>Als je nog vragen, opmerkingen of andere zorgen hebt kun je een reply sturen naar deze email. Tot snel!";

    $content .= get_email_footer();

    send_mail($row['email'], $fullname, "Familiar Voorjaar 2017 Half ticket", $content);
    return true;
}

function isFullTicket($mysqli, $code) {
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
    return ($row['share'] == "FULL");
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
