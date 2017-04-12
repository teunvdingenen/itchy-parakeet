<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_PARTICIPANT) != PERMISSION_PARTICIPANT ) {
    header('Location: oops.php');
}
$show_form = true;
$returnVal = "";
if( $_SERVER["REQUEST_METHOD"] == "POST") {
    if( empty($_POST["confirm"]) || $_POST["confirm"] != "Ik wil mijn ticket verkopen" ) {
        if( empty($_POST["confirm"]) || $_POST["confirm"] != "ik wil mijn ticket verkopen" ) {
            echo $_POST["confirm"];
            addError('Je moet even bevestigen dat je het echt wil door de tekst in te typen.');
        }
    }
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if( $mysqli->connect_errno ) {
        addError("Het lijkt erop dat de website kapot is, probeer het later nog eens!");
        $email_error("Database connectie is kapot: " . $mysqli->error);
    }
    $result = $mysqli->query(sprintf("SELECT rafflecode, complete, share, task FROM $current_table WHERE `email` = '%s'",
        $mysqli->real_escape_string($user_email)));
    if( !$result ) {
        addError("Het is niet gelukt om je gegevens op te halen. Probeer het later nog eens of mail naar: ".$mailtolink);
        $mysqli->close();
    }
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $code = $row['rafflecode'];
    if( $row['complete'] != 1 && ($row['share'] != "FULL" || $row['task'] == 'crew' ) ) {
        addError("Het lijkt erop dat het niet mogelijk is voor je om je ticket te verkopen. Mail even naar: ".$mailtolink." voor meer informatie.");
        $mysqli->close();
    } 
    $result = $mysqli->query(sprintf("SELECT 1 FROM `swap` WHERE `code` = '%s'", $mysqli->real_escape_string($code)));
    if( !$result || $result->num_rows != 0 ) {
        addError("Het is niet gelukt je ticket te koop te zetten. Mail naar: ".$mailtolink." voor meer informatie.".$code);
        $mysqli->close();
    }
    if( $returnVal == "" ) {
        //all is good
        $result = $mysqli->query(sprintf("INSERT INTO `swap` (`code`, `seller`, `date_sold`, `lock_expire`) VALUES ('%s','%s',now(),now())",
            $mysqli->real_escape_string($code),
            $mysqli->real_escape_string($user_email)));
        if( !$result ) {
            addError("Het is niet gelukt je ticket te koop te zetten. Mail naar: ".$mailtolink." voor meer informatie.".$mysqli->error);
            email_error("INSERT swap failed: ".$mysqli->error);
            $mysqli->close();
            return;
        } else {
            $returnVal = "<div class='alert alert-success'>We hebben je verzoek in goede orde ontvangen. Binnen een dag zullen anderen de mogelijkheid hebben om in jou plaats naar Familiar Voorjaar te gaan. Zodra iemand op dat aanbod in gaat ontvang je een email ter bevestiging.</div>";
        }
    }
    $mysqli->close();
} else {
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if( $mysqli->connect_errno ) {
        addError("Het lijkt erop dat de website kapot is, probeer het later nog eens!");
        $email_error("Database connectie is kapot: " . $mysqli->error);
    }

    $result = $mysqli->query(sprintf("SELECT 1 FROM `swap` WHERE `seller` = '%s'",
        $mysqli->real_escape_string($user_email)));
    if( !$result || $result->num_rows != 0 ) {
        addError("Het lijkt erop dat je ticket al te koop staat.");
        $show_form = false;
    }

    $mysqli->close();
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
                        <?php echo $returnVal ?>
                        <div class="jumbotron">
                            <h2>Ticket verkopen</h2>
                            <p class='lead'>
                                Lieve <?php echo $_SESSION['firstname'] ?>,
                            </p>
                            <p>
                                Wat ontzettend jammer dat we je op deze pagina treffen. Dat betekent namelijk dat je speelt met het idee om toch niet mee te gaan naar Familiar Voorjaar en we hebben je natuurlijk wel heel graag erbij!
                            </p>
                            <p>
                                We begrijpen ook dat je waarschijnlijk heel goede redenen hebt voor deze keuze en je misschien zelf niets eraan kan doen. Mocht het iets zijn waar we je bij kunnen helpen of ondersteunen aarzel dan niet om het aan ons te vragen. Je kunt ons daarvoor mailen op: <?php echo $mailtolink ?>.
                            </p>
                            <p>
                                Afzien van je deelname is heel simpel. Je drukt hieronder op de grote knop en vanaf dan kan iedereen die zich heeft ingeschreven voor Familiar Voorjaar je ticket van je overnemen. Wanneer het ticket verkocht is ontvang je een email ter bevestiging en wordt er €119,81 naar je terug overgemaakt, dat is je ticketgeld min de transactiekosten voor het terugstorten.  
                            </p>
                            <?php if( $show_form ) {
                                echo '
                            <form id="seller-form" class="form" method="post" action="'.substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4).'" target="_top">
                                <div class="form-group row">
                                    <label for="confirm" class="col-sm-4 form-control-label">Typ hier: "Ik wil mijn ticket verkopen" ter bevestiging</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="confirm" id="confirm" placeholder="Ik wil mijn ticket verkopen" name="confirm">
                                    </div>
                                </div>
                                <button id="submit" class="btn btn-lg btn-primary btn-block" type="submit" disabled>Bevestigen</button>
                            </form>
                            ';
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include("default-js.html"); ?>
        <?php if( $show_form ) {
            echo '
        <script>
            $(document).ready(function() {
                $("#confirm").keyup(function() {
                    if( $("#confirm").val() == "Ik wil mijn ticket verkopen" || 
                        $("#confirm").val() == "ik wil mijn ticket verkopen") {
                        $("#submit").removeAttr("disabled");
                    } else {
                        $("#submit").attr("disabled","disabled");
                    }
                });
            });
        </script>
        '; }?>
        </body>
</html>
