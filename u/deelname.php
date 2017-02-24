<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_PARTICIPANT) != PERMISSION_PARTICIPANT ) {
    header('Location: oops');
}

if( strtotime('now') > strtotime('2017-03-09 00:00') ) {
    header('Location: voorjaar');
}
try {
    include "mollie_api_init.php";
} catch (Mollie_API_Exception $e) {
    addError("Er is iets fout gegaan met de iDeal link");
    _exit();
}

$methods = ["",'ideal', 'mistercash', 'creditcard'];
$method_names = ["Betaalmethode Selecteren","IDeal (+€0,29)","BanContact/Mister Cash (+€2,05)", "CreditCard (+€3,61)"];
$returnVal = "";
$disp_amount = $code = $method = $street = $city = $postal = $terms4 = $share = "";


$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    addError("Het lijkt erop dat de website kapot is, probeer het later nog eens!");
    $email_error("Database connectie is kapot: " . $mysqli->error);
}

$result = $mysqli->query(sprintf("SELECT rafflecode, valid FROM $current_table WHERE `email` = '%s'",
        $mysqli->real_escape_string($user_email)));

if( !$result ) {
    addError("Het lijkt erop dat de website kapot is, probeer het later nog eens!");
    $email_error("Broken query in deelname: " . $mysqli->error);
} else {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    if( $row['rafflecode'] == "" || $row['valid'] != 1 ) {
        header("Location: oops");
    }
}

$result = $mysqli->query(sprintf("SELECT city, street, postal, rafflecode, share from $current_table s join person p on s.email = p.email WHERE p.email = '%s'",
    $mysqli->real_escape_string($user_email)));
if(!$result) {
    addError("We konden niet je gegevens ophalen, ververs de pagina of stuur een email naar: ".$mailtolink);
} else {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $city = $row['city'];
    $street = $row['street'];
    $postal = $row['postal'];
    $code = $row['rafflecode'];
    $share = $row['share'];
}

if( $share == "HALF" ) {
    $disp_amount = "60,00";
} else if( $share == "FREE" ) {
    $disp_amount = "0,00";
} else {
    $disp_amount = "120,00";
}

if( $_SERVER["REQUEST_METHOD"] == "POST") {
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
        if( $returnVal == "" ) {
            $paystatus = hasPaid($mysqli, $user_email);
            if( $paystatus == 1 ) {
                addError("Het lijkt erop dat je al betaald hebt. Als je twijfelt of alles wel goed is gegaan kun je mailen naar: " . $mailtolink);
            } else if( $paystatus == 2 ) {
                addError("Je hebt nog een betaling open staan. We kunnen op dit moment niet verifiëren of deze betaald is. Probeer het over een kwartiertje nog eens. Voor meer informatie kun je mailen naar: " . $mailtolink);
            }
        }
        if( $returnVal == "" ) {
            $query = sprintf("UPDATE person SET street = '%s', city = '%s', postal = '%s' WHERE email = '%s'",
                $mysqli->real_escape_string($street),
                $mysqli->real_escape_string($city),
                $mysqli->real_escape_string($postal),
                $mysqli->real_escape_string($user_email)
            );
            $sql_result = $mysqli->query($query);
            if( $sql_result === FALSE ) {
                addError("We hebben niet je gegevens kunnen aanpassen. Probeer het later nog eens of stuur een email naar: ".$mailtolink);
                email_error("Error updating person: " . $mysqli->error);
            } else if( $mysqli->affected_rows != 1 ) {
                //email_error("More then one row effected updating person<br>".$query."<br>Affected rows: ".$mysqli->affected_rows);
            }
            $query = sprintf("UPDATE $current_table SET terms4 = '%s' WHERE email = '%s'",
                $mysqli->real_escape_string($terms4),
                $mysqli->real_escape_string($user_email)
            );
            $sql_result = $mysqli->query($query);
            if( $sql_result === FALSE ) {
                addError("We hebben niet je gegevens kunnen aanpassen. Probeer het later nog eens of stuur een email naar: ".$mailtolink);
                email_error("Error updating person: " . $mysqli->error);
            } else if( $mysqli->affected_rows != 1 ) {
                //email_error("More then one row effected updating person<br>".$query."<br>Affected rows: ".$mysqli->affected_rows);
            }
            if( $returnVal == "" ) {
                //all checks out!
                $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off',$_SERVER['HTTPS']) !== 0 ? "https" : "http";
                $hostname = $_SERVER['HTTP_HOST'];
                $path = dirname(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] :
                    $_SERVER['PHP_SELF']);
                $amount = 120;
                if( $share == "HALF" ) {
                    $amount = 60;
                }
                
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
                      "description" => "FV 2017 " . $code,
                      "redirectUrl" => "{$protocol}://{$hostname}/redirect?raffle={$raffle}",
                      "metadata" => array("raffle" => $raffle,)
                    ));
                } catch (Mollie_API_Exception $e) {
                    addError("Er is iets fout gegaan met het aanmaken van de betaling");
                }
                if( !updatePaymentId($mysqli, $payment->id, $user_email)) {
                    addError("Er is iets fout gegaan met het aanmaken van de betaling. Je kunt het nogmaals proberen of even mailen naar: ".$mailtolink);
                    email_error("Failed to update payment ID: ".$mysqli->error);
                }
            }
        }
    }
    $mysqli->close();
    if( $returnVal == "") {
        //sendoff to payment
        header('Location: ' . $payment->getPaymentUrl());
    } else {
        //try again..
        $returnVal .= "</ul>";
    }
} //End POST

