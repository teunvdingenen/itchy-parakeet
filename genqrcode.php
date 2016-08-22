<?php 

    include('phpqrcode/qrlib.php');
     
    // outputs image directly into browser, as PNG stream 
    QRcode::png('code=AA11BB22&id=tr_kdjlfjkl',FALSE,3);

?>