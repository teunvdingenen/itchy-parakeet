<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_PARTICIPANT) != PERMISSION_PARTICIPANT ) {
    header('Location: oops.php');
}

date_default_timezone_set('Europe/Amsterdam');

$wanttobuy = $coolname = $whichphoto = $whichphotoedition = $whichphotowhy = $allowdata = $helplayout = "";
$returnVal = "";


if( $_SERVER["REQUEST_METHOD"] == "POST") {
    $returnVal = "";
    if( !empty($_POST["wanttobuy"])) {
        $wanttobuy = test_input($_POST["wanttobuy"]);
    } else {
        //impossible
    }
    if( !empty($_POST["coolname"])) {
        $coolname = test_input($_POST["coolname"]);
    } else {
        //impossible
    }
    if( !empty($_POST["whichphoto"])) {
        $whichphoto = test_input($_POST["whichphoto"]);
    } else {
        //impossible
    }   
    if( !empty($_POST["whichphotoedition"])) {
        $whichphotoedition = test_input($_POST["whichphotoedition"]);
    } else {
        //impossible
    }
    if( !empty($_POST["whichphotowhy"])) {
        $whichphotowhy = test_input($_POST["whichphotowhy"]);
    } else {
        //impossible
    }
    if( !empty($_POST["allowdata"])) {
        $allowdata = test_input($_POST["allowdata"]);
    } else {
        //impossible
    }
    if( !empty($_POST["helplayout"])) {
        $helplayout = test_input($_POST["helplayout"]);
    } else {
        //impossible
    }

    if( $returnVal == "" ) {
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

        $query = sprintf("SELECT * FROM photobook WHERE email = '%s'",
            $mysqli->real_escape_string($user_email));
        $sqlresult = $mysqli->query($query);
        $query = "";
        if( $sqlresult->num_rows == 0 ) {
            $query = sprintf("INSERT INTO `photobook` (`wanttobuy`, `coolname`, `whichphoto`, `whichphotoedition`, `whichphotowhy`, `allowdata`, `helplayout`, `email`) VALUES ('%s','%s','%s','%s','%s','%s','%s', '%s');",
                $mysqli->real_escape_string($wanttobuy),
                $mysqli->real_escape_string($coolname),
                $mysqli->real_escape_string($whichphoto),
                $mysqli->real_escape_string($whichphotoedition),
                $mysqli->real_escape_string($whichphotowhy),
                $mysqli->real_escape_string($allowdata),
                $mysqli->real_escape_string($helplayout),
                $mysqli->real_escape_string($user_email));
        } else {
            $query = sprintf("UPDATE `photobook` SET `wanttobuy` = '%s', `coolname` = '%s', `whichphoto` = '%s', `whichphotoedition` = '%s', `whichphotowhy` = '%s', `allowdata` = '%s', `helplayout` = '%s' WHERE `email` = '%s'",
                $mysqli->real_escape_string($wanttobuy),
                $mysqli->real_escape_string($coolname),
                $mysqli->real_escape_string($whichphoto),
                $mysqli->real_escape_string($whichphotoedition),
                $mysqli->real_escape_string($whichphotowhy),
                $mysqli->real_escape_string($allowdata),
                $mysqli->real_escape_string($helplayout),
                $mysqli->real_escape_string($user_email));
        }
        $result = $mysqli->query($query);
        if( !$result ) {
            addError($mysqli->error."We hebben niet je gegevens kunnen opslaan. Probeer het later nog eens of mail naar: ".$mailtolink);
           // email_error("Error bij inschrijven: ".$mysqli->error."<br>".$query);
        } else {
            $returnVal = '<div class="alert alert-success" role="alert"><i class="glyphicon glyphicon-ok"></i></span> We hebben je antwoorden in goede orde ontvangen.</div>';
        }
        $mysqli->close();
    } else {
        //try again..
    }
} else { //End POST
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $query = sprintf("SELECT * FROM `photobook` WHERE `email` = '%s'",
        $mysqli->real_escape_string($user_email)
        );
    $result = $mysqli->query($query);
    if( $result && $result->num_rows == 1 ) {
     header('Location: oops.php');
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
                <h1>Familiar Fotoboek</h1>
                <p>
Al jaren dromen we ervan om 'Familiar door de jaren heen' te bundelen tot een mooie tastbare verzameling van ons collectieve geheugen en gemeenschappelijke historie. En eindelijk gaat het er dan ook echt van komen. Zo rond het tweede weekend van september 2020 willen we deze kunnen uitreiken aan de mensen die deze in hun kast willen zetten, op het koffietafel boek leggen, of later aan hun kleinkinderen willen laten zien. Of je er nu 1x bent geweest, of al jaren niet uit het bos weg te slaan bent; ook jij hebt Familiar gemaakt. En daarom vragen we ook nu, jouw input. Je kunt dit formulier een keer insturen en hebt daarvoor de tijd tot en met 4 mei.
                </p>
            </div>
            <?php echo $returnVal; ?>

            <form id="signup-form" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">

                <fieldset>
                    <div class="form-group row">
                        <label class="col-sm-5 form-control-label" for="wanttobuy">Zou jij een Familiar Fotoboek willen aanschaffen voor max 30,-? (+- 150 pagina's van waarschijnlijk 25x25cm) </label>
                        <div class="col-sm-7">
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="wanttobuy" id="wanttobuyJ" value="J">
                            <label class="form-check-label" for="wanttobuyJ">
                              Ja
                            </label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="wanttobuy" id="wanttobuyN" value="N">
                            <label class="form-check-label" for="wanttobuyN">
                              Nee
                            </label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="wanttobuy" id="wanttobuyM" value="M">
                            <label class="form-check-label" for="wanttobuyM">
                              Misschien
                            </label>
                          </div>
                          <label for="wanttobuy">Dit gebruiken we als inventarisatie voor het drukken. Het is geen bindend akkoord.</label>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <div class="form-group row">
                        <label class="col-sm-5 form-control-label" for="coolname">Wat zou een toffe naam zijn voor het fotoboek?</label>
                        <div class="col-sm-7">
                            <input class="form-control" type="text" name="coolname" id="coolname">
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <div class="form-group row">
                        <label class="col-sm-5 form-control-label" for="whichphoto">Welke foto mag er van jou echt niet missen?</label>
                        <div class="col-sm-7">
                            <input class="form-control" type="text" name="whichphoto" id="whichphoto">
                            <label for="whichphoto">Plak hier de url van de foto op facebook</label>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <div class="form-group row">
                        <label class="col-sm-5 form-control-label" for="whichphotoedition">Uit welke editie komt de foto die je hebt gekozen?</label>
                        <div class="col-sm-7">
                          <select class="form-control" id="whichphotoedition" name="whichphotoedition">
                            <option value="ff2010">Familiar Forest 2010</option>
                            <option value="ff2011">Familiar Forest 2011</option>
                            <option value="ffcastle">Familiar Castle</option>
                            <option value="fw2012">Familiar Winter 2012</option>
                            <option value="fh2012">Familiar Hemelvaart 2012</option>
                            <option value="ff2012">Familiar Forest 2012</option>
                            <option value="fh2013">Familiar Hemelvaart 2013</option>
                            <option value="fw2013">Familiar Winter 2013</option>
                            <option value="ff2013">Familiar Forest 2013</option>
                            <option value="fw2014">Familiar Winter 2014</option>
                            <option value="ff2014">Familiar Forest 2014</option>
                            <option value="fw2015">Familiar Winter 2015</option>
                            <option value="ff2015">Familiar Forest 2015</option>
                            <option value="ff2016">Familiar Forest 2016</option>
                            <option value="fv2017">Familiar Voorjaar 2017</option>
                            <option value="ff2017">Familiar Forest 2017</option>
                            <option value="fv2018">Back to the FFFuture: '95</option>
                            <option value="ff2018">Familiar Forest 2018</option>
                            <option value="ff2019">Familiar Forest 2019</option>
                          </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <div class="form-group row">
                        <label class="col-sm-5 form-control-label" for="whichphotowhy">Waarom mag deze foto niet missen?</label>
                        <div class="col-sm-7">
                            <input class="form-control" type="text" name="whichphotowhy" id="whichphotowhy">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-5 form-control-label" for="allowdata">Mogen we het bovenstaande antwoord gebruiken in het boek?</label>
                        <div class="col-sm-7">
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="allowdata" id="allowdataJ" value="J">
                            <label class="form-check-label" for="allowDataJ">
                              Ja, je mag me met mijn voornaam vermelden
                            </label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="allowdata" id="allowdataA" value="A">
                            <label class="form-check-label" for="allowdataA">
                              Ja, maar ik blijf graag anoniem
                            </label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="allowdata" id="allowdataN" value="N">
                            <label class="form-check-label" for="allowdataN">
                              Nee
                            </label>
                          </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <div class="form-group row">
                        <label class="col-sm-5 form-control-label" for="helplayout">Heb jij toffe layout skills en wil je meewerken aan het boek?</label>
                        <div class="col-sm-7">
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="helplayout" id="helplayoutj" value="J">
                            <label class="form-check-label" for="helplayoutJ">
                              Ja
                            </label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="helplayout" id="helplayoutN" value="N">
                            <label class="form-check-label" for="helplayout">
                              Nee
                            </label>
                          </div>
                       </div>
                    </div>
                </fieldset>

                <button class="btn btn-lg btn-primary btn-block" type="submit">
                  <i class="fa fa-paper-plane"></i> Versturen
                </button>
            </form>
        </div>
    </div>
  </div>
</div>
        <?php include("form-js.html"); ?>
    </body>
</html>
