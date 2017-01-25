<?php
include_once "functions.php";

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

if( $user_permissions & PERMISSION_PARTICIPANT != PERMISSION_PARTICIPANT ) {
    header('Location: oops.php');
}

if( strtotime('now') > strtotime('2016-06-29 00:00') ) {
    header('Location: index');
}

$email = "";
$returnVal = "";

if( $_SERVER["REQUEST_METHOD"] == "POST") {
    if( !empty($_POST["email"]) ) {
        $email = test_input($_POST["email"]);
    } else {
        $email = "";
        addError("Je moet wel je email adres opgeven.");
    }

    if( $returnVal == "" ) {
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        $query = sprintf("SELECT 1 from $current_table where email = '%s' and complete = 1", $mysqli->real_escape_string($email));
        $sqlresult = $mysqli->query($query);
        if( $sqlresult->num_rows > 0 ) {
            addError("Het lijkt erop dat je al een ticket hebt. Stuur voor meer informatie een email naar".$mailtolink);
        }
        if( $returnVal == "" ) {
            $sqlresult = $mysqli->query(sprintf("SELECT 1 from $current_table where email = '%s'",$mysqli->real_escape_string($email)));
            if( $sqlresult->num_rows < 1 ) {
                addError("We hebben geen aanmelding van jou ontvangen dus kunnen we je helaas niet inschrijven voor de reservelijst");
            } else {
                $query = sprintf("UPDATE $current_table SET round = 2 WHERE email = '%s'",$mysqli->real_escape_string($email));
                if( !$mysqli->query($query) ) {
                    addError("Er is iets fout gegaan met het opslaan van je verzoek. Stuur voor meer infomatie een email naar:".$mailtolink);
                }
            }
        }
        $mysqli->close();
    }
    if( $returnVal == "" ) {
        $returnVal = '<div class="alert alert-success" role="alert"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> ' . 
            "We hebben je aanmelding voor de reservelijst in goede orde ontvangen. Als er een ticket voor je vrijkomt ontvang je meer informatie daarover via email." . '</div>';
            $email = "";
    }
}

function addError($value) {
    global $returnVal;
    $returnVal .= '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ' . $value . '</div>';
}

?>

<!DOCTYPE html>
<html lang="en">
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
                <div class="col-xs-12 col-sm-9"> 
                <h1>Familiar Voorjaar 2017</h1>
                <p class="lead">
                    Lieve Lenteliefhebbers,
                </p>
                <p>De ervaring leert dat het nog wel eens voorkomt dat een deelnemer onverhoopt toch niet mee kan naar Familiar Forest. Wanneer iemand ervoor kiest zijn ticket terug te
                    willen verkopen zullen we proberen deze opnieuw te verkopen aan iemand die daar interesse in heeft en deze alsnog de mogelijkheid geven om deel te nemen aan Familiar Forest 2016</p>
                <p>Je kunt tot en met 27 juni aangeven of je hier interesse in hebt. Deelnemers met een ticket hebben vanaf die datum tot en met 5 augustus de mogelijkheid
                    hun ticket terug te verkopen aan Familiar Forest.<p>
                <p>Om je aan te melden voor deze extra ronde hoef je alleen maar je email adres in te vullen, je moet je dus al wel ingeschreven hebben
                    tijdens een voorgaande inschrijfronde.

                <p>De high fives zijn gratis, de knuffels oprecht en de liefde oneindig.<br>Familiar Forest</p>
            </div>
            <?=$returnVal?>
            <form class='form-small' method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">
                <div class="form-group row">
                    <label for="email" class="col-sm-2 form-control-label">Email</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="email" placeholder="Email" value="<?php echo $email;?>" name="email">
                    </div>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Aanmelden <span class="glyphicon glyphicon-send" aria-hidden="true"></span></button>
            </form>
        </div>
    </div>
        </div><!-- /.container -->


    <?php include("default-js.html"); ?>
    <script>
    $('#togglebutton').on('click', function(){
        $(this).children().closest('.glyphicon').toggleClass('glyphicon-chevron-right glyphicon-chevron-down');
    });
    </script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
  </body>
</html>
