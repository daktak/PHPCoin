<?php
  defined("_V") || die("Direct access not allowed!");
  
  $db_user = ""; //Your database username
  $db_pass = ""; //Your database password
  $db_name = ""; //Your database name
  $db_host = "localhost"; //Your database host
  $btc_user = ""; //Your bitcoind username (set in rpcusername in bitcoin.conf)
  $btc_pass = ""; //Your bitcoind password (set in rpcusername in bitcoin.conf)
  $btc_ip = "127.0.0.1";
  $btc_port = "8332";
  
//----------------------- NOTHING TO CONFIGURE BELLOW THIS LINE ---------//
  mysql_connect($db_host,$db_user,$db_pass) || die("Unable to connect to DB!");
  mysql_select_db($db_name) || die("Unable to select DB!");
  
  $config = array();
  $sql = "SELECT * FROM config";
  $q = mysql_query($sql);
  while($r = mysql_fetch_assoc($q)){
      $config[$r['key']] = array("value" => $r['value'], "explain" => $r['explain']);
  }
?>
