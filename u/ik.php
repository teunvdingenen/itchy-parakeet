<?php session_start();
include_once "../functions.php";
include_once "createmenu.php";

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

$menu_html = get_menu_html();

date_default_timezone_set('Europe/Amsterdam');
$returnVal = "";
$firstname = $lastname = $birthdate = $gender = $phone = $city = $phone = $postal = $steet = "";
$email = $user_email;

if( $_SERVER["REQUEST_METHOD"] == "POST") {
    if( !empty($_POST["firstname"]) ) {
        $firstname = test_input($_POST["firstname"]);
    } else {
        $firstname = "";
        addError("Je hebt je voornaam niet opgegeven.");
    }
    if( !empty($_POST["lastname"]) ) {
        $lastname = test_input($_POST["lastname"]);
    } else {
        $lastname = "";
        addError("Je hebt je achternaam niet opgegeven.");
    }
    if( !empty($_POST["street"]) ) {
        $street = test_input($_POST["street"]);
    } else {
        $street = "";
    }
    if( !empty($_POST["postal"]) ) {
        $postal = test_input($_POST["postal"]);
    } else {
        $postal = "";
    }
    if( !empty($_POST["city"]) ) {
        $city = test_input($_POST["city"]);
    } else {
        $city = "";
        addError("Je hebt je woonplaats niet opgegeven.");
    }
    if( !empty($_POST["birthdate"]) ) {
        $birthdate = test_input($_POST["birthdate"]);
        $date = DateTime::createFromFormat('d/m/Y', $birthdate);
        if( $date == FALSE ) {
            if( ($timestamp = strtotime($birthdate)) == FALSE ) {
                addError("De opgegeven geboortedatum klopt niet.");
            } else {
                $birthdate = date( 'Y-m-d H:i:s', $timestamp );
            }
        } else {
            $birthdate = $date->format('Y-m-d H:i:s');
        }
    } else {
        addError("Je hebt je geboortedatum niet opgegeven");
    }
    if( !empty($_POST["gender"]) ) {
        $gender = test_input($_POST["gender"]);
    } else {
        $gender = "";
        addError("Je hebt je geslacht niet opgegeven.");
    }
    if( !empty($_POST["phone"]) ) {
        $phone = test_input($_POST["phone"]);
    } else {
        $phone = "";
        addError("Je hebt geen telefoonnummer opgegeven");
    }

    if( $returnVal == "" ) {
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        $query = sprintf("UPDATE person SET `firstname` = '%s', `lastname` = '%s', `city` = '%s', `gender` = '%s', `phone` = '%s', `birthdate` = '%s', `street` ='%s', `postal` = '%s' WHERE `email` = '%s'",
            $mysqli->real_escape_string($firstname),
            $mysqli->real_escape_string($lastname),
            $mysqli->real_escape_string($city),
            $mysqli->real_escape_string($gender),
            $mysqli->real_escape_string($phone),
            $mysqli->real_escape_string($birthdate),
            $mysqli->real_escape_string($street),
            $mysqli->real_escape_string($postal),
            $mysqli->real_escape_string($email)
        );
        $result = $mysqli->query($query);
        if( !$result ) {
            addError("We konden je gegevens niet opslaan. Je kunt het beste even contact opnemen met Familiar Forest op het email adres: ".$mailtolink);
        }
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $birthdate);
        $birthdate = $date->format('d/m/Y');
        $mysqli->close();
    } else {
        //try again..
    }
    if( $returnVal == "") {
        $returnVal .= '<div class="alert alert-success" role="alert"><span class="glyphicon glyphicon-save" aria-hidden="true"></span> Je gegevens zijn opgeslagen</div>';
    } else {
    }
} else { //End POST
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $query = sprintf("SELECT * FROM person where `email` = '%s'",
        $mysqli->real_escape_string($user_email));
    $result = $mysqli->query($query);
    if( $result === FALSE ) {
        addError("We konden niet je gegevens ophalen. Je kunt het beste even contact opnemen met Familiar Forest op: ".$mailtolink);
    } else if( $result->num_rows != 1 ) {
        addError("We konden niet je huidige gevens ophalen. Je kunt het beste even contact opnemen met Familiar Forest op: ".$mailtolink);
    } else {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $firstname = htmlspecialchars_decode($row['firstname']);
        $lastname = htmlspecialchars_decode($row['lastname']);
        $city = htmlspecialchars_decode($row['city']);
        $gender = htmlspecialchars_decode($row['gender']);
        $phone = htmlspecialchars_decode($row['phone']);
        $postal = htmlspecialchars_decode($row['postal']);
        $street = htmlspecialchars_decode($row['street']);
        $birthdate = htmlspecialchars_decode($row['birthdate']);
        $birthdate = DateTime::createFromFormat('Y-m-d', $birthdate);
        $birthdate = $birthdate->format('d/m/Y');
    }
    $mysqli->close();
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
        <title>Familiar Forest</title>
        <meta name="description" content="">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <link rel="icon" href="../favicon.ico">
        <!-- Place favicon.ico in the root directory -->
        <link href="../css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/main.css">
        <link rel="stylesheet" type="text/css" media="all"
            href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/smoothness/jquery-ui.css"/>        
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
                <div class="form-intro-text">
                <h2>Gegevens aanpassen</h2>
            </div>
                
            <?php echo $returnVal; ?>
            <form id="create-form" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">
                <div class="form-group row">
                    <label for="email" class="col-sm-2 form-control-label">Email*</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="email" id="email" placeholder="Email" value="<?php echo $email;?>" name="email" disabled>
                    </div>
                </div>
                <fieldset>
                <div class="form-group row">
                    <label for="firstname" class="col-sm-2 form-control-label">Voornaam*</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="firstname" placeholder="Voornaam" value="<?php echo $firstname;?>" name="firstname">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="lastname" class="col-sm-2 form-control-label">Achternaam*</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="lastname" placeholder="Achternaam" value="<?php echo $lastname;?>" name="lastname">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="street" class="col-sm-2 form-control-label">Straatnaam & Huisnummer</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="street" placeholder="Straatnaam & Huisnummer" value="<?php echo $street;?>" name="street">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="postal" class="col-sm-2 form-control-label">Postcode</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="postal" placeholder="Postcode" value="<?php echo $postal;?>" name="postal">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="city" class="col-sm-2 form-control-label">Woonplaats*</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="city" placeholder="Woonplaats" value="<?php echo $city;?>" name="city">
                    </div>
                </div>
            
                <div class="form-group row">
                    <label for="birthdate" class="col-sm-2 form-control-label">Geboortedatum*<br>(dd/mm/yyyy)</label>
                    <div class="col-sm-10">
                        <input class="form-control ignore datepicker" type="text" id="birthdate" value="<?php echo $birthdate;?>" name="birthdate" placeholder="dd/mm/yyyy">
                        <div>
                            <label for="birthdate" class="error" style="display:none;"></label>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2">Geslacht*</label>
                    <div class="col-sm-10">
                        <div class="radio">
                            <label>
                                <input type="radio" name="gender" id="male" value="male" <?php if($gender == "male") echo( "checked"); ?>>
                                Jongeman
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="gender" id="female" value="female" <?php if($gender == "female") echo( "checked"); ?> >
                                Jongedame
                            </label>
                        </div>
                        <label for="gender" class="error" style="display:none;"></label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="phone" class="col-sm-2 form-control-label">Telefoonnummer*</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="phone" placeholder="Telefoonnummer" value="<?php echo $phone;?>" name="phone">
                    </div>
                </div>
                </fieldset>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Opslaan</button>
            </form>
            </div>
        </div>

    <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
        <scirpt src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/localization/messages_nl.js"></script>
        <script src="../js/vendor/bootstrap.min.js"></script>
        <script src="../js/vendor/bootstrap-datepicker.js"></script>
        <script src="../js/vendor/bootstrap-datepicker.nl.min.js"></script>
        <script src="../js/vendor/modernizr-2.8.3.min.js"></script>
        <script src="../js/plugins.js"></script>
        <script src="../js/main.js"></script>
        <script src="js/secure.js"></script>
        <script src="js/ik.js"></script>
    </body>
</html>
