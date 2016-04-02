<?php session_start();

include "dbstore.php";
include "functions.php";

$returnVal = "";
$firstname = $lastname = $birthday = $birthmonth = $birthyear = $birthdate = $gender = $email = $phone = $city = $editions_str = $nr_editions = $contrib0 = $contrib1 = $contrib0desc = $contrib1desc = $act0type = $act0desc = $act0need = $act1type = $act1desc = $act1need =$partner = $terms0 = $terms1 = $terms2 = "";
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
    if( !empty($_POST["birthday"]) ) {
        $birthday = intval(test_input($_POST["birthday"]));
    } else {
        $birthday = "";
    }
    if( !empty($_POST["birthmonth"]) ) {
        $birthmonth = intval(test_input($_POST["birthmonth"]));
    } else {
        $birthmonth = "";
    }
    if( !empty($_POST["birthyear"]) ) {
        $birthyear = intval(test_input($_POST["birthyear"]));
    } else {
        $birthyear = "";
    }
    if( $birthday != "" && $birthmonth != "" && $birthyear != "" ) {
        date_default_timezone_set('UTC');
        if( !mktime(0,0,0,$birthday, $birthmonth, $birthyear)) {
            addError("De opgegeven geboortedatum klopt niet.");
        } else {
            $birthdate = date( 'Y-m-d H:i:s', mktime(0,0,0, $birthmonth, $birthday, $birthyear));
        }
    } else {
        addError("Je hebt je geboortedatum niet goed opgegeven");
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
            addError("Je email adres klopt niet");
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
        $editions_str .= test_input($edition) . ", ";
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
            addError("Het email adres van je lieveling klopt niet");
        }
    } else {
        $partner = "";
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

    if( $terms0 == "" || $terms1 == "" || $terms2 == "") {
        addError("Je moet alle voorwaarden accepteren");
    }

    if( $returnVal == "" ) {
        $returnVal = storeSignup($email, $firstname, $lastname, $birthdate, $city, $gender, $phone, $nr_editions, $editions_str, $partner, $db_contrib0, $db_contrib1, $db_contrib0_desc, $db_contrib1_desc, $db_contrib0_need, $db_contrib1_need, $terms0, $terms1, $terms2);
    } else {
        //try again..
        $returnVal .= "</ul>";
    }
    if( $returnVal == "") {
        $_SESSION['success_firstname'] = $firstname;
        header('Location: success');
    }
} //End POST

function addError($value) {
    global $returnVal;
    $returnVal .= '<div class="alert alert-danger" role="alert">' . $value . '</div>';
}

