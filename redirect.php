<?php session_start(); 

// TODO Login stuff, load into frame

include "initialize.php";
$message = "";
try {
    include "mollie_api_init.php";

    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    $code = $_GET["raffle"];
    $firstname = get_name($mysqli, $code);

    $paid = get_paid($mysqli, $code);

    if( $paid === FALSE ) {
        email_error("redirect.php: paid returned FALSE for: ".$firstname. " with code: ". $code);
        $message = "<p>Er is een fout opgetreden in het betalingsysteem. <br> Voor mail informatie of geruststellingen kun je mailen naar: ".$mailtolink.".</p>";
    } else if( $paid == 0 ) {
        $message = "<p>Het lijkt erop dat de betaling niet gelukt is of je hebt deze afgebroken.<p>";
        $message .= "<p>Als je per ongeluk iets fout gedaan hebt kun het je nogmaals proberen door naar het <a href='buyer'>betalingscherm</a> te gaan.</p>";
        $message .= "<p>Bij zorgen, voor vragen of je wilt iets anders kwijt, dan kun je altijd mailen naar: ". $mailtolink.".</p>";
    } else {
        $message = "<p>De betaling is helemaal rond! We hebben erg veel zin om met jou deze zomer Nieuw Babylon te gaan ontdekken.</p>";
        $message .= "<p>Ter bevestiging ontvang je ook nog een email met wat aanvullende gegevens.</p>";
        $message .= "<p>Als je zorgen, vragen of je wilt iets anders kwijt wilt kun je altijd mailen naar: ". $mailtolink.".</p>";
    }
    $message .= "<p>De high fives zijn gratis, de knuffels oprecht en de liefde oneindig,<br><br>Familiar Forest</p>";
    $mysqli->close();
} catch (Mollie_API_Exception $e) {
    //email error
    $message = "API call failed: " . htmlspecialchars($e->getMessage());
}

function email_error($message) {
    send_mail('info@stichtingfamiliarforest.nl', 'Web Familiar Forest', 'Found ERROR!', $message);
            
}

function get_name($mysqli, $code) {
    $sqlquery = sprintf("SELECT p.firstname FROM person p join $current_table s on p.email = s.email
        WHERE  s.rafflecode = '%s'", $mysqli->real_escape_string($code));
    $sqlresult = $mysqli->query($sqlquery);
    if( $sqlresult === FALSE) {
        return FALSE;
        //log error
    }
    if( $sqlresult->num_rows != 1 ) {
        return false;
    }
    $row = $sqlresult->fetch_array(MYSQLI_ASSOC);
    return $row['firstname'];
}

function get_paid($mysqli, $code) {
    global $mollie;
    $sqlquery = sprintf("SELECT s.transactionid FROM $current_table s WHERE s.rafflecode = '%s'",
        $mysqli->real_escape_string($code));
    $sqlresult = $mysqli->query($sqlquery);
    if( $sqlresult === FALSE) {
        return FALSE;
        //log error
    }
    if( $sqlresult->num_rows != 1 ) {
        return FALSE;
    }
    $row = $sqlresult->fetch_array(MYSQLI_ASSOC);
    $id = $row["id"];
    if( $mollie->payments->get($id)->isPaid()) {
        return 1;
    }
    return 0;
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Familiar Forest Account Activeren</title>
        <meta name="description" content="">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <link rel="icon" href="favicon.ico">
        <!-- Place favicon.ico in the root directory -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" type="text/css" media="all"
            href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/smoothness/jquery-ui.css"/>        
    </head>
    <body>

    <div class="container">
      <div class="default-text">
        <div></div>
        <p></p> 

        <h2>Familiar Forest 2016</h2>
        <p class="lead">
            Lieve <? echo $firstname ?>,
        </p>
        <? echo $message ?>
        </div>
      </div>

    </div><!-- /.container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.3.min.js"><\/script>')</script>
    <script src="js/vendor/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
  </body>
</html>
