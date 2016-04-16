<?php session_start();

include "initialize.php";
include "functions.php";
try {
    include "mollie_api_init.php";
} catch (Mollie_API_Exception $e) {
    addError("Er is iets fout gegaan met de iDeal link");
    _exit();
}

$returnVal = "";
$email = $code = $issuer = "";

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
    if( !empty($_POST["issuer"]) ) {
        $issuer = test_input($_POST["issuer"]);
    } else {
        $issuer = "";
    }

    if( $returnVal == "" ) {
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        if( $mysqli->connect_errno ) {
            addError("Het lijkt erop dat de website kapot is, probeer het later nog eens!");
            //TODO email error
        }

        $code = $mysqli->real_escape_string($code);
        $email = $mysqli->real_escape_string($email);
        if( !checkCode($mysqli, $code, $email)) {
            addError("We hebben helaas niet je code kunnen verifieren.");
        }
        if( $returnVal == "" && hasPaid($mysqli, $code)) {
            addError("Het lijkt erop dat je al betaald hebt. Het word nagekeken en je krijgt snel even bericht hierover!");
        }
        if( $returnVal == "" ) {
            //all checks out!
            $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off',$_SERVER['HTTPS']) !== 0 ? "https" : "http";
            $hostname = $_SERVER['HTTP_HOST'];
            $path = dirname(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] :
                $_SERVER['PHP_SELF']);

            $amount = 100;
            $raffle = $code;

            try {
                $payment = $mollie->payments->create(array(
                  "amount" => $amount,
                  "method" => Mollie_API_Object_Method::IDEAL,
                  "description" => "FFF 2016 " . $code,
                  "redirectUrl" => "{$protocol}://{$hostname}{$path}/redirect.php?raffle={$raffle}",
                  "metadata" => array("raffle" => $raffle,),
                  "issuer" => !empty($issuer) ? $issuer : NULL
                ));
            } catch (Mollie_API_Exception $e) {
                addError("Er is iets fout gegaan met het aanmaken van de betaling" . $e);
            }
            storePaymentId($mysqli, $payment->id, $code, $email);
        } 
        $mysqli->close();
    }
    if( $returnVal == "") {
        //sendoff to payment
        header('Location: ' . $payment->getPaymentUrl());
    } else {
        //try again..
        $returnVal .= "</ul>";
    }
} //End POST

function storePaymentId($mysqli, $paymentid, $code, $email) {
    $sqlresult = $mysqli->query(sprintf("INSERT INTO `buyer` (`id`, `code`, `email`) VALUES ('%s','%s','%s')",
        $mysqli->real_escape_string($paymentid),
        $mysqli->real_escape_string($code),
        $mysqli->real_escape_string($email)));
    if( $sqlresult === FALSE) {
        return FALSE;
        //log error: $sqlresult->error
    } else {
        
    }
    return true;
}

function checkCode($mysqli, $code, $email) {
    $sqlresult = $mysqli->query(sprintf("SELECT code FROM raffle WHERE email = '%s'", $email));
    if( $sqlresult === FALSE) {
        //log error
        return FALSE;
    }
    if( $sqlresult->num_rows != 1 ) {
        //log error
        return false;
    }
    $row = $sqlresult->fetch_array(MYSQLI_ASSOC);
    if( $row['code'] != $code ) {
        return false;
    }
    return true;
}

function hasPaid($mysqli, $code) {
    $sqlresult = $mysqli->query(sprintf("SELECT * FROM buyer 
        WHERE `code`='%s' AND `complete`=1;",$code));
    if($sqlresult === FALSE) {
        //log error
        return FALSE;
    }
    if( $sqlresult->num_rows > 0 ) {
        //TODO IMPORTANT! VERIFY MANUAL through mollie?
        return TRUE;
    }
    return FALSE;
}

function addError($value) {
    global $returnVal;
    $returnVal .= "<div class='alert alert-danger'>".$value."</div>";
}

?>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Familiar Forest Festival Inschrijfformulier</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <!-- Place favicon.ico in the root directory -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" type="text/css" media="all"
            href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/smoothness/jquery-ui.css"/>
        <script src="js/vendor/modernizr-2.8.3.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
        <scirpt src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/localization/messages_nl.js"></script>
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>


        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='https://www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-XXXXX-X','auto');ga('send','pageview');
        </script>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->

        <div class="container">
            <h1>Kaartje Kopen</h1>
            <div>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ut ligula quis lacus consectetur tempus. Integer pretium quam vel nunc aliquet fringilla. Maecenas enim nulla, faucibus ut tincidunt id, auctor at orci. Praesent faucibus tellus ipsum, nec varius erat consectetur at. Etiam ac ultricies ex, a gravida quam. Suspendisse fringilla congue massa a cursus. Nunc condimentum mauris id erat tincidunt laoreet. Sed maximus tortor id mi vestibulum pulvinar. Vestibulum ultricies
                erat sit amet posuere euismod. Curabitur orci mauris, vehicula et dolor at, egestas luctus nunc. Sed non egestas massa. Curabitur eget bibendum arcu. Aliquam erat volutpat. Fusce placerat lacus a dapibus accumsan. Cras vitae interdum metus. Phasellus neque sem, mattis et imperdiet sed, eleifend vel lorem.</p>
            </div>
            <form id="buyer-form" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">

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
                    <label for="issuer" class="col-sm-2 form-control-label">Selecteer je bank:</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="issuer">
                            <?php
                                $issuers = $mollie->issuers->all();
                                foreach ($issuers as $issuer) {
                                    if($issuer->method == Mollie_API_Object_Method::IDEAL) {
                                        echo '<option value=' . htmlspecialchars($issuer->id) . '>' . htmlspecialchars($issuer->name) . '</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Naar betalen</button>
            </form>
        </div>
    </body>
</html>
