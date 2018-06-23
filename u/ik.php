<?php
include_once "../model/loginmanager.php";
include_once "../model/person.php";
include_once "../model/user.php";
model\LoginManager::Instance()->isLoggedIn();
if( model\LoginManager::Instance()->getPermissions() & PERMISSION_PARICIPANT != PERMISSION_PARICIPANT ) {
    header('Location: oops');
}

date_default_timezone_set('Europe/Amsterdam');
$person = model\LoginManager::Instance()->person;
$returnVal = "";
if( $_SERVER["REQUEST_METHOD"] == "POST") {
    if( !empty($_POST["street"]) ) {
        $person->street = ($_POST["street"]);
    } else {
        $person->street = "";
    }
    if( !empty($_POST["postal"]) ) {
        $person->postal = ($_POST["postal"]);
    } else {
        $person->postal = "";
    }
    if( !empty($_POST["city"]) ) {
        $person->city = ($_POST["city"]);
    } else {
        $person->city = "";
        addError("Je hebt je woonplaats niet opgegeven.");
    }
    if( !empty($_POST["birthdate"]) ) {
        $birthdate_str = ($_POST["birthdate"]);
        $date = DateTime::createFromFormat('d/m/Y', $birthdate_str);
        if( $date == FALSE ) {
            if( ($timestamp = strtotime($birthdate_str)) == FALSE ) {
                addError("De opgegeven geboortedatum klopt niet.");
            } else {
                $person->birthdate = date( 'Y-m-d H:i:s', $timestamp );
            }
        } else {
            $person->birthdate = $date->format('Y-m-d H:i:s');
        }
    } else {
        addError("Je hebt je geboortedatum niet opgegeven");
    }
    if( !empty($_POST["gender"]) ) {
        $person->gender = ($_POST["gender"]);
    } else {
        $person->gender = "";
        addError("Je hebt je geslacht niet opgegeven.");
    }
    if( !empty($_POST["phone"]) ) {
        $person->phone = ($_POST["phone"]);
    } else {
        $person->phone = "";
        addError("Je hebt geen telefoonnummer opgegeven");
    }

    if( !empty($_POST["allow_email"]) ) {
        $person->allow_email = $_POST["allow_email"] == 'Y';
    } else {
        $email_future = false;
    }

    if( $returnVal == "" ) {
        $person->save();
    } else {
        //try again..
    }
    if( $returnVal == "") {
        $returnVal .= '<div class="alert alert-success" role="alert"><span class="glyphicon glyphicon-save" aria-hidden="true"></span> Je gegevens zijn opgeslagen</div>';
    } else {
    }
} //End POST
function addError($value) {
    global $returnVal;
    $returnVal .= '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ' . $value . '</div>';
}
?>

<!doctype html>
<html class="no-js" lang="">
    <?php include ("head.html"); ?>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div class="page-container">
        <?php include("header.php"); ?>
            
            <div class="container">
                <div class="row row-offcanvas row-offcanvas-left">
                    <?php include("navigation.php");?>
                    <div class="col-xs-13 col-sm-10"> 
                        <div class="form-intro-text">

                        </div>
                       
                        <?php echo $returnVal; ?>
                        <form id="edit-form" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">
                            <div class="form-group row">
                                <label for="email" class="col-sm-2 form-control-label">Email*</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="email" id="email" placeholder="Email" value="<?php echo $person->email;?>" name="email" disabled>
                                </div>
                            </div>
                            <fieldset>
                            <div class="form-group row">
                                <label for="firstname" class="col-sm-2 form-control-label">Voornaam*</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="text" id="firstname" placeholder="Voornaam" value="<?php echo $person->firstname;?>" name="firstname" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="lastname" class="col-sm-2 form-control-label">Achternaam*</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="text" id="lastname" placeholder="Achternaam" value="<?php echo $person->lastname;?>" name="lastname" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="street" class="col-sm-2 form-control-label">Straatnaam & Huisnummer</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="text" id="street" placeholder="Straatnaam & Huisnummer" value="<?php echo $person->street;?>" name="street">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="postal" class="col-sm-2 form-control-label">Postcode</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="text" id="postal" placeholder="Postcode" value="<?php echo $person->postal;?>" name="postal">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="city" class="col-sm-2 form-control-label">Woonplaats*</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="text" id="city" placeholder="Woonplaats" value="<?php echo $person->city;?>" name="city">
                                </div>
                            </div>
                        
                            <div class="form-group row">
                                <label for="birthdate" class="col-sm-2 form-control-label">Geboortedatum*<br>(dd/mm/yyyy)</label>
                                <div class="col-sm-10">
                                    <input class="form-control ignore datepicker" type="text" id="birthdate" value="<?php echo $person->birthdate->format('d/m/Y');?>" name="birthdate" placeholder="dd/mm/yyyy">
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
                                            <input type="radio" name="gender" id="male" value="male" <?php if($person->gender == "M") echo( "checked"); ?>>
                                            Jongeman
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="gender" id="female" value="female" <?php if($person->gender == "F") echo( "checked"); ?> >
                                            Jongedame
                                        </label>
                                    </div>
                                    <label for="gender" class="error" style="display:none;"></label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="phone" class="col-sm-2 form-control-label">Telefoonnummer*</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="text" id="phone" placeholder="Telefoonnummer" value="<?php echo $person->phone;?>" name="phone">
                                </div>
                            </div>
                            </fieldset>
                          
                            <div class="form-group row">
                                <label class="col-sm-2 form-control-label" for="email">Email:</label>
                                <div class="col-sm-10">
                                    <div class="checkbox">
                                        <label>
                                            <input class="checkbox" type="checkbox" name="allow_email" value="Y" <?php if($person->allow_email) echo( "checked"); ?>>
                                            Ik wil graag op de hoogte worden gehouden van toekomstige Familiar edities
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-lg btn-primary btn-block" type="submit"><i class="glyphicon glyphicon-floppy-disk"></i> Opslaan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php include("form-js.html");?>
        <script src="js/ik.js"></script>
    </body>
</html>
