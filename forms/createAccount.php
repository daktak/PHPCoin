<?php
    defined("_V") || die("Direct access not allowed!");
    include("menus/menus.php");
?>
<script language="javascript" type="text/javascript"> 
    function updateSettings(form){
        var err = new Array;
        if(form.name.value == "") err.push("Account name can't be blank!");
        if(form.fwd.options[form.fwd.options.selectedIndex].value == 1 && form.fwdto.value == "") err.push("You need to set an address to forward to!");
        
        if(err.length > 0){
            alert(err.join("\r\n"));
            return false;
        }
        return true;
    }
</script>
<div id="mainBodyLMenu">
    <h2>Create Account</h2>
      <p>NOTE:<br>
      if you activate funds forwarding to an address outside this server, upon forwarding a network fee of 0.0005 will be held.<br>
      If the transfer didn't pay fee, the held funds will remain in your account.<br>
      Transfers bellow 0.0005 will not be forwarded!</p>
    <form action="index.php" method="post" onsubmit="return updateSettings(this)">
    <input type="hidden" name="f" value="doCreateAccount">
        <div class="formLine">
            <label>Account Name</label>
            <input type="text" name="name" size="40" value="">
        </div>
        <div class="formLine">
            <label>Forward?</label>
            <select name="fwd">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>
        <div class="formLine">
            <label>Forward to Address</label>
            <input type="text" name="fwdto" size="60" value="">
        </div>
       <div class="formLine">
             <label>Account Type</label>
	     <select name="account_type">
	     <?php
	     for ($x=0; $x < count($coin_list); $x++){
	     	echo "<option value='".$coin_code[$x]."'>".$coin_code[$x]."</option>".PHP_EOL;
	     } ?>
	     </select>
	</div>
        <div class="formLine">
            <label>&nbsp;</label>
            <input type="submit" value="Create account">
        </div>
    </form>
</div>
