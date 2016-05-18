<?php session_start();

include_once "dbstore.php";
include_once "functions.php";

date_default_timezone_set('Europe/Amsterdam');

if( strtotime('now') > strtotime('2016-06-08 16:00') ) {
    header('Location: verlopen');
}

$signupround = 1;

$returnVal = "";
$firstname = $lastname = $birthdate = $gender = $email = $phone = $city = $editions_str = $nr_editions = $contrib0 = $contrib1 = $contrib0desc = $contrib1desc = $act0type = $act0desc = $act0need = $act1type = $act1desc = $act1need = $partner = $motivation = $familiar = $preparations = $terms0 = $terms1 = $terms2 = $terms3 = "";
$preparationsbox = false;
$editions = array();

if( $_SERVER["REQUEST_METHOD"] == "GET") {
    if(!empty($_GET["email"]) ) {
        $email = test_input($_GET["email"]);
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        $query = sprintf("SELECT p.lastname, p.firstname, p.birthdate, p.gender, p.city, p.phone, p.motivation, p.familiar, 
                    p.editions, p.partner, c0.type as `c0type`, c0.description as `c0desc`, c0.needs as `c0needs`, c1.type as `c1type`, c1.description as `c1desc`, c1.needs as `c1needs`, p.preparations, 
                    p.visits
            FROM person p join contribution c0 on p.contrib0 = c0.id join contribution c1 on p.contrib1 = c1.id
            WHERE p.email = '%s'", $mysqli->real_escape_string($email));
        $sqlresult = $mysqli->query($query);
        if($sqlresult === FALSE ) {
            addError("We konden helaas niet je gegevens ophalen uit de database.".$mysqli->error);
        } else if( $sqlresult->num_rows == 0 ) {
            addError("We hebben geen inschrijving kunnen vinden onder het opgegeven email adres.");
        } else {
            $row = $sqlresult->fetch_array(MYSQLI_ASSOC);
            $firstname = htmlspecialchars_decode($row['firstname']);
            $lastname = htmlspecialchars_decode($row['lastname']);
            $birthdate = htmlspecialchars_decode($row['birthdate']);
            $gender = htmlspecialchars_decode($row['gender']);
            $city = htmlspecialchars_decode($row['city']);
            $phone = htmlspecialchars_decode($row['phone']);
            $motivation = htmlspecialchars_decode($row['motivation']);
            $familiar = htmlspecialchars_decode($row['familiar']);
            $editions_str = htmlspecialchars_decode($row['editions']);
            $editions = explode(",", $editions_str);
            $nr_editions = htmlspecialchars_decode($row['visits']);
            $partner = htmlspecialchars_decode($row['partner']);
            $preparations = htmlspecialchars_decode($row['preparations']);
            if( $preparations != "N") {
                $preparationsbox = true;
            } else {
                $preparations = "";
            }

            $contrib0 = htmlspecialchars_decode($row['c0type']);
            if( in_array($contrib0, ['iv','bar','keuken','afb'])) {
                $contrib0desc = htmlspecialchars_decode($row['c0desc']);    
            } else {
                $act0type = $contrib0;
                $contrib0 = 'act';
                $act0desc = htmlspecialchars_decode($row['c0desc']);
                $act0need = htmlspecialchars_decode($row['c0needs']);    
            }
        
            $contrib1 = htmlspecialchars_decode($row['c1type']);
            if( in_array($contrib1, ['iv','bar','keuken','afb'])) {
                $contrib1desc = htmlspecialchars_decode($row['c1desc']);    
            } else {
                $act1type = $contrib1;
                $contrib1 = 'act';
                $act1desc = htmlspecialchars_decode($row['c1desc']);
                $act1need = htmlspecialchars_decode($row['c1needs']);
            }
        }
        $mysqli->close();
    }
}

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

    $nr_editions = 0;
    $editions = isset($_POST['editions']) ? $_POST['editions'] : array();
    foreach($editions as $edition) {
        $editions_str .= test_input($edition) . ",";
        $nr_editions += 1;
    }

    if( !empty($_POST["contrib0"])) {
        $contrib0 = test_input($_POST["contrib0"]);
    } else {
        //impossible
    }
    if( !empty($_POST["contrib0desc"])) {
        $contrib0desc = test_input($_POST["contrib0desc"]);
    } else {
        $contrib0desc = "";
    }

    if( !empty($_POST["contrib1"])) {
        $contrib1 = test_input($_POST["contrib1"]);
    } else {
        //impossible
    }
    if( !empty($_POST["contrib1desc"])) {
        $contrib1desc = test_input($_POST["contrib1desc"]);
    } else {
        $contrib1desc = "";
    }

    if( !empty($_POST["act0type"])) {
        $act0type = test_input($_POST["act0type"]);
    } else {
        $act0type = "";
    }

    if( !empty($_POST["act0desc"])) {
        $act0desc = test_input($_POST["act0desc"]);
    } else {
        $act0desc = "";
    }

    if( !empty($_POST["act0need"])) {
        $act0need = test_input($_POST["act0need"]);
    } else {
        $act0need = "";
    }

    if( !empty($_POST["act1type"])) {
        $act1type = test_input($_POST["act1type"]);
    } else {
        $act1type = "";
    }
    
    if( !empty($_POST["act1desc"])) {
        $act1desc = test_input($_POST["act1desc"]);
    } else {
        $act1desc = "";
    }

    if( !empty($_POST["act1need"])) {
        $act1need = test_input($_POST["act1need"]);
    } else {
        $act1need = "";
    }

    if( !empty($_POST["motivation"])) {
        $motivation = test_input($_POST["motivation"]);
    } else {
        $motivation = "";
    }

    if( !empty($_POST["familiar"])) {
        $familiar = test_input($_POST["familiar"]);
    } else {
        $familiar = "";
    }

    $db_contrib0 = $db_contrib0_desc = $db_contrib0_need = "";
    $db_contrib1 = $db_contrib1_desc = $db_contrib1_need = "";

    if( $contrib0 == "act" ) {
        $db_contrib0 = $act0type;
        $db_contrib0_desc = $act0desc;
        $db_contrib0_need = $act0need;
    } else {
        $db_contrib0 = $contrib0;
        $db_contrib0_desc = $contrib0desc;
    }

    if( $contrib1 == "act" ) {
        $db_contrib1 = $act1type;
        $db_contrib1_desc = $act1desc;
        $db_contrib1_need = $act1need;
    } else {
        $db_contrib1 = $contrib1;
        $db_contrib1_desc = $contrib1desc;
    }

    if( !empty($_POST["partner"])) {
        $partner = test_input($_POST["partner"]);
        if( !filter_var($partner, FILTER_VALIDATE_EMAIL)) {
            addError("Het email adres van je lieveling is niet geldig");
        }
    } else {
        $partner = "";
    }

    if( !empty($_POST["preparationsbox"])) {
        $preparationsbox = true;
        if( !empty($_POST["preparations"])) {
            $preparations = test_input($_POST["preparations"]);
        } else {
            $preparations = "J";
        }
    } else {
        $preparationsbox = false;
        $preparations = "N";
    }

    if( !empty($_POST["terms0"])) {
        $terms0 = test_input($_POST["terms0"]);
    } else {
        $terms0 = "";
    }
    if( !empty($_POST["terms1"])) {
        $terms1 = test_input($_POST["terms1"]);
    } else {
        $terms1 = "";
    }
    if( !empty($_POST["terms2"])) {
        $terms2 = test_input($_POST["terms2"]);
    } else {
        $terms2 = "";
    }

    if( !empty($_POST["terms3"])) {
        $terms3 = test_input($_POST["terms3"]);
    } else {
        $terms3 = "";
    }

    if( $terms0 == "" || $terms1 == "" || $terms2 == "" || $terms3 == "") {
        addError("Je moet alle voorwaarden accepteren");
    }

    if( $returnVal == "" ) {
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        $query = sprintf("SELECT * FROM person WHERE email = '%s'",
            $mysqli->real_escape_string($email));
        $sqlresult = $mysqli->query($query);
        if( $sqlresult === FALSE ) {
            addError("Helaas konden we je gegevens niet opslaan, probeer het later nog eens of mail naar: ".$mailtolink);
            email_error("Error getting user to determine update: ".$mysqli->error);
        } else if( $sqlresult->num_rows == 0 ) {
            $db_error = storeSignup($email, $firstname, $lastname, $birthdate, $city, $gender, $phone, $nr_editions, $editions_str, $partner, $motivation, $familiar, $db_contrib0, $db_contrib1, $db_contrib0_desc, $db_contrib1_desc, $db_contrib0_need, $db_contrib1_need, $preparations, $terms0, $terms1, $terms2, $terms3, $signupround);
        } else {
            $db_error = updateSignup($email, $firstname, $lastname, $birthdate, $city, $gender, $phone, $nr_editions, $editions_str, $partner, $motivation, $familiar, $db_contrib0, $db_contrib1, $db_contrib0_desc, $db_contrib1_desc, $db_contrib0_need, $db_contrib1_need, $preparations, $terms0, $terms1, $terms2, $terms3, $signupround);
        }
        if( $db_error != "" ) {
            addError($db_error);
        }
        $mysqli->close();
    } else {
        //try again..
    }
    if( $returnVal == "") {
        $_SESSION['success_email'] = $email;
        header('Location: success');
    } else {
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
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Familiar Forest Inschrijfformulier</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <link rel="icon" href="favicon.ico">
        <!-- Place favicon.ico in the root directory -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/bootstrap-datepicker3.css">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" type="text/css" media="all"
            href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/smoothness/jquery-ui.css"/>

        <script src="js/vendor/modernizr-2.8.3.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
        <scirpt src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/localization/messages_nl.js"></script>
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
        <script src="js/signup.js"></script>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <div class="container">
            <div class="form-intro-text">
                <h1>Inschrijven Familiar Forest 2016: Nieuw Babylon</h1>
                <p class="lead">
                    10 en 11 september 2016
                </p>
                <p>
                    Vul het zo volledig mogelijk in, als je wat langer wilt nadenken over bepaalde velden kan dat. Het inschrijfformulier blijft tot 8 Juni 2016 beschikbaar. Heb je hulp nodig? Of wil je meer informatie over het inschrijven dan kun je mailen naar: <?php echo $mailtolink ?>
                </p>
                <p>
                    Velden gemarkeerd met een * zijn verplicht.
                </p>
            </div>
            <?php echo $returnVal; ?>

            <form id="signup-form" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">
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
                    <label for="birthdate" class="col-sm-2 form-control-label">Geboortedatum*</label>
                    <div class="col-sm-10">
                        <input class="form-control ignore datepicker" type="text" id="birthdate" value="<?php echo $birthdate;?>" name="birthdate">
                        <div><label for="birthdate" class="error" style="display:none;"></label></div>
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
                    <label for="email" class="col-sm-2 form-control-label">Email*</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="email" id="email" placeholder="Email" value="<?php echo $email;?>" name="email">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="phone" class="col-sm-2 form-control-label">Telefoonnummer*</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="phone" placeholder="Telefoonnummer" value="<?php echo $phone;?>" name="phone">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2" for="partner">Lieveling<br>Email</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="email" name="partner" id="partner" placeholder="Lieveling" value="<?php echo $partner; ?>">
                        <div class="alert alert-info">
                            Het kan zijn dat je bij de eerste inschrijving een typfout gemaakt hebt in het emailadres van je lieveling of 
                            je was je vergeten in te schrijven en daardoor jullie niet samen zijn ingeloot. We doen ons best om in deze gevallen 
                            alsnog lievelingen te koppelen maar helaas kunnen we niet garanderen dat je alsnog wordt ingeloot bij je lieveling.
                        </div>
                        <div class="alert alert-success">
                            Vanaf dit jaar kun je voor het eerst je beste vriend, vriendin, partner, kind of oma opgeven waarmee jij naar 
                            Familiar Forest wilt! Het is belangrijk dat jij zijn of haar email adres correct invult en andersom! 
                            <strong>Communiceer dit dus samen goed naar elkaar! En let op: Als jullie van deze optie gebruik maken worden 
                                jullie samen ingeloot <i>of beide uitgeloot</i></strong>
                        </div>
                    </div>
                </div>

                <fieldset>
                    <legend>Waarom wil jij naar Familiar Forest 2016?</legend>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="motivation">Motivatie</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="motivation" id="motivation" cols="60" rows="4"><?php echo $motivation; ?></textarea>
                            <label for="motivation">Max 1024 karakters</label>
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
                        </div>
                    </div>
                </fieldset>
                        
                <fieldset>
                    <legend>Jouw bijdrage aan het Familiar Forest 2016</legend>
                    <div class="form-group row">
                        <label for="contrib0" class="col-sm-2 form-control-label">Eerste keus</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="contrib0" id="contrib0">
                                <option value="iv" <?= $contrib0 == 'iv' ? ' selected="selected"' : '';?>>Interieur verzorging</option>
                                <option value="bar" <?= $contrib0 == 'bar' ? ' selected="selected"' : '';?>>Bar</option>
                                <option value="keuken" <?= $contrib0 == 'keuken' ? ' selected="selected"' : '';?>>Keuken</option>
                                <option value="act" <?= $contrib0 == 'act' ? ' selected="selected"' : '';?>>Act of Performance</option>
                                <option value="afb" <?= $contrib0 == 'afb' ? ' selected="selected"' : '';?>>Afbouw</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="contrib0desc">Vertel iets over je ervaring hierin</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="contrib0desc" id="contrib0desc" cols="60" rows="4"><?php echo $contrib0desc; ?></textarea>
                            <label id="contrib0counter" for="contrib0desc">Max 1024 karakters</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="act0type">Informatie over je act of performance</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="act0type" id="act0type">
                                <option value="workshop" <?= $act0type == 'workshop' ? ' selected="selected"' : '';?>>Workshop / Cursus</option>
                                <option value="game" <?= $act0type == 'game' ? ' selected="selected"' : '';?>>Ervaring / Game</option>
                                <option value="lecture" <?= $act0type == 'lecture' ? ' selected="selected"' : '';?>>Lezing</option>
                                <option value="schmink" <?= $act0type == 'schmink' ? ' selected="selected"' : '';?>>Schmink</option>
                                <option value="other" <?= $act0type == 'other' ? ' selected="selected"' : '';?>>Anders</option>
                                <option value="perform" <?= $act0type == 'perform' ? ' selected="selected"' : '';?>>Performance</option>
                                <option value="install" <?= $act0type == 'install' ? ' selected="selected"' : '';?>>Installatie / Beeld</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="act0desc">Omschrijving van je act</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="act0desc" id="act0desc" cols="60" rows="4"><?php echo $act0desc; ?></textarea>
                            <label for="act0desc">Max 1024 karakters</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="act0need">Wat heb je voor je act nodig?</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="act0need" id="act0need" cols="60" rows="4"><?php echo $act0need; ?></textarea>
                            <label for="act0need">Max 1024 karakters</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="contrib0" class="col-sm-2 form-control-label">Tweede keus</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="contrib1" id="contrib1">
                                <option value="iv" <?= $contrib1 == 'iv' ? ' selected="selected"' : '';?>>Interieur verzorging</option>
                                <option value="bar" <?= $contrib1 == 'bar' ? ' selected="selected"' : '';?>>Bar</option>
                                <option value="keuken" <?= $contrib1 == 'keuken' ? ' selected="selected"' : '';?>>Keuken</option>
                                <option value="act" <?= $contrib1 == 'act' ? ' selected="selected"' : '';?>>Act of Performance</option>
                                <option value="afb" <?= $contrib1 == 'afb' ? ' selected="selected"' : '';?>>Afbouw</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="contrib1desc">Vertel iets over je ervaring hierin</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="contrib1desc" id="contrib1desc" cols="60" rows="4"><?php echo $contrib1desc; ?></textarea>
                            <label id="contrib1counter" for="contrib1desc">Max 1024 karakters</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="act1type">Informatie over je act of performance</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="act1type" id="act1type">
                                <option value="workshop" <?= $act1type == 'workshop' ? ' selected="selected"' : '';?>>Workshop / Cursus</option>
                                <option value="game" <?= $act1type == 'game' ? ' selected="selected"' : '';?>>Ervaring / Game</option>
                                <option value="lecture" <?= $act1type == 'lecture' ? ' selected="selected"' : '';?>>Lezing</option>
                                <option value="schmink" <?= $act1type == 'schmink' ? ' selected="selected"' : '';?>>Schmink</option>
                                <option value="other" <?= $act1type == 'other' ? ' selected="selected"' : '';?>>Anders</option>
                                <option value="perform" <?= $act1type == 'perform' ? ' selected="selected"' : '';?>>Performance</option>
                                <option value="install" <?= $act1type == 'install' ? ' selected="selected"' : '';?>>Installatie / Beeld</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="act1desc">Omschrijving van je act</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="act1desc" id="act1desc" cols="60" rows="4"><?php echo $act1desc; ?></textarea>
                            <label for="act1desc">Max 1024 karakters</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="act1need">Wat heb je voor je act nodig?</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="act1need" id="act1need" cols="60" rows="4"><?php echo $act1need; ?></textarea>
                            <label for="act1need">Max 1024 karakters</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="preparations">Voorbereidingen</label>
                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label for="preparationsbox">
                                    <input class="checkbox" type="checkbox" id="preparationsbox" name="preparationsbox" <?php if($preparationsbox) echo( "checked"); ?>>
                                    Ik vind het leuk om te helpen in de voorbereidingen
                                </label>
                            </div>
                            <div class="alert alert-success" id="prepinfo">We zijn altijd op zoek naar enthoursiastelingen die ons willen helpen bij de voorbereidingen voor Famliar Forest. Lijkt het je leuk om ons hierbij te helpen?</div>
                            <div class="alert alert-success" id="prepintro">Te gek! Wat zou je leuk vinden om te doen?</div>
                            <textarea class="form-control" name="preparations" id="preparations" cols="60" rows="4"><?php echo $preparations; ?></textarea>
                            <label id="prepcounter" for="preparations">Max 1024 karakters</label>
                        </div>
                    </div>
                </fieldset>
                    
                <fieldset>
                    <legend>Voorwaarden</legend>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="terms3">Telefoon en Foto's</label>
                        <div class="col-sm-10">
                            <div class="alert alert-warning">Familiar Forest zorgt voor een professionele fotograaf. Om de sfeer te verhogen en het contact tussen deelnemers te verbeteren is het niet toegestaan een telefoon of camera mee te nemen op het terrein van Familiar Forest 2016.</div>
                            <div class="checkbox">
                                <label>
                                    <input class="checkbox" type="checkbox" id="terms0" name="terms0" value="J">
                                    Ik ga akkoord met deze voorwaarde
                                </label>
                            </div>
                            <label for="terms0" class="error" style="display:none;"></label>
                        </div>
                    </div>

                    <div class="form-group row">
                        
                        <label class="col-sm-2 form-control-label" for="terms1">Verzekering</label>
                        <div class="col-sm-10">
                            <div class="alert alert-warning">Familiar Forest 2016 is een reis. De locatie verplicht de deelnemer om zich te kunnen identificeren en minimaal WA verzekerd te zijn.</div>
                            <div class="checkbox">
                                <label>
                                    <input class="checkbox" type="checkbox" id="terms1" name="terms1" value="J">
                                    Ik ga akkoord met deze voorwaarde
                                </label>
                            </div>
                            <label for="terms1" class="error" style="display:none;"></label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="terms2">Gezondheid</label>
                        <div class="col-sm-10">
                            <div class="alert alert-warning">Tijdens Familiar Forest 2016 is de deelnemer voor zijn eigen gezondheid verantwoordelijk. Als deelnemer is het niet mogelijk Familiar Forest aansprakelijk te stellen voor materiële en immateriële schade. <i>Je kunt deze verzekeren door een reisverzekering af te sluiten</i></div>
                            <div class="checkbox">
                                <label>
                                    <input class="checkbox" type="checkbox" id="terms2" name="terms2" value="J">
                                    Ik ga akkoord met deze voorwaarde
                                </label>
                            </div>
                            <label for="terms2" class="error" style="display:none;"></label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="terms0">Kaart verkoop</label>
                        
                        <div class="col-sm-10">
                            <div class="alert alert-warning">Aanmeldingen en toegangsbewijzen zijn persoonlijk en mogen niet zelf door de deelnemer worden doorverkocht. Het is wel mogelijk om tussen 7 juli 2016 en 5 augustus 2016 het toegangsbewijs terug te verkopen aan stichting Familiar Forest.</div>
                            <div class="checkbox">
                                <label>
                                    <input class="checkbox" type="checkbox" id="terms3" name="terms3" value="J">
                                    Ik ga akkoord met deze voorwaarde
                                </label>
                            </div>
                            <label for="terms3" class="error" style="display:none;"></label>
                        </div>
                    </div>
                </fieldset>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Versturen</button>
            </form>
        </div>
    </body>
</html>