function updatePaymentId($mysqli, $paymentid, $email) {
    global $current_table;
    $sqlresult = $mysqli->query(sprintf("UPDATE $current_table SET transactionid = '%s' WHERE email = '%s'",
        $mysqli->real_escape_string($paymentid),
        $mysqli->real_escape_string($email)));
    if( $sqlresult === FALSE) {
        return FALSE;
    } 
    return TRUE;
}

function hasPaid($mysqli, $email) {
    global $mollie, $current_table;
    $sqlresult = $mysqli->query(sprintf("SELECT transactionid FROM $current_table 
        WHERE `email`='%s';",$mysqli->real_escape_string($email)));
    if($sqlresult === FALSE) {
        //log error
        return 0;
    }
    if( $sqlresult->num_rows > 0 ) {
        $row = $sqlresult->fetch_array(MYSQLI_ASSOC);
        if( $row['transactionid'] == '' ) {
            return 0;
        } else if( $mollie->payments->get($row['transactionid'])->isPaid() ) {
            return 1;
        } else if( $mollie->payments->get($row['transactionid'])->isOpen()) {
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
                        <div class="form-intro-text">
                            <h1>Deelnemen Familiar voorjaar</h1>
                            <p>Familiar Voorjaar vindt plaats op 5, 6 en 7 mei 2017. Je kan via dit formulier een kaartje kopen tot en met 9 maart 2017.</p>
                            <p>Deelname aan Familiar Voorjaar kost 120 euro. We hebben ervoor gekozen om de transactiekosten niet hierin te verwerken, omdat niet alle betaalmethodes dezelfde kosten hebben. Hierdoor is het voor ons makkelijker om een betrouwbare begroting te maken.</p>
                            <p>Het kan altijd zo zijn dat je onverhoopt toch niet meer naar Familiar Voorjaar kan komen. We raden je daarom aan een annuleringsverzekering af te sluiten bij je reisverzekering.</p>
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
                                    <input class="form-control" type="email" id="email" placeholder="Email" value="<?php echo $user_email;?>" name="email" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="code" class="col-sm-2 form-control-label">Code</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="text" id="code" placeholder="Code" value="<?php echo $code;?>" name="code" disabled>
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
                                    <select id="transactionmethod" class="form-control" name="method">
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
                                    <div class="alert alert-warning">Deelname aan Familiar Voorjaar betekent dat je jezelf aanmeldt als vrijwilliger bij Stichting Familiar Forest. Tijdens het weekend zal je nader te bepalen werkzaamheden uit gaan voeren, een vrijwilligersshift. Vanuit de organisatie wordt een werkbegeleider en aanspreekpunt aangewezen.</div>
                                    <div class="checkbox">
                                        <label>
                                            <input class="checkbox" type="checkbox" id="terms4" name="terms4" value="J">
                                            Ik ga akkoord met deze voorwaarde
                                        </label>
                                    </div>
                                    <label for="terms4" class="error" style="display:none;"></label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 form-control-label">Kosten</label> 
                                <div class="col-sm-10">
                                    <div class="alert alert-success">
                                        De onderstaande bedragen zijn op basis van onze begroting voor Familiar Voorjaar. De kans bestaat dat uitgaven afwijken van de hieronder genoemde bedragen.
                                    </div>
                                    <table class="table table-condensed">
                                        <tbody style='text-align:right'>
                                            <tr>
                                                <th>
                                                    <span class="btn data" href="#" data-content="Hieronder vallen de kosten voor de locatie zoals het gebruik van een bed, de douches, wc's en afvalverwerking" rel="popover" data-placement="right" data-original-title="Locatie" data-trigger="hover">
                                                        <i class="fa fa-info"></i>
                                                    </span>
                                                    Locatie</th>
                                                <td>30,00</td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    <span class="btn data" href="#" data-content="Dit zijn de kosten voor ontbijt, lunch, avondeten en gezonde snacks zoals fruit tussendoor" rel="popover" data-placement="right" data-original-title="Eten" data-trigger="hover">
                                                        <i class="fa fa-info"></i>
                                                    </span>
                                                    Eten</th>
                                                <td>21,50</td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    <span class="btn data" href="#" data-content="Hiermee wordt naast de busreis, ook al het transport betaald van bijvoorbeeld decoratie of techniek." rel="popover" data-placement="right" data-original-title="Transport" data-trigger="hover">
                                                        <i class="fa fa-info"></i>
                                                    </span>
                                                    Transport</th>
                                                <td>19,50</td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    <span class="btn data" href="#" data-content="Dit wordt besteed aan beveiliging en EHBO." rel="popover" data-placement="right" data-original-title="Beveiliging" data-trigger="hover">
                                                        <i class="fa fa-info"></i>
                                                    </span>
                                                    Veiligheid</th>
                                                <td>14,00</td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    <span class="btn data" href="#" data-content="Van dit bedrag wordt het licht en geluid betaald in de verschillende zalen of buitenruimtes" rel="popover" data-placement="right" data-original-title="Techniek" data-trigger="hover">
                                                        <i class="fa fa-info"></i>
                                                    </span>
                                                    Techniek</th>
                                                <td>13,00</td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    <span class="btn data" href="#" data-content="Dit zijn doorlopende kosten. Denk daarbij aan verzekeringen, atelierhuur, keuringen en andere onverziene kosten" rel="popover" data-placement="right" data-original-title="Doorlopend" data-trigger="hover">
                                                        <i class="fa fa-info"></i>
                                                    </span>
                                                    Doorlopend</th>
                                                <td>10,00</td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    <span class="btn data" href="#" data-content="Voor dit bedrag zorgen we dat alles er leuk uitziet. Hiervan wordt bijvoorbeeld verf, stof, papier, hout, planten en nog veel meer gekocht." rel="popover" data-placement="right" data-original-title="Decoraties" data-trigger="hover">
                                                        <i class="fa fa-info"></i>
                                                    </span>
                                                    Decoraties</th>
                                                <td>8,00</td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    <span class="btn data" href="#" data-content="Van dit bedrag geven we DJ's en muzikanten een kleine onkoste vergoeding en/of reiskosten vergoeding" rel="popover" data-placement="right" data-original-title="Muziek" data-trigger="hover">
                                                        <i class="fa fa-info"></i>
                                                    </span>
                                                    Muziek</th>
                                                <td>4,00</td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    <span class="btn data" href="#" data-content="Dit geld gaat naar het bedrijf dat de betaling verwerkt en je bank of creditcard maatschappij" rel="popover" data-placement="right" data-original-title="Transactie kosten" data-trigger="hover">
                                                        <i class="fa fa-info"></i>
                                                    </span>
                                                    Transactie kosten</th>
                                                <td class='transaction'></td>
                                            </tr>
                                            <?php
                                                if( $share == "HALF" ) {
                                                    echo "<tr>";
                                                    echo '<th>
                                                        <span class="btn data" href="#" data-content="Omdat je veel meer tijd investeert in Familiar dan anderen krijg je korting!" rel="popover" data-placement="right" data-original-title="Korting" data-trigger="hover">
                                                            <i class="fa fa-info"></i>
                                                        </span> Korting</th>';
                                                    echo "<td>-60,00</td>";
                                                    echo "</tr>";
                                                } else if( $share == "FREE" ) {
                                                    echo "<tr>";
                                                    echo '<th>
                                                        <span class="btn data" href="#" data-content="Omdat je veel meer tijd investeert in Familiar dan anderen krijg je korting!" rel="popover" data-placement="right" data-original-title="Korting" data-trigger="hover">
                                                            <i class="fa fa-info"></i>
                                                        </span> Korting</th>';
                                                    echo "<td>-120,00</td>";
                                                    echo "</tr>";
                                                }
                                            ?>
                                            <tr class='lead'>
                                                <th>Totaal</th>
                                                <td class='total'><?=$disp_amount ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php
                                if( $share != "FREE" ) {
                                    echo "<button class='btn btn-lg btn-primary btn-block' type='submit'>Naar betalen</button>";
                                }
                            ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id='hidden-total' class='hidden'><?=$disp_amount?></div>
        <?php include("form-js.html"); ?>
        <script src="js/deelname.js"></script>
        </body>
</html>
