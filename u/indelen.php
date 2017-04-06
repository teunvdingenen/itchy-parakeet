<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_VOLUNTEERS) != PERMISSION_VOLUNTEERS ) {
        header('Location: oops');
}

$tasks = array("keuken", "bar", "other", "interiour", "thee", "camping", "afbouw", "act", "game", "schmink", "other_act", "perform", "install", "crew");

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
                        <div>
                            <select class='form-control taskselect' id="taskselect" name='taskselect'>
                                <?php
                                foreach( $tasks as $task ) {
                                    echo "<option name='".$task."' value='".$task."' ".($task == $taskselect ? " selected='selected'" : "").">".translate_task($task)."</option>";
                                }
                                ?>
                            </select> 
                        </div>
                        <div class='shiftcontent scollable'>
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
