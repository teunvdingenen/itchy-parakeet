<?php session_start();
include "../functions.php";
include "createmenu.php";

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

if( $user_permissions & PERMISSION_CALLER != PERMISSION_CALLER ) {
    header('Location: oops.php');
}

$menu_html = get_menu_html();

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    echo "De website is kapot! Bel Teun!";
    return false;
}

$fullname = $firstname = $lastname = $contrib0 = $motivation = $phone = $code = $familiar = "";
$headertext = "Bellen maar!";
$query = "SELECT p.firstname, p.lastname, s.contrib0_type, s.familiar, s.motivation, p.email, p.phone, s.rafflecode, s.called FROM person p join $current_table s on s.email = p.email WHERE s.called = 0 AND s.valid = 1 ORDER BY RAND() LIMIT 1";
$result = $mysqli->query($query);
if( $result === FALSE ) {
    //error
} else if( $result->num_rows != 1 ) {
    $headertext = "Er is niemand meer om te bellen!";
} else {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $firstname = $row['firstname'];
    $lastname = $row['lastname'];
    $email = $row['email'];
    $phone = $row['phone'];
    $code = $row['code'];
    $contrib0 = translate_contrib($row['type']);
    $motivation = $row['motivation'];
    $familiar = $row['familiar'];
    $fullname = $firstname . " " . $lastname;

    //$query = sprintf("UPDATE raffle SET called = 2 WHERE code = '%s'", $mysqli->real_escape_string($code));
    //$mysqli->query($query);
    //if( $mysqli->affected_rows != 1 ) {
        //error
    //}


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
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="text-center"><?=$headertext?></h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        Naam:
                    </div>
                    <div class="col-md-4">
                        <?=$fullname?>
                    </div>
                    <div class="col-md-2">
                        Bijdrage:
                    </div>
                    <div class="col-md-4">
                        <?=$contrib0?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        Telefoonnummer:
                    </div>
                    <div class="col-md-4">
                        <?=$phone?>
                    </div>
                    <div class="col-md-2">
                        Motivatie:
                    </div>
                    <div class="col-md-4">
                        <?=$motivation?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        Code:
                    </div>
                    <div class="col-md-4">
                        <?=$code?>
                    </div>
                    <div class="col-md-2">
                        Bekend door:
                    </div>
                    <div class="col-md-4">
                        <?=$familiar?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <button class='btn btn-lg btn-info btn-block' id='noanswer' onclick=<?php echo "notAnswered('".$code."');" ?>>Geen antwoord </button>
                    </div>
                    <div class="col-md-6">
                        <button class='btn btn-lg btn-success btn-block' id='answered' onclick=<?php echo "called('".$code."');" ?>>Gebeld!</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class='btn btn-lg btn-danger btn-block' id='stalk' onclick=<?php echo "window.open('https://www.facebook.com/search/top/?q=".str_replace(' ', '%20', $fullname)."')" ?>>STALK</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="code" style="display:none"><?=$code?></div>
    <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
        <script src="../js/vendor/bootstrap.min.js"></script>

        <script src="../js/plugins.js"></script>
        <script src="../js/main.js"></script>
        <script src="js/secure.js"></script>
        <script src="js/called.js"></script>
    </body>
</html>
