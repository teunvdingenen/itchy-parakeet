<?php
include_once "../model/loginmanager.php";
?>
<div class="col-xs-5 col-sm-2 sidebar-offcanvas" id="sidebar" role="navigation">
<?php
if( model\LoginManager::Instance()->getPermissions() & PERMISSION_PARTICIPANT ) {
    echo "<ul class='nav first'>";
    echo "<li><a class='' href='ik'>Mijn gegevens</a></li>";
    echo "<li><a class='menulink' href='future'>Back to the FFFuture: '95</a></li>";
    echo "<li><a class='menulink' href='signup'>Inschrijven</a></li>";
    echo "</ul>";
}

if( model\LoginManager::Instance()->getPermissions() & PERMISSION_DISPLAY ) {
    echo "<ul class='nav'>";
    echo "<li><a class='menulink' href='signups'>Inschrijvingen</a></li>";
    echo "<li><a class='menulink' href='buyer'>Tickets</a></li>";
    echo "<li><a class='menulink' href='displayraffle'>Loting</a></li>";
    echo "<li><a class='menulink' href='preparations'>Voorbereiding aanbod</a></li>";
    echo "<li><a class='menulink' href='swap'>Ticketaanbod in swap</a></li>";
    echo "</ul>";
}

if( model\LoginManager::Instance()->getPermissions() & PERMISSION_VOLUNTEERS ) {
    echo "<ul class='nav'>";
    echo "<li><a class='menulink' href='shifts?t=vrijwilligers'>Vrijwilliger shifts</a></li>";
    echo "<li><a class='menulink' href='indelen?t=vrijwilligers'>Vrijwilligers indelen</a></li>";
    echo "</ul>";
}

if( model\LoginManager::Instance()->getPermissions() & PERMISSION_ACTS ) {
    echo "<ul class='nav'>";
    echo "<li><a class='menulink' href='shifts?t=acts'>Act shifts</a></li>";
    echo "<li><a class='menulink' href='indelen?t=acts'>Acts indelen</a></li>";
    echo "</ul>";
}

if( model\LoginManager::Instance()->getPermissions() & PERMISSION_RAFFLE ) {
    echo "<ul class='nav'>";
    echo "<li><a class='menulink' href='raffle'>Loten</a></li>";
    echo "<li><a class='menulink' href='unraffle'>Uitloten</a></li>";
    echo "</ul>";
}

if( model\LoginManager::Instance()->getPermissions() & PERMISSION_CALLER ) {
    echo "<ul class='nav'>";
    echo "<li><a class='menulink' href='bellen'>Bellijst</a></li>";
    echo "</ul>";
}

if( model\LoginManager::Instance()->getPermissions() & PERMISSION_EDIT ) {
    echo "<ul class='nav'>";
    echo "<li><a class='menulink' href='crew'>Crew</a></li>";
    echo "<li><a class='menulink' href='people'>Accounts</a></li>";
    echo "</ul>";
}

if( model\LoginManager::Instance()->getPermissions() & PERMISSION_PARTICIPANT ) {
    echo "<ul class='nav'>";
    echo "<li><a class='menulink' id='logout' href='logout'>Uitloggen</a></li>";
    echo "</ul>";
}

?>
</div>
