<?php
include_once "../model/signup.php";
include_once "../model/loginmanager.php";
include_once "../model/contribution.php";
include_once "../model/act.php";
include_once "../model/user.php";
model\LoginManager::Instance()->isLoggedIn();
if( model\LoginManager::Instance()->getPermissions() & PERMISSION_PARICIPANT != PERMISSION_PARICIPANT ) {
    header('Location: oops');
}

date_default_timezone_set('Europe/Amsterdam');

$returnVal = "";
$signup = model\Signup::findByPersonAndEvent(model\LoginManager::Instance()->person, model\Event::getCurrentEvent());
if( !$signup ) {
    $signup = new model\Signup();
}
$terms0 = $terms1 = $terms2 = $terms3 = "";
$preparationsbox = false;

if( $_SERVER["REQUEST_METHOD"] == "POST") {
    $returnVal = "";
    if( !empty($_POST["contrib0"])) {
        $signup->contrib0->id = test_input($_POST["contrib0"]);
    } else {
        //impossible
    }
    if( !empty($_POST["contrib0desc"])) {
        $signup->contrib0->description = test_input($_POST["contrib0desc"]);
    } else {
        $signup->contrib0->description = "";
    }

    if( !empty($_POST["contrib1"])) {
        $signup->contrib1->id  = test_input($_POST["contrib1"]);
    } else {
        //impossible
    }
    if( !empty($_POST["contrib1desc"])) {
        $signup->contrib1->description = test_input($_POST["contrib1desc"]);
    } else {
        $signup->contrib1->description = "";
    }

    if( !empty($_POST["motivation"])) {
        $signup->motivation = test_input($_POST["motivation"]);
    } else {
        $signup->motivation = "";
    }

    if( !empty($_POST["question"])) {
        $signup->question = test_input($_POST["question"]);
        
    } else {
        $signup->question = "";
    }

    if( !empty($_POST["partner"])) {
        $partner = test_input($_POST["partner"]);
        if( !filter_var($partner, FILTER_VALIDATE_EMAIL)) {
            addError("Het email adres van je lieveling is niet geldig");
        }
        $signup->partner = model\Person::findByEmail($partner);
    } else {
        $partner = "";
    }

    if( !empty($_POST["preparationsbox"])) {
        $preparationsbox = true;
        if( !empty($_POST["preparations"])) {
            $signup->preparations = test_input($_POST["preparations"]);
        } else {
            $signup->preparations = "J";
        }
    } else {
        $preparationsbox = false;
        $signup->preparations = "N";
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

    $signup->terms = false;
    if( $terms0 == "" || $terms1 == "" || $terms2 == "" || $terms3 == "") {
        addError("Je moet alle voorwaarden accepteren");
    } else {
        $signup->terms = true;
    }

    if( $returnVal == "" ) {
        $signup->contrib0->save();
        $signup->contrib1->save();
        $signup->save();
        
        //TODO send mail
    } else {
        //try again..
    }
    if( $returnVal == "") {
        header('Location: success');
    }
} else { //End POST
    if( $signup->preparations == "N" ) {
        $preparationsbox = FALSE;
        $signup->preparations = "";
    } else {
        $preparationsbox = TRUE;
    }
    if( $signup->id != NULL ) {
        $returnVal = '<div class="alert alert-success" role="alert">We hebben al een inschrijving van je ontvangen. Als je wilt kun je die hier aanpassen.</div>';
    }
} 

function addError($value) {
    global $returnVal;
    $returnVal .= '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ' . $value . '</div>';
}

?>
<html class="no-js" lang="">
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
                <div class="col-xs-13 col-sm-10">  
                <div class="form-intro-text">
                <h1>Inschrijven Back to the FFFuture: '95</h1>
                <p class="lead">
                    27 & 28 april 2018.
                </p>
                <p>
                    Vul dit formulier zo volledig mogelijk in. Ook nadat je het formulier hebt verstuurd, kun je nog tot en met 8 maart 2018 je antwoorden wijzigen. Pas op 9 maart 2018 maken wij je inschrijving definitief. Heb je hulp nodig of wil je meer informatie over het inschrijven? Dan kun je mailen naar: <?php echo $mailtolink ?>
                </p>
            </div>
            <?php echo $returnVal; ?>

            <form id="signup-form" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">
                
                <fieldset>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="motivation">Waarom wil je naar Back to the FFFuture: '95?</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="motivation" id="motivation" cols="60" rows="4"><?php echo $signup->motivation; ?></textarea>
                            <label for="motivation">Max 1024 karakters</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="question">Wat stel je je voor bij '95?</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="question" id="question" cols="60" rows="4"><?php echo $signup->question; ?></textarea>
                            <label for="question">Max 1024 karakters</label>
                        </div>
                    </div>
                </fieldset>
                <div class="form-group row">
                    <label class="col-sm-2" for="partner">Lieveling<br>Email</label>
                    <div class="col-sm-10">
                        <div class='input-group'>
                            <input class="form-control" type="email" name="partner" id="partner" placeholder="Lieveling" value="<?php echo $signup->partner != NULL ? $signup->partner->email : ""; ?>">
                            <span class="partnercheck working input-group-addon">
                                <span class="glyphicon glyphicon-question-sign"></span>
                            </span>
                        </div>
                        <div id='partnerdefault' class="alert alert-success">
                            Je kunt voor deze editie wederom je beste vriend, vriendin, partner, kind of oma opgeven waarmee jij naar Familiar wilt! <br>
                            Je lieveling moet het email adres invullen waarmee jij je registreert hebt en jij het emailadres waarmee jouw lieveling zich inschrijft. Als deze niet overeen komen, kunnen wij jullie niet aan elkaar linken. <strong>Let op: Als jullie van deze optie gebruik maken worden 
                                jullie samen ingeloot <i>of beide uitgeloot.</i> </strong>
                        </div>
                        <div id='partnersuccess' class="alert alert-success">
                            In de inschrijving van dit email adres staat ook die van jouw. Helemaal in orde dus!
                        </div>
                        <div id='partnernosignup' class="alert alert-info">
                            We hebben op dit moment nog geen inschrijving ontvangen op dit email adres. Misschien heb je een typfoutje gemaakt of heeft je lieveling zich nog niet ingeschreven.
                        </div>
                        <div id='partnernotsame' class="alert alert-info">
                            Uh oh! We houden er niet van om ons in relaties te mengen maar in de inschrijving van je lieveling staat iets anders dan jouw email adres! Hij of zij heeft vast een typfoutje gemaakt of iets dergelijks. Dubbelcheck dit nog even want op dit moment kunnen we jullie niet aan elkaar linken.
                        </div>
                    </div>
                </div>
                <fieldset>
                    <legend>Hoe wil jij bijdragen aan het Familiar?</legend>
                    <div class="form-group row">
                        <label for="contrib0" class="col-sm-2 form-control-label">Eerste keus</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="contrib0" id="contrib0">
                            <?php forEach(model\ShiftType::getAll() as $shifttype) {
                                echo "<option value='".$shifttype->id ."'". ($signup->contrib0->id == $shifttype->id ? 'selected=selected' : '').">".$shifttype->name."</option>";
                            }?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="contrib0desc">
                        		Vertel iets over je ervaring hierin</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="contrib0desc" id="contrib0desc" cols="60" rows="4">
                            <?php echo $signup->contrib0->description; ?></textarea>
                            <label id="contrib0counter" for="contrib0desc">Max 1024 karakters</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="contrib0" class="col-sm-2 form-control-label">Tweede keus</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="contrib1" id="contrib1">
                                <?php forEach(model\ShiftType::getAll() as $shifttype) {
                                    echo "<option value='".$shifttype->id."'". ($signup->contrib1->id == $shifttype->id ? 'selected=selected' : '').">".$shifttype->name."</option>";
                            }?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="contrib1desc">Vertel iets over je ervaring hierin</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="contrib1desc" id="contrib1desc" cols="60" rows="4">
                            <?php echo $signup->contrib1->description; ?></textarea>
                            <label id="contrib1counter" for="contrib1desc">Max 1024 karakters</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="preparations">Voorbereidingen</label>
                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label for="preparationsbox">
                                    <input class="checkbox" type="checkbox" id="preparationsbox" name="preparationsbox" 
                                    <?php if($preparationsbox) echo( "checked"); ?>>
                                    Ik vind het leuk om te helpen in de voorbereidingen
                                </label>
                            </div>
                            <div class="alert alert-success" id="prepinfo">We zijn altijd op zoek naar enthoursiastelingen die ons willen helpen bij de voorbereidingen voor Famliar Forest. Lijkt het je leuk om ons hierbij te helpen?</div>
                            <div class="alert alert-success" id="prepintro">Te gek! Wat zou je leuk vinden om te doen?</div>
                            <textarea class="form-control" name="preparations" id="preparations" cols="60" rows="4">
                            <?php echo $signup->preparations; ?></textarea>
                            <label id="prepcounter" for="preparations">Max 1024 karakters</label>
                        </div>
                    </div>
                </fieldset>
                    
                <fieldset>
                    <legend>Voorwaarden</legend>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="terms3">Telefoon en Foto's</label>
                        <div class="col-sm-10">
                            <div class="alert alert-warning">Familiar Forest zorgt voor een professionele fotograaf. Om de sfeer te verhogen en het contact tussen deelnemers te stimuleren is het niet toegestaan een telefoon of camera mee te nemen op het terrein van Familiar Forest.</div>
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
                            <div class="alert alert-warning">Familiar Forest is een reis. De locatie verplicht de deelnemer om zich te kunnen identificeren en een aansprakelijkheidsverzekerd te hebben.</div>
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
                            <div class="alert alert-warning">Tijdens Familiar Forest is de deelnemer verantwoordelijk voor zijn eigen gezondheid. Als deelnemer is het niet mogelijk Familiar Forest aansprakelijk te stellen voor materiële en immateriële schade. <i>Je kunt deze verzekeren door een reisverzekering af te sluiten</i></div>
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
                        <label class="col-sm-2 form-control-label" for="terms3">Kaart verkoop</label>
                        <div class="col-sm-10">
                            <div class="alert alert-warning">Aanmeldingen en toegangsbewijzen zijn persoonlijk en mogen niet zelf door de deelnemer worden doorverkocht.</div>
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
                <button class="btn btn-lg btn-primary btn-block" type="submit">
                    <?php if( !$is_save ) { 
                        echo '<i class="fa fa-paper-plane"></i> Versturen';
                    } else {
                        echo '<i class="glyphicon glyphicon-floppy-disk"></i> Opslaan';
                    }
                    ?>
                </button>
            </form>
        </div>
    </div>
</div>
	</div>
        <?php include("form-js.html"); ?>
        <script src="js/signup.js"></script>
    </body>
</html>
