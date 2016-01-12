<?php session_start(); 

include "initialize.php";
$message = "";
try {
    include "mollie_api_init.php";

    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    $code = $_GET["raffle"];
    $firstname = get_name($mysqli, $code);

    $paid = get_paid($mysqli, $code);

    if( $paid === FALSE ) {
        //log error
        //sql error
        $message = "OOPS er is iet fout gegaan";
    } else if( $paid == 0 ) {
        $message = "NIET BETAALD TEKST";
    } else {
        $message = "HELEMAAL GOED TEKST, JE KRIJGT NOG EMAIL";
    }
    $message.="<br>Lorem ipsum dolor sit amet, admodum mnesarchum est in. Accusam oporteat adolescens ius et, mea dicit adolescens ex, nec everti veritus detraxit ne. Sale virtute offendit mel at, rebum nulla vim ea, mel id semper epicurei. Vis case eripuit percipit cu, solet noluisse persecuti sea eu. Id has viris invenire, tamquam lobortis pri et.";

    $mysqli->close();
} catch (Mollie_API_Exception $e) {
    //email error
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}

function get_name($mysqli, $code) {
    $sqlquery = sprintf("SELECT p.firstname FROM person p join buyer b on b.email = p.email
        WHERE  b.code = '%s'", $mysqli->real_escape_string($code));
    $sqlresult = $mysqli->query($sqlquery);
    if( $sqlresult === FALSE) {
        return FALSE;
        //log error
    }
    if( $sqlresult->num_rows != 1 ) {
        return false;
    }
    $row = $sqlresult->fetch_array(MYSQLI_ASSOC);
    return $row['firstname'];
}

function get_paid($mysqli, $code) {
    global $mollie;
    $sqlquery = sprintf("SELECT b.id FROM buyer b WHERE b.code = '%s'",
        $mysqli->real_escape_string($code));
    $sqlresult = $mysqli->query($sqlquery);
    if( $sqlresult === FALSE) {
        return FALSE;
        //log error
    }
    if( $sqlresult->num_rows != 1 ) {
        return FALSE;
    }
    $row = $sqlresult->fetch_array(MYSQLI_ASSOC);
    $id = $row["id"];
    if( $mollie->payments->get($id)->isPaid()) {
        return 1;
    }
    return 0;
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

        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/main.css">
        <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <h1 class="header">Lorem ipsum dolor sit amet</h1>
        <div class="content">
        <div>Lieve <? echo $firstname ?>,</div>
        <p><? echo $message ?></p> 
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.3.min.js"><\/script>')</script>
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>

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
