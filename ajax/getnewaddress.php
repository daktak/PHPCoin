<?php
    defined("_V") || die("Direct access not allowed!");

    $new = $b[$_SESSION['wallet']]->getnewaddress($_SESSION['btaccount']);
    echo $new;
?>
