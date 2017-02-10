<?php
include_once "../functions.php";

?>
<div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">
<?php
if( $user_permissions & PERMISSION_PARTICIPANT ) {
    echo "<ul class='nav first'>";
    echo "<li><a class='' id='ik' href='ik'>Mijn gegevens</a></li>";
    echo "<li><a class='menulink' id='voorjaar' href='voorjaar'>Familiar Voorjaar 2017</a></li>";
    echo "<li><a class='menulink' id='signup' href='signup'>Inschrijven Familiar Voorjaar</a></li>";
    //echo "<li><a class='menulink' id='forest' href='forest'>Familiar Forest 2017</a></li>";    
    echo "</ul>";
}

if( $user_permissions & PERMISSION_DISPLAY ) {
    echo "<ul class='nav'>";
    echo "<li><a class='menulink' id='displaysignup' href='signups'>Inschrijvingen Voorjaar</a></li>";
    echo "</ul>";
}

if( $user_permissions & PERMISSION_RAFFLE ) {
    echo "<ul class='nav'>";
    echo "<li><a class='menulink' href='raffle'>Loten</a></li>";
    echo "<li><a class='menulink' href='showraffle'>Voorjaar Loting</a></li>";
    echo "</ul>";
}

if( $user_permissions & PERMISSION_PARTICIPANT ) {
    echo "<ul class='nav'>";
    echo "<li><a class='menulink' id='logout' href='logout'>Uitloggen</a></li>";
    echo "</ul>";
}
/*
if( $user_permissions & PERMISSION_DISPLAY ) {
    echo "<ul class='nav'>";
    echo "<li><a class='menulink' id='displaysignup' href='signups'>Inschrijvingen</a></li>";
    echo "<li><a class='menulink' id='displayraffle' href='displayraffle'>Loting</a></li>";
    echo "<li><a class='menulink' id='displaybuyers' href='buyers'>Verkochte tickets</a></li>";
    echo "<li><a class='menulink' id='displaytransactions' href='verifypayments'>Transacties</a></li>";
    echo "<li><a class='menulink' id='preparations' href='preparations'>Voorbereidingen</a></li>";
    echo "</ul>";
}

if( $user_permissions & PERMISSION_CALLER) {
    echo "<ul class='nav '>";
    //echo "<li><a class='menulink' id='callerview' href='callerview''>Bellen</a></li>";
    echo "<li><a class='menulink' id='calleroverview' href='calleroverview''>Bel lijst</a></li>";
    echo "</ul>";
}
if( $user_permissions & PERMISSION_EDIT ) {
    echo "<ul class='nav '>";
    echo "<li><a class='menulink' id='editsignup' href='#''>Wijzigingen</a></li>";
    echo "<li><a class='menulink' id='removesignup' href='#''>Verwijderen</a></li>";
    echo "</ul>";
}
if( $user_permissions & PERMISSION_USER) {
    echo "<ul class='nav '>";
    echo "<li><a class='menulink' id='massmail' href='massmail'>Massa Email</a></li>";
    echo "<li><a class='menulink' id='usermanage' href='users'>Gebruikers</a></li>";
    echo "</ul>";
}
if( $user_permissions & PERMISSION_VOLUNTEERS ) {
    echo "<ul class='nav '>";
    echo "<li><a class='menulink' id ='volunteers' href='volunteers'>Vrijwilligers</a></li>";
    echo "<li><a class='menulink' id ='volunteerbar' href='volunteerbar'>Bar</a></li>";
    echo "<li><a class='menulink' id ='kitchen' href='kitchen'>Keuken</a></li>";
    echo "<li><a class='menulink' id ='interiour' href='interiour'>Interieur</a></li>";
    echo "<li><a class='menulink' id ='camping' href='campingwinkel'>Campingwinkel</a></li>";
    echo "<li><a class='menulink' id ='theetent' href='theetent'>Theetent</a></li>";
    echo "<li><a class='menulink' id ='break' href='break'>Afbouw</a></li>";
    echo "<li><a class='menulink' id ='other' href='other'>Anders</a></li>";
    echo "<li><a class='menulink' id ='search' href='volunteersearch'>Zoeken</a></li>";
    echo "</ul>";

}
if( $user_permissions & PERMISSION_ACTS ) {
    echo "<ul class='nav '>";
    echo "<li><a class='menulink' id ='search' href='volunteersearch'>Zoeken</a></li>";
    echo "<li><a class='menulink' id ='acts' href='acts'>Niet ingedeelde Acts</a></li>";
    echo "<li><a class='menulink' href='workshops'>Workshops</a></li>";
    echo "<li><a class='menulink' href='games'>Games</a></li>";
    echo "<li><a class='menulink' href='lectures'>Lezingen</a></li>";
    echo "<li><a class='menulink' href='schmink'>Schmink</a></li>";
    echo "<li><a class='menulink' href='otheracts'>Anders</a></li>";
    echo "<li><a class='menulink' href='perform'>Performance</a></li>";
    echo "<li><a class='menulink' href='install'>Installatie</a></li>";
    echo "<li><a class='menulink' id ='rejectedacts' href='rejectedacts'>Afgewezen Acts</a></li>";
    echo "</ul>";
}
if( $user_permissions & PERMISSION_BAR ) {
    echo "<ul class='nav '>";
    echo "<li><a class='menulink' href='bar'>Bar</a></li>";
    echo "</ul>";
}
if( $user_permissions & PERMISSION_NACHT ) {
    echo "<ul class='nav '>";
    echo "<li><a class='menulink' href='nachtprogramma'>Nachtprogramma</a></li>";
    echo "</ul>";
}
*/
?>
</div>
