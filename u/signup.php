<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_PARTICIPANT) != PERMISSION_PARTICIPANT ) {
    header('Location: oops.php');
}

date_default_timezone_set('Europe/Amsterdam');

$signupround = 0;

$returnVal = "";
$is_save = FALSE;
$contrib0 = $contrib1 = $contrib0desc = $contrib1desc = $act0type = $act0desc = $act0need = $act1type = $act1desc = $act1need = $partner = $motivation = $question = $preparations = $terms0 = $terms1 = $terms2 = $terms3 = $nightshift = "";
$preparationsbox = false;
$editions = array();

if( $_SERVER["REQUEST_METHOD"] == "POST") {
    $returnVal = "";
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

    if( !empty($_POST["question"])) {
        $question = test_input($_POST["question"]);

    } else {
        $question = "";
    }

    $db_contrib0 = $db_contrib0_desc = $db_contrib0_need = "";
    $db_contrib1 = $db_contrib1_desc = $db_contrib1_need = "";
    $is_act = false;
    if( $contrib0 == "act" ) {
        $is_act = true;
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

    if( !empty($_POST["nightshift"])) {
        $nightshift = test_input($_POST["nightshift"]);
    } else {
        $nightshift = "";
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

        //$query = sprintf("SELECT 1 FROM $current_table WHERE email = '%s' and (complete = 1 or valid = 1)",
        //    $mysqli->real_escape_string($user_email));
        $query = sprintf("SELECT 1 FROM $current_table WHERE email = '%s' and (complete = 1)",
            $mysqli->real_escape_string($user_email));
        $sqlresult = $mysqli->query($query);
        if( $sqlresult === FALSE ) {
            addError("Helaas konden we je gegevens niet opslaan, probeer het later nog eens of mail naar: ".$mailtolink);
            email_error("Error looking for user in buyer: ".$mysqli->error);
        } else if( $sqlresult->num_rows != 0 ) {
            addError("Zo te zien heb je al een ticket en daarom kun je op dit moment niet jezelf inschrijven. Voor meer informatie kun je mailen naar: ".$mailtolink);
        } else {
            $query = sprintf("SELECT * FROM $current_table WHERE email = '%s'",
                $mysqli->real_escape_string($user_email));
            $sqlresult = $mysqli->query($query);
            $query = "";
            $signupdate = date( 'Y-m-d H:i:s');
            $task = '';
            if( $is_act ) {
                $task = 'act';
            }
            if( $sqlresult->num_rows == 0 ) {
                $query = sprintf("INSERT INTO `$current_table` (`email`, `partner`, `motivation`, `question`, `contrib0_type`, `contrib0_desc`, `contrib0_need`, `contrib1_type`, `contrib1_desc`, `contrib1_need`, `preparations`, `round`, `signupdate`, `task`, `terms0`, `terms1`, `terms2`, `terms3`, `nightshift`) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',%s,'%s','%s','%s','%s','%s','%s','%s');",
                    $mysqli->real_escape_string($user_email),
                    $mysqli->real_escape_string($partner),
                    substr($mysqli->real_escape_string($motivation), 0, 1024),
                    substr($mysqli->real_escape_string($question), 0, 1024),
                    $mysqli->real_escape_string($db_contrib0),
                    substr($mysqli->real_escape_string($db_contrib0_desc), 0, 1024),
                    substr($mysqli->real_escape_string($db_contrib0_need), 0, 1024),
                    $mysqli->real_escape_string($db_contrib1),
                    substr($mysqli->real_escape_string($db_contrib1_desc), 0, 1024),
                    substr($mysqli->real_escape_string($db_contrib1_need), 0, 1024),
                    $mysqli->real_escape_string($preparations),
                    $mysqli->real_escape_string($signupround),
                    $mysqli->real_escape_string($signupdate),
                    $mysqli->real_escape_string($task),
                    $mysqli->real_escape_string($terms0),
                    $mysqli->real_escape_string($terms1),
                    $mysqli->real_escape_string($terms2),
                    $mysqli->real_escape_string($terms3),
                    $mysqli->real_escape_string($nightshift));
            } else {
                $query = sprintf("UPDATE `$current_table` SET `partner` = '%s', `motivation` = '%s', `question` = '%s', `contrib0_type` = '%s', `contrib0_desc` = '%s', `contrib0_need` = '%s', `contrib1_type` = '%s', `contrib1_desc` = '%s', `contrib1_need` = '%s', `preparations` = '%s', `round` = %s, `signupdate` = '%s', `terms0` = '%s', `terms1` = '%s', `terms2` = '%s', `terms3` = '%s', `task` = '%s', `nightshift` = '%s' WHERE `email` = '%s'",
                    $mysqli->real_escape_string($partner),
                    substr($mysqli->real_escape_string($motivation), 0, 1024),
                    substr($mysqli->real_escape_string($question), 0, 1024),
                    $mysqli->real_escape_string($db_contrib0),
                    substr($mysqli->real_escape_string($db_contrib0_desc), 0, 1024),
                    substr($mysqli->real_escape_string($db_contrib0_need), 0, 1024),
                    $mysqli->real_escape_string($db_contrib1),
                    substr($mysqli->real_escape_string($db_contrib1_desc), 0, 1024),
                    substr($mysqli->real_escape_string($db_contrib1_need), 0, 1024),
                    $mysqli->real_escape_string($preparations),
                    $mysqli->real_escape_string($signupround),
                    $mysqli->real_escape_string($signupdate),
                    $mysqli->real_escape_string($terms0),
                    $mysqli->real_escape_string($terms1),
                    $mysqli->real_escape_string($terms2),
                    $mysqli->real_escape_string($terms3),
                    $mysqli->real_escape_string($task),
                    $mysqli->real_escape_string($nightshift),
                    $mysqli->real_escape_string($user_email));
            }
            $result = $mysqli->query($query);
            if( !$result ) {
                addError($mysqli->error."We hebben niet je gegevens kunnen opslaan. Probeer het later nog eens of mail naar: ".$mailtolink);
               // email_error("Error bij inschrijven: ".$mysqli->error."<br>".$query);
            } else {
                $returnVal = '<div class="alert alert-success" role="alert"><i class="glyphicon glyphicon-ok"></i></span> We hebben je inschrijving in goede orde ontvangen.</div>';
            }
        }
        $mysqli->close();
    } else {
        //try again..
    }
} else { //End POST
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $query = sprintf("SELECT * FROM `$current_table` WHERE `email` = '%s'",
        $mysqli->real_escape_string($user_email)
        );
    $result = $mysqli->query($query);
    if( $result && $result->num_rows == 1 ) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $act_types = array('workshop', 'game', 'lecture', 'schmink', 'other_act','perform','install', 'acteren');

        if( in_array($row['contrib0_type'], $act_types) ) {
            $contrib0 = 'act';
            $act0type = htmlspecialchars_decode($row['contrib0_type']);
            $act0desc = htmlspecialchars_decode($row['contrib0_desc']);
            $act0need = htmlspecialchars_decode($row['contrib0_need']);
        } else {
            $contrib0 = htmlspecialchars_decode($row['contrib0_type']);
            $contrib0desc = htmlspecialchars_decode($row['contrib0_desc']);
        }

        if( in_array($row['contrib1_type'], $act_types) ) {
            $contrib1 = 'act';
            $act1type = htmlspecialchars_decode($row['contrib1_type']);
            $act1desc = htmlspecialchars_decode($row['contrib1_desc']);
            $act1need = htmlspecialchars_decode($row['contrib1_need']);
        } else {
            $contrib1 = htmlspecialchars_decode($row['contrib1_type']);
            $contrib1desc = htmlspecialchars_decode($row['contrib1_desc']);
        }

        $partner = htmlspecialchars_decode($row['partner']);
        $motivation = htmlspecialchars_decode($row['motivation']);
        $question = htmlspecialchars_decode($row['question']);
        $preparations = htmlspecialchars_decode($row['preparations']);
        $nightshift = htmlspecialchars_decode($row['nightshift']);
        if( $preparations == "N" ) {
            $preparationsbox = FALSE;
            $preparations = "";
        } else {
            $preparationsbox = TRUE;
        }
        if( $row['round'] == $signupround ) {
            $returnVal = '<div class="alert alert-success" role="alert">We hebben al een inschrijving van je ontvangen. Als je wilt kun je die hier aanpassen.</div>';
            $is_save = TRUE;
        }
    } else {
        //this is ok
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
                <h1>Familiar Forest 2019 : evolutie van de homo familiaris</h1>
                <p class="lead">
                    7 & 8 september 2019.
                </p>
                <p>
                    Vul dit formulier zo volledig mogelijk in. Ook nadat je het formulier hebt verstuurd, kun je nog tot en met 9 mei 2019 je antwoorden wijzigen. Pas op 10 mei 2019 maken wij je inschrijving definitief. Heb je hulp nodig of wil je meer informatie over het inschrijven? Dan kun je mailen naar: <?php echo $mailtolink ?>
                </p>
            </div>
            <?php echo $returnVal; ?>

            <form id="signup-form" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">

                <fieldset>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="motivation">Waarom wil je naar Familiar Forest 2019?</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="motivation" id="motivation" cols="60" rows="4"><?php echo $motivation; ?></textarea>
                            <label for="motivation">Max 1024 karakters</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="question">Wat is het allervetste wat je ooit in een museum hebt gezien? Waarom? Gebruik bij je motivatie een moment uit het verleden en de toekomst.</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="question" id="question" cols="60" rows="4"><?php echo $question; ?></textarea>
                            <label for="question">Max 1024 karakters</label>
                        </div>
                    </div>
                </fieldset>
                <div class="form-group row">
                        <label class="col-sm-2" for="partner">Lieveling<br>Email</label>
                        <div class="col-sm-10">
                            <div class='input-group'>
                                <input class="form-control" type="email" name="partner" id="partner" placeholder="Lieveling" value="<?php echo $partner; ?>">
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
                                In de inschrijving van dit email adres staat ook jouw email adres. Helemaal in orde dus!
                            </div>
                            <div id='partnernosignup' class="alert alert-info">
                                We hebben op dit moment nog geen inschrijving ontvangen op dit email adres. Misschien heb je een typfoutje gemaakt of heeft je lieveling zich nog niet ingeschreven.
                            </div>
                            <div id='partnernotsame' class="alert alert-info">
                                Uh oh! We houden er niet van om ons in relaties te mengen maar in de inschrijving van je lieveling staat iets anders dan jouw email adres! Hij of zij heeft vast een typfoutje gemaakt of iets dergelijks. Dubbelcheck dit nog even want op dit moment kunnen we jullie niet aan elkaar linken.
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Hoe wil jij bijdragen aan het Familiar?</legend>
                    <div class="form-group row">
                        <label for="contrib0" class="col-sm-2 form-control-label">Eerste keus</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="contrib0" id="contrib0">
                                <option value="iv" <?= $contrib0 == 'iv' ? ' selected="selected"' : '';?>>Interieurverzorging</option>
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
                                <option value="other_act" <?= $act0type == 'other_act' ? ' selected="selected"' : '';?>>Anders</option>
                                <option value="perform" <?= $act0type == 'perform' ? ' selected="selected"' : '';?>>Performance</option>
                                <option value="install" <?= $act0type == 'install' ? ' selected="selected"' : '';?>>Installatie / Beeld</option>
                                <option value="acteren" <?= $act0type == 'acteren' ? ' selected="selected"' : '';?>>Acteren in Familiar productie</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="act0desc">Vertel iets over je ervaring met dit soort act</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="act0desc" id="act0desc" cols="60" rows="4"><?php echo $act0desc; ?></textarea>
                            <label for="act0desc">Max 1024 karakters</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="act0need">Indien van toepassing: Geef een omschrijving van je act of performance</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="act0need" id="act0need" cols="60" rows="4"><?php echo $act0need; ?></textarea>
                            <label for="act0need">Max 1024 karakters</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="contrib0" class="col-sm-2 form-control-label">Tweede keus</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="contrib1" id="contrib1">
                                <option value="iv" <?= $contrib1 == 'iv' ? ' selected="selected"' : '';?>>Interieurverzorging</option>
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
                                <option value="other_act" <?= $act1type == 'other_act' ? ' selected="selected"' : '';?>>Anders</option>
                                <option value="perform" <?= $act1type == 'perform' ? ' selected="selected"' : '';?>>Performance</option>
                                <option value="install" <?= $act1type == 'install' ? ' selected="selected"' : '';?>>Installatie / Beeld</option>
                                <option value="acteren" <?= $act1type == 'acteren' ? ' selected="selected"' : '';?>>Acteren in Familiar productie</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="act1desc">Vertel iets over je ervaring met dit soort act</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="act1desc" id="act1desc" cols="60" rows="4"><?php echo $act1desc; ?></textarea>
                            <label for="act1desc">Max 1024 karakters</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="act1need">Indien van toepassing: Geef een omschrijving van je act of performance</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="act1need" id="act1need" cols="60" rows="4"><?php echo $act1need; ?></textarea>
                            <label for="act1need">Max 1024 karakters</label>
                        </div>
                    </div>
                    <div id='afbdesc' class="alert alert-success">Super fijn dat je wilt komen afbouwen! Als je in de gelegenheid bent om naast zondag en maandag ook nog meer dagen te helpen met de afbouw horen we dat graag! Ook als je bijvoorbeeld alleen dinsdag beschikbaar bent horen we dat graag en proberen we dat mogelijk te maken.</div>
                    <div class="form-group row">
                      <label class="col-sm-2 form-control-label" for="preparations">Nachtdienst</label>
                      <div class="col-sm-10">
                        <div class="checkbox">
                          <label for="nightshift">
                            <input class="checkbox" type="checkbox" id="nightshift" name="nightshift" value="J" <?php if($nightshift) echo("checked"); ?>>
                            Ik vind het niet erg om een nachtshift uit te voeren. Denk bijvoorbeeld aan nachtbar of vuurmeester.
                          </label>
                        </div>
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
                            <div class="alert alert-success" id="prepintro">Te gek! Wat zou je leuk vinden om te doen? Het is niet zeker dat we contact met je opnemen via deze route. Voor een snelle uitnodiging kun je het beste mailen naar: <?php echo $mailtolink ?></div>
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
                            <div class="alert alert-warning">Familiar Forest is een reis. De locatie verplicht de deelnemer om zich te kunnen identificeren en een aansprakelijkheidsverzekering te hebben.</div>
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
                        <label class="col-sm-2 form-control-label" for="terms3">Kaartverkoop</label>
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
