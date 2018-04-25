<?php
include "../functions.php";

include("checklogin.php");

header('Location: oops');

if( ($user_permissions & PERMISSION_PARTICIPANT) != PERMISSION_PARTICIPANT ) {
    header('Location: oops');
}
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

$result = $mysqli->query(sprintf("SELECT 1 FROM $current_table WHERE `email` = '%s'",
    $mysqli->real_escape_string($user_email)));
if( !$result || $result->num_rows < 1 ) {
    header('Location: oops');
}

$ticket_reserved = FALSE;
$can_sell = FALSE;
$result = $mysqli->query(sprintf("SELECT 1 FROM `swap` where `buyer` = '%s' and lock_expire > now()",
    $mysqli->real_escape_string($user_email)));
if( !$result || $result->num_rows == 0 ) {
    //do nothing
} else {
    $ticket_reserved = TRUE;
}
$result = $mysqli->query(sprintf("SELECT 1 FROM $current_table s WHERE `email` = '%s' and `complete` = 1 and `share` = 'FULL' and `task` != 'crew' and NOT EXISTS (SELECT 1 FROM `swap` WHERE seller = '%s')",
    $mysqli->real_escape_string($user_email),
    $mysqli->real_escape_string($user_email)));
if( !$result || $result->num_rows == 0 ) {
    //do nothing
} else {
    $can_sell = true;
}

$tickets_available = FALSE;
$result = $mysqli->query("SELECT * from `swap` where lock_expire < now()");
if( !$result ) {
    //do nothing
} else {
    if ($result->num_rows > 0) {
        $result = $mysqli->query(sprintf("SELECT complete FROM $current_table WHERE `email` = '%s'",
            $mysqli->real_escape_string($user_email)));
        if( !$result || $result->num_rows == 0 ) {
            // do nothing
        } else if( $result->fetch_array(MYSQLI_ASSOC)['complete'] == 0 ) {
            $tickets_available = TRUE;
        }
    }
}

$can_undo = false;
$result = $mysqli->query(sprintf("SELECT 1 from `swap` where seller = '%s' and lock_expire < now()",
    $mysqli->real_escape_string($user_email)));
if( !$result || $result->num_rows == 0 ) {
    //do nothing
} else {
    $can_undo = TRUE;
}

$mysqli->close();

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
                <div id='swapmodal' class="modal fade" tabindex="-1" role="dialog">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Ticket niet meer beschikbaar</h4>
                      </div>
                      <div class="modal-body">
                        <p id="raffle-modal-content">Ondertussen zijn er helaas geen tickets meer beschikbaar.</p>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default refresh" data-dismiss="modal">ok</button>
                      </div>
                    </div><!-- /.modal-content -->
                  </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <div class="row row-offcanvas row-offcanvas-left">
                    <?php include("navigation.php");?>
                    <div class="col-xs-13 col-sm-10">
                        <div class="jumbotron">
                            <h2>Familiar Ticketruil</h2>
                            <p>
                                Om ervoor te zorgen dat zoveel mogelijk mensen mee kunnen naar onze weekendjes weg, kan je hier aangeven of je je ticket wilt verkopen of een van de beschikbare tickets wilt overnemen.
                            </p>
                            <p>
                                We vinden het belangrijk dat dit zo eerlijk mogelijk verloopt. Dat betekent dat de tickets voor de originele ticketprijs verkocht worden en degene die als eerste te koop staan ook weer als eerste verkocht worden. Dat gaat gelukkig allemaal vanzelf, het enige wat jij hoeft te doen is hieronder aan te geven wat je graag wilt doen.
                            </p>
                            <?php
                                if( $can_sell ) {
                                    echo "<div role='separator' class='divider'></div><a href='verkopen' class='btn btn-info btn-sm btn-block'>Ik wil mijn ticket verkopen</a>";
                                }
                                if( $can_undo ) {
                                    echo "<div role='separator' class='divider'></div><div><p class='undosale'>Je ticket staat op dit moment te koop. Nog niemand heeft je ticket gekocht, dus je kan het nu nog ongedaan maken.</p></div>";
                                    echo "<a class='btn btn-info btn-sm btn-block undo'>Ik wil mijn ticket uit de verkoop halen</a>";
                                }
                                if( $ticket_reserved ) {
                                    echo "<div role='separator' class='divider'></div><div><p>Je hebt aangegeven een ticket te willen kopen. Klik op de knop hieronder om je deelname te bevestigen.</p></div>";
                                    echo "<a href='deelname' class='btn btn-info btn-sm btn-block'>Deelnemen</a>";   
                                } else if($tickets_available) {
                                    echo "<div role='separator' class='divider'></div><div><p>Op dit moment is er een ticket beschikbaar! Druk op de onderstaande knop om deze te kopen.</p></div>";
                                    echo "<div class='btn btn-success btn-sm btn-block buyticket'>Ticket kopen</div>";
                                } else if( !$can_sell && !$can_undo ){
                                    echo "<div role='separator' class='divider'></div><div><p>Er zijn op dit moment geen tickets beschikbaar.</p></div>";
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include("default-js.html"); ?>
        <script src='js/ticketruil.js'></script>
        </body>
</html>
