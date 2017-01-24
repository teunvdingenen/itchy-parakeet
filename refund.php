<?php session_start();
include "initialize.php";
include "functions.php";

if( strtotime('now') > strtotime('2016-08-06 00:00') ) {
    header('Location: deelnemen');
}

try {
    include "mollie_api_init.php";
} catch (Mollie_API_Exception $e) {
    addError("Er is iets fout gegaan met de iDeal link");
    _exit();
}

$returnVal = "";
$email = $code = $transaction_id = "";


if( $_SERVER["REQUEST_METHOD"] == "POST") {
    if( !empty($_POST["email"]) ) {
        $email = test_input($_POST["email"]);
    } else {
        $email = "";
        addError("Je hebt je email adres niet opgegeven.");
    }
    if( !empty($_POST["code"]) ) {
        $code = test_input($_POST["code"]);
    } else {
        $code = "";
        addError("Je hebt je code niet opgegeven.");
    }
    if( !empty($_POST["transaction_id"]) ) {
        $transaction_id = test_input($_POST["transaction_id"]);
    } else {
        $transaction_id = "";
        addError("Je hebt je transactie informatie niet opgegeven.");
    }

    if( $returnVal == "" ) {
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        if( $mysqli->connect_errno ) {
            addError("Het lijkt erop dat de website kapot is, probeer het later nog eens!");
            $email_error("Database connectie is kapot: " . $mysqli->error);
        }

        $code = $mysqli->real_escape_string($code);
        $email = $mysqli->real_escape_string($email);
        $transaction_id = $mysqli->real_escape_string($transaction_id);
        if( !checkCode($mollie, $mysqli, $code, $transaction_id, $email)) {
            addError("We hebben niet je gegevens kunnen verifiëren. Voor meer informatie kun je mailen naar: ".$mailtolink);
        }
        if( $returnVal == "" ) {
            //all checks out!

            $amount = 119.81;

            try {
                $payment = $mollie->payments->get($transaction_id);
                $refund = $mollie->payments->refund($payment, $amount);
            } catch (Mollie_API_Exception $e) {
                addError("Er is iets fout gegaan met het ophalen van je betaling. Stuur voor meer infomatie een email naar: ".$mailtolink);
            }
            $sqlresult = $mysqli->query(sprintf("UPDATE $current_table set complete = 2 where email = '%s'",$email));
            if( !$sqlresult ) {
                email_error("Buyer.complete niet geupdate naar 2 voor email: ".$email." sqlerror: ".$mysqli->error);
            }
        } 
        $mysqli->close();
    }
    if( $returnVal == "") {
        $returnVal .= "<div class='alert alert-success'>We hebben je verzoek ontvangen en verwerkt. Zodra je betaling is teruggestort ontvang je een bevestiging van via mail. Heb je nog vragen? Stuur dan een email naar: ".$mailtolink."</div>";
    } else {
        //try again..
        $returnVal .= "</ul>";
    }
} //End POST

function checkCode($mollie, $mysqli, $code, $transaction_id, $email) {
    $sqlresult = $mysqli->query(sprintf("SELECT * FROM $current_table WHERE email = '%s'", 
        $mysqli->real_escape_string($email)));
    if( $sqlresult === FALSE) {
        //log error
        return FALSE;
    }
    if( $sqlresult->num_rows != 1 ) {
        //log error
        return false;
    }
    $row = $sqlresult->fetch_array(MYSQLI_ASSOC);
    if( $row['rafflecode'] != $code ) {
        return false;
    } else if( $row['transactionid'] != $transaction_id) { 
        return false;
    } else if( $row['complete'] != 1 ) {
        return false;
    }
    if( !$mollie->payments->get($row['id'])->isPaid() ) {
        return false;
    }
    return true;
}

function addError($value) {
    global $returnVal;
    $returnVal .= "<div class='alert alert-danger'>".$value."</div>";
}

?>
<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Familiar Forest Refund aanvragen</title>
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
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->

        <div class="container">
            <div class="form-intro-text">
                <h1>Refund</h1>
                <p>Voorgaande jaren is het niet mogelijk geweest voor deelnemers om van hun ticket af te komen. Toch merken we dat daar wel veel vraag naar is en willen dit jaar dus die mogelijkheid aanbieden. Als je een ticket gekocht hebt heb je tot en met 5 augustus de mogelijk om je ticket terug te verkopen aan ons. We zullen dan proberen deze weer te verkopen aan iemand die daar interesse in heeft.</p>
                <p>Om je ticket terug te verkopen hebben we de code nodig waarmee je bent ingeloot en je transactie nummer. Deze heb je beide ontvangen in een email op het moment dat je betaald hebt voor deelname.</p>
                <p>Het terugstorten van je ticketgeld kost €0,19. Je ontvangt dus €119,81 terug op je bankrekening.
                <p><strong>Let wel op: Het is niet mogelijk om de refund terug te draaien. Weet dus wel zeker dat je niet meer naar Familiar Forest wilt komen dit jaar.</strong></p>
            </div>
            <?php
                if( $returnVal != "" ) {
                    echo $returnVal;
                }
            ?>
            <form id="buyer-form" class="form" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">

                <div class="form-group row">
                    <label for="email" class="col-sm-2 form-control-label">Email</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="email" id="email" placeholder="Email" value="<?php echo $email;?>" name="email">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="code" class="col-sm-2 form-control-label">Code</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="code" placeholder="Code" value="<?php echo $code;?>" name="code">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="street" class="col-sm-2 form-control-label">Transactie nummer</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="transaction_id" placeholder="Transactie nummer" value="<?php echo $transaction_id;?>" name="transaction_id">
                    </div>
                </div>
                <p><a id="togglebutton" class="btn btn-info btn-lg btn-block" data-toggle="collapse" data-target="#refund-panel">Refund aanvragen <i class="glyphicon glyphicon-chevron-right"></i></a></p>
                <div class="row">
                    <div id="refund-panel" class="collapse refund-panel">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <button class="btn btn-lg btn-primary btn-block" type="submit"><i class="glyphicon glyphicon-ok"></i> Ik weet het zeker en wil doorgaan</button>
                            </div>
                        </div>
                    </div>
                </div>
                
            </form>
        </div>
        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
        <script src="js/vendor/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
        <scirpt src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/localization/messages_nl.js"></script>

        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>
        <script src="js/refund.js"></script>
        <script>
        $('#togglebutton').on('click', function(){
            $(this).children().closest('.glyphicon').toggleClass('glyphicon-chevron-right glyphicon-chevron-down');
        });
        </script>
        </body>
</html>
