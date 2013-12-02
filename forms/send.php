<?php
    defined("_V") || die("Direct access not allowed!");
    include("menus/menus.php");
             for ($x=0; $x < count($coin_list); $x++){
               if ($account_to_edit['account_type'] == $coin_code[$x]){
	       	$code = $coin_code[$x];
		$num = $x;
                }

                #echo "<option value='".$x."'>".$coin_code[$x]."</option>".PHP_EOL;
             } 

switch ($code) {
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
        $sql = "SELECT `value` FROM config WHERE `key` = '".$code."';";
	$q = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	while($r = mysqli_fetch_assoc($q)){
	     $rate = $r['value'];
	}
?>
<div id="mainBodyLMenu">
<h2>Send Coins</h2>
<script language="javascript" type="text/javascript">
    function validateWithdraw(form){
        var max = <?php echo $available;?>;
        var err = new Array;
        if(form.addrto.value == "") err.push("Destination address missing!");
        if(form.amount.value == "") err.push("Amount missing!");
        if(isNaN(form.amount.value)){
            err.push("Amount has to be numeric!");
        }else{
            if(form.amount.value > max) err.push("Selected amount exceeds your available balance!");
        }
        if(form.pass.value == "") err.push("Your password is needed to send coins!");
        
        if(err.length > 0){
            alert(err.join("\r\n"));
            return false;
        }
        return true;        
        
    }
    window.onload=function(){
    var input = document.getElementById('amount');
    input.onkeyup = function () {
        var result = document.getElementById('usd');
        result.innerHTML = "<?php echo $pref; ?>"+eval(this.value*<?php echo $rate; ?>)+" <?php echo $pair2; ?>";
    };

    //evaluate initial value
    //input.onkeyup();
    }
</script>
<p>The maximum amount you can withdraw is <strong><?php echo number_format($available,8,".",",");?></strong></p>
<form method="post" action="index.php" onsubmit="return validateWithdraw(this)">
<input type="hidden" name="f" value="sendcoins">
    <div class="formLine">
        <label>To Address</label>
        <input id="addrto" type="text" name="addrto" size="60">
    </div>
    <div class="formLine">
        <label>Amount</label>
        <input type="text" style="text-align: right;" name="amount" size="10" id="amount">
	<?php echo $code;
	?>
	<span id="usd"></span>
    </div>  
        <div class="formLine">
            <label>Your Password</label>
            <input type="password" name="pass">
        </div>        
       <div class="formLine">
             <label>Account Type</label>
             <select name="account_type">
	     <?php
                echo "<option value='".$num."'";
                        echo " selected";
                echo ">".$code."</option>".PHP_EOL;
              ?>
             </select>
        </div>
        <div class="formLine">
            <label>&nbsp;</label>
            <input type="submit" value="Send coins">
        </div>
</form>
</div>
