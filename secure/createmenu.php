<?php

function get_menu_html() {
    global $db_user_name, $db_user_permissions;
    $menu_html = "";
    $user_info = get_user_info($_SESSION['loginuser']);
    $user_info_name = $user_info[$db_user_name];
    $user_info_permissions = $user_info[$db_user_permissions];


    if( $user_info_permissions & PERMISSION_DISPLAY ) {
        $menu_html .= "<ul class='nav nav-sidebar'>";
        $menu_html .= "<li><a class='menulink' id ='showstats' href='index'>Main</a></li>";
        $menu_html .= "<li><a class='menulink' id='displaysignup' href='signups'>Inschrijvingen</a></li>";
        $menu_html .= "<li><a class='menulink' id='displayraffle' href='displayraffle'>Loting</a></li>";
        $menu_html .= "<li><a class='menulink' id='displaybuyers' href='buyers'>Verkochte tickets</a></li>";
        $menu_html .= "<li><a class='menulink' id='displaytransactions' href='verifypayments'>Transacties</a></li>";
        $menu_html .= "</ul>";
    }
    if( $user_info_permissions & PERMISSION_RAFFLE ) {
        $menu_html .= "<ul class='nav nav-sidebar'>";
        $menu_html .= "<li><a class='menulink' id='raffle' href='raffle'>Loten</a></li>";
        $menu_html .= "</ul>";
    }
    if( $user_info_permissions & PERMISSION_CALLER) {
        $menu_html .= "<ul class='nav nav-sidebar'>";
        //$menu_html .= "<li><a class='menulink' id='callerview' href='callerview''>Bellen</a></li>";
        $menu_html .= "<li><a class='menulink' id='calleroverview' href='calleroverview''>Bel lijst</a></li>";
        $menu_html .= "</ul>";
    }
    if( $user_info_permissions & PERMISSION_EDIT ) {
        $menu_html .= "<ul class='nav nav-sidebar'>";
        $menu_html .= "<li><a class='menulink' id='editsignup' href='#''>Wijzigingen</a></li>";
        $menu_html .= "<li><a class='menulink' id='removesignup' href='#''>Verwijderen</a></li>";
        $menu_html .= "</ul>";
    }
    if( $user_info_permissions & PERMISSION_USER) {
        $menu_html .= "<ul class='nav nav-sidebar'>";
        $menu_html .= "<li><a class='menulink' id='massmail' href='massmail'>Massa Email</a></li>";
        $menu_html .= "<li><a class='menulink' id='usermanage' href='users'>Gebruikers</a></li>";
        $menu_html .= "</ul>";
    }
    if( $user_info_permissions & PERMISSION_VOLUNTEERS ) {
        $menu_html .= "<ul class='nav nav-sidebar'>";
        $menu_html .= "<li><a class='menulink' id ='volunteers' href='volunteers'>Vrijwilligers</a></li>";
        $menu_html .= "<li><a class='menulink' id ='bar' href='bar'>Bar</a></li>";
        $menu_html .= "<li><a class='menulink' id ='kitchen' href='kitchen'>Keuken</a></li>";
        $menu_html .= "<li><a class='menulink' id ='interiour' href='interiour'>Interieur</a></li>";
        $menu_html .= "<li><a class='menulink' id ='camping' href='campingwinkel'>Campingwinkel</a></li>";
        $menu_html .= "<li><a class='menulink' id ='theetent' href='theetent'>Theetent</a></li>";
        $menu_html .= "<li><a class='menulink' id ='other' href='other'>Anders</a></li>";
        $menu_html .= "</ul>";

    }
    if( $user_info_permissions & PERMISSION_ACTS ) {
        $menu_html .= "<ul class='nav nav-sidebar'>";
        $menu_html .= "<li><a class='menulink' id ='acts' href='acts'>Acts</a></li>";
        $menu_html .= "<li><a class='menulink' href='workshops'>Workshops</a></li>";
        $menu_html .= "<li><a class='menulink' href='games'>Games</a></li>";
        $menu_html .= "<li><a class='menulink' href='lectures'>Lezingen</a></li>";
        $menu_html .= "<li><a class='menulink' href='schmink'>Schmink</a></li>";
        $menu_html .= "<li><a class='menulink' href='otheracts'>Anders</a></li>";
        $menu_html .= "<li><a class='menulink' href='perform'>Performance</a></li>";
        $menu_html .= "<li><a class='menulink' href='install'>Installatie</a></li>";
        $menu_html .= "<li><a class='menulink' id ='rejectedacts' href='rejectedacts'>Afgewezen Acts</a></li>";
        $menu_html .= "</ul>";
    }
    if( $user_info_permissions & PERMISSION_BUYERS ) {
        $menu_html .= "<ul class='nav nav-sidebar'>";
        $menu_html .= "<li><a class='menulink' href='deelnemers'>Deelnemers</a></li>";
        $menu_html .= "</ul>";
    }
    return $menu_html;
}

?>