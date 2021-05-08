<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_PARTICIPANT) != PERMISSION_PARTICIPANT ) {
    header('Location: oops');
}

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    addError("Het lijkt erop dat de website kapot is, probeer het later nog eens!");
    $email_error("Database connectie is kapot: " . $mysqli->error);
    exit;
}

try {
    include "mollie_api_init.php";
} catch (Mollie_API_Exception $e) {
    addError("Er is iets fout gegaan met de iDeal link");
    _exit();
}

$methods = ["",'ideal', 'mistercash', 'creditcard'];
$method_names = ["Betaalmethode Selecteren","IDeal","BanContact/Mister Cash", "CreditCard"];
$returnVal = "";
$method = "";
$book_price = 32.5;
$send = "";
$number = 1;
$disp_amount = "32.50";

$city = $street = $postal = "";

$result = $mysqli->query(sprintf("SELECT city, street, postal from person WHERE email = '%s'",
    $mysqli->real_escape_string($user_email)));
if(!$result) {
    addError("We konden niet je gegevens ophalen, ververs de pagina of stuur een email naar: ".$mailtolink);
} else {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $city = $row['city'];
    $street = $row['street'];
    $postal = $row['postal'];
}

$current_purchaes = hasPaid($mysqli, $user_email);

if( $_SERVER["REQUEST_METHOD"] == "POST") {
    if( !empty($_POST["method"]) ) {
        $method = test_input($_POST["method"]);
    } else {
        $method = "";
        addError('Je hebt geen betalingsmethode opgeven.');
    }
    if( !empty($_POST["number"])) {
      $number = test_input($_POST["number"]);
      if( $number < 0 ) {
        addError("Haha, grapjas");
      }
    } else {
      $number = 1;
      addError('Je hebt geen aantal opgegeven.');
    }
    if( !empty($_POST["send"]) ) {
        $send = test_input($_POST["send"]);
    } else {
        $send = "";
        addError('Je hebt geen verzendmethode opgegeven.');
    }
    if( $returnVal == "" ) {
          $sql_result = $mysqli->query(sprintf("INSERT INTO buybook (`email`, `number`, `send`) VALUES ('%s', %s, '%s')",
            $mysqli->real_escape_string($user_email),
            $mysqli->real_escape_string($number),
            $mysqli->real_escape_string($send)
          ));
          if( $sql_result === FALSE ) {
              addError("We hebben niet je bestelling kunnen opslaan. Probeer het later nog eens of stuur een email naar: ".$mailtolink);
          }
          $order_id = $mysqli->insert_id;
          if( $returnVal == "" ) {
              //all checks out!
              $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off',$_SERVER['HTTPS']) !== 0 ? "https" : "http";
              $hostname = $_SERVER['HTTP_HOST'];
              $path = dirname(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] :
                  $_SERVER['PHP_SELF']);
              $amount = $number * $book_price;
              if( $send == 'send') {
                $amount += 6.75;
              }
              if( $method == 'ideal' ) {
                  $amount += 0.29;
              } else if( $method == 'creditcard') {
                  $amount += 0.25 + $amount * 0.028;
              } else if( $method == 'mistercash') {
                  $amount += 0.39;
              }
              try {
                  $payment = $mollie->payments->create(array(
                    "amount" => $amount,
                    "method" => $method,
                    "description" => "BOEK " . $order_id,
                    "redirectUrl" => "{$protocol}://{$hostname}/bookredirect?book={$order_id}"
                  ));
              } catch (Mollie_API_Exception $e) {
                  addError("Er is iets fout gegaan met het aanmaken van de betaling");
              }
              if( !updatePaymentId($mysqli, $payment->id, $order_id)) {
                  addError("Er is iets fout gegaan met het aanmaken van de betaling. Je kunt het nogmaals proberen of even mailen naar: ".$mailtolink);
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

function updatePaymentId($mysqli, $paymentid, $order_id) {
    $sqlresult = $mysqli->query(sprintf("UPDATE `buybook` SET `transactionid` = '%s' WHERE `id` = %s",
        $mysqli->real_escape_string($paymentid),
        $mysqli->real_escape_string($order_id)));
    if( $sqlresult === FALSE) {
        return false;
    }
    return true;
}

function hasPaid($mysqli, $email) {
    global $mollie;
    $sqlresult = $mysqli->query(sprintf("SELECT sum(number) as total FROM buybook
        WHERE `email`='%s' and `complete`= 1;",$mysqli->real_escape_string($email)));
    if($sqlresult === FALSE) {
        //log error
        return 0;
    }
    return $sqlresult->fetch_array(MYSQLI_ASSOC)['total'];
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
                    <div class="col-xs-13 col-sm-10">
                        <div class="form-intro-text">
                            <h1>Fotoboek Familiar Forest</h1>
                            <p>Je kunt hier een bestelling plaatsen voor een Familiar Forest fotoboek. Een boek kost 32 euro en 50 cent en je mag er zoveel kopen als je wilt.</p>
                            <p>Het liefst hebben we een leuk moment om het boek te presenteren en te verspreiden maar het kan zijn dat dat niet mogelijk gaat zijn en het een simpelere ophaaldag wordt. Als je sowieso het boek liever via de post ontvangt is dat natuurlijk ook mogelijk.</p>
                            <p>We nemen de laatste bestellingen aan op 1 augustus 2020, zodra we die binnen hebben gaan we aankloppen bij de drukker. Je kunt dus verwachten dat je het boek midden september in handen hebt!</p>
                        </div>
                        <?php
                            if( $returnVal != "" ) {
                                echo $returnVal;
                            }
                        ?>
                        <?php
                          if( $current_purchaes > 0 ) {
                            echo "<div class='alert alert-success'>We hebben een bestelling van je ontvangen voor ".$current_purchaes." boek(en).</div>";
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
                                <label for="street" class="col-sm-2 form-control-label">Aantal boeken</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="number" id="number" value="<?php echo $number;?>" name="number">
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
                                <label class="col-sm-2">Verzenden</label>
                                <div class="col-sm-10">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="send" id="send" value="send" <?php if($send == "send") echo( "checked"); ?>>
                                            Ik ontvang het boek graag per post op: <br>
                                            <?=$street?><br><?=$postal." ".$city?><br>
                                            Je kunt dit adres wijzigen bij <a href="ik">Mijn gegevens</a>
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="send" id="pickup" value="pickup" <?php if($send == "pickup") echo( "checked"); ?> >
                                            Ik kom het boek gezellig ophalen op een nader te bepalen locatie in Amsterdam
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                    <table class="table table-condensed">
                                        <tbody style='text-align:right'>
                                             <tr>
                                                <th>
                                                    Fotoboek</th>
                                                <td class='bookcost'><?=$disp_amount?></td>
                                            </tr>
                                            <tr>
                                               <th>
                                                   Verzendkosten</th>
                                               <td class='sendcost'>0.00</td>
                                           </tr>
                                            <tr>
                                                <th>Transactie kosten</th>
                                                <td class='transaction'>0.00</td>
                                            </tr>
                                            <tr class='lead'>
                                                <th>Totaal</th>
                                                <td class='total'><?=$disp_amount ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            <button class='btn btn-lg btn-primary btn-block' type='submit'>Naar betalen</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id='hidden-total' class='hidden'><?=$disp_amount?></div>
        <?php include("form-js.html"); ?>
        <script>
          var transaction = 0;
          var books = 32.50;
          var send = 0;
          $('#send').change(function() {
            if( $(this).prop('checked', true)) {
              send = 6.75;
            } else {
              send = 0;
            }
            $('.sendcost').text(send);
            $('.total').text(transaction + books + send);
          });
          $('#pickup').change(function() {
            if( $(this).prop('checked', true)) {
              send = 0;
            } else {
              send = 6.75;
            }
            $('.sendcost').text(send);
            $('.total').text(transaction + books + send);
          });
          $('#number').change(function() {
            books = parseFloat($('#hidden-total').text()) * parseFloat($(this).val())
            $('.bookcost').text(books);
            $('.total').text(books + transaction + send);
          })
          $('#transactionmethod').change(function(){
            total = books + send;
        		if( $(this).val() == "ideal") {
        			$('.transaction').text('0,29');
              transaction = 0.29;
        			total += 0.29;
        		} else if( $(this).val() == "mistercash") {
        			$('.transaction').text('0,39');
              transaction = 0.39;
        			total += 0.39;
        		} else if( $(this).val() == "creditcard") {
        			transaction = 0.25 + total * 0.028;
              transaction = parseFloat(transaction.toFixed(2));
        			total += transaction;
        			$('.transaction').text(transaction);
        		} else {
        			$('.transaction').text('0,00');
        		}
        		$('.total').text(total);
          })
        </script>
    </body>
</html>
