<?PHP
if (($result_cust['cust_lastsub']+$functionObject->convertDays(365)) < $timestamp ){ 
	$visibility = 'block';
	if($result_cust['cust_active'] == 1) showMessage('This Client needs to renew his / her Subscription!');
}
else $visibility = 'none';

//RENEW-MEMBERSHIP-Button
if(isset($_POST['subscr_renew'])){
	
	//Sanitize user input
	$subscr_date = strtotime(sanitize($_POST['subscr_date']));
	$subscr_receipt = sanitize($_POST['subscr_receipt']);
	$subscr_from_sav = sanitize($_POST['subscr_from_sav']);
	$timestamp = time();
	$sav_id = array();
	
	// Get Subscription Fee
	getFees();
	
	// Insert Subscription Fee into SAVINGS if applicable
	if ($subscr_from_sav == 1){
		$fee_subscr_sav = $_SESSION['fee_subscr'] * (-1);
		
		$sql_insert_fee = "INSERT INTO savings (cust_id, sav_date, sav_amount, sav_balance, savtype_id, sav_receipt, sav_created, user_id) VALUES ('$_SESSION[cust_id]', '$subscr_date', '$fee_subscr_sav', $sav_balance, '5', '$subscr_receipt', '$timestamp', '$_SESSION[log_id]')";
		$query_insert_fee = mysql_query ($sql_insert_fee);
		checkSQL($query_insert_fee);
		
		// Update savings account balance
		updateSavingsBalance($_SESSION['cust_id']);
		
		// Get SAV_ID for the latest entry
		$sql_savid = "SELECT MAX(sav_id) FROM savings WHERE cust_id = '$_SESSION[cust_id]' AND sav_receipt = '$subscr_receipt' AND sav_created = '$timestamp'";
		$query_savid = mysql_query($sql_savid);
		checkSQL($query_savid);
		$sav_id = mysql_fetch_row($query_savid);
	}
	else $sav_id[0] = NULL;
		
	// Insert Subscription Fee into INCOMES
	$sql_insert_fee = "INSERT INTO incomes (cust_id, inctype_id, sav_id, inc_amount, inc_date, inc_receipt, inc_created, user_id) VALUES ('$_SESSION[cust_id]', '8', '$sav_id[0]', '$_SESSION[fee_subscr]', '$subscr_date', '$subscr_receipt', '$timestamp', '$_SESSION[log_id]')";
	$query_insert_fee = mysql_query ($sql_insert_fee);
	checkSQL($query_insert_fee);
	
	//Set Customer to ACTIVE and update SUBSCRIPTION DATE
	$query_active = mysql_query("UPDATE customer SET cust_lastsub = '$subscr_date', cust_active = '1' WHERE cust_id = '$_SESSION[cust_id]'");
	checkSQL($query_active);
	
	//header('Location: customer.php?cust='.$_SESSION['cust_id']);
}
?>

<div id="content_hidden" style="display:<?PHP echo $visibility; ?>;">
	<form action="customer.php" method="post" onSubmit="return validateSubscr(this)">
		<input type="text" name="subscr_date" value="<?PHP echo date('d.m.Y', $timestamp); ?>" placeholder="DD.MM.YYYY" />
		<br/><br/>
		<input type="number" name="subscr_receipt" placeholder="Receipt No." min="1"/>
		<br/><br/>
		<input type="checkbox" name="subscr_from_sav" value="1" /> deduct from Savings
		<br/><br/>
		<input type="submit" name="subscr_renew" value="Renew Subscription" />
	</form>
</div>