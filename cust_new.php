<!DOCTYPE HTML>
<?PHP	
	require 'functions.php';
	checkLogin();
	connect();
	
	//Generate timestamp
	$timestamp = time();
	
	//CREATE-Button
	if (isset($_POST['create'])){
				
		//Sanitize user input
		$cust_no = sanitize($_POST['cust_no']);
		$cust_name = sanitize($_POST['cust_name']);
		$cust_dob = strtotime(sanitize($_POST['cust_dob']));
		$custsex_id = sanitize($_POST['custsex_id']);
		$cust_address = sanitize($_POST['cust_address']);
		$cust_phone = sanitize($_POST['cust_phone']);
		$cust_email = sanitize($_POST['cust_email']);
		$cust_occup = sanitize($_POST['cust_occup']);
		$custmarried_id = sanitize($_POST['custmarried_id']);
		$cust_heir = sanitize($_POST['cust_heir']);
		$cust_heirrel = sanitize($_POST['cust_heirrel']);
		$custsick_id = sanitize($_POST['custsick_id']);
		$cust_since = strtotime(sanitize($_POST['cust_since']));
		$_SESSION['receipt_no'] = sanitize($_POST['receipt_no']);
		
		//Insert new Customer into CUSTOMER
		$sql_insert = "INSERT INTO customer (cust_no, cust_name, cust_dob, custsex_id, cust_address, cust_phone, cust_email, cust_occup, custmarried_id, cust_heir, cust_heirrel, cust_since, custsick_id, cust_lastsub, cust_active, cust_lastupd, user_id) VALUES ('$cust_no', '$cust_name', '$cust_dob', '$custsex_id', '$cust_address', '$cust_phone', '$cust_email', '$cust_occup', $custmarried_id, '$cust_heir', '$cust_heirrel', $cust_since, $custsick_id, $cust_since, '1', $timestamp, $_SESSION[log_id])";
		$query_insert = mysql_query($sql_insert);
		checkSQL($query_insert);
		
		//Get new Customer's ID from CUSTOMER
		$sql_maxid = "SELECT MAX(cust_id) FROM customer";
		$query_maxid = mysql_query ($sql_maxid);
		checkSQL($query_maxid);
		$maxid = mysql_fetch_assoc($query_maxid);
		$_SESSION['cust_id'] = $maxid['MAX(cust_id)'];
		
		//Insert Entrance Fee and Stationary Sales into INCOMES
		$sql_insert_fee = "INSERT INTO incomes (cust_id, inctype_id, 	inc_amount, inc_date, inc_receipt, inc_created, user_id) VALUES ($_SESSION[cust_id], '1', $_SESSION[fee_entry], $cust_since, '$_SESSION[receipt_no]', $timestamp, $_SESSION[log_id]), ($_SESSION[cust_id], '6', $_SESSION[fee_stationary], $cust_since, '$_SESSION[receipt_no]', $timestamp, $_SESSION[log_id])";
		$query_insert_fee = mysql_query ($sql_insert_fee);
		checkSQL($query_insert_fee);
		
		//Refer to cust_new_pic.php
		header('Location: cust_new_pic.php?from=new');
	}
	
	//Select Marital Status for Drop-down-Menu
	$sql_mstat = "SELECT * FROM custmarried";
	$query_mstat = mysql_query($sql_mstat);
	checkSQL($query_mstat);
	
	//Select Sicknesses for Drop-down-Menu
	$sql_sick = "SELECT * FROM custsick";
	$query_sick = mysql_query($sql_sick);
	checkSQL($query_sick);
	
	//Select Sexes from custsex for dropdown-menu
	$sql_sex = "SELECT * FROM custsex";
	$query_sex = mysql_query($sql_sex);
	checkSQL($query_sex);
	
	//Determine new CUST_ID
	$sql_maxid = "SELECT MAX(cust_id) AS maxid FROM customer";
	$query_maxid = mysql_query($sql_maxid);
	checkSQL($query_maxid);
	$result_maxid = mysql_fetch_array($query_maxid);
	$new_id = $result_maxid['maxid'] + 1;
?>

