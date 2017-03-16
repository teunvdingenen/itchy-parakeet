<?php
include_once "../functions.php";

?>
<div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">
<?php
if( $user_permissions & PERMISSION_PARTICIPANT ) {
    echo "<ul class='nav first'>";
    echo "<li><a class='' href='ik'>Mijn gegevens</a></li>";
    echo "<li><a class='menulink' href='voorjaar'>Familiar Voorjaar 2017</a></li>";
    if( strtotime('now') < strtotime('2017-03-16 10:00') && !add_buy($user_email)) {
        echo "<li><a class='menulink' href='signup'>Inschrijven Familiar Voorjaar</a></li>";
    }
    //echo "<li><a class='menulink' id='forest' href='forest'>Familiar Forest 2017</a></li>";
    if( add_buy($user_email) ) {
        echo "<li><a class='menulink' href='deelname'>Deelname Familiar Voorjaar</a></li>";
    }
    echo "</ul>";
}

if( $user_permissions & PERMISSION_DISPLAY ) {
    echo "<ul class='nav'>";
    echo "<li><a class='menulink' href='signups'>Inschrijvingen Voorjaar</a></li>";
    echo "<li><a class='menulink' href='buyer'>Tickets Voorjaar</a></li>";
    echo "</ul>";
}

if( $user_permissions & PERMISSION_RAFFLE ) {
    echo "<ul class='nav'>";
    echo "<li><a class='menulink' href='raffle'>Loten</a></li>";
    echo "<li><a class='menulink' href='showraffle'>Voorjaar Loting</a></li>";
    echo "</ul>";
}

if( $user_permissions & PERMISSION_CALLER ) {
    echo "<ul class='nav'>";
    echo "<li><a class='menulink' href='bellen'>Bellijst</a></li>";
    echo "</ul>";
}

if( $user_permissions & PERMISSION_EDIT ) {
    echo "<ul class='nav'>";
    echo "<li><a class='menulink' href='crew'>Crew</a></li>";
    echo "</ul>";
}

if( $user_permissions & PERMISSION_PARTICIPANT ) {
    echo "<ul class='nav'>";
    echo "<li><a class='menulink' id='logout' href='logout'>Uitloggen</a></li>";
    echo "</ul>";
}


//TODO close mysqli
function add_buy($email) {
    global $db_host, $db_user, $db_pass, $db_name, $current_table;
    if( strtotime('now') > strtotime('2017-04-07 00:00') ) {
        return false;
    }
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if( $mysqli->connect_errno ) {
        return false;
    }
    $result = $mysqli->query(sprintf("SELECT rafflecode, valid FROM $current_table WHERE `email` = '%s'",
        $mysqli->real_escape_string($email)));
    $mysqli->close();
    if( !$result ) {
        return false;
    } else {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        if( $row['rafflecode'] != "" && $row['valid'] == 1 && $row['complete'] != 1 ) {
            return true;
        }
    }
    return false;
}

?>
</div>
