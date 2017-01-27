<?php
include_once "../functions.php";
include_once "../sendmail.php";

include("checklogin.php");

if( $user_permissions & PERMISSION_USER != PERMISSION_USER ) {
    header('Location: oops.php');
}

$returnVal = $mailto = $subject = $content = "";

$insfirst = "%FIRSTNAME%";
$inslast = "%LASTNAME%";
$insraffle = "%RAFFLECODE%";
$instransaction = "%TRANSACTIECODE%";
$inssignature = "%SIGNATURE%";
$insticketurl = "%TICKETURL%";

if( $_SERVER["REQUEST_METHOD"] == "POST") {
    if( !empty($_POST["mailto"]) ) {
        $mailto = test_input($_POST["mailto"]);
    } else {
        $mailto = "";
        addError("Je hebt niet opgegeven wie de mail moet ontvangen.");
    }
    if( !empty($_POST["subject"]) ) {
        $subject = test_input($_POST["subject"]);
    } else {
        $subject = "";
        addError("Je hebt geen onderwerp ingevoerd.");
    }
    if( !empty($_POST["content"]) ) {
        $content = test_input($_POST["content"]);
    } else {
        $content = "";
        addError("Je hebt geen inhoud ingevoerd.");
    }
    $query = "";
    if( $mailto == 'signup') {
        if( substr_count($content, $insraffle,$insticketurl) > 0 ) {
            addError("Je hebt inschrijvingen geselecteerd en probeert loting codes te versturen, dat kan niet..");
        }
        if( substr_count($content, $instransaction,$insticketurl) > 0 ) {
            addError("Je hebt inschrijvingen geselecteerd en probeert transactie ids te versturen, dat kan niet..");
        }
        $query = "SELECT p.email, p.firtname, p.lastname FROM person p WHERE 1";
    } else if( $mailto == 'raffle') {
        if( substr_count($content, $instransaction,$insticketurl) > 0 ) {
            addError("Je hebt loting geselecteerd en probeert transactie ids te versturen, dat kan niet..");
        }
        $query = "SELECT p.firstname, p.lastname, r.email, r.code FROM person p join raffle r on r.email = p.email WHERE 1";
    } else if( $mailto == 'buyer') {
        $query = "SELECT p.firstname, p.lastname, p.email, b.code, b.id, b.ticket FROM person p join buyer b on p.email = b.email WHERE b.complete = 1";
    } else if( $mailto == 'secondraffle' ) {
        if( substr_count($content, $instransaction,$insticketurl) > 0 ) {
            addError("Je hebt loting geselecteerd en probeert transactie ids te versturen, dat kan niet..");
        }
        $query = "SELECT p.firstname, p.lastname, r.email, r.code FROM person p join raffle r on r.email = p.email WHERE r.valid = 1 AND NOT EXISTS (SELECT 1 FROM buyer b WHERE b.email = r.email)";
    } else if( $mailto == 'noticket') {
        if( substr_count($content, $insraffle, $instransaction,$insticketurl) > 0 ) {
            addError("Je hebt inschrijvingen geselecteerd en probeert transactie ids of codes te versturen, dat kan niet..");
        }
        $query = "SELECT p.firstname, p.lastname, p.email FROM person p WHERE NOT EXISTS (SELECT 1 FROM buyer b WHERE b.email = p.email and b.complete = 1) AND NOT EXISTS (SELECT 1 from raffle r WHERE r.email = p.email and r.valid = 1)";
    } else if( $mailto == 'test' ) {
        addError("Testmail send");
        $mailcontent = nl2br($content);
        $email = 'info@stichtingfamiliarforest.nl';
        $fullname = "Voornaam Achternaam";
        if( substr_count($mailcontent, $insfirst)) {
            $mailcontent = str_replace($insfirst, 'Voornaam', $mailcontent);
        }
        if( substr_count($mailcontent, $inslast)) {
            $mailcontent = str_replace($inslast, 'Achternaam', $mailcontent);
        }
        if( substr_count($mailcontent, $insraffle)) {
            $mailcontent = str_replace($insraffle, 'AA00BB01', $mailcontent);
        }
        if( substr_count($mailcontent, $instransaction)) {
            $mailcontent = str_replace($instransaction, "tr_abcdefg", $mailcontent);
        }
        if( substr_count($mailcontent, $insticketurl)) {
            $url = "http://stichtingfamiliarforest.nl/ticket.php?ticket=123456789abcdefg";
            $htmltag = "<a href='".$url."'>".$url."</a>";
            $mailcontent = str_replace($insticketurl, $htmltag, $mailcontent);
        }
        $mailcontent = get_email_header() . $mailcontent . get_email_footer();
        send_mail($email, $fullname, $subject, $mailcontent);
    }
    if( $returnVal == "" && $mailto != 'test' ) {
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        if( $mysqli->connect_errno ) {
            addError("Connectie met Database is kapot, zoek hulp!");
            return false;
        }

        $result = $mysqli->query($query);
        if( $result === FALSE ) {
            addError("Er iets mis gegaan met het zoeken van mensen in de database, zoek hulp!");
        } else {
            while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
                $mailcontent = nl2br($content);
                $email = $row['email'];
                $fullname = $row['firstname']." ".$row['lastname'];
                if( substr_count($mailcontent, $insfirst)) {
                    $mailcontent = str_replace($insfirst, $row['firstname'], $mailcontent);
                }
                if( substr_count($mailcontent, $inslast)) {
                    $mailcontent = str_replace($inslast, $row['lastname'], $mailcontent);
                }
                if( substr_count($mailcontent, $insraffle)) {
                    $mailcontent = str_replace($insraffle, $row['code'], $mailcontent);
                }
                if( substr_count($mailcontent, $instransaction)) {
                    $mailcontent = str_replace($instransaction, $row['id'], $mailcontent);
                }
                if( substr_count($mailcontent, $insticketurl)) {
                    $url = "http://stichtingfamiliarforest.nl/ticket.php?ticket=".$row['ticket'];
                    $htmltag = "<a href='".$url."'>".$url."</a>";
                    $mailcontent = str_replace($insticketurl, $htmltag, $mailcontent);
                }
                $mailcontent = get_email_header() . $mailcontent . get_email_footer();
                send_mail($email, $fullname, $subject, $mailcontent);
            }
        }
        $mysqli->close();
    }
}

