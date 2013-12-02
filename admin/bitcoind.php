<?php
	defined("_V") || die("Direct access not allowed!");
	if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) die("You're not admin!");
	
	include("menus/menus.php");
	echo '<div id="mainBodyLMenu">';
	for ($x=0; $x < count($coin_list); $x++) {

	$info = $b[$x]->getinfo();
	
	//This is a dirty hack, but should go ok until version 9.x
	if(strlen($info['version']) < 6){
		$version = 0;
		$sub_version = substr($info['version'],0,1);
		$build = substr($info['version'],1,2);
	}else{
		$version = substr($info['version'],0,1);
		$sub_version = substr($info['version'],1,1);
		$build = substr($info['version'],2,2);		
	}
	echo "<h3>".$coin_list[$x]."</h3>";
?>
	<div class="infoLine">
		<label>Version</label>
		<?php echo "$version.$sub_version.$build";?>
	</div>
	<div class="infoLine">
		<label>Blocks</label>
		<?php echo $info['blocks'];?>
	</div>	
<?php
$pair2 = '';
switch ($coin_code[$x]) {
	case 'BTC';
	case 'NMC';
	case 'LTC';
		$pair2 = 'USD';
		break;
	default;
		$pair2 = 'BTC';
		break;
}
$pref = '';
if ($pair2 == 'USD') {
    $pref='$';
}
        $sql = "SELECT `value` FROM config WHERE `key` = '".$coin_code[$x]."';";
	$q = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	while($r = mysqli_fetch_assoc($q)){
	     $rate = $r['value'];
	}
	if (isset($rate)){
	  $usd = $rate * $info['balance'];
	  $usd = number_format($usd,2,'.',',');
	}
	echo "<div class='infoLine'>".PHP_EOL;
	echo "<label>Balance</label>".PHP_EOL;
	echo number_format($info['balance'],8,".",","); echo " ".$coin_code[$x];
	if (isset($rate)){
	  echo " <font color='gray' title='Rate: {$pref}{$rate} {$pair2}'>".$pref.$usd." ".$pair2."</font>";
	}
	echo "</div>".PHP_EOL;
	$users_balance = 0;
	$sql = "SELECT `balance` FROM accounts WHERE account_type = '".$coin_code[$x]."';";
	$q = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	while($r = mysqli_fetch_assoc($q)){
		$users_balance += $r['balance'];
	}
	$diff = $info['balance'] - $users_balance;
?>	
	<div class="infoLine">
		<label>Difference</label>
		<?php echo $diff; echo " ".$coin_code[$x];?> <small>This value should always be zero if everything is ok</small>
	</div>
	<div class="infoLine">
		<label>Connections</label>
		<?php echo $info['connections'];?>
	</div>	
<?php
	if($info['proxy'] != ""){
?>
	<div class="infoLine">
		<label>Proxy</label>
		<?php echo $info['proxy'];?>
	</div>	
<?php		
	}
	
	$sql = "SELECT COUNT(*) AS nUsers FROM users";
	$q = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	$r = mysqli_fetch_array($q);
?>	
	<div class="infoLine">
		<label>Reg. Users</label>
		<?php echo $r['nUsers'];?>
	</div>	
<?php
	$sql = "SELECT COUNT(*) AS nAccounts FROM accounts where account_type = '{$coin_code[$x]}'";
	$q = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	$t = mysqli_fetch_array($q);	
?>	
	<div class="infoLine">
		<label>Nr. Accounts</label>
		<?php echo $t['nAccounts'];?> (Average: <?php echo round($t['nAccounts'] / $r['nUsers']);?> accounts per user)
	</div>	
<?php
	$waiting = $b[$x]->listaccounts(0);
	$waitDep = 0;
	foreach($waiting as $k => $w){
		if($w > 0 && $k != $config['central_account']['value']) $waitDep += $w;
	}
?>	
	<div class="infoLine">
		<label>Deposits incomming</label>
		<?php echo $waitDep;  echo " ".$coin_code[$x];?> 
	</div>
<?php } ?>
</div>
