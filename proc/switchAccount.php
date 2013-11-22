<?php
    defined("_V") || die("Direct access not allowed!");
    
    $e = array();
    
    isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) ? $id = $_REQUEST['id'] : $e[] = "Account ID missing!";
    
    if(empty($e)){
        $sql = "SELECT * FROM accounts WHERE account_id = $id AND uid = {$_SESSION['id']} LIMIT 0,1";
        $q = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
        if(!mysqli_num_rows($q)) $e[] = "Account not found!";
	$mu = mysqli_fetch_assoc($q);
	for ($x=0; $x < count($coin_list); $x++) {
	    if ($mu['account_type'] == $coin_code[$x]) {
	        $_SESSION['wallet'] = $x;
	    }
	}
    }
    
    if(empty($e)){
        $_SESSION['btaccount'] = $config['account_prefix']['value'] ."_" . $_SESSION['id'] . "_$id";
        header("Location: index.php");
        exit();
    }else{
        $error = implode("<br/>",$e);
    }
    
?>
