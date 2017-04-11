<?php
include "../functions.php";

include("checklogin.php");

//todo check signup
if( ($user_permissions & PERMISSION_PARTICIPANT) != PERMISSION_PARTICIPANT ) {
    header('Location: oops');
}
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

$ticket_reserved = FALSE;
$can_sell = FALSE;
$result = $mysqli->query(sprintf("SELECT 1 FROM `swap` where `buyer` = '%s' and lock_expire < now()",
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

$result = $mysqli->query("SELECT sw.*, s.task from `swap` sw join $current_table s on s.email = sw.seller where lock_expire < now()");
if( !$result ) {
    email_error("Error getting from swap".$mysqli->error);
    exit;
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
                        <p id="raffle-modal-content">Dit ticket is helaas niet meer beschikbaar.</p>
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
                        <div>
                            <?php
                                if( $can_sell ) {
                                    echo "<div><p>Als je jou ticket te koop wilt aanbieden kun je op de knop hieronder klikken.</p></div>";
                                    echo "<a href='verkopen' class='btn btn-info btn-sm btn-block'>Ik wil mijn ticket verkopen</a>";
                                }
                                if( $ticket_reserved ) {
                                    echo "<div><p>Je hebt al aangegeven een ticket te willen kopen. Klik op de knop hieronder om je deelname te bevestigen.</p></div>";
                                    echo "<a href='deelname' class='btn btn-info btn-sm btn-block'>Deelnemen</a>";   
                                }
                            ?>
                        </div>
                        <div class='tickets'>
                            <table class='table table-striped table-bordered table-hover table-condensed'>
                                <thead>
                                    <th>Ticket</th><th>Taak</th><th>Aangeboden sinds</th><th>Kopen</th>
                                </thead>
                                <tbody>
                                    <?php
                                    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
                                        $task = substr($row['task'], 0, -10);
                                        if( $task == 'crew') {
                                            continue;
                                        } else if( is_act($task) ) {
                                            $task = '';
                                        }
                                        echo "<tr>";
                                        echo "<td class='code'>".$row['code']."</td>";
                                        echo "<td>".translate_task($task)."</td>";
                                        echo "<td>".$row['date_sold']."</td>";
                                        echo "<td><a class='btn btn-info btn-sm btn-block buyticket'>Kopen</a></td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include("default-js.html"); ?>
        <script src='js/ticketruil.js'></script>
        </body>
</html>
