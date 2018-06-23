<?php
include_once "initialize.php";
include_once "fields.php";
include_once "sendmail.php";

function email_error($message) {
    send_mail('info@stichtingfamiliarforest.nl', 'Web Familiar Forest', 'Found ERROR!', $message);  
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function translate_contrib($type) {
  if( $type == "iv") {
    return "Interieurverzorging";
  } else if( $type == "bar" ) {
    return "Bar";
  } else if( $type == "keuken" ) {
    return "Keuken";
  } else if( $type == "workshop" ) {
    return "Workshop of Cursus";
  } else if( $type == "game" ) { 
    return "Ervaring of Game";
  } else if ( $type == "lecture" ) {
    return "Lezing";
  } else if( $type == "other" ) { 
    return "Anders";
  } else if ( $type == "perform" ) {
    return "Performance";
  } else if( $type == "install" ) {
    return "Installatie of Beeldend";
  } else if( $type == "afb" ) {
    return "Afbouw";
  } else if( $type == "opb" ) {
    return "Opbouw";
  } else if( $type == "ontw" ) {
    return "Ontwerpen en/of bouw decoraties, podia etc.";
  } else if( $type == "" ) {
    return "";
  } else if( $type == "other_act" ) {
    return "Act Overig";
  } else {
    return $type;
  }
}

function translate_edition($edition) {
  if( $edition == "fff2010" ) {
    return "Familiar Forest Festival 2010";
  } else if( $edition == "fff2011" ) {
    return "Familiar Forest Festival 2011";
  } else if( $edition == "ffcastle" ) {
    return "Familiar Castle Festival";
  } else if( $edition == "fwf2012" ) {
    return "Familiar Winter Festival 2012";
  } else if( $edition == "fh2012" ) {
    return "Familiar Hemelvaartsnacht 2012";
  } else if( $edition == "fff2012" ) {
    return "Familiar Forest Festival 2012";
  } else if( $edition == "fh2013" ) {
    return "Familiar Hemelvaartsnacht 2013";
  } else if( $edition == "fwf2013" ) {
    return "Familiar Winter Festival 2013";
  } else if( $edition == "fff2013" ) {
    return "Familiar Forest Festival 2013";
  } else if( $edition == "fwf2014" ) {
    return "Familiar Winter Festival 2014";
  } else if( $edition == "fff2014" ) {
    return "Familiar Forest Festival 2014";
  } else if( $edition == "fwf2015" ) {
    return "Familiar Winter Festival 2015";
  } else if( $edition == "fff2015" ) {
    return "Familiar Forest Festival 2015";
  } else if( $edition == "fff2016" ) {
    return "Familiar Forest Festival 2016";
  } else if( $edition == "fv2017" ) {
    return "Familiar Voorjaar 2017";
  } else if ( $edition == "fff2017") {
    return "Familiar Forest 2017";
  } else if( $edition == "" ) {
    return "";
  } else {
    return "Onbekend";
  }
}

function translate_gender($gender) {
  if( $gender == "male" ) { 
    return "Jongeman";
  } else if( $gender == "female" ) {
    return "Jongedame";
  } else {
    return "Onbekend";
  }
}

function translate_task($task) {
  if( $task == "" ) {
    return "Niet ingedeeld: ".$task;
  } else if( $task == "keuken") { 
    return "Keuken";
  } else if( $task == "bar" ) {
    return "Bar";
  } else if( $task == "other" ) {
    return "Anders";
  } else if ($task == "iv" ) {
    return "Interieur Verzorging";
  } else if ( $task == "thee") {
    return "Theetent";
  } else if( $task == "camping") {
    return "Campingwinkel";
  } else if ($task == "afb" ) {
    return "Afbouw";
  } else if ($task == "act" ) {
    return "Act, niet ingedeeld";
  } else if ( $task == "game" ) {
    return "Game";
  } else if ( $task == "lecture" ) {
    return "Lezing";
  } else if ( $task == "schmink" ) {
    return "Schmink";
  } else if ( $task == "other_act" ) {
    return "Act anders";
  } else if ( $task == "perform" ) {
    return "Performance";
  } else if ( $task == "install" ) {
    return "Installatie";
  } else if( $task == "workshop" ) {
    return "Workshop";
  } else if( $task == "crew" ) {
    return "Crew";
  } else if( $task == "other_act") {
    return "Act Overig";
  } else if( $task == "jip" ) {
    return "Jips hoekje";
  } else if( $task = "silent" ) {
    return "Silent Disco";
  } else if( $task == "vuur") { 
    return "Vuurmeester";
  }
  return "Onbekend: ".$task;
}

function is_act($task) {
  return in_array($task, ['act','game','lecture','schmink','other_act','perform','install','workshop']);
}
?>
