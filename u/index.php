<?php
include_once "../model/loginmanager.php";

model\LoginManager::Instance()->isLoggedIn();
if( model\LoginManager::Instance()->getPermissions() != PERMISSION_PARTICIPANT ) {
    header('Location: oops.php');
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

        <div class="page-container">
            <?php include("header.php"); ?>
            <div class="container">
                <div class="row row-offcanvas row-offcanvas-left">
                    <?php include("navigation.php");?>
                    <div class="col-xs-13 col-sm-10"> 
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
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    	<?php include("default-js.html"); ?>
    </body>
</html>
