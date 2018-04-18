<?php

if( !defined('PERMISSION_PARTICIPANT')){
	define('PERMISSION_PARTICIPANT', 1);
}
if( !defined('PERMISSION_DISPLAY')){
	define('PERMISSION_DISPLAY', 2);
}
if( !defined('PERMISSION_RAFFLE')){
    define('PERMISSION_RAFFLE',4);
}
if( !defined('PERMISSION_EDIT')){
   define( 'PERMISSION_EDIT',8);
}
if( !defined('PERMISSION_CALLER')) {
	define('PERMISSION_CALLER', 16);
}
if( !defined('PERMISSION_VOLUNTEERS')) {
	define('PERMISSION_VOLUNTEERS', 32);
}
if( !defined('PERMISSION_ACTS')) {
	define('PERMISSION_ACTS', 64);
}
if( !defined('PERMISSION_BAR')) {
	define('PERMISSION_BAR', 128);
}
if( !defined('PERMISSION_ALL')) {
	define('PERMISSION_ALL', 65535);
}

$current_table = 'fv2018';

?>