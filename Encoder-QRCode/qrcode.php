 <?php

 	//error_reporting(E_ALL);

    include('../lib/phpqrcode/qrlib.php');
    
    // outputs image directly into browser, as PNG stream
    QRcode::png('Je creer un qr code', 'test.png', 'L', 4); 

    //echo 'qrcode ok';

 ?>
