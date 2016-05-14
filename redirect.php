<?php session_start(); 
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
    $sqlquery = sprintf("SELECT p.firstname FROM person p join buyer b on b.email = p.email
        WHERE  b.code = '%s'", $mysqli->real_escape_string($code));
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
    $sqlquery = sprintf("SELECT b.id FROM buyer b WHERE b.code = '%s'",
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
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>Familiar Forest 2016</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <!-- <link href="../../assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet"> -->

    <!-- Custom styles for this template -->
    <link href="css/main.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">

      <div class="default-text">
        <div></div>
        <p></p> 

        <h1>Familiar Forest 2016</h1>
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