function addError($value) {
    global $returnVal;
    $returnVal .= '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ' . $value . '</div>';
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
                <h3>Met massa mail kun je alle inschrijvingen / loting / kopers in een keer mailen. Maar pas hiermee dus op! </h3>
                <?=$returnVal?>
                <form id="mail-form" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top" onsubmit="confirmSubmit();">
                    <div class="form-group row">
                        <label class="col-sm-2">Mailen naar:</label>
                        <div class="col-sm-10">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="mailto" id="signup" value="signup" <?php if($mailto == "signup") echo( "checked"); ?>>
                                    Inschrijvingen
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="mailto" id="noticket" value="noticket" <?php if($mailto == "noticket") echo( "checked"); ?> >
                                    Inschrijvingen zonder ticket of loting
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="mailto" id="raffle" value="raffle" <?php if($mailto == "raffle") echo( "checked"); ?> >
                                    Loting
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="mailto" id="secondraffle" value="secondraffle" <?php if($mailto == "secondraffle") echo( "checked"); ?> >
                                    Tweede Loting
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="mailto" id="buyer" value="buyer" <?php if($mailto == "buyer") echo( "checked"); ?> >
                                    Verkochte tickets
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="mailto" id="test" value="test" <?php if($mailto == "test") echo( "checked"); ?> >
                                    Testmail naar info@..
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="subject" class="col-sm-2 form-control-label">Onderwerp</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" id="subject" placeholder="Onderwerp" value="<?php echo $subject;?>" name="subject">
                        </div>
                    </div>

                    <div class="row">
                        <label class="col-sm-2" for="content">Voeg info toe:</label>
                        <div class="col-sm-10">
                            <div class="col-sm-2">
                                <div class="btn btn-sm btn-info btn-block" onclick=<?php echo "insertValue('".$insfirst."');";?>>Voornaam</div>
                            </div>
                            <div class="col-sm-2">
                                <div class="btn btn-sm btn-info btn-block" onclick=<?php echo "insertValue('".$inslast."');";?>>Achternaam</div>
                            </div>
                            <div class="col-sm-2">
                                <div class="btn btn-sm btn-info btn-block" onclick=<?php echo "insertValue('".$insraffle."');";?>>Code</div>
                            </div>
                            <div class="col-sm-2">
                                <div class="btn btn-sm btn-info btn-block" onclick=<?php echo "insertValue('".$instransaction."');";?>>Transactie ID</div>
                            </div>
                            <div class="col-sm-2">
                                <div class="btn btn-sm btn-info btn-block" onclick=<?php echo "insertValue('".$insticketurl."');";?>>Ticket URL</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="content">Tekst</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="content" id="content" cols="60" rows="10"><?php echo $content; ?></textarea>
                            <label class="form-control-label" for="content">Handtekening wordt automatisch toegevoegd!</label>
                        </div>
                    </div>
                    <button id="submitbutton" class="btn btn-lg btn-primary btn-block" type="submit">Verzenden</button>
                </form>
            </div>
        </div>
    </div>
</div>

    	<?php include("default-js.html"); ?>
        <script src="js/massmail.js"></script>
    </body>
</html>
