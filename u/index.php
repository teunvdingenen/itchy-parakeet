<?php session_start();
include "../functions.php";

$user_email = $user_firstname = $user_permissions = "";

if(!isset($_SESSION['email'])) {
    header('Location: ../login');
} else {
    $user_email = $_SESSION['email'];
}
if(!isset($_SESSION['firstname'])) {
    header('Location: ../login');
} else {
    $user_firstname = $_SESSION['firstname'];
}
if(!isset($_SESSION['permissions'])) {
    header('Location: ../login');
} else {
    $user_permissions = $_SESSION['permissions'];
}

?>

<!doctype html>
<html class="no-js" lang="">
    <?php include("head.html"); ?>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <?php include("header.php"); ?>

        <div class="container-fluid">
            <?php include("navigation.php"); ?>
            <div id="content" class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <div class="row">
                    <div class="col-md-6">
                        <img class="img-responsive" src="../img/familie2016.jpg" alt="Familie Foto 2016">
                    </div>
                    <div class="col-md-6">
                        <img class="img-responsive" src="../img/familie2015.jpg" alt="Familie Foto 2015">
                    </div>
                </div>
                <div class="row" style="padding-top:5px">
                    <div class="col-md-6">
                        <img class="img-responsive" src="../img/familie2014.jpg" alt="Familie Foto 2014">
                    </div>
                    <div class="col-md-6">
                        <img class="img-responsive" src="../img/familie2013.jpg" alt="Familie Foto 2013">
                    </div>
                </div>
                <div class="row" style="padding-top:5px;">
                    <div class="col-md-6">
                        <img class="img-responsive" src="../img/familie2012.jpg" alt="Familie Foto 2012">
                    </div>
                    <div class="col-md-6" style='vertical-align: middle;'>
                        <img class="img-responsive center-block" src="../img/logo_small.png" alt="Familiar">
                    </div>
                </div>
            </div>
        </div>

    	<?php include("default-js.html"); ?>
    </body>
</html>