?>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Familiar Forest Festival Inschrijfformulier</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <!-- Place favicon.ico in the root directory -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" type="text/css" media="all"
            href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/smoothness/jquery-ui.css"/>
        <script src="js/vendor/modernizr-2.8.3.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>
        <script src="js/signup.js"></script>

        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='https://www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-XXXXX-X','auto');ga('send','pageview');
        </script>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <div class="container">
            <div class="default-text">
                <h1>Inschrijven</h1>
                <p class="lead">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ut ligula quis lacus consectetur tempus. Integer pretium quam vel nunc aliquet fringilla. Maecenas enim nulla, faucibus ut tincidunt id, auctor at orci. Praesent faucibus tellus ipsum, nec varius erat consectetur at. Etiam ac ultricies ex, a gravida quam. Suspendisse fringilla congue massa a cursus. Nunc condimentum mauris id erat tincidunt laoreet. Sed maximus tortor id mi vestibulum pulvinar. Vestibulum ultricies
                    erat sit amet posuere euismod. Curabitur orci mauris, vehicula et dolor at, egestas luctus nunc. Sed non egestas massa. Curabitur eget bibendum arcu. Aliquam erat volutpat. Fusce placerat lacus a dapibus accumsan. Cras vitae interdum metus. Phasellus neque sem, mattis et imperdiet sed, eleifend vel lorem.
                </p>
            </div>
            <?php echo $returnVal; ?>

            <form method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">
                <div class="form-group row">
                    <label for="firstname" class="col-sm-2 form-control-label">Voornaam</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="firstname" placeholder="Voornaam" value="<?php echo $firstname;?>" name="firstname" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="lastname" class="col-sm-2 form-control-label">Achternaam</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="lastname" placeholder="Achternaam" value="<?php echo $lastname;?>" name="lastname"required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="city" class="col-sm-2 form-control-label">Woonplaats</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="city" placeholder="Woonplaats" value="<?php echo $city;?>" name="city"required>
                    </div>
                </div>
            
                <div class="form-group row">
                    <label for="birthdate" class="col-sm-2 form-control-label">Geboortedatum</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="date" id="birthdate" value="<?php echo $birthdate;?>" name="birthday"required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2">Geslacht</label>
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
                    </div>
                </div>

                <div class="form-group row">
                    <label for="email" class="col-sm-2 form-control-label">Email</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="email" id="email" placeholder="Email" value="<?php echo $email;?>" name="email" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="phone" class="col-sm-2 form-control-label">Telefoonnummer</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="phone" placeholder="Telefoonnummer" value="<?php echo $phone;?>" name="phone" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2" for="partner">Lieveling<br>Email</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="email" name="partner" id="partner" placeholder="Lieveling" value="<?php echo $partner; ?>">
                        <div class="alert alert-success">
                            Proin ultricies quis lacus in porttitor. Vivamus ullamcorper felis est, in congue neque bibendum sed. Donec egestas lorem quam, vitae ullamcorper tortor efficitur eu.
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Voorgaande edities</label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="editions[]" id="fff2010" value="fff2010" <?php if(in_array("fff2010", $editions)) echo( "checked"); ?> >
                                Familiar Forest Festival 2010
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="editions[]" id="fff2011" value="fff2011" <?php if(in_array("fff2011", $editions)) echo( "checked"); ?>>
                                Familiar Forest Festival 2011
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="editions[]" id="ffcastle" value="ffcastle" <?php if(in_array("ffcastle", $editions)) echo( "checked"); ?>>
                                Familiar Castle Festival                                
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="editions[]" id="fwf2012" value="fwf2012" <?php if(in_array("fwf2012", $editions)) echo( "checked"); ?>>
                                Familiar Winter Festival 2012
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
                                Familiar Forest Festival 2012
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
                                Familiar Winter Festival 2013
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="editions[]" id="fff2013" value="fff2013" <?php if(in_array("fff2013", $editions)) echo( "checked"); ?>>
                                Familiar Forest Festival 2013
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="editions[]" id="fwf2014" value="fwf2014" <?php if(in_array("fwf2014", $editions)) echo( "checked"); ?>>
                                Familiar Winter Festival 2014
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="editions[]" id="fff2014" value="fff2014" <?php if(in_array("fff2014", $editions)) echo( "checked"); ?>>
                                Familiar Forest Festival 2014
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="editions[]" id="fwf2015" value="fwf2015" <?php if(in_array("fwf2015", $editions)) echo( "checked"); ?>>
                                Familiar Winter Festival 2015
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="editions[]" id="fff2015" value="fff2015" <?php if(in_array("fff2015", $editions)) echo( "checked"); ?>>
                                Familiar Forest Festival 2015
                            </label>
                        </div>
                    </div>
                </div>
                        
                <fieldset>
                    <legend>Jouw bijdrage aan het Familiar Forest Festival 2016</legend>
                    <div class="form-group row">
                        <label for="contrib0" class="col-sm-2 form-control-label">Eerste keus</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="contrib0" id="contrib0">
                                <option value="ivbk" <?= $contrib0 == 'ivbk' ? ' selected="selected"' : '';?>>Interieur verzorging, bar of keuken</option>
                                <option value="act" <?= $contrib0 == 'act' ? ' selected="selected"' : '';?>>Act of Performance</option>
                                <option value="afb" <?= $contrib0 == 'afb' ? ' selected="selected"' : '';?>>Afbouw</option>
                                <option value="ontw" <?= $contrib0 == 'ontw' ? ' selected="selected"' : '';?>>Helpen bij het ontwerpen en opbouwen van decoraties, podia, stands, etc.</option>
                            </select>
                            <div class="alert alert-success" id="ivbk0desc">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque placerat id turpis quis dignissim. Maecenas elementum scelerisque pharetra. Sed tincidunt tincidunt purus, quis molestie eros blandit non. Vestibulum consequat dolor a enim porttitor, a vehicula mauris imperdiet. Ut et lectus vestibulum, finibus dolor posuere, elementum purus. Suspendisse at ipsum dapibus, dapibus neque ut, cursus lacus. Ut tristique id orci id aliquam. Integer consectetur magna ac justo ornare, nec imperdiet ipsum accumsan. 
                            </div>
                            <div class="alert alert-success" id="act0desc">
                                 Ut viverra pulvinar nisl, in dictum lacus. Aliquam non porta mauris, nec ornare mauris. Fusce metus neque, sodales id dictum vestibulum, vehicula non turpis. Nulla quis placerat enim. Morbi et lorem a dui pharetra interdum in vel nunc. In nec cursus lacus, eu egestas lorem. In elit felis, hendrerit quis enim non, sollicitudin tempus urna. Donec congue sollicitudin libero, non rutrum lorem fringilla vitae. Sed et tempus lectus. Nulla ac scelerisque leo. Sed fermentum facilisis sapien, vel suscipit odio porttitor tempus. Integer sit amet eros quis lorem interdum ullamcorper non quis nisi. Etiam aliquam massa nec magna volutpat, eget vehicula nibh laoreet. 
                            </div>
                            <div class="alert alert-success" id="afb0desc">
                                In luctus nisi vitae risus gravida placerat. Etiam quis aliquam metus. Vestibulum sed mattis diam. Nullam sollicitudin vel felis eu imperdiet. Nunc at diam porttitor, aliquam lectus sed, ornare est. Cras et lectus id elit commodo lobortis. Morbi feugiat massa lacus, sit amet mattis lorem convallis nec. Cras vehicula lacus quis risus tempus sagittis. Donec consectetur turpis libero, vitae lobortis arcu pretium quis. Ut ac turpis a ante volutpat pulvinar tincidunt vel enim. Maecenas pretium et diam ac feugiat. Curabitur finibus quam eu sagittis molestie. Duis facilisis pretium mi ut elementum. Praesent volutpat lectus eu mollis ullamcorper. 
                            </div>
                            <div class="alert alert-success" id="ontw0desc">
                                 Praesent quis lorem mollis, eleifend turpis gravida, interdum lacus. Vivamus ornare tellus turpis, id congue enim sagittis vel. Etiam dapibus, dui non posuere suscipit, nibh tellus tempor massa, id luctus velit lorem eu sapien. Integer suscipit ante non sapien sagittis luctus. Sed venenatis eros vel ante finibus, et vehicula quam varius. Etiam viverra venenatis dapibus. Nulla euismod nisi dolor, vitae varius libero facilisis non. Ut convallis augue in ultricies faucibus. Curabitur sed nunc quis nibh tincidunt semper.
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="contrib0desc">Vertel iets over je ervaring hierin</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="contrib0desc" id="contrib0desc" cols="60" rows="4"><?php echo $contrib0desc; ?></textarea>
                            <label id="contrib0counter" for="contrib0desc">Max 256 karakters</label>
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
                                <option value="install" <?= $act0type == 'install' ? ' selected="selected"' : '';?>>Installatie Beeld</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="act0desc">Omschrijving van je act</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="act0desc" id="act0desc" cols="60" rows="4"><?php echo $act0desc; ?></textarea>
                            <label for="act0desc">Max 256 karakters</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="act0need">Wat heb je voor je act nodig?</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="act0need" id="act0need" cols="60" rows="4"><?php echo $act0need; ?></textarea>
                            <label for="act0need">Max 256 karakters</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="contrib0" class="col-sm-2 form-control-label">Tweede keus</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="contrib1" id="contrib1">
                                <option value="ivbk" <?= $contrib1 == 'ivbk' ? ' selected="selected"' : '';?>>Interieur verzorging, bar of keuken</option>
                                <option value="act" <?= $contrib1 == 'act' ? ' selected="selected"' : '';?>>Act of Performance</option>
                                <option value="afb" <?= $contrib1 == 'afb' ? ' selected="selected"' : '';?>>Afbouw</option>
                                <option value="ontw" <?= $contrib1 == 'ontw' ? ' selected="selected"' : '';?>>Helpen bij het ontwerpen en opbouwen van decoraties, podia, stands, etc.</option>
                            </select>
                            <div class="alert alert-success" id="ivbk1desc">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque placerat id turpis quis dignissim. Maecenas elementum scelerisque pharetra. Sed tincidunt tincidunt purus, quis molestie eros blandit non. Vestibulum consequat dolor a enim porttitor, a vehicula mauris imperdiet. Ut et lectus vestibulum, finibus dolor posuere, elementum purus. Suspendisse at ipsum dapibus, dapibus neque ut, cursus lacus. Ut tristique id orci id aliquam. Integer consectetur magna ac justo ornare, nec imperdiet ipsum accumsan. 
                            </div>
                            <div class="alert alert-success" id="act1desc">
                                 Ut viverra pulvinar nisl, in dictum lacus. Aliquam non porta mauris, nec ornare mauris. Fusce metus neque, sodales id dictum vestibulum, vehicula non turpis. Nulla quis placerat enim. Morbi et lorem a dui pharetra interdum in vel nunc. In nec cursus lacus, eu egestas lorem. In elit felis, hendrerit quis enim non, sollicitudin tempus urna. Donec congue sollicitudin libero, non rutrum lorem fringilla vitae. Sed et tempus lectus. Nulla ac scelerisque leo. Sed fermentum facilisis sapien, vel suscipit odio porttitor tempus. Integer sit amet eros quis lorem interdum ullamcorper non quis nisi. Etiam aliquam massa nec magna volutpat, eget vehicula nibh laoreet. 
                            </div>
                            <div class="alert alert-success" id="afb1desc">
                                In luctus nisi vitae risus gravida placerat. Etiam quis aliquam metus. Vestibulum sed mattis diam. Nullam sollicitudin vel felis eu imperdiet. Nunc at diam porttitor, aliquam lectus sed, ornare est. Cras et lectus id elit commodo lobortis. Morbi feugiat massa lacus, sit amet mattis lorem convallis nec. Cras vehicula lacus quis risus tempus sagittis. Donec consectetur turpis libero, vitae lobortis arcu pretium quis. Ut ac turpis a ante volutpat pulvinar tincidunt vel enim. Maecenas pretium et diam ac feugiat. Curabitur finibus quam eu sagittis molestie. Duis facilisis pretium mi ut elementum. Praesent volutpat lectus eu mollis ullamcorper. 
                            </div>
                            <div class="alert alert-success" id="ontw1desc">
                                 Praesent quis lorem mollis, eleifend turpis gravida, interdum lacus. Vivamus ornare tellus turpis, id congue enim sagittis vel. Etiam dapibus, dui non posuere suscipit, nibh tellus tempor massa, id luctus velit lorem eu sapien. Integer suscipit ante non sapien sagittis luctus. Sed venenatis eros vel ante finibus, et vehicula quam varius. Etiam viverra venenatis dapibus. Nulla euismod nisi dolor, vitae varius libero facilisis non. Ut convallis augue in ultricies faucibus. Curabitur sed nunc quis nibh tincidunt semper.
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="contrib1desc">Vertel iets over je ervaring hierin</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="contrib1desc" id="contrib1desc" cols="60" rows="4"><?php echo $contrib1desc; ?></textarea>
                            <label id="contrib1counter" for="contrib1desc">Max 256 karakters</label>
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
                                <option value="install" <?= $act1type == 'install' ? ' selected="selected"' : '';?>>Installatie Beeld</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="act1desc">Omschrijving van je act</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="act1desc" id="act1desc" cols="60" rows="4"><?php echo $act1desc; ?></textarea>
                            <label for="act1desc">Max 256 karakters</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="act1need">Wat heb je voor je act nodig?</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="act1need" id="act1need" cols="60" rows="4"><?php echo $act1need; ?></textarea>
                            <label for="act1need">Max 256 karakters</label>
                        </div>
                    </div>
                </fieldset>
                    
                <fieldset>
                    <legend>Voorwaarden</legend>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="terms0">Kaart verkoop</label>
                        <div class="col-sm-10">
                            <div class="alert alert-warning">Nam sit amet varius orci, vitae venenatis quam. Vestibulum varius nulla non augue placerat, id feugiat tellus pulvinar. Etiam luctus elit massa. Proin in sem nulla. Maecenas sit amet turpis lectus. Donec id leo iaculis, tincidunt nibh venenatis, fringilla dolor. Nunc sit amet quam sem. Quisque eget purus lobortis, tempor odio ut, ultricies diam. Donec ac ultrices turpis. Maecenas egestas tristique dolor at consequat. Aenean sed lectus at lectus ornare iaculis. Ut viverra lectus tortor, ac lacinia dolor vestibulum at. Curabitur rutrum auctor nibh et tempor. </div>
                            <div class="checkbox">
                                <label>
                                    <input class="checkbox" type="checkbox" id="terms0" name="terms0" value="J">
                                    Ik ga akkoord met deze voorwaarden
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="terms1">Verzekering</label>
                        <div class="col-sm-10">
                            <div class="alert alert-warning">Praesent fringilla bibendum efficitur. Curabitur hendrerit, neque posuere gravida tempus, nibh felis maximus justo, id aliquam enim risus id sapien. Aliquam lobortis eros et turpis egestas mattis. Nullam tortor nunc, condimentum a sem nec, porttitor ullamcorper erat. Nulla gravida cursus neque, molestie tempus mauris tincidunt a. Proin eu luctus nisi. Morbi ac pulvinar neque. Donec mollis diam elit, lacinia euismod massa gravida ut. Nullam metus orci, egestas eget libero at, porttitor bibendum ipsum. Ut ac justo mollis, pulvinar elit sit amet, accumsan ligula. Sed quis fringilla est. Integer quis risus vitae lectus accumsan consequat sed sit amet sem. Maecenas mi nisi, sagittis vitae pulvinar vitae, imperdiet non ante. Curabitur porttitor tristique sem vel ultricies.</div>
                            <div class="checkbox">
                                <label>
                                    <input class="checkbox" type="checkbox" id="terms1" name="terms1" value="J">
                                    Ik ga akkoord met deze voorwaarden
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="terms2">Gezondheid</label>
                        <div class="col-sm-10">
                            <div class="alert alert-warning">Morbi ac mauris arcu. Donec ac sollicitudin lectus. Donec imperdiet volutpat purus quis suscipit. Cras eu purus congue, imperdiet nisl vel, tristique urna. Nulla facilisi. Donec neque dui, lobortis at felis et, porttitor aliquet erat. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce consectetur luctus felis, in vehicula est aliquam vel. Sed mollis at libero sit amet cursus. Aliquam libero orci, ultricies a lobortis nec, finibus eget sapien. Curabitur eget auctor diam. Phasellus cursus lectus in semper mattis. Nunc vitae scelerisque lorem, ut mollis lacus. Duis fringilla risus in odio fermentum mattis. Mauris turpis metus, molestie vitae leo id, pellentesque vestibulum erat. Pellentesque ac lacinia dui, malesuada blandit risus. </div>
                            <div class="checkbox">
                                <label>
                                    <input class="checkbox" type="checkbox" id="terms2" name="terms2" value="J">
                                    Ik ga akkoord met deze voorwaarden
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="terms3">Telefoon en Foto's</label>
                        <div class="col-sm-10">
                            <div class="alert alert-warning">Morbi ac mauris arcu. Donec ac sollicitudin lectus. Donec imperdiet volutpat purus quis suscipit. Cras eu purus congue, imperdiet nisl vel, tristique urna. Nulla facilisi. Donec neque dui, lobortis at felis et, porttitor aliquet erat. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce consectetur luctus felis, in vehicula est aliquam vel. Sed mollis at libero sit amet cursus. Aliquam libero orci, ultricies a lobortis nec, finibus eget sapien. Curabitur eget auctor diam. Phasellus cursus lectus in semper mattis. Nunc vitae scelerisque lorem, ut mollis lacus. Duis fringilla risus in odio fermentum mattis. Mauris turpis metus, molestie vitae leo id, pellentesque vestibulum erat. Pellentesque ac lacinia dui, malesuada blandit risus. </div>
                            <div class="checkbox">
                                <label>
                                    <input class="checkbox" type="checkbox" id="terms3" name="terms3" value="J">
                                    Ik ga akkoord met deze voorwaarden
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Versturen</button>
            </form>
        </div>
    </body>
</html>
