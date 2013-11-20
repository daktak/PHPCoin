<?php
    defined("_V") || die("Direct access not allowed!");
    include("menus/menus.php");
?>
<script language="javascript">
var clip = null;
$(function(){
       clip = new ZeroClipboard.Client();
       clip.setHandCursor( true );
<?php
for ($x=0; $x < count($coin_list); $x++){
echo "        clip.addEventListener('mouseOver', function (client) {
            clip.setText( $('#btaddress".$x."').html() );
        });";
	}
?>
       clip.addEventListener('complete', function (client, text) {
                alert("Done");
       });
       clip.glue('copyToClip');
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
    $tmp = explode("_",$_SESSION['btaccount']);
    $actAcount = end($tmp);
    $accountBalance = 0;
?>
<div class="infoLine">
    <label>Active Account</label>
    <select id="active_account">
<?php
    $sql = "SELECT * FROM accounts WHERE uid = {$_SESSION['id']}";
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
    </select> <img src="icon/arrow.png" border="0" title="Switch to selected account" style="cursor: pointer;" onclick="document.location.href='index.php?f=switchAccount&id=' + document.getElementById('active_account').options[document.getElementById('active_account').options.selectedIndex].value" />
      <img src="icon/book--pencil.png" border="0" title="Edit accounts" style="cursor: pointer;" onclick="document.location.href='index.php?f=accounts'" />
</div>
<?php
for ($x=0; $x < count($coin_list); $x++){
echo "<div class='infoLine'>";
echo "   <label>".$coin_list[$x]." Network</label>";
echo "    Blocks: ".$b[$x]->getblockcount();
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Connection:";
    $cons = $b[$x]->getconnectioncount();
    if($cons >= 9) $cons = 9;
    switch($cons){
        case 0: echo '<img src="connection/0.jpg" border="0" title="connection status: offline" />'; break;
        case 1: echo '<img src="connection/1.jpg" border="0" title="connection status: 1 node" />'; break;
        case 2: echo '<img src="connection/2.jpg" border="0" title="connection status: 2 nodes" />'; break;
        case 3: echo '<img src="connection/3.jpg" border="0" title="connection status: 3 nodes" />'; break;
        case 4: echo '<img src="connection/4.jpg" border="0" title="connection status: 4 nodes" />'; break;
        case 5: echo '<img src="connection/5.jpg" border="0" title="connection status: 5 nodes" />'; break;
        case 6: echo '<img src="connection/6.jpg" border="0" title="connection status: 6 nodes" />'; break;
        case 7: echo '<img src="connection/7.jpg" border="0" title="connection status: 7 nodes" />'; break;
        case 8: echo '<img src="connection/8.jpg" border="0" title="connection status: 8 nodes" />'; break;
        case 9: echo '<img src="connection/9.jpg" border="0" title="connection status: 9 or more nodes" />'; break;
    }
echo "</div>";
echo "    <div class='infoLine'>";
echo "        <label>".$coin_list[$x]." Address</label>";
echo "<span id='btaddress".$x."'>";
echo $b[$x]->getaccountaddress($_SESSION['btaccount']);
echo <<<END
</span> <img src="icon/new.png" border="0" title="Get a new address" style="cursor:pointer" onclick="changeMyAddress(this,'<?php echo $x; ?>')" />      
<img src="icon/clipboard--plus.png" border="0" title="Copy to clipboard" style="cursor: pointer;" id="copyToClip" />
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
        <img src="icon/wallet--arrow.png" border="0" title="Send coins" style="cursor: pointer;" onclick="document.location.href='index.php?f=send'" />
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

    $sql = "SELECT * FROM movements WHERE account_id = $activeAccounID ORDER BY id DESC LIMIT 0,10";
    $q = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
    if(!mysqli_num_rows($q)){
echo "    <tr><td colspan='5' align='center'>nothing to display</td></tr>";
    }
    $k = 0;
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
