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
if( !defined('PERMISSION_REMOVE')) {
    define('PERMISSION_REMOVE', 16);
}
if( !defined('PERMISSION_USER')) {
    define('PERMISSION_USER', 32);
}
if( !defined('PERMISSION_CALLER')) {
	define('PERMISSION_CALLER', 64);
}
if( !defined('PERMISSION_VOLUNTEERS')) {
	define('PERMISSION_VOLUNTEERS', 128);
}
if( !defined('PERMISSION_ACTS')) {
	define('PERMISSION_ACTS', 256);
}
if( !defined('PERMISSION_BAR')) {
	define('PERMISSION_BAR', 512);
}
if( !defined('PERMISSION_NACHT')) {
	define('PERMISSION_NACHT', 1024);
}
if( !defined('PERMISSION_ALL')) {
	define('PERMISSION_ALL', 65535);
}

$current_table = 'fv2018';

?>