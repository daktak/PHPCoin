<?php
    defined("_V") || die("Direct access not allowed!");
    include("menus/menus.php");
?>
<div id="mainBodyLMenu">
<div class="infoLine">
    <label>User</label> <?php echo $_SESSION['user'];?>
</div>
<div class="infoLine">
    <label>Name</label> <?php echo $_SESSION['name'];?>
</div>
<?php
    #for ($x=0; $x < count($coin_list); $x++){
    $tmp = explode("_",$_SESSION['btaccount']);
    $actAcount = end($tmp);
    $accountBalance = 0;
    $activeAccounID = 0;
    #echo "<h3>{$coin_list[$x]}</h3>";
?>
<div class="infoLine">
    <label>Active Account</label>
<?php
    echo "<select id='active_account'>";
    $sql = "SELECT * FROM accounts WHERE uid = {$_SESSION['id']} order by account_type"; # AND account_type = '{$coin_code[$x]}'";
    $q = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
    while($r = mysqli_fetch_assoc($q)){
        if($actAcount == $r['account_id']){
         $accountBalance = $r['balance'];
         $activeAccounID = $r['id'];
	 $activeCoin = $r['account_type'];
        }
?>
  <option value="<?php echo $r['account_id'];?>"<?php if($actAcount == $r['account_id']) echo " selected"?>><?php echo stripslashes($r['account_name'])." ".$r['account_type'];?></option>
<?php        
    }
?>    
    </select> <img src="icon/arrow.png" border="0" title="Switch to selected account" style="cursor: pointer;" onclick="document.location.href='index.php?f=switchAccount&amp;id=' + document.getElementById('active_account').options[document.getElementById('active_account').options.selectedIndex].value" alt="Switch to selected account">
      <img src="icon/book--pencil.png" border="0" title="Edit accounts" style="cursor: pointer;" onclick="document.location.href='index.php?f=accounts'" alt="Edit accounts">
