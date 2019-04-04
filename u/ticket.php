<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_PARTICIPANT) != PERMISSION_PARTICIPANT ) {
    header('Location: oops.php');
}

header('Location: oops.php');
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

$result = $mysqli->query(sprintf("SELECT p.firstname, p.lastname, p.birthdate, s.rafflecode, s.transactionid, s.ticket, s.motivation, s.question from person p join $current_table s on p.email = s.email where p.email = '%s' and s.complete = 1",$mysqli->real_escape_string($user_email)));
$mysqli->close();
if( !$result || $result->num_rows != 1 ) {
    header('Location: evolutie');
}
$row = $result->fetch_array(MYSQLI_ASSOC);

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
                        <div class="jumbotron">
                            <h2>Familiar Forest 2019 : evolutie van de homo familiaris</h2>
                            <p class='lead'>7 & 8 september 2019</p>
                            <p>Hieronder vind je alle informatie die je nodig hebt om deel te nemen aan Familiar Forest. Omdat je natuurlijk je laptop, telefoon, tablet en andere schermen thuis laat wil je dit printen. Gelukkig bieden we daarvoor een <a href='ticketpdf' target='_blank'>PDF</a> aan.
                            <p>
                                <table class='table table-striped table-bordered table-hover table-condensed'>
                                    <tr>
                                        <th>Naam</th>
                                        <td><?php echo $row['firstname']." ".$row['lastname']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Geboortedatum</th>
                                        <td><?php echo $row['birthdate']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Code</th>
                                        <td><?php echo $row['rafflecode']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Betalingsnummer</th>
                                        <td><?php echo $row['transactionid']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Wil naar Familiar Forest omdat:</th>
                                        <td><?php echo $row['motivation']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Favoriete Kleur:</th>
                                        <td><?php echo $row['question']; ?></td>
                                    </tr>
                                </table>
                                <img src=<?php echo "'https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=".$row['ticket']."'"; ?> >
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include("default-js.html"); ?>
        </body>
</html>
