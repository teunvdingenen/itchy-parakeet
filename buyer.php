<?php session_start();
include "initialize.php";
include "functions.php";

try {
    include "mollie_api_init.php";
} catch (Mollie_API_Exception $e) {
    addError("Er is iets fout gegaan met de iDeal link");
    _exit();
}

//$methods = ['', 'ideal', 'creditcard'];
//$method_names = ["", "IDeal (+€0,29)", "CreditCard (+€3,61)"];
$methods = ["",'ideal', 'mistercash', 'banktransfer'];
$method_names = ["Betaalmethode Selecteren","IDeal (+€0,29)","BanContact/Mister Cash (+€2,05)", "Overboeking(+€0.25)"];
$returnVal = "";
$email = $code = $method = $street = $city = $postal = $terms4 = "";


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
    if( !empty($_POST["method"]) ) {
        $method = test_input($_POST["method"]);
    } else {
        $method = "";
        addError('Je hebt geen betalingsmethode opgeven.');
    }
    if( !empty($_POST["city"]) ) {
        $city = test_input($_POST["city"]);
    } else {
        $city = "";
        addError("Je hebt je woonplaats niet opgegeven.");
    }
    if( !empty($_POST["postal"]) ) {
        $postal = test_input($_POST["postal"]);
    } else {
        $postal = "";
        addError("Je hebt je postcode niet opgegeven.");
    }
    if( !empty($_POST["street"]) ) {
        $street = test_input($_POST["street"]);
    } else {
        $street = "";
        addError('Je hebt je straat en huisnummer niet opgegeven.');
    }
    if( !empty($_POST["terms4"]) ) {
        $terms4 = test_input($_POST["terms4"]);
        if( $terms4 != 'J' ) {
            addError('Je hebt de voorwaarde niet geaccepteerd.');    
        }
    } else {
        $terms4 = "";
        addError('Je hebt de voorwaarde niet geaccepteerd.');
    }

    if( $returnVal == "" ) {
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        if( $mysqli->connect_errno ) {
            addError("Het lijkt erop dat de website kapot is, probeer het later nog eens!");
            $email_error("Database connectie is kapot: " . $mysqli->error);
        }

        $code = $mysqli->real_escape_string($code);
        $email = $mysqli->real_escape_string($email);
        if( !checkCode($mysqli, $code, $email)) {
            addError("We hebben helaas niet je code kunnen verifieren.");
        }
        if( $returnVal == "" ) {
            $paystatus = hasPaid($mysqli, $code);
            if( $paystatus == 1 ) {
                addError("Het lijkt erop dat je al betaald hebt. Als je twijfelt of alles wel goed is gegaan kun je mailen naar: " . $mailtolink);
            } else if( $paystatus == 2 ) {
                addError("Je hebt nog een betaling open staan. We kunnen op dit moment niet verifiëren of deze betaald is. Probeer het over een kwartiertje nog eens. Voor meer informatie kun je mailen naar: " . $mailtolink);
            }
        }
        if( $returnVal == "" ) {
            $query = sprintf("UPDATE person SET street = '%s', city = '%s', postal = '%s', terms4 = '%s' WHERE email = '%s'",
                $mysqli->real_escape_string($street),
                $mysqli->real_escape_string($city),
                $mysqli->real_escape_string($postal),
                $mysqli->real_escape_string($terms4),
                $mysqli->real_escape_string($email)
            );
            $sql_result = $mysqli->query($query);
            if( $sql_result === FALSE ) {
                addError("We hebben niet je gegevens kunnen aanpassen. Probeer het later nog eens of stuur een email naar: ".$mailtolink);
                email_error("Error updating person: " . $mysqli->error);
            } else if( $mysqli->affected_rows != 1 ) {
                email_error("More then one row effected updating person<br>".$query."<br>Affected rows: ".$mysqli->affected_rows);
            }
            if( $returnVal == "" ) {
                //all checks out!
                $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off',$_SERVER['HTTPS']) !== 0 ? "https" : "http";
                $hostname = $_SERVER['HTTP_HOST'];
                $path = dirname(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] :
                    $_SERVER['PHP_SELF']);

                $amount = 120;
                $raffle = $code;

                if( $method == 'ideal' ) {
                    $amount += 0.29;
                } else if( $method == 'creditcard') {
                    $amount += 0.25 + $amount * 0.028;
                } else if( $method == 'mistercash') {
                    $amount += 0.25 + $amount *0.015;
                } else if( $method == 'banktransfer' ) {
                    $amount += 0.25;
                }

                try {
                    $payment = $mollie->payments->create(array(
                      "amount" => $amount,
                      "method" => $method,
                      "description" => "FFF 2016 " . $code,
                      "redirectUrl" => "{$protocol}://{$hostname}{$path}/redirect.php?raffle={$raffle}",
                      "metadata" => array("raffle" => $raffle,)
                    ));
                } catch (Mollie_API_Exception $e) {
                    addError("Er is iets fout gegaan met het aanmaken van de betaling" . $e);
                }
                if( isInBuyers($mysqli, $code) ) {
                    if( !updatePaymentId($mysqli, $payment->id, $code)) {
                        email_error("Failed to update payment ID: ".$mysqli->error);
                    }
                } else {
                    if( !storePaymentId($mysqli, $payment->id, $code, $email) ) {
                        email_error("Error storing payment ID: ".$mysqli->error);
                    }
                }
            }
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

function isInBuyers($mysqli, $code) {
    $sqlresult = $mysqli->query(sprintf("SELECT b.id FROM buyer b WHERE b.code = '%s'",
        $mysqli->real_escape_string($code)));
    if( $sqlresult->num_rows == 1 ) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function storePaymentId($mysqli, $paymentid, $code, $email) {
    $sqlresult = $mysqli->query(sprintf("INSERT INTO `buyer` (`id`, `code`, `email`) VALUES ('%s','%s','%s')",
        $mysqli->real_escape_string($paymentid),
        $mysqli->real_escape_string($code),
        $mysqli->real_escape_string($email)));
    if( $sqlresult === FALSE) {
        return FALSE;
    } else {
        
    }
    return TRUE;
}

function updatePaymentId($mysqli, $paymentid, $code) {
    $sqlresult = $mysqli->query(sprintf("UPDATE buyer SET id = '%s' WHERE code = '%s'",
        $mysqli->real_escape_string($paymentid),
        $mysqli->real_escape_string($code)));
    if( $sqlresult === FALSE) {
        return FALSE;
    } else {
        
    }
    return TRUE;
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
    global $mollie;
    $sqlresult = $mysqli->query(sprintf("SELECT * FROM buyer 
        WHERE `code`='%s';",$code));
    if($sqlresult === FALSE) {
        //log error
        return 0;
    }
    if( $sqlresult->num_rows > 0 ) {
        $row = $sqlresult->fetch_array(MYSQLI_ASSOC);
        if( $mollie->payments->get($row['id'])->isPaid() ) {
            return 1;
        } else if( $mollie->payments->get($row['id'])->isOpen()) {
            return 2;
        }
    }
    return 0;
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
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="Teun van Dingenen">
        <link rel="icon" href="favicon.ico">

        <title>Code verzilveren</title>

        <!-- Bootstrap core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="css/main.css" rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->

        <div class="container">
            <div class="form-intro-text">
                <h1>Code verzilveren</h1>
                <p>Dit jaar kost deelname aan Familiar Forest 120 euro. Omdat niet alle betaalmethodes hetzelfde kosten hebben we ervoor gekozen de transactiekosten niet hierin te rekenen. Dat maakt het voor ons gemakkelijker om een betrouwbare begroting te maken. Kort geleden hebben we op onze Facebook een bericht geplaatst over <a href='https://www.facebook.com/events/591534081011159/permalink/601364646694769/?ref=1&amp;action_history=null'>de kosten</a></p>
                <p>Daarnaast moeten we ook jullie adresgegevens opslaan zodat jullie ook officieel mee kunnen als vrijwilligers bij Familiar Forest.</p>
                <p>Familiar Forest vindt plaats op 10 en 11 september 2016, dit formulier blijft toegankelijk tot 1 juni 2016.</p>
                <p>Het kan altijd zo zijn dat je onverhoopt toch niet kunt op 10 en 11 september. We raden daarom aan een annuleringsverzekering af te sluiten bij je reisverzekering</p>
                <p>Ben je wel ingelood maar je code vergeten? Ga dan naar <a href="codevergeten">deze pagina</a> om je code opnieuw op te vragen.</p>
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
                    <label for="street" class="col-sm-2 form-control-label">Straat &amp; Huisnummer</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="street" placeholder="Straat en Huisnummer" value="<?php echo $street;?>" name="street">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="postal" class="col-sm-2 form-control-label">Postcode</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="postal" placeholder="Postcode" value="<?php echo $postal;?>" name="postal">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="city" class="col-sm-2 form-control-label">Stad</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="city" placeholder="Stad" value="<?php echo $city;?>" name="city">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="method" class="col-sm-2 form-control-label">Selecteer betalingsmethode:</label>
                    <div class="col-sm-10">
                        <div class="alert alert-info" role="alert">
                            Op dit moment kun je betalen met iDeal, Bancontact/Mister Cash en doormiddel van een overboeking. We vermoeden dat we hiermee het grootste deel van de bezoekers van dienst kunnen zijn.
                    We hopen ook binnenkort creditcard betalingen te kunnen ontvangen maar weten nog niet wanneer we hiermee kunnen beginnen. Mocht je niet gebruik kunnen maken van de beschikbare betaalmethodes mail dan naar: <?=$mailtolink?>
                        </div>
                        <select class="form-control" name="method">
                            <?php
                                $i = 0;
                                foreach ($methods as $my_method) {
                                    echo '<option name="method" value=' . $my_method . '>' . $method_names[$i++] . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 form-control-label" for="terms4">Vrijwilliger</label>
                    
                    <div class="col-sm-10">
                        <div class="alert alert-warning">Deelname aan Familiar Forest betekent dat je jezelf aanmeldt als vrijwilliger bij Stichting Familiar Forest. Tijdens het weekend zul je nader te bepalen werkzaamheden verrichten en zal er een werkbegeleider en aanspreekpunt vanuit de organisatie worden aangewezen.</div>
                        <div class="checkbox">
                            <label>
                                <input class="checkbox" type="checkbox" id="terms4" name="terms4" value="J">
                                Ik ga akkoord met deze voorwaarde
                            </label>
                        </div>
                        <label for="terms4" class="error" style="display:none;"></label>
                    </div>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Naar betalen</button>
            </form>
        </div>
        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
        <script src="js/vendor/bootstrap.min.js"></script>
        <script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
        <scirpt src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/localization/messages_nl.js"></script>

        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>
        <script src="js/buyer.js"></script>
        </body>
</html>