</div>
<?php
for ($x=0; $x < count($coin_list); $x++){
if ($coin_code[$x]==$activeCoin) {
echo "<h3>{$coin_list[$x]}</h3>".PHP_EOL;
echo "<div class='infoLine'>".PHP_EOL;
echo "   <label>".$coin_list[$x]." Network</label>".PHP_EOL;
$cBlock = $b[$x]->getblockcount(); 
echo "    Blocks: ".$cBlock;
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Connection:";
    $cons = $b[$x]->getconnectioncount();
    if($cons >= 9) $cons = 9;
    switch($cons){
        case 0: echo '<img src="connection/0.jpg" border="0" title="connection status: offline" alt="connection statuts: offline">'.PHP_EOL; break;
        case 1: echo '<img src="connection/1.jpg" border="0" title="connection status: 1 node" alt="connection status: 1">'.PHP_EOL; break;
        case 2: echo '<img src="connection/2.jpg" border="0" title="connection status: 2 nodes" alt="connection status: 2">'; break;
        case 3: echo '<img src="connection/3.jpg" border="0" title="connection status: 3 nodes" alt="connection status: 3">'.PHP_EOL; break;
        case 4: echo '<img src="connection/4.jpg" border="0" title="connection status: 4 nodes" alt="connection status: 4">'.PHP_EOL; break;
        case 5: echo '<img src="connection/5.jpg" border="0" title="connection status: 5 nodes" alt="connection status: 5">'.PHP_EOL; break;
        case 6: echo '<img src="connection/6.jpg" border="0" title="connection status: 6 nodes" alt="connection status: 6">'.PHP_EOL; break;
        case 7: echo '<img src="connection/7.jpg" border="0" title="connection status: 7 nodes" alt="connection status: 7">'.PHP_EOL; break;
        case 8: echo '<img src="connection/8.jpg" border="0" title="connection status: 8 nodes" alt="connection status: 8">'.PHP_EOL; break;
        case 9: echo '<img src="connection/9.jpg" border="0" title="connection status: 9 or more nodes" alt="connection status: 9">'.PHP_EOL; break;
    }
echo "</div>".PHP_EOL;
echo "    <div class='infoLine'>".PHP_EOL;
echo "        <label>".$coin_list[$x]." Address</label>".PHP_EOL;
echo "<span id='btaddress".$x."'>".PHP_EOL;
echo $b[$x]->getaccountaddress($_SESSION['btaccount']);
echo '</span> <img src="icon/new.png" border="0" title="Get a new address" style="cursor:pointer" onclick="changeMyAddress(this,'.$x.')" alt="Get new address">'.PHP_EOL;
echo <<<END
<script type="text/javascript" language="javascript">
var clip = null
$(function(){
       clip = new ZeroClipboard.Client();
       clip.setHandCursor( true );
        clip.addEventListener('mouseOver', function (client) {
END;
echo PHP_EOL."            clip.setText( $('#btaddress{$x}').html() );".PHP_EOL;
echo <<<END
        });
       clip.addEventListener('complete', function (client, text) {
                alert("Done");
       });
       clip.glue('copyToClip');
});
</script>
END;
echo '<img src="icon/clipboard--plus.png" border="0" title="Copy to clipboard" style="cursor: pointer;" id="copyToClip" alt="Copy to clipboard">'.PHP_EOL;
echo <<<END
    </div>
    <div class="infoLine">
        <label>Balance</label>
 
END;
$pair2 = get_price_unit($coin_code[$x]);
$pref = '';
if ($pair2 == 'USD') {
    $pref='$';
}
        $sql = "SELECT `value` FROM config WHERE `key` = '".$coin_code[$x]."';";
	$q = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	while($r = mysqli_fetch_assoc($q)){
	     $rate = $r['value'];
	}
	if (isset($rate)) {
	$usd = $rate * $accountBalance;
	$usd = number_format($usd,2,'.',',');
	$incomming = $b[$x]->getbalance($_SESSION['btaccount'],0) * $rate;

	}

   	 echo "<strong>".PHP_EOL;
	 echo number_format($accountBalance,8,".",",");
	 echo " ".$coin_code[$x]."</strong>".PHP_EOL;
	 if (isset($rate)) {
	   echo " <font color='gray' title='Rate: {$pref}{$rate} {$pair2}'>".$pref.$usd." ".$pair2."</font>";
	 }
	 echo " <small><i";
	 if(isset($incomming)) {
	 echo "title='{$pref}{$incomming} {$pair2}'";
	 } 
	 echo ">"; 
	 echo number_format($b[$x]->getbalance($_SESSION['btaccount'],0),8,".",".");
	 echo $coin_code[$x];
	 $coin_sufx = $coin_code[$x];
echo <<<END
</i></small>
        <img src="icon/wallet--arrow.png" border="0" title="Send coins" style="cursor: pointer;" onclick="document.location.href='index.php?f=send'" alt="Send coins">
    </div>
END;
#if ($activeAccounID <> 0) {
}
}
echo <<<END
    <h2>Last 10 movements</h2>
    <table class="listingTable">
        <tr class="listingHeader">
            <td>Date</td>
            <td>Description</td>
            <td>Block</td>
            <td>Debit</td>
            <td>Credit</td>
            <td>Balance</td>
        </tr>
END;
    $sql = "SELECT * FROM movements WHERE account_id = '".trim($activeAccounID)."' ORDER BY id DESC LIMIT 0,10";
    $q = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
    if(!$q || mysqli_num_rows($q)==0){
	echo "    <tr><td colspan='5' align='center'>nothing to display</td></tr>".PHP_EOL;
    }
    else
    while($r = mysqli_fetch_assoc($q)){
      $k = 0;
?>
     <tr class="listingRow<?php echo $k;?>">
        <td><?php echo $r['dtime'];?></td>
        <td><?php echo stripslashes($r['description']);?></td>
        <td align="right"><?php echo $r['txblock'];?> (<?php echo $cBlock - $r['txblock'];?> conf.)</td>
        <td align="right"<?php if ($r['credit'] == 1 ) { echo ">&nbsp;"; } else { if (isset($rate)) { echo " title='".$pref.number_format($r['amount']*$rate,2,".",",")." ".$pair2."'";} echo ">".number_format($r['amount'],8,".",",") . " ".$coin_sufx;}?></td>
        <td align="right"<?php if ($r['credit'] == 0 ) { echo ">&nbsp;"; } else { if (isset($rate)) { echo " title='".$pref.number_format($r['amount']*$rate,2,".",",")." ".$pair2."'";} echo ">".number_format($r['amount'],8,".",",") . " ".$coin_sufx;}?></td>
        <td align="right"<?php if (isset($rate)) { echo " title='".$pref.number_format($r['balance']*$rate,2,".",",")." ".$pair2."'";} echo ">".number_format($r['balance'],8,".",",") . " ".$coin_sufx;?></td>
     </tr>
<?php        
        $k = 1 - $k;
    }
?>    
    </table>    
<?php
#}
#}
?>
</div>
