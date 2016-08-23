<?php 

    include('phpqrcode/qrlib.php');

    if( !isset($_GET['hash'])) {
    	return false;
    }
     
    // outputs image directly into browser, as PNG stream 
    QRcode::png($_GET['hash'],FALSE,3);

?>