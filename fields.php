<?php

if( !defined('PERMISSION_DISPLAY')){
	define('PERMISSION_DISPLAY', 1);
}
if( !defined('PERMISSION_RAFFLE')){
    define('PERMISSION_RAFFLE',2);
}
if( !defined('PERMISSION_EDIT')){
   define( 'PERMISSION_EDIT',3);
}
if( !defined('PERMISSION_REMOVE')) {
    define('PERMISSION_REMOVE', 4);
}
if( !defined('PERMISSION_USER')) {
    define('PERMISSION_USER', 5);
}

$db_table_person    = "person";
$db_table_contrib   = "contribution";
$db_table_raffle    = "raffle";
$db_table_buyer     = "buyer";
$db_table_users		= "users";

$db_person_first    = "firstname";
$db_person_last     = "lastname";
$db_person_birth    = "birthdate";
$db_person_email    = "email";
$db_person_city     = "city";
$db_person_gender   = "gender";
$db_person_phone    = "phone";
$db_person_visits    = "visits";
$db_person_editions = "editions";
$db_person_partner  = "partner";
$db_person_motivation = "motivation";
$db_person_familiar = "familiar";
$db_person_date		= "signupdate";

$db_person_contrib0 = "contrib0";
$db_person_contrib1 = "contrib1";
$db_person_preparations = "preparations";
$db_person_terms0   = "terms0";
$db_person_terms1   = "terms1";
$db_person_terms2   = "terms2";
$db_person_terms3   = "terms3";

$db_contrib_id      = "id";
$db_contrib_type    = "type";
$db_contrib_desc    = "description";
$db_contrib_needs   = "needs";

$db_raffle_code     = "code";
$db_raffle_email    = "email";
$db_raffle_called   = "called";

$db_buyer_email     = "email";
$db_buyer_raffle    = "code";
$db_buyer_id        = "id";
$db_buyer_complete  = "complete";

$db_user_username  		= "username";
$db_user_password		= "password";
$db_user_name 			= "name";
$db_user_permissions	= "permissions";

$db_fk_person_contrib0 = "fk_percontrib0";
$db_fk_person_contrib1 = "fk_percontrib1";

$db_fk_raffle_person = "fk_rafper";

$db_fk_buyer_person = "fk_buyper";
$db_fk_buyer_raffle = "fk_buyraf";

?>
