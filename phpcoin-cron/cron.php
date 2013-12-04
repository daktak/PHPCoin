<?php
  define("_V",1);
  //This file must NOT be accessible from the Web!
  $cron_dir = substr(__FILE__, 0, strrpos(__FILE__, '/'));
  $coin_install_path = substr($cron_dir,0,strrpos($cron_dir,'/'));

  include($coin_install_path ."/sys/config.php");
  include($coin_install_path ."/inc/general_functions.php");
  error_reporting(E_ALL);
  ini_set("display_errors",1);
  include($coin_install_path ."/classes/jsonRPCClient.php");
  
  //Starting CRON sequence
  
 for ($x=0; $x < count($coin_list); $x++) {

  $b[$x] = new jsonRPCClient("http://$btc_user[$x]:$btc_pass[$x]@$btc_ip[$x]:$btc_port[$x]");
  
  //Checking for new deposits
  //$accounts = $b->listaccounts((int)$config['confirmations']['value']);
  $accounts = $b[$x]->listaccounts(1); //Test only
  
  foreach($accounts as $k => $a){
      if($a == 0) continue; //Nothing to do
      $acc = explode("_",$k);
      if(!is_array($acc) || sizeof($acc) != 3) continue; //Invalid account identifier
      //Get the account
      $sql = "SELECT * FROM accounts WHERE uid = {$acc[1]} AND account_id = {$acc[2]}";
      $q = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
      if(!mysqli_num_rows($q)) continue; //Account not found
      $act = mysqli_fetch_assoc($q);
      $b[$x]->move($k,$config['central_account']['value'],$a);
      $prevBal = 0;
      $sql = "SELECT balance FROM movements WHERE account_id = {$act['id']} ORDER BY id DESC LIMIT 0,1";
      $q = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
      if(mysqli_num_rows($q)){
          $pbal = mysqli_fetch_assoc($q);
          $prevBal = $pbal['balance'];
      }
      $newBal = $prevBal + $a;
      //Get the current block
      $cBlock = $b[$x]->getblockcount();      
      mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO movements(`account_id`,`dtime`,`description`,`amount`,`credit`,`balance`,`txblock`) VALUES({$act['id']},'".date("Y-m-d H:i:s")."','{$coin_list[$x]} deposit',$a,1,$newBal,$cBlock)");
      mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE accounts SET balance = balance + $a WHERE id = {$act['id']}");
      
      //Check if account is forwarded
      if($act['forward'] == 1){
          $isValid = $b[$x]->validateaddress($act['forward_to']);
          if($isValid['isvalid'] != 1){
              $invBTC = makeSQLSafe($act['forward_to']);
              mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages(`uid`,`dtime`,`message`) VALUES({$acc[1]},'".date("Y-m-d H:i:s")."','ERROR Invalid address to forward your deposits to :: $invBTC. Amount remains in your account!')");
          }elseif($isValid['ismine'] == 1){
              //It's forward to a local address, so we just move the balance
              $recAct = explode("_",$isValid['account']);
              
              if(!is_array($recAct) || sizeof($recAct) != 3){
                mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages(`uid`,`dtime`,`message`) VALUES({$acc[1]},'".date("Y-m-d H:i:s")."','ERROR Invalid account to forward your deposits to - local account is not an user account :: $invBTC. Amount remains in your account!')");    
              }else{
                $sql = "SELECT * FROM accounts WHERE uid = {$recAct[1]} AND account_id = {$recAct[2]}";
                $q = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
                if(!mysqli_num_rows($q)){
                    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages(`uid`,`dtime`,`message`) VALUES({$acc[1]},'".date("Y-m-d H:i:s")."','ERROR Invalid account to forward your deposits to - local account not found :: $invBTC. Amount remains in your account!')");                            
                }else{
                    $receiver = mysqli_fetch_assoc($q);  
                    $nextBal = $newBal - $a;    
                    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO movements(`account_id`,`dtime`,`description`,`amount`,`credit`,`balance`,`txblock`) VALUES({$act['id']},'".date("Y-m-d H:i:s")."','Forward to {$act['forward_to']}',$a,0,$nextBal,$cBlock)");
                    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE accounts SET balance = balance - $a WHERE id = {$act['id']}"); 
                    //A small issue; re-forwarded accounts will not forward to prevent loop attacks.
                   $prevBal = 0;
                   $sql = "SELECT balance FROM movements WHERE account_id = {$receiver['id']} ORDER BY id DESC LIMIT 0,1";
                   $q = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
                   if(mysqli_num_rows($q)){
                       $pbal = mysqli_fetch_assoc($q);
                       $prevBal = $pbal['balance'];
                   }
                   $newBal = $prevBal + $a;                    
                   mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO movements(`account_id`,`dtime`,`description`,`amount`,`credit`,`balance`,`txblock`) VALUES({$receiver['id']},'".date("Y-m-d H:i:s")."','{$coin_list[$x]} forward',$a,1,$newBal,$cBlock)");
                   mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE accounts SET balance = balance + $a WHERE id = {$receiver['id']}");                    
                    
                }
              }
          }else{
                    $txamount = $a - 0.0005;
                    if($txamount < 0){
                       mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages(`uid`,`dtime`,`message`) VALUES({$acc[1]},'".date("Y-m-d H:i:s")."','ERROR Funds to forward aren\'t enough to pay the network fee. Amount remains in your account!')");                             
                    }else{
                        $txid = $b[$x]->sendfrom($config['central_account']['value'],$act['forward_to'],$txamount,(int)$config['confirmations']['value']);
                        $nextBal = $newBal - $txamount;    
                        mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO movements(`account_id`,`dtime`,`description`,`amount`,`credit`,`balance`,`txblock`) VALUES({$act['id']},'".date("Y-m-d H:i:s")."','Forward to {$act['forward_to']}',$txamount,0,$nextBal,$cBlock)");
                        mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE accounts SET balance = balance - $txamount WHERE id = {$act['id']}");               
                        //Get the transaction info to see what went with fees
                        $txinfo = $b[$x]->gettransaction($txid);
                        $fee = 0;
                        $fee -= $txinfo['fee'];
                        $nextBal -= $fee;
                        if($fee > 0){
                            mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO movements(`account_id`,`dtime`,`description`,`amount`,`credit`,`balance`,`txblock`) VALUES({$act['id']},'".date("Y-m-d H:i:s")."','{$coin_list[$x]} Network Fee',$fee,0,$nextBal,$cBlock)");
                            mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE accounts SET balance = balance - $fee WHERE id = {$act['id']}");                                           
                        }
                    }
          }
      }//Forward EOF
  }//Deposits EOF

 /*
 * @author marinu666
 * @license MIT License - https://github.com/marinu666/PHP-btce-api
 */
if ($btce_price) {
require_once('btce-api.php');
$BTCeAPI = new BTCeAPI(
                    /*API KEY:    */    '',
                    /*API SECRET: */    ''
                      );
  
$btc_usd = array();
//$btc_usd['fee'] = $BTCeAPI->getPairFee('btc_usd');
// Ticker Call
$pair2 = get_unit($coin_code[$x]);
try {
$pair = strtolower($coin_code[$x]).$pair2;
$btc_usd = $BTCeAPI->getPairTicker($pair);
$usd = $btc_usd['ticker']['avg'];
#print $pair."\t".$usd.PHP_EOL;
$sql = "SELECT * FROM config WHERE `key` = '{$coin_code[$x]}';";
$q = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
if(!mysqli_num_rows($q)){
   $sql = "INSERT INTO config(`key`,`value`,`explain`) VALUES ('{$coin_code[$x]}','{$usd}','BTC-E {$pair} current price');";
} else {
   $sql = "UPDATE config SET `value` = '$usd' WHERE `key` = '{$coin_code[$x]}';";
}
$q = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
} catch(BTCeAPIException $e) {
    echo $e->getMessage();
    }
}
}
?>
