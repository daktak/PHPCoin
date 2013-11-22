<?php
  defined("_V") || die("Direct access not allowed!");
  
  $db_user = ""; //Your database username
  $db_pass = ""; //Your database password
  $db_name = ""; //Your database name
  $db_host = "localhost"; //Your database host
  //You can specify multiple daemons eg
  //$coin_list = array("Bitcoin", "Namecoin", "Litecoin");
  //If you add a new coin type, you will need to add a new account for 
  //that coin type using accounts/create account
  $coin_list = array("Bitcoin"); 
  //Specify the trading code, eg
  //$coin_code = array("BTC","NMC","LTC");
  $coin_code = array("BTC"); 
  //specify username from bitcoin.conf for each daemon, eg
  //btc_user = array("bitcoinrpc", "namecoinrpc", "litecoinrpc");
  $btc_user = array(""); 
  //specify each password
  $btc_pass = array(""); 
  //Specify each host machine
  $btc_ip = array("127.0.0.1");
  //specify each port, eg
  //$btc_port = array("8332","8336","9332");
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
