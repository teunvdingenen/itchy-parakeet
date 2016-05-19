<?php session_start();
include_once "../functions.php";
include_once "../sendmail.php";
include_once "createmenu.php";

if(!isset($_SESSION['loginuser'])) {
    header('Location: ../login');
}
$menu_html = "";
$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info[$db_user_name];
$user_info_permissions = $user_info[$db_user_permissions];

$menu_html = get_menu_html();

$returnVal = $mailto = $subject = $content = "";

$insfirst = "%FIRSTNAME%";
$inslast = "%LASTNAME%";
$insraffle = "%RAFFLECODE%";
$instransaction = "%TRANSACTIECODE%";
$inssignature = "%SIGNATURE%";

if( $_SERVER["REQUEST_METHOD"] == "POST") {
    if( !empty($_POST["mailto"]) ) {
        $mailto = test_input($_POST["mailto"]);
    } else {
        $mailto = "";
        addError("Je hebt niet opgegeven wie de mail moet ontvangen.");
    }
    if( !empty($_POST["subject"]) ) {
        $subject = test_input($_POST["subject"]);
    } else {
        $subject = "";
        addError("Je hebt geen onderwerp ingevoerd.");
    }
    if( !empty($_POST["content"]) ) {
        $content = test_input($_POST["content"]);
    } else {
        $content = "";
        addError("Je hebt geen inhoud ingevoerd.");
    }
    $query = "";
    if( $mailto == 'signup') {
        if( substr_count($content, $insraffle) > 0 ) {
            addError("Je hebt inschrijvingen geselecteerd en probeert loting codes te versturen, dat kan niet..");
        }
        if( substr_count($content, $instransaction) > 0 ) {
            addError("Je hebt inschrijvingen geselecteerd en probeert transactie ids te versturen, dat kan niet..");
        }
        $query = "SELECT p.email, p.firtname, p.lastname FROM person p WHERE 1";
    } else if( $mailto == 'raffle') {
        if( substr_count($content, $instransaction) > 0 ) {
            addError("Je hebt loting geselecteerd en probeert transactie ids te versturen, dat kan niet..");
        }
        $query = "SELECT p.firstname, p.lastname, r.email, r.code FROM person p join raffle r on r.email = p.email WHERE 1";
    } else if( $mailto == 'buyer') {
        $query = "SELECT p.firstname, p.lastname, p.email, b.code, b.id FROM person p join buyer b on p.email = b.email WHERE 1";
    }
    if( $returnVal == "" ) {
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        if( $mysqli->connect_errno ) {
            addError("Connectie met Database is kapot, zoek hulp!");
            return false;
        }

        $result = $mysqli->query($query);
        if( $result === FALSE ) {
            addError("Er iets mis gegaan met het zoeken van mensen in de database, zoek hulp!");
        } else {

            while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
                $mailcontent = nl2br($content);
                $email = $row['email'];
                $fullname = $row['firstname']." ".$row['lastname'];
                if( substr_count($mailcontent, $insfirst)) {
                    $mailcontent = str_replace($insfirst, $row['firstname'], $mailcontent);
                }
                if( substr_count($mailcontent, $inslast)) {
                    $mailcontent = str_replace($inslast, $row['lastname'], $mailcontent);
                }
                if( substr_count($mailcontent, $insraffle)) {
                    $mailcontent = str_replace($insraffle, $row['code'], $mailcontent);
                }
                if( substr_count($mailcontent, $instransaction)) {
                    $mailcontent = str_replace($instransaction, $row['id'], $mailcontent);
                }

                $mailcontent = get_email_header() . $mailcontent . get_email_footer();
                send_mail($email, $fullname, $subject, $mailcontent);
            }
        }
    }
}

function addError($value) {
    global $returnVal;
    $returnVal .= '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ' . $value . '</div>';
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
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <h3>Met massa mail kun je alle inschrijvingen / loting / kopers in een keer mailen. Maar pas hiermee dus op! </h3>
                <?=$returnVal?>
                <form id="mail-form" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top" onsubmit="confirmSubmit();">
                    <div class="form-group row">
                        <label class="col-sm-2">Mailen naar:</label>
                        <div class="col-sm-10">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="mailto" id="signup" value="signup" <?php if($mailto == "signup") echo( "checked"); ?>>
                                    Inschrijvingen
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="mailto" id="raffle" value="raffle" <?php if($mailto == "raffle") echo( "checked"); ?> >
                                    Loting
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="mailto" id="buyer" value="buyer" <?php if($mailto == "buyer") echo( "checked"); ?> >
                                    Verkochte tickets
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="subject" class="col-sm-2 form-control-label">Onderwerp</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" id="subject" placeholder="Onderwerp" value="<?php echo $subject;?>" name="subject">
                        </div>
                    </div>

                    <div class="row">
                        <label class="col-sm-2" for="content">Voeg info toe:</label>
                        <div class="col-sm-10">
                            <div class="col-sm-2">
                                <div class="btn btn-sm btn-info btn-block" onclick=<?php echo "insertValue('".$insfirst."');";?>>Voornaam</div>
                            </div>
                            <div class="col-sm-2">
                                <div class="btn btn-sm btn-info btn-block" onclick=<?php echo "insertValue('".$inslast."');";?>>Achternaam</div>
                            </div>
                            <div class="col-sm-2">
                                <div class="btn btn-sm btn-info btn-block" onclick=<?php echo "insertValue('".$insraffle."');";?>>Code</div>
                            </div>
                            <div class="col-sm-2">
                                <div class="btn btn-sm btn-info btn-block" onclick=<?php echo "insertValue('".$instransaction."');";?>>Transactie ID</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="content">Tekst</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="content" id="content" cols="60" rows="10"><?php echo $content; ?></textarea>
                            <label class="form-control-label" for="content">Handtekening wordt automatisch toegevoegd!</label>
                        </div>
                    </div>
                    <button id="submitbutton" class="btn btn-lg btn-primary btn-block" type="submit">Verzenden</button>
                </form>
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
        <script src="js/massmail.js"></script>
    </body>
</html>