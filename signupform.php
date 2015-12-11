<?php

$returnVal = "";
$firstname = $lastname = $insertion = $birthdate = $gender = $email = $phone = $city = $editions 
    = $contrib0 = $contrib1 = $partner = $terms0 = $terms1 = $terms2 = "";
$firstnameErr = $lastnameErr = $insertionErr = $birthdateErr = $genderErr = $emailErr = $phoneErr = $cityErr = $editionsErr 
    = $contrib0Err = $contrib1Err = $partnerErr = $terms0Err = $terms1Err = $terms2Err = "";


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

        <link rel="stylesheet" href="css/normalize.css">
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
        <h1 class="header">Inschrijven</h1>
        <div class="content">
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ut ligula quis lacus consectetur tempus. Integer pretium quam vel nunc aliquet fringilla. Maecenas enim nulla, faucibus ut tincidunt id, auctor at orci. Praesent faucibus tellus ipsum, nec varius erat consectetur at. Etiam ac ultricies ex, a gravida quam. Suspendisse fringilla congue massa a cursus. Nunc condimentum mauris id erat tincidunt laoreet. Sed maximus tortor id mi vestibulum pulvinar. Vestibulum ultricies
        erat sit amet posuere euismod. Curabitur orci mauris, vehicula et dolor at, egestas luctus nunc. Sed non egestas massa. Curabitur eget bibendum arcu. Aliquam erat volutpat. Fusce placerat lacus a dapibus accumsan. Cras vitae interdum metus. Phasellus neque sem, mattis et imperdiet sed, eleifend vel lorem.</p>
        </div>

        <div class="content">
        <div class="info"><?php echo $returnVal; ?></div>
        <form class="user-form" method="post" 
            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" target="_top">
                <fieldset>
                    <legend>Persoonlijke Informatie</legend>
                    <label for="firstname">Voornaam</label>
                    <input type="text" name="firstname" value="<?php echo $firstname;?>">
                    <span class="error"><?php echo $firstnameErr;?></span><br>
                    <label for="insertion">Tussenvoegsel</label>
                    <input type="text" name="insertion" value="<?php echo $insertion;?>">
                    <span class="error"><?php echo $insertionErr;?></span><br>
                    <label for="lastname">Achternaam</label>
                    <input type="text" name="lastname" value="<?php echo $lastname;?>">
                    <span class="error"><?php echo $lastnameErr;?></span><br>

                    <label for="birthdate">Geboortedatum</label>
                    <select id="birthday" name="birthday" value="<?php echo $birthday;?>">
                    </select>
                    <select id="birthmonth" name="birthmonth" value="<?php echo $birthmonth;?>">
                    </select>
                    <select id="birthyear" name="birthyear" value="<?php echo $birthyear;?>">
                    </select>
                    <span class="error"><?php echo $birthdateErr;?></span><br>
                    
                    <label for="birthdate">Woonplaats</label>
                    <input type="text" id="city" name="city" value="<?php echo $city;?>">
                    <span class="error"><?php echo $cityErr;?></span><br>

                    <!-- TODO: fill gender value from php-->
                    <label for="gender">Geslacht</label>
                    <input type="radio" name="gender" id="male" value="male"</input>
                    <label for="male">Man</label>
                    <input type="radio" name="gender" id="female" value="female"</input>
                    <label for="female">Vrouw</label>
                    <span class="error"><?php echo $genderErr;?></span><br>
                    <label for="email">E-mail</label>
                    <input type="text" name="email" value="<?php echo $email;?>">
                    <span class="error"><?php echo $emailErr;?></span><br>
                    <label for="phone">Telefoonnummer</label>
                    <input type="text" name="phone" value="<?php echo $phone;?>">
                    <span class="error"><?php echo $phoneErr;?></span><br>
                </fieldset> 
                <fieldset>
                    <legend>Voorgaande edities</legend>
                    <input type="checkbox" name="editions" id="fff2010" value="fff2010">
                        Familiar Forest Festival 2010</input><br>
                    <input type="checkbox" name="editions" id="fff2011" value="fff2011">
                        Familiar Forest Festival 2011</input><br>
                    <input type="checkbox" name="editions" id="ffcastle" value="ffcastle">
                        Familiar Castle Festival</input><br>
                    <input type="checkbox" name="editions" id="fwf2012" value="fwf2012">
                        Familiar Winter Festival 2012</input><br>
                    <input type="checkbox" name="editions" id="fh2012" value="fh2012">
                        Familiar Hemelvaartsnacht 2012</input><br>
                    <input type="checkbox" name="editions" id="fff2012" value="fff2012">
                        Familiar Forest Festival 2012</input><br>
                    <input type="checkbox" name="editions" id="fh2013" value="fh2013">
                        Familiar Hemelvaartsnacht 2013</input><br>
                    <input type="checkbox" name="editions" id="fwf2013" value="fwf2013">
                        Familiar Winter Festival 2013</input><br>
                    <input type="checkbox" name="editions" id="fff2013" value="fff2013">
                        Familiar Forest Festival 2013</input><br>
                    <input type="checkbox" name="editions" id="fwf2014" value="fwf2014">
                        Familiar Winter Festival 2014</input><br>
                    <input type="checkbox" name="editions" id="fff2014" value="fff2014">
                        Familiar Forest Festival 2014</input><br>
                    <input type="checkbox" name="editions" id="fwf2015" value="fwf2015">
                        Familiar Winter Festival 2015</input><br>
                    <input type="checkbox" name="editions" id="fff2015" value="fff2015">
                        Familiar Forest Festival 2015</input><br>
                    <span class="error"><?php echo $editionsErr;?></span><br>
                </fieldset>
                <fieldset>
                    <legend>Jouw bijdrage aan het Familiar Forest Festival 2016</legend>
                    <label for="contrib0">Eerste keus</label><br>
                    <select name="contrib0" id="contrib0">
                        <option value="ivbk">Interieur verzorging, bar of keuken</option>
                        <option value="act">Act of Performance</option>
                        <option value="afb">Afbouw</option>
                        <option value="ontw">Helpen bij het ontwerpen en opbouwen van decoraties, podia, stands, etc.</option>
                    </select><br>

                    <fieldset id="act0">
                        <legend>Informatie over je act of performance</legend>
                        <select name="act0type" id="act0type">
                            <option value="workshop">Workshop / Cursus</option>
                            <option value="game">Ervaring / Game</option>
                            <option value="lecture">Lezing</option>
                            <option value="schmink">Schmink</option>
                            <option value="other">Anders</option>
                            <option value="perform">Performance</option>
                            <option value="install">Installatie Beeld</option>
                        </select><br>
                        <input class="textfield-large" type="text" name="act0desc" id="act0desc"><br>
                        <input class="textfield-large" type="text" name="act0need" id="act0need"><br>
                    </fieldset>

                    <span class="error"><?php echo $contrib0Err;?></span><br>
                    <label for="contrib1">Tweede keus</label><br>
                    <select name="contrib1" id="contrib1">
                        <option value="ivbk">Interieur verzorging, bar of keuken</option>
                        <option value="act">Act of Performance</option>
                        <option value="afb">Afbouw</option>
                        <option value="ontw">Helpen bij het ontwerpen en opbouwen van decoraties, podia, stands, etc.</option>
                    </select><br>

                    <fieldset id="act1">
                        <legend>Informatie over je act of performance</legend>
                        <select name="act1type" id="act1type">
                            <option value="workshop">Workshop / Cursus</option>
                            <option value="game">Ervaring / Game</option>
                            <option value="lecture">Lezing</option>
                            <option value="schmink">Schmink</option>
                            <option value="other">Anders</option>
                            <option value="perform">Performance</option>
                            <option value="install">Installatie Beeld</option>
                        </select><br>
                        <input class="textfield-large" type="text" name="act1desc" id="act1desc"><br>
                        <input class="textfield-large" type="text" name="act1need" id="act1need"><br>
                    </fieldset>

                    <span class="error"><?php echo $contrib1Err;?></span><br>
                </fieldset>
                <fieldset>
                    <legend>Partner</legend>
                    <input type="text" name"partner" id="partner" value="<?php echo $partner;?>"/>
                    <span class="error"><?php echo $partnerErr;?></span><br>
                </fieldset>
                <fieldset>
                    <legend>Voorwaarden</legend>
                    <label for="terms0">Voorwaarden 1</label><br>
                    <input type="checkbox" name="terms0" id="terms0" value="J">
                        Ik ga akkoord met deze voorwaarden</input>
                    <span class="error"><?php echo $terms0Err;?></span><br>
                    <label for="terms1">Voorwaarden 2</label><br>
                    <input type="checkbox" name="terms1" id="terms1" value="J">
                        Ik ga akkoord met deze voorwaarden</input>
                    <span class="error"><?php echo $terms1Err;?></span><br>
                    <label for="terms2">Voorwaarden 3</label><br>
                    <input type="checkbox" name="terms1" id="terms1" value="J">
                        Ik ga akkoord met deze voorwaarden</input>
                    <span class="error"><?php echo $terms2Err;?></span><br>
                </fieldset>
            <input type="submit" name="submit" value="Versturen"/>
            </form>
        </div>

        
    </body>
</html>
