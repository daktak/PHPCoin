<?php
    defined("_V") || die("Direct access not allowed!");

    $new = $b[$_SESSION['wallet']]->getnewaddress($_SESSION['btaccount']);
    $file = "cache/".$new.".png";
    if (isset($phpqrcode)) {
      include $phpqrcode;
      if (!file_exists($file)) {
	     QRCode::png($new,$file);
	}
    }
    echo $new;
?>