<html>
	<?PHP includeHead('New Customer',0) ?>	
		<script>
			function validate(form){
				fail = validateName(form.cust_name.value)
				fail += validateDob(form.cust_dob.value)
				fail += validateAddress(form.cust_address.value)
				fail += validatePhone(form.cust_phone.value)
				fail += validateEmail(form.cust_email.value)
				if (fail == "") {
					receipt_no = prompt('Please enter Bank Slip/Receipt Number for Entrance Fee Payment:')
					if (receipt_no == "" || receipt_no == null) { 
						alert('You have not specified the Receipt Number. Please try again.'); 
						return false;
					}
					else {
						document.getElementById("receipt_no").value = receipt_no; 
						return true;
					}
				}
				else { alert(fail); return false; }
			}
		</script>
		<script src="functions_validate.js"></script>
	</head>
	<body>
		<!-- MENU -->
		<?PHP includeMenu(2); ?>
		<div id="menu_main">
			<a href="cust_search.php">Search</a>
			<a href="cust_new.php" id="item_selected">New Client</a>
			<a href="cust_act.php">Active Client</a>
			<a href="cust_inact.php">Inactive Client</a>
		</div>
		
		<!-- PAGE HEADING -->
		<p class="heading">New Client (<?PHP echo $new_id; ?>)</p> 
		
		<!-- CONTENT -->
		<div class="content_center">
			<form action="cust_new.php" method="post" onSubmit="return validate(this)" enctype="multipart/form-data">
				
				<table id ="tb_fields">
					<tr>
						<td>Number:</td>
						<td><input type="text" name="cust_no" value="<?PHP echo $new_id; ?>" tabindex="1" /></td>
						<td>Address:</td>
						<td><input type="text" name="cust_address" placeholder="Place of Residence" tabindex="6" /></td>
						<td>Representative:</td>
						<td><input type="text" name="cust_heir" tabindex="11" /></td>
					</tr>
					<tr>
						<td>Name:</td>
						<td><input type="text" name="cust_name" placeholder="Full Name" tabindex="2" /></td>
						<td>Phone No:</td>
						<td><input type="text" name="cust_phone" tabindex="7"/></td>
						<td>Relation:</td>
						<td><input type="text" name="cust_heirrel" placeholder="e.g. Wife, Secretary..." tabindex="12" /></td>
					</tr>
					<tr>
						<td>Gender:</td>
						<td>
							<select name="custsex_id" size="1" tabindex="3">';
								<?PHP
									while ($row_sex = mysql_fetch_assoc($query_sex)){
										echo '<option value="'.$row_sex['custsex_id'].'">'.$row_sex['custsex_name'].'</option>';
									}
								?>
							</select>
						</td>
						<td>E-Mail:</td>
						<td><input type="text" name="cust_email" placeholder="abc@xyz.com" tabindex="8"/></td>
						<td>Active:</td>
						<td><input type="checkbox" disabled="disabled" checked="checked" /></td>
					</tr>
					<tr>
						<td>DoB:</td>
						<td><input type="text" id="datepicker" name="cust_dob" placeholder="DD.MM.YYYY" tabindex="4" /></td>
						<td>Occupation:</td>
						<td><input type="text" name="cust_occup" tabindex="9" /></td>
						<td>Member since:</td>
						<td><input type="text" id="datepicker2" name="cust_since" value="<?PHP echo date("d.m.Y", $timestamp) ?>" tabindex="13" /></td>
					</tr>
					<tr>
						<td>Marital Status:</td>
						<td>
							<select name="custmarried_id" size="1" tabindex="5">';
								<?PHP
								while ($row_mstat = mysql_fetch_assoc($query_mstat)){
									echo '<option value="'.$row_mstat['custmarried_id'].'">'.$row_mstat['custmarried_status'].'</option>';
								}
								?>
							</select>
						</td>
						<td>Sickness:</td>
						<td>
							<select name="custsick_id" size="1" tabindex="10">
								<?PHP
								while ($row_sick = mysql_fetch_assoc($query_sick)){
									echo '<option value="'.$row_sick['custsick_id'].'">'.$row_sick['custsick_name'].'</option>';
								}
								?>
							</select>
						</td>
						<td></td>
						<td>
							<input type="hidden" name="receipt_no" id="receipt_no" value="" />
							<input type="submit" name="create" value="Continue" tabindex="14" />
						</td>
					</tr>
				</table>
			</form>
		</div>
	</body>
</html>