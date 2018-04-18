<?php
include_once "../functions.php";

?>
<div class="col-xs-5 col-sm-2 sidebar-offcanvas" id="sidebar" role="navigation">
<?php
if( $user_permissions & PERMISSION_PARTICIPANT ) {
    echo "<ul class='nav first'>";
    echo "<li><a class='' href='ik'>Mijn gegevens</a></li>";
    echo "<li><a class='menulink' href='future'>Back to the FFFuture: '95</a></li>";
    if( !add_buy($user_email) ) {
        echo "<li><a class='menulink' href='signup'>Inschrijven</a></li>";
    }
    if( add_buy($user_email) ) {
        echo "<li><a class='menulink' href='deelname'>Deelname</a></li>";
    }
    if( add_swap($user_email) ) {
        echo "<li><a class='menulink' href='ticketruil'>Ticketruil</a></li>";    
    }
    if( add_ticket($user_email) ) {
        //echo "<li><a class='menulink' href='info'>Reis Info</a></li>";
        echo "<li><a class='menulink' href='ticket'>Ticket</a></li>";
        //echo "<li><a class='menulink' href='ticketpdf' target='_blank'>Ticket [PDF]</a></li>";
    }
    echo "</ul>";
}

if( $user_permissions & PERMISSION_DISPLAY ) {
    echo "<ul class='nav'>";
    echo "<li><a class='menulink' href='signups'>Inschrijvingen</a></li>";
    echo "<li><a class='menulink' href='buyer'>Tickets</a></li>";
    echo "<li><a class='menulink' href='displayraffle'>Loting</a></li>";
    echo "<li><a class='menulink' href='preparations'>Voorbereiding aanbod</a></li>";
    echo "<li><a class='menulink' href='swap'>Ticketaanbod in swap</a></li>";
    echo "</ul>";
}

if( $user_permissions & PERMISSION_VOLUNTEERS ) {
    echo "<ul class='nav'>";
    echo "<li><a class='menulink' href='shifts?t=vrijwilligers'>Vrijwilliger shifts</a></li>";
    echo "<li><a class='menulink' href='indelen?t=vrijwilligers'>Vrijwilligers indelen</a></li>";
    echo "</ul>";
}

if( $user_permissions & PERMISSION_ACTS ) {
    echo "<ul class='nav'>";
    echo "<li><a class='menulink' href='shifts?t=acts'>Act shifts</a></li>";
    echo "<li><a class='menulink' href='indelen?t=acts'>Acts indelen</a></li>";
    echo "</ul>";
}

if( $user_permissions & PERMISSION_RAFFLE ) {
    echo "<ul class='nav'>";
    echo "<li><a class='menulink' href='raffle'>Loten</a></li>";
    echo "<li><a class='menulink' href='unraffle'>Uitloten</a></li>";
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
    echo "<li><a class='menulink' href='people'>Accounts</a></li>";
    echo "</ul>";
}

if( $user_permissions & PERMISSION_PARTICIPANT ) {
    echo "<ul class='nav'>";
    echo "<li><a class='menulink' id='logout' href='logout'>Uitloggen</a></li>";
    echo "</ul>";
}


function add_buy($email) {
    global $db_host, $db_user, $db_pass, $db_name, $current_table;
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if( $mysqli->connect_errno ) {
        return false;
    }
    $result = $mysqli->query(sprintf("SELECT rafflecode, valid, complete FROM $current_table WHERE `email` = '%s'",
        $mysqli->real_escape_string($email)));
    $swapresult = $mysqli->query(sprintf("SELECT 1 FROM swap where `buyer` = '%s' and lock_expire > now()",
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
    if( !$swapresult || $swapresult->num_rows == 0 ) {
        return false;
    } else {
        return true;
    }
    return false;
}

function add_swap($email) {
    global $db_host, $db_user, $db_pass, $db_name, $current_table;
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if( $mysqli->connect_errno ) {
        return false;
    }
    $result = $mysqli->query(sprintf("SELECT 1 FROM $current_table WHERE `email` = '%s'",
        $mysqli->real_escape_string($email)));
    $mysqli->close();
    if( !$result || $result->num_rows < 1 ) {
        return false;
    } else {
        return true;
    }
    return false;
}

function add_ticket($email) {
    global $db_host, $db_user, $db_pass, $db_name, $current_table;
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if( $mysqli->connect_errno ) {
        return false;
    }
    $result = $mysqli->query(sprintf("SELECT 1 FROM $current_table WHERE `email` = '%s' and complete = 1 and ticket != ''",
        $mysqli->real_escape_string($email)));
    $mysqli->close();
    if( !$result || $result->num_rows < 1 ) {
        return false;
    } else {
        return true;
    }
    return false;
}

?>
</div>
