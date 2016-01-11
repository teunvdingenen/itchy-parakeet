<?php session_start();
include "../functions.php";

if(!isset($_SESSION['loginuser'])) {
    header('Location: ../login');
}
$menu_html = "";
$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info[$db_user_name];
$user_info_permissions = $user_info[$db_user_permissions];

// Assemble menu:
if( $user_info_permissions & PERMISSION_DISPLAY ) {
    $menu_html .= "<div class='menuitem'><a class='menulink' id ='showstats' href='#'>Main</a></div>";
    $menu_html .= "<div class='menuitem'><a class='menulink' id='displaysignup' href='#'>Inschrijvingen tonen</a></div>";
    $menu_html .= "<div class='menuitem'><a class='menulink' id='displayraffle' href='#'>Loting tonen</a></div>";
    $menu_html .= "<div class='menuitem'><a class='menulink' id='displayBuyers' href='#'>Verkochte tickets tonen</a></div>";
}
if( $user_info_permissions & PERMISSION_RAFFLE ) {
    $menu_html .= "<div class='menuitem'><a class='menulink' id='raffle' href='#'>Loting</a></div>";
}
if( $user_info_permissions & PERMISSION_EDIT ) {
    $menu_html .= "<div class='menuitem'><a class='menulink' id='editsignup' href='#''>Wijzigingen</a></div>";
}
if( $user_info_permissions & PERMISSION_REMOVE) {
    $menu_html .= "<div class='menuitem'><a class='menulink' id='removesignup' href='#''>Verwijderen</a></div>";
}
if( $user_info_permissions & PERMISSION_USER) {
    $menu_html .= "<div class='menuitem'><a class='menulink' id='usermanage' href='#''>Gebruikers</a></div>";
}
$menu_html .= "<div class='menuitem'><a class='menulink' href='logout.php'>Logout</a></div>";
?>

<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <!-- Place favicon.ico in the root directory -->

        <link rel="stylesheet" href="../css/normalize.css">
        <link rel="stylesheet" href="../css/main.css">
        <link rel="stylesheet" href="css/secure.css">
        <script src="../js/vendor/modernizr-2.8.3.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <div>
            <div id="menu" class="menu">
                <?php echo $menu_html ?>
                <div class='footer'>Ingelogd als: <?php echo $user_info_name ?></div>
            </div>

            <div id="content" class="secure_content">

            </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.3.min.js"><\/script>')</script>
        <script src="../js/plugins.js"></script>
        <script src="../js/main.js"></script>
        <script src="js/secure.js"></script>
        <script src="js/Chart.js"></script>
        <script src="js/chartfunctions.js"></script>

        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='https://www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-XXXXX-X','auto');ga('send','pageview');
        </script>
    </body>
</html>
