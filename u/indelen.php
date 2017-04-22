<?php
include "../functions.php";

include("checklogin.php");

$is_acts = false;
$tasks = array();
$team = '';
if( !isset($_GET['t'])) {
    $team = 'vrijwilligers';
} else {
    $team = $_GET['t'];
}
if( $team == 'vrijwilligers' && ($user_permissions & PERMISSION_VOLUNTEERS) == PERMISSION_VOLUNTEERS ) {
    $tasks = array("keuken", "bar", "other", "iv", "thee", "camping", "afb");
} else if ( $team == 'acts' && ( $user_permissions & PERMISSION_ACTS) == PERMISSION_ACTS ) {
    $is_acts = true;
    $tasks = array("act", "game", "schmink", "other_act", "perform", "install","workshop");
} else {
    header('Location: oops');
}

//$tasks = array("keuken", "bar", "other", "interiour", "thee", "camping", "afbouw", "act", "game", "schmink", "other_act", "perform", "install", "crew");


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
                <div id='modal' class="modal fade" tabindex="-1" role="dialog">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Deelnemer overzetten naar ander team</h4>
                      </div>
                      <div class="modal-body">
                        <p id="modal-content"></p>
                        <div id="trid" class="hidden"></div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Annuleren</button>
                        <button id='signoff' type="button" class="btn btn-primary">Overzetten</button>
                      </div>
                    </div><!-- /.modal-content -->
                  </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <div class="row row-offcanvas row-offcanvas-left">
                    <?php include("navigation.php");?>
                    <div class="col-xs-13 col-sm-10"> 
                        <span class='btn btn-warning btn-block team_droppable'>
                            <?php if($is_acts) {
                                echo "<span class='hidden oth_team'></span>";
                                echo "Naar vrijwilligers";
                            } else {
                                echo "<span class='hidden oth_team'>act</span>";
                                echo "Naar Acts";
                            }
                            ?>
                        </span>
                    </div>
                    <div class="col-xs-10 col-sm-7"> 
                        <div>
                            <select class='form-control taskselect' id="taskselect" name='taskselect'>
                                <?php
                                foreach( $tasks as $task ) {
                                    echo "<option name='".$task."' value='".$task."' ".($task == $taskselect ? " selected='selected'" : "").">".translate_task($task)."</option>";
                                }
                                ?>
                            </select> 
                        </div>
                        <div class='shiftcontent scrollable'>
                        </div>
                        <div>
                            <h4 id='emailheader'></h4>
                            <textarea class="form-control" id="emailadressen" cols="60" rows="4" readonly></textarea>
                        </div>
                    </div>
                    
                    <div class="col-xs-3 col-sm-3">
                        <div>
                            <select class='form-control volunteerselect' id='volunteerselect'>
                                <option value='contrib0'>Eerste keus</option>
                                <option value='contrib1'>Tweede keus</option>
                                <option value='all'>Alles</option>
                            </select>
                        </div>
                        <div class='volunteercontent scrollable'>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include("default-js.html"); ?>
        <script src="js/indelen.js"></script>
        </body>
</html>
