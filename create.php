<?php session_start();
use model\User;
use model\Person;
use model\PasswordReset;
use model\Event;
use model\PersonEvent;
use model\Utilities;
include_once "functions.php";
include "model/user.php";
include "model/person.php";
include "model/passwordreset.php";
include "model/event.php";
include "model/personevent.php";
include "model/util.php";
date_default_timezone_set('Europe/Amsterdam');
$returnVal = "";
$firstname = $lastname = $birthdate = $inputdate = $street = $postal = $gender = $email = $phone = $city = $familiar = $allow_email = ""; 
$editions = array();

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
    if( !empty($_POST["city"]) ) {
        $city = test_input($_POST["city"]);
    } else {
        $city = "";
        addError("Je hebt je woonplaats niet opgegeven.");
    }
    if( !empty($_POST["inputdate"]) ) {
        $birthdate_str = test_input($_POST["inputdate"]);
        $birthdate = DateTime::createFromFormat('d/m/Y', $birthdate_str);
        if( $birthdate == FALSE ) {
            if( ($timestamp = strtotime($birthdate_str)) == FALSE ) {
                addError("De opgegeven geboortedatum klopt niet.");
            } else {
                $birthdate = new DateTime(date( 'Y-m-d H:i:s', $timestamp ));
            }
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
    if( !empty($_POST["email"]) ) {
        $email = test_input($_POST["email"]);
        if( !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            addError("Het email adres wat je hebt opgegeven lijkt niet te kloppen.");
        }
    } else {
        $email = "";
        addError("Je hebt geen email adres opgegeven");
    }
    if( !empty($_POST["phone"]) ) {
        $phone = test_input($_POST["phone"]);
    } else {
        $phone = "";
        addError("Je hebt geen telefoonnummer opgegeven");
    }

    if( !empty($_POST["familiar"])) {
        $familiar = test_input($_POST["familiar"]);
    } else {
        $familiar = "";
    }
    
    if( !empty($_POST["allow_email"])) {
        $allow_email = $_POST["allow_email"] == 'Y';
    }
    
    if( $returnVal == "" ) {
        if( User::exists($email) || Person::exists($email)) {
            addError("Het lijkt erop dat je al een account hebt. Mocht dat niet zo zijn neem dan even contact met ons op via ".$emailtolink);
        } else {
            $date_now = new DateTime();
            $user = new User();
            $user->setValues($email, model\Utilities::generateRandomToken(128), model\Utilities::generateRandomToken(128), PERMISSION_PARTICIPANT, $date_now, $date_now);
            $user->save();
            $person = new Person();
            $person->setValues($email, $firstname, $lastname, $birthdate, $street, $postal, $city, $gender, $phone, $familiar, $allow_email);
            $person->save();
            forEach($editions as $edition) {
                $event = Event::findByShorthand($edition);
                if( $event != FALSE ) {
                    $personevent = new PersonEvent();
                    $personevent->setValues($event, $person);
                    $personevent->save();
                }
            }
            $passwordreset = null;
            $expire = new DateTime();
            if( PasswordReset::exists($email)) {
              $passwordreset = new PasswordReset();
              $passwordreset = $passwordreset->findByEmail($email);
              $passwordreset->token = Utilities::generateRandomToken(128);
              $passwordreset->expire = $expire->add("P1W");
            } else {
              $passwordreset = new PasswordReset();
              $passwordreset->setValues($user, Utilities::generateRandomToken(128), $expire->add(new DateInterval("P1W")));
            }
            $passwordreset->save();
            $link = "https://stichtingfamiliarforest.nl/pw?t=".$passwordreset->token;
            $subject = "Familiar Forest wachtwoord";
            $content = "<html>".get_email_header();
            $content .= "<p>Lieve ".$person->firstname.",</p>";
            $content .= "<p>Je kunt een wachtwoord instellen door op de onderstaande link te klikken, of deze in de adresbalk van je browser te plakken:</p>";
            $content .= "<p><a href='".$link."'>".$link."</a>";
            $content .= "<p>De link blijft een week geldig.</p>";
            $content .= "<p>Je ontvangt deze email omdat je een account hebt aangemaakt bij Familiar Forest. Weet je hier niets van? Stuur dan even een reply op deze email.</p>";
            $content .= get_email_footer();
            $content .= "</html>";
            //send_mail($person->email, $person->getFullName(), $subject, $content);
            $returnVal .= '<div class="alert alert-success" role="alert">Gelukt! We hebben je een email verstuurd waarmee je een wachtwoord kunt instellen.</div>';
            $firstname = $lastname = $birthdate = $gender = $email = $phone = $city = $familiar = $allow_email = "";
            $editions = array(); 
        }
    } else {
        //try again..
    }
} //End POST
function addError($value) {
    global $returnVal;
    $returnVal .= '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ' . $value . '</div>';
}
?>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Familiar Forest Inschrijven</title>
        <meta name="description" content="">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <link rel="icon" href="favicon.ico">
        <!-- Place favicon.ico in the root directory -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" type="text/css" media="all"
            href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/smoothness/jquery-ui.css"/>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <?php include("header.php"); ?>
        <div class="container">
            <div class="form-intro-text">
                <div>
                    <h2>Aanmaken Familiar Forest account</h2>
                    <p>Om in te toekomst het inschrijf- en deelname proces te vergemakkelijken willen we graag dat je een account aanmaakt. Zodra je een account hebt kun je jezelf inschrijven voor de aankomende editie. Meer informatie over het inloggen vind je <a href="https://www.facebook.com/FamiliarForest/posts/1308244099219461" target="_blank">op onze Facebook pagina. <span class="fa fa-facebook"></span></a>
                </div>
            </div>
                
            <?php echo $returnVal; ?>
            <form id="create-form" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">
                <fieldset>
                <legend>Jouw gegevens</legend>
                <div class="form-group row">
                    <label for="email" class="col-sm-2 form-control-label">Email*</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="email" id="email" placeholder="Email" value="<?php echo $email;?>" name="email">
                    </div>
                </div>
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
                    <label for="city" class="col-sm-2 form-control-label">Woonplaats*</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="city" placeholder="Woonplaats" value="<?php echo $city;?>" name="city">
                    </div>
                </div>
            
                <div class="form-group row">
                    <label for="inputdate" class="col-sm-2 form-control-label">Geboortedatum*<br>(dd/mm/yyyy)</label>
                    <div class="col-sm-10">
                        <input class="form-control ignore datepicker" type="text" id="inputdate" value="<?php echo $inputdate;?>" name="inputdate" placeholder="dd/mm/yyyy">
                        <div>
                            <label for="inputdate" class="error" style="display:none;"></label>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2">Geslacht*</label>
                    <div class="col-sm-10">
                        <div class="radio">
                            <label>
                                <input type="radio" name="gender" id="male" value="M" <?php if($gender == "M") echo( "checked"); ?>>
                                Jongeman
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="gender" id="female" value="F" <?php if($gender == "F") echo( "checked"); ?> >
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
                <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="familiar">Hoe ken je Familiar Forest?</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="familiar" id="familiar" cols="60" rows="4"><?php echo $familiar; ?></textarea>
                            <label for="familiar">Max 1024 karakters</label>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Bij welke edities was je aanwezig?</legend>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Voorgaande edities</label>
                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fff2010" value="fff2010" <?php if(in_array("fff2010", $editions)) echo( "checked"); ?> >
                                    Familiar Forest 2010
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fff2011" value="fff2011" <?php if(in_array("fff2011", $editions)) echo( "checked"); ?>>
                                    Familiar Forest 2011
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="ffcastle" value="ffcastle" <?php if(in_array("ffcastle", $editions)) echo( "checked"); ?>>
                                    Familiar Castle                                
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fwf2012" value="fwf2012" <?php if(in_array("fwf2012", $editions)) echo( "checked"); ?>>
                                    Familiar Winter 2012
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fh2012" value="fh2012" <?php if(in_array("fh2012", $editions)) echo( "checked"); ?>>
                                    Familiar Hemelvaartsnacht 2012
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fff2012" value="fff2012" <?php if(in_array("fff2012", $editions)) echo( "checked"); ?>>
                                    Familiar Forest 2012
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fh2013" value="fh2013" <?php if(in_array("fh2013", $editions)) echo( "checked"); ?>>
                                    Familiar Hemelvaartsnacht 2013
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fwf2013" value="fwf2013" <?php if(in_array("fwf2013", $editions)) echo( "checked"); ?>>
                                    Familiar Winter 2013
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fff2013" value="fff2013" <?php if(in_array("fff2013", $editions)) echo( "checked"); ?>>
                                    Familiar Forest 2013
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fwf2014" value="fwf2014" <?php if(in_array("fwf2014", $editions)) echo( "checked"); ?>>
                                    Familiar Winter 2014
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fff2014" value="fff2014" <?php if(in_array("fff2014", $editions)) echo( "checked"); ?>>
                                    Familiar Forest 2014
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fwf2015" value="fwf2015" <?php if(in_array("fwf2015", $editions)) echo( "checked"); ?>>
                                    Familiar Winter 2015
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fff2015" value="fff2015" <?php if(in_array("fff2015", $editions)) echo( "checked"); ?>>
                                    Familiar Forest 2015
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fff2016" value="fff2016" <?php if(in_array("fff2016", $editions)) echo( "checked"); ?>>
                                    Familiar Forest 2016
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <div class="form-group row">
                    <label class="col-sm-2">Email Instellingen</label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="allow_email" id="male" value="Y" <?php if($allow_email) echo( "checked"); ?>>
                                Ik wil graag email ontvangen over toekomstige evenementen van Stichting Familiar Forest.
                            </label>
                        </div>
                    </div>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Versturen</button>
            </form>
        </div>
        <script src="js/vendor/modernizr-2.8.3.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
        <scirpt src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/localization/messages_nl.js"></script>
        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.3.min.js"><\/script>')</script>
        <script src="js/vendor/bootstrap.min.js"></script>
        <script src="js/vendor/bootstrap-datepicker.js"></script>
        <script src="js/vendor/bootstrap-datepicker.nl.min.js"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>
        <script src="js/create.js"></script>
    </body>
</html>
