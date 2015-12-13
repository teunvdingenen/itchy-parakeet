<?php

$returnVal = "";
$firstname = $lastname = $insertion = $birthdate = $gender = $email = $phone = $city = $editions 
    = $contrib0 = $contrib1 = $contrib0desc = $contrib1desc = $act0type = $act0desc = $act0need = $act1type = $act1desc = $act1need =$partner = $terms0 = $terms1 = $terms2 = "";
$firstnameErr = $lastnameErr = $insertionErr = $birthdateErr = $genderErr = $emailErr = $phoneErr = $cityErr = $editionsErr 
    = $contrib0Err = $contrib1Err = $partnerErr = $terms0Err = $terms1Err = $terms2Err = "";

if( $_SERVER["REQUEST_METHOD"] == "POST") {


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
        <form id="signup-form" class="signup-form" method="post" 
            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" target="_top">
                <fieldset>
                    <legend>Persoonlijke Informatie</legend>
                    <ul>
                        <li>
                            <span>
                                <label for="firstname">Voornaam</label>
                                <input class="field text verify" type="text" name="firstname" value="<?php echo $firstname;?>">
                            </span>
                            <span>
                                <label for="lastname">Achternaam</label>
                                <input class="field text verify" type="text" name="lastname" value="<?php echo $lastname;?>">
                            </span>
                        </li>
                        <li>
                            <span>
                                <label for="city">Woonplaats</label>
                                <input class="field text verify" type="text" id="city" name="city" value="<?php echo $city;?>">
                            </span>
                            <span>
                                <label for="birthday">Geboortedatum</label>
                                <span>
                                    <input id="birthday" name="birthday" class="field text number" size="2" maxlength="2" type="text" placeholder="DD">
                                </span>
                                <span>
                                    <input id="birthmonth" name="birthmonth" class="field text number" size="2" maxlength="2" type="text" placeholder="MM">
                                </span>
                                <span>
                                    <input id="birthyear" name="birthyear" class="field text number" size="4" maxlength="4" type="text" placeholder="YYYY">
                                </span>
                            </span>
                            
                        </li>
                        <li>
                                    <span>
                                <label for="gender">Geslacht</label>
                            <span>
                            <!-- TODO: fill gender value from php-->
                                <input class="field radio" type="radio" name="gender" id="male" value="male"</input>
                                <label class="choice" for="male">Man</label>
                            </span>
                            <span>
                                <input class="field radio" type="radio" name="gender" id="female" value="female"</input>
                                <label class="choice" for="female">Vrouw</label>
                            </span>
                                    </span>
                        </li>
                        <li>
                            <span>
                                <label for="email">E-mail</label>
                                <input class="field text verify email" type="text" name="email" value="<?php echo $email;?>">
                            </span>
                            <span>
                                <label for="phone">Telefoonnummer</label>
                                <input class="field text phone" type="text" name="phone" value="<?php echo $phone;?>">
                            </span>
                        </li>
                    </ul>
                </fieldset> 
                <fieldset>
                    <legend>Voorgaande edities</legend>
                    <input class="field checkbox" type="checkbox" name="editions" id="fff2010" value="fff2010">
                    <label class="choice">Familiar Forest Festival 2010</label>
                    <input class="field checkbox" type="checkbox" name="editions" id="fff2011" value="fff2011">
                    <label class="choice">Familiar Forest Festival 2011</label>
                    <input class="field checkbox" type="checkbox" name="editions" id="ffcastle" value="ffcastle">
                    <label class="choice">Familiar Castle Festival</label>
                    <input class="field checkbox" type="checkbox" name="editions" id="fwf2012" value="fwf2012">
                    <label class="choice">Familiar Winter Festival 2012</label>
                    <input class="field checkbox" type="checkbox" name="editions" id="fh2012" value="fh2012">
                    <label class="choice">Familiar Hemelvaartsnacht 2012</label>
                    <input class="field checkbox" type="checkbox" name="editions" id="fff2012" value="fff2012">
                    <label class="choice">Familiar Forest Festival 2012</label>
                    <input class="field checkbox" type="checkbox" name="editions" id="fh2013" value="fh2013">
                    <label class="choice">Familiar Hemelvaartsnacht 2013</label>
                    <input class="field checkbox" type="checkbox" name="editions" id="fwf2013" value="fwf2013">
                    <label class="choice">Familiar Winter Festival 2013</label>
                    <input class="field checkbox" type="checkbox" name="editions" id="fff2013" value="fff2013">
                    <label class="choice">Familiar Forest Festival 2013</label>
                    <input class="field checkbox" type="checkbox" name="editions" id="fwf2014" value="fwf2014">
                    <label class="choice">Familiar Winter Festival 2014</label>
                    <input class="field checkbox" type="checkbox" name="editions" id="fff2014" value="fff2014">
                    <label class="choice">Familiar Forest Festival 2014</label>
                    <input class="field checkbox" type="checkbox" name="editions" id="fwf2015" value="fwf2015">
                    <label class="choice">Familiar Winter Festival 2015</label>
                    <input class="field checkbox" type="checkbox" name="editions" id="fff2015" value="fff2015">
                    <label class="choice">Familiar Forest Festival 2015</label>
                </fieldset>
                <fieldset>
                    <legend>Jouw bijdrage aan het Familiar Forest Festival 2016</legend>
                    <ul>
                        <li>
                            <div>
                                <label for="contrib0">Eerste keus</label>
                                <select class="field select" name="contrib0" id="contrib0">
                                    <option value="ivbk">Interieur verzorging, bar of keuken</option>
                                    <option value="act">Act of Performance</option>
                                    <option value="afb">Afbouw</option>
                                    <option value="ontw">Helpen bij het ontwerpen en opbouwen van decoraties, podia, stands, etc.</option>
                                </select>
                            </div>
                            <div class="terms" id="ivbk0desc">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque placerat id turpis quis dignissim. Maecenas elementum scelerisque pharetra. Sed tincidunt tincidunt purus, quis molestie eros blandit non. Vestibulum consequat dolor a enim porttitor, a vehicula mauris imperdiet. Ut et lectus vestibulum, finibus dolor posuere, elementum purus. Suspendisse at ipsum dapibus, dapibus neque ut, cursus lacus. Ut tristique id orci id aliquam. Integer consectetur magna ac justo ornare, nec imperdiet ipsum accumsan. 
                            </div>
                            <div class="terms" id="act0desc">
                                 Ut viverra pulvinar nisl, in dictum lacus. Aliquam non porta mauris, nec ornare mauris. Fusce metus neque, sodales id dictum vestibulum, vehicula non turpis. Nulla quis placerat enim. Morbi et lorem a dui pharetra interdum in vel nunc. In nec cursus lacus, eu egestas lorem. In elit felis, hendrerit quis enim non, sollicitudin tempus urna. Donec congue sollicitudin libero, non rutrum lorem fringilla vitae. Sed et tempus lectus. Nulla ac scelerisque leo. Sed fermentum facilisis sapien, vel suscipit odio porttitor tempus. Integer sit amet eros quis lorem interdum ullamcorper non quis nisi. Etiam aliquam massa nec magna volutpat, eget vehicula nibh laoreet. 
                            </div>
                            <div class="terms" id="afb0desc">
                                In luctus nisi vitae risus gravida placerat. Etiam quis aliquam metus. Vestibulum sed mattis diam. Nullam sollicitudin vel felis eu imperdiet. Nunc at diam porttitor, aliquam lectus sed, ornare est. Cras et lectus id elit commodo lobortis. Morbi feugiat massa lacus, sit amet mattis lorem convallis nec. Cras vehicula lacus quis risus tempus sagittis. Donec consectetur turpis libero, vitae lobortis arcu pretium quis. Ut ac turpis a ante volutpat pulvinar tincidunt vel enim. Maecenas pretium et diam ac feugiat. Curabitur finibus quam eu sagittis molestie. Duis facilisis pretium mi ut elementum. Praesent volutpat lectus eu mollis ullamcorper. 
                            </div>
                            <div class="terms" id="ontw0desc">
                                 Praesent quis lorem mollis, eleifend turpis gravida, interdum lacus. Vivamus ornare tellus turpis, id congue enim sagittis vel. Etiam dapibus, dui non posuere suscipit, nibh tellus tempor massa, id luctus velit lorem eu sapien. Integer suscipit ante non sapien sagittis luctus. Sed venenatis eros vel ante finibus, et vehicula quam varius. Etiam viverra venenatis dapibus. Nulla euismod nisi dolor, vitae varius libero facilisis non. Ut convallis augue in ultricies faucibus. Curabitur sed nunc quis nibh tincidunt semper.
                            </div>
                        </li>
                        <li id="contrib0row">
                            <span>
                                <label for="contrib0desc">Vertel iets over je ervaring hierin</label>
                                <textarea class="textarea" name="contrib0desc" id="contrib0desc" cols="60" rows="4"><?php echo $contrib0desc; ?></textarea>
                                <label id="contrib0counter" for="contrib0desc">Max 256 karakters</label>
                            </span>
                        </li>
                        <li id="act0row">
                            <div>
                                <label for="act0type">Informatie over je act of performance</label>
                                <select class="field select" name="act0type" id="act0type">
                                    <option value="workshop">Workshop / Cursus</option>
                                    <option value="game">Ervaring / Game</option>
                                    <option value="lecture">Lezing</option>
                                    <option value="schmink">Schmink</option>
                                    <option value="other">Anders</option>
                                    <option value="perform">Performance</option>
                                    <option value="install">Installatie Beeld</option>
                                </select>
                            </div>
                            <div>
                                <label for="act0desc">Omschrijving van je act</label>
                                <textarea class="textarea" name="act0desc" id="act0desc" cols="60" rows="4"><?php echo $act0desc; ?></textarea>
                                <label for="act0desc">Max 256 karakters</label>
                            </div>
                            <div>
                                <label for="act0need">Wat heb je voor je act nodig?</label>
                                <textarea class="textarea" name="act0need" id="act1need" cols="60" rows="4"><?php echo $act0need; ?></textarea>
                                <label for="act0need">Max 256 karakters</label>
                            </div>
                        </li>
                        <li>
                            <div>
                                <label for="contrib1">Tweede keus</label>
                                <select class="field select" name="contrib1" id="contrib1">
                                    <option value="ivbk">Interieur verzorging, bar of keuken</option>
                                    <option value="act">Act of Performance</option>
                                    <option value="afb">Afbouw</option>
                                    <option value="ontw">Helpen bij het ontwerpen en opbouwen van decoraties, podia, stands, etc.</option>
                                </select>
                            </div>
                            <div class="terms" id="ivbk1desc">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque placerat id turpis quis dignissim. Maecenas elementum scelerisque pharetra. Sed tincidunt tincidunt purus, quis molestie eros blandit non. Vestibulum consequat dolor a enim porttitor, a vehicula mauris imperdiet. Ut et lectus vestibulum, finibus dolor posuere, elementum purus. Suspendisse at ipsum dapibus, dapibus neque ut, cursus lacus. Ut tristique id orci id aliquam. Integer consectetur magna ac justo ornare, nec imperdiet ipsum accumsan. 
                            </div>
                            <div class="terms" id="act1desc">
                                 Ut viverra pulvinar nisl, in dictum lacus. Aliquam non porta mauris, nec ornare mauris. Fusce metus neque, sodales id dictum vestibulum, vehicula non turpis. Nulla quis placerat enim. Morbi et lorem a dui pharetra interdum in vel nunc. In nec cursus lacus, eu egestas lorem. In elit felis, hendrerit quis enim non, sollicitudin tempus urna. Donec congue sollicitudin libero, non rutrum lorem fringilla vitae. Sed et tempus lectus. Nulla ac scelerisque leo. Sed fermentum facilisis sapien, vel suscipit odio porttitor tempus. Integer sit amet eros quis lorem interdum ullamcorper non quis nisi. Etiam aliquam massa nec magna volutpat, eget vehicula nibh laoreet. 
                            </div>
                            <div class="terms" id="afb1desc">
                                In luctus nisi vitae risus gravida placerat. Etiam quis aliquam metus. Vestibulum sed mattis diam. Nullam sollicitudin vel felis eu imperdiet. Nunc at diam porttitor, aliquam lectus sed, ornare est. Cras et lectus id elit commodo lobortis. Morbi feugiat massa lacus, sit amet mattis lorem convallis nec. Cras vehicula lacus quis risus tempus sagittis. Donec consectetur turpis libero, vitae lobortis arcu pretium quis. Ut ac turpis a ante volutpat pulvinar tincidunt vel enim. Maecenas pretium et diam ac feugiat. Curabitur finibus quam eu sagittis molestie. Duis facilisis pretium mi ut elementum. Praesent volutpat lectus eu mollis ullamcorper. 
                            </div>
                            <div class="terms" id="ontw1desc">
                                 Praesent quis lorem mollis, eleifend turpis gravida, interdum lacus. Vivamus ornare tellus turpis, id congue enim sagittis vel. Etiam dapibus, dui non posuere suscipit, nibh tellus tempor massa, id luctus velit lorem eu sapien. Integer suscipit ante non sapien sagittis luctus. Sed venenatis eros vel ante finibus, et vehicula quam varius. Etiam viverra venenatis dapibus. Nulla euismod nisi dolor, vitae varius libero facilisis non. Ut convallis augue in ultricies faucibus. Curabitur sed nunc quis nibh tincidunt semper.
                            </div>
                        </li>
                        <li id="contrib1row">
                            <span>
                                <label for="contrib1desc">Vertel iets over je ervaring hierin</label>
                                <textarea class="textarea" name="contrib1desc" id="contrib1desc" cols="60" rows="4"><?php echo $contrib1desc; ?></textarea>
                                <label for="contrib1desc">Max 256 karakters</label>
                            </span>
                        </li>
                        <li id="act1row">
                            <div>
                                <label for="act1type">Informatie over je act of performance</label>
                                <select class="field select" name="act1type" id="act1type">
                                    <option value="workshop">Workshop / Cursus</option>
                                    <option value="game">Ervaring / Game</option>
                                    <option value="lecture">Lezing</option>
                                    <option value="schmink">Schmink</option>
                                    <option value="other">Anders</option>
                                    <option value="perform">Performance</option>
                                    <option value="install">Installatie Beeld</option>
                                </select>
                            </div>
                            <div>
                                <label for="act1desc">Omschrijving van je act</label>
                                <textarea class="textarea" name="act1desc" id="act1desc" cols="60" rows="4"><?php echo $act1desc; ?></textarea>
                                <label for="act1desc">Max 256 karakters</label>
                            </div>
                            <div>
                                <label for="act1need">Wat heb je voor je act nodig?</label>
                                <textarea class="textarea" name="act1desc" id="act1need" cols="60" rows="4"><?php echo $act1need; ?></textarea>
                                <label for="act1need">Max 256 karakters</label>
                            </div>
                        </li>
                    </ul>
                </fieldset>
                <fieldset>
                    <legend>Partner</legend>
                    <ul>
                        <li>
                            <div class="terms">
                                Proin ultricies quis lacus in porttitor. Vivamus ullamcorper felis est, in congue neque bibendum sed. Donec egestas lorem quam, vitae ullamcorper tortor efficitur eu. Quisque varius elementum metus, vel luctus nunc elementum vitae. Curabitur lacinia ipsum velit, non facilisis est fermentum nec. Nam varius dolor vitae felis sagittis consectetur. Donec tortor ipsum, suscipit vitae augue non, tempor pellentesque sem. Vivamus aliquet arcu non felis dignissim, quis iaculis dui porta. Nulla pulvinar placerat est, quis sollicitudin augue elementum in. Nunc eleifend placerat dolor eu pulvinar. Proin venenatis auctor bibendum. In ac venenatis lectus, eget tempus augue. 
                            </div>
                            <div>
                                <input class="field text email" type="text" name"partner" id="partner" value="<?php echo $partner;?>"/>
                                <label for="email">E-mail</label>
                            </div>
                        </li>
                    </ul>
                </fieldset>
                <fieldset>
                    <legend>Voorwaarden</legend>
                    <ul>
                        <li>
                            <span>
                                <div class="terms">Nam sit amet varius orci, vitae venenatis quam. Vestibulum varius nulla non augue placerat, id feugiat tellus pulvinar. Etiam luctus elit massa. Proin in sem nulla. Maecenas sit amet turpis lectus. Donec id leo iaculis, tincidunt nibh venenatis, fringilla dolor. Nunc sit amet quam sem. Quisque eget purus lobortis, tempor odio ut, ultricies diam. Donec ac ultrices turpis. Maecenas egestas tristique dolor at consequat. Aenean sed lectus at lectus ornare iaculis. Ut viverra lectus tortor, ac lacinia dolor vestibulum at. Curabitur rutrum auctor nibh et tempor. </div>
                                <input class="field checkbox" type="checkbox" name="terms0" id="terms0" value="J">
                                <label for="terms0" class="choice">Ik ga akkoord met deze voorwaarden</label>
                            </span>
                        </li>
                        <li>
                            <span>
                                <div class="terms">Praesent fringilla bibendum efficitur. Curabitur hendrerit, neque posuere gravida tempus, nibh felis maximus justo, id aliquam enim risus id sapien. Aliquam lobortis eros et turpis egestas mattis. Nullam tortor nunc, condimentum a sem nec, porttitor ullamcorper erat. Nulla gravida cursus neque, molestie tempus mauris tincidunt a. Proin eu luctus nisi. Morbi ac pulvinar neque. Donec mollis diam elit, lacinia euismod massa gravida ut. Nullam metus orci, egestas eget libero at, porttitor bibendum ipsum. Ut ac justo mollis, pulvinar elit sit amet, accumsan ligula. Sed quis fringilla est. Integer quis risus vitae lectus accumsan consequat sed sit amet sem. Maecenas mi nisi, sagittis vitae pulvinar vitae, imperdiet non ante. Curabitur porttitor tristique sem vel ultricies.</div>
                                <input class="field checkbox" type="checkbox" name="terms1" id="terms1" value="J">
                                <label for="terms1" class="choice">Ik ga akkoord met deze voorwaarden</label>
                            </span>
                        </li>
                        <li>
                            <span>
                                <div class="terms">Morbi ac mauris arcu. Donec ac sollicitudin lectus. Donec imperdiet volutpat purus quis suscipit. Cras eu purus congue, imperdiet nisl vel, tristique urna. Nulla facilisi. Donec neque dui, lobortis at felis et, porttitor aliquet erat. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce consectetur luctus felis, in vehicula est aliquam vel. Sed mollis at libero sit amet cursus. Aliquam libero orci, ultricies a lobortis nec, finibus eget sapien. Curabitur eget auctor diam. Phasellus cursus lectus in semper mattis. Nunc vitae scelerisque lorem, ut mollis lacus. Duis fringilla risus in odio fermentum mattis. Mauris turpis metus, molestie vitae leo id, pellentesque vestibulum erat. Pellentesque ac lacinia dui, malesuada blandit risus. </div>
                                <input class="field checkbox" type="checkbox" name="terms1" id="terms1" value="J">
                                <label for="terms1" class="choice">Ik ga akkoord met deze voorwaarden</label>
                            </span>
                        </li>
                    </ul>
                </fieldset>
            <input class="submit" type="submit" name="submit" value="Versturen"/>
            </form>
        </div>

        
    </body>
</html>
