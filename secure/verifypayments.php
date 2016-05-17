<?php session_start();
include "../functions.php";
include "createmenu.php";

if(!isset($_SESSION['loginuser'])) {
    header('Location: ../login');
}
$menu_html = "";
$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info[$db_user_name];
$user_info_permissions = $user_info[$db_user_permissions];

if( $user_info_permissions & PERMISSION_DISPLAY != PERMISSION_DISPLAY ) {
    addError("No permission to display!");
    _exit();
}

$menu_html = get_menu_html();
$returnVal = "";
$infoBlock = "";

$tableHTML="<table class='table table-striped table-bordered table-hover table-condensed'>";
$tableHTML.="<thead><tr class='header-row'>";
$tableHTML.="<th>ID</th>";
$tableHTML.="<th>Code</th>";
$tableHTML.="<th>Email</th>";
$tableHTML.="<th>Status (extern)</th>";
$tableHTML.="<th>Status (intern)</th>";
$tableHTML.="<th>Bedrag</th>";
$tableHTML.="</th></thead>";

try {
    include "../mollie_api_init.php";
} catch (Mollie_API_Exception $e) {
    addError("Er is iets fout gegaan met de Mollie link");
    _exit();
}
$offset = 0;
$requested = 50;
$total = 999999999;
$nr_paid = 0;
$complete_amount = 0;
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
while( $offset < $total ) {
    $payments = $mollie->payments->all($offset,$requested);
    $total = $payments->totalCount;
    foreach ($payments->data as $payment ) {
        if( $payment->status != 'paid' ) {
            continue;
        }
        $nr_paid += 1;
        $complete_amount += $payment->amount;
        $query = sprintf("SELECT * FROM buyer WHERE id = '%s'", $mysqli->real_escape_string($payment->id));
        $sqlresult = $mysqli->query($query);
        if( !$sqlresult ) {
            addError("Unable to query database");
        } else if ( $sqlresult->num_rows == 0) {
            addError(sprintf("Transactie met id: %s en code: %s staat niet in de database", $payment->id, $payment->metadata->raffle));
        } else {
            $string = "";
            $err = false;
            if( $sqlresult->num_rows > 1 ) {
                $err = true;
                $string .= "Meerdere entries voor: ".$payment->id."<br>";
            }
            while( $row = $sqlresult->fetch_array(MYSQLI_ASSOC) ) {                
                if( $payment->status == 'paid' && $row['complete'] != 1 ) {
                    $err = true;
                    $string .= sprintf("Betaling met id: %s, code %s is niet als betaald gemarkeerd! Status is: %s", $payment->id, $payment->metadata->raffle, $row['complete']);
                }
                addToTable($row['id'], $row['code'], $row['email'], $payment->status, $row['complete'], $payment->amount);
            }
            if( $err ) {
                addInfo($string);
            } 
        }
    }
    $offset += $requested;
}
$tableHTML.="</table>";

function addError($value) {
    global $returnVal;
    $returnVal .= "<div class='alert alert-danger'><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span> ".$value."</div>";
}

function addInfo($value) {
    global $infoBlock;
    $infoBlock .= "<div class='alert alert-info'><span class='glyphicon glyphicon-exclamation-remove' aria-hidden='true'></span> ".$value."</div>";
}

function addtoTable($id, $code, $email, $mollie_status, $complete, $amount) {
    global $tableHTML;
    $tableHTML .= "<tr>";
    $tableHTML .= "<td>".$id."</td>";
    $tableHTML .= "<td>".$code."</td>";
    $tableHTML .= "<td>".$email."</td>";
    $tableHTML .= "<td>".$mollie_status."</td>";
    $tableHTML .= "<td>".$complete."</td>";
    $tableHTML .= "<td>".$amount."</td>";
    $tableHTML .= "</tr>";
}

function addOK($value) {
    global $infoBlock;
    $infoBlock .= "<div class='alert alert-success'><span class='glyphicon glyphicon-exclamation-ok' aria-hidden='true'></span> ".$value."</div>";
}
?>

<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="Teun van Dingenen">
        <link rel="icon" href="../favicon.ico">

        <title>Familiar Forest Dashboard</title>

        <!-- Bootstrap core CSS -->
        <link href="../css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="../css/main.css" rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <nav class="navbar navbar-inverse navbar-fixed-top">
          <div class="container-fluid">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="#">Familiar Forest Festival</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
              <ul class="nav navbar-nav navbar-right">
                <li><a class='menulink' href='logout.php'>Logout</a></li>
              </ul>
            </div>
          </div>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-3 col-md-2 sidebar">
                  <?php echo $menu_html ?>
                </div>
            </div>
            <div id="content" class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <?=$returnVal?>
                <div class='alert alert-success'><span class='glyphicon glyphicon-exclamation-ok' aria-hidden='true'></span> Totaal: <?=$nr_paid?> successvolle transacties (€ <?=$complete_amount?>) </div>
                <?=$infoBlock?>
                <?=$tableHTML?>
            </div>
        </div>

    <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
        <script src="../js/vendor/bootstrap.min.js"></script>

        <script src="../js/plugins.js"></script>
        <script src="../js/main.js"></script>
        <script src="js/secure.js"></script>
    </body>
</html>
