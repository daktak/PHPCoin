<?php
    defined("_V") || die("Direct access not allowed!");
    include("menus/menus.php");
?>
<script type="text/javascript" language="javascript">
var clip = null;
$(function(){
<?php
for ($x=0; $x < count($coin_list); $x++){

echo "       clip{$x} = new ZeroClipboard.Client();".PHP_EOL;
echo "       clip{$x}.setHandCursor( true );".PHP_EOL;
echo "        clip{$x}.addEventListener('mouseOver', function (client) {
            clip{$x}.setText( $('#btaddress".$x."').html() );
        });".PHP_EOL;
echo "       clip{$x}.addEventListener('complete', function (client, text) {".PHP_EOL;
echo '                alert("Done");'.PHP_EOL;
echo "       });";
echo "clip{$x}.glue('copyToClip{$x}');".PHP_EOL;
}
?>
});
</script>
<div id="mainBodyLMenu">
<div class="infoLine">
    <label>User</label> <?php echo $_SESSION['user'];?>
</div>
<div class="infoLine">
    <label>Name</label> <?php echo $_SESSION['name'];?>
</div>
<?php
    for ($x=0; $x < count($coin_list); $x++){
    $tmp = explode("_",$_SESSION['btaccount']);
    $actAcount = end($tmp);
    $accountBalance = 0;
    echo "<h3>{$coin_list[$x]}</h3>";
?>
<div class="infoLine">
    <label>Active Account</label>
<?php
    echo "<select id='active_account{$x}'>";
    $sql = "SELECT * FROM accounts WHERE uid = {$_SESSION['id']} AND account_type = '{$coin_code[$x]}'";
    $q = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
    while($r = mysqli_fetch_assoc($q)){
        if($actAcount == $r['account_id']){
         $accountBalance = $r['balance'];
         $activeAccounID = $r['id'];
        }
?>
  <option value="<?php echo $r['account_id'];?>"<?php if($actAcount == $r['account_id']) echo " selected"?>><?php echo stripslashes($r['account_name']);?></option>
<?php        
    }
?>    
    </select> <img src="icon/arrow.png" border="0" title="Switch to selected account" style="cursor: pointer;" onclick="document.location.href='index.php?f=switchAccount&amp;id=' + document.getElementById('active_account').options[document.getElementById('active_account<?php echo $x; ?>').options.selectedIndex].value" alt="Switch to selected account">
      <img src="icon/book--pencil.png" border="0" title="Edit accounts" style="cursor: pointer;" onclick="document.location.href='index.php?f=accounts'" alt="Edit accounts">
</div>
<?php
echo "<div class='infoLine'>";
echo "   <label>".$coin_list[$x]." Network</label>";
echo "    Blocks: ".$b[$x]->getblockcount();
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Connection:";
    $cons = $b[$x]->getconnectioncount();
    if($cons >= 9) $cons = 9;
    switch($cons){
        case 0: echo '<img src="connection/0.jpg" border="0" title="connection status: offline" alt="connection statuts: offline">'; break;
        case 1: echo '<img src="connection/1.jpg" border="0" title="connection status: 1 node" alt="connection status: 1">'; break;
        case 2: echo '<img src="connection/2.jpg" border="0" title="connection status: 2 nodes" alt="connection status: 2">'; break;
        case 3: echo '<img src="connection/3.jpg" border="0" title="connection status: 3 nodes" alt="connection status: 3">'; break;
        case 4: echo '<img src="connection/4.jpg" border="0" title="connection status: 4 nodes" alt="connection status: 4">'; break;
        case 5: echo '<img src="connection/5.jpg" border="0" title="connection status: 5 nodes" alt="connection status: 5">'; break;
        case 6: echo '<img src="connection/6.jpg" border="0" title="connection status: 6 nodes" alt="connection status: 6">'; break;
        case 7: echo '<img src="connection/7.jpg" border="0" title="connection status: 7 nodes" alt="connection status: 7">'; break;
        case 8: echo '<img src="connection/8.jpg" border="0" title="connection status: 8 nodes" alt="connection status: 8">'; break;
        case 9: echo '<img src="connection/9.jpg" border="0" title="connection status: 9 or more nodes" alt="connection status: 9">'; break;
    }
echo "</div>";
echo "    <div class='infoLine'>";
echo "        <label>".$coin_list[$x]." Address</label>";
echo "<span id='btaddress".$x."'>";
echo $b[$x]->getaccountaddress($_SESSION['btaccount']);
echo '</span> <img src="icon/new.png" border="0" title="Get a new address" style="cursor:pointer" onclick="changeMyAddress(this,'.$x.')" alt="Get new address">';
echo '<img src="icon/clipboard--plus.png" border="0" title="Copy to clipboard" style="cursor: pointer;" id="copyToClip'.$x.'" alt="Copy to clipboard">';
echo <<<END
    </div>
    <div class="infoLine">
        <label>Balance</label>
 
        <strong>
END;
	 echo number_format($accountBalance,8,".",",");
	 echo " ".$coin_code[$x]."</strong>  <small><i>";
	 echo number_format($b[$x]->getbalance($_SESSION['btaccount'],0),8,".",".");
	 echo $coin_code[$x];
echo <<<END
</i></small>
        <img src="icon/wallet--arrow.png" border="0" title="Send coins" style="cursor: pointer;" onclick="document.location.href='index.php?f=send'" alt="Send coins">
    </div>
    
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

    $sql = "SELECT * FROM movements WHERE account_id = $activeAccounID and ORDER BY id DESC LIMIT 0,10";
    $q = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
    if(!$q || mysqli_num_rows($q)==0){
echo "    <tr><td colspan='5' align='center'>nothing to display</td></tr>";
    
    $k = 0;
    }
    else
    while($r = mysqli_fetch_assoc($q)){
?>
     <tr class="listingRow<?php echo $k;?>">
        <td><?php echo $r['dtime'];?></td>
        <td><?php echo stripslashes($r['description']);?></td>
        <td align="right"><?php echo $r['txblock'];?> (<?php echo $cBlock - $r['txblock'];?> conf.)</td>
        <td align="right"><?php echo $r['credit'] == 1 ? "&nbsp;" : number_format($r['amount'],8,".",",") . " ".$coin_code[$x];?></td>
        <td align="right"><?php echo $r['credit'] == 0 ? "&nbsp;" : number_format($r['amount'],8,".",",") . " ".$coin_code[$x];?></td>
        <td align="right"><?php echo number_format($r['balance'],8,".",",") . " ".$coin_code[$x];?></td>
     </tr>
<?php        
        $k = 1 - $k;
    }
?>    
    </table>    
<?php
}
?>
</div>
