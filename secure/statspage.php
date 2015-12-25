<?php session_start();
include "../functions.php";

if(!isset($_SESSION['loginuser'])) {
    header('Location: ../login');
}

$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info[$db_user_name];
$user_info_permissions = $user_info[$db_user_permissions];

if( !($user_info_permissions & PERMISSION_DISPLAY )) {
    echo "No access";
    return;
}

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

        <script src="../js/vendor/modernizr-2.8.3.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->

        <div class="secure_content">
            <div class="toprow">
                <canvas class="third" id="genderchart"></canvas>
                <canvas class="twothird" id="signupschart"></canvas>
            </div>
            <div class="secondrow">
                <canvas class="third" id="visitschart"></canvas>
                <canvas class="third" id="contrib0chart"></canvas>
                <canvas class="third" id="contrib1chart"></canvas>
            </div>
            <div class="thirdrow">
                <canvas class="third" id="citychart"></canvas>
                <canvas class="twothird" id="agechart"></canvas>
            </div>
        </div>


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.3.min.js"><\/script>')</script>
        <script src="../js/plugins.js"></script>
        <script src="../js/main.js"></script>
        <script src="js/Chart.js"></script>
        <script src="js/chartfunctions.js"></script>

    </body>
</html>
