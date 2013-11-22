<?php
  defined("_V") || die("Direct access not allowed!");
  
  $db_user = ""; //Your database username
  $db_pass = ""; //Your database password
  $db_name = ""; //Your database name
  $db_host = "localhost"; //Your database host
  $coin_list = array("Bitcoin"); //List of Crypto Coin wallets
  $coin_code = array("BTC"); //List of coin trading codes
  $btc_user = array(""); //Your bitcoind username (set in rpcusername in bitcoin.conf)
  $btc_pass = array(""); //Your bitcoind password (set in rpcusername in bitcoin.conf)
  $btc_ip = array("127.0.0.1");
  $btc_port = array("8332");
  
//----------------------- NOTHING TO CONFIGURE BELLOW THIS LINE ---------//
  ($GLOBALS["___mysqli_ston"] = mysqli_connect($db_host, $db_user, $db_pass)) || die("Unable to connect to DB!");
  ((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE $db_name")) || die("Unable to select DB!");
  
  $config = array();
  $sql = "SELECT * FROM config";
  $q = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
  while($r = mysqli_fetch_assoc($q)){
      $config[$r['key']] = array("value" => $r['value'], "explain" => $r['explain']);
  }
?>
