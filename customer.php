<!DOCTYPE HTML>
<?PHP
	require 'functions.php';
	$functionObject=new functions();
	$functionObject->checkLogin();
	$functionObject->getCustID();
	
	unset($_SESSION['interest_sum'], $_SESSION['balance']);	
	$timestamp = time();
	//Calculate Balance on Savings account
	$savbalance = $functionObject->getSavingsBalance($_SESSION['cust_id']);
	
	//UPDATE-Button
	if (isset($_POST['update'])){				
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
		if ($cust_lengthres == 0 OR $cust_lengthres == NULL) $cust_lengthres = NULL;
		$custsick_id = sanitize($_POST['custsick_id']);
		$cust_active = sanitize($_POST['cust_active']);
		$timestamp = time();
		
		//Update CUSTOMER
		$functionObject->getCustomer($cust_no,$cust_name,$cust_dob,$custsex_id,$cust_address,$cust_phone,$cust_email,$cust_occup,
		$cust_active,$timestamp,$_SESSION['log_id'],$_SESSION['cust_id']);
		header('Location: customer.php?cust='.$_SESSION['cust_id']);
	}
	
	//Get current customer's details
	$result_cust = $functionObject->getCustomer($_SESSION['cust_id']);
	
	//Error-Message, if customer is not found
	if ($result_cust['cust_id']==''){
		echo '<script>alert(\'Client not found in database.\');window.location = "cust_search.php";</script>';
	}
	
	//Select Marital Status from custmarried for dropdown-menu
	$query_mstat = $functionObject->getMaritalStatus();

	//Select Sicknesses from custsick for dropdown-menu
	$sql_sick = $functionObject->getSickness();
	
	//Select Sexes from custsex for dropdown-menu
	$query_sex = $functionObject->getGender();
	
	//Select Shares from SHARES
	$sql_sha = $functionObject->getShares($_SESSION['cust_id']);
	$share_amount = 0;
	$share_value = 0;
	foreach($sql_sha as $row_shares){
		$share_amount = $share_amount + $row_shares['share_amount'];
		$share_value = $share_value + $row_shares['share_value'];
	}
	
	//Select the five most recent savings transactions for display
	$sql_sav =$functionObject->getLastFiveRecentSavings();
?>

<html>
	<?PHP $functionObject->includeHead('Customer',0) ?>		
		<script>
			function validate(form){
				fail = validateName(form.cust_name.value)
				fail += validateDob(form.cust_dob.value)
				fail += validateAddress(form.cust_address.value)
				fail += validatePhone(form.cust_phone.value)
				fail += validateEmail(form.cust_email.value)
				if (fail == "") return true
				else { alert(fail); return false }
			}
			
			function validateSubscr(form){
				fail = validateDate(form.subscr_date.value)
				fail += validateReceipt(form.subscr_receipt.value)
				fail += validateOverdraft(<?PHP echo $_SESSION['fee_subscr']; ?>, <?PHP echo $savbalance; ?>, 0, <?PHP echo $_SESSION['set_msb']; ?>)
				if (fail == "") return true
				else { alert(fail); return false }
			}
			
			function setVisibility(id, visibility) {
				document.getElementById(id).style.display = visibility;
			}
		</script>
		<script src="functions_validate.js"></script>
	</head>
	
	<body>
		<!-- MENU -->
		<?PHP $functionObject->includeMenu(2); ?>
		<div id="menu_main">
			<a href="cust_search.php">Search</a>
			<?PHP
			if ($result_cust['cust_active'] == 1) echo '
				<a href="acc_sav_depos.php?cust='.$_SESSION['cust_id'].'">Deposit</a>
				<a href="acc_sav_withd.php?cust='.$_SESSION['cust_id'].'">Withdrawal</a>
				<a href="acc_share_buy.php?cust='.$_SESSION['cust_id'].'">Share Buy</a>
				<a href="acc_share_sale.php?cust='.$_SESSION['cust_id'].'">Share Sale</a>';
			if ($result_cust['cust_active'] == 1 AND ($timestamp-$result_cust['cust_since']) > 
			$functionObject->convertMonths($_SESSION['set_minmemb'])) echo '
				<a href="loan_new.php?cust='.$_SESSION['cust_id'].'">New Loan</a>';
			?>
			<a href="cust_new.php">New Client</a>
			<a href="cust_act.php">Active Clients</a>
			<a href="cust_inact.php">Inactive Clients</a>
		</div>
		
		<!-- LEFT SIDE: Customer Details -->
		<div class="content_left" style="width:60%;">

			<!-- HEADING -->
			<p class="heading" style="margin-bottom:.3em;">
				<?PHP echo $result_cust['cust_name'].' ('.$result_cust['cust_no'].')'; ?>
			</p>
			
			<form action="customer.php" method="post" onSubmit="return validate(this)">
				
				<table id ="tb_fields" style="border-spacing:0.1em 1.25em;">
					<colgroup>
						<col width="9%"/>
						<col width="25%"/>
						<col width="8%"/>
						<col width="25%"/>
						<col width="8%"/>
						<col width="25%"/>
					</colgroup>					
					<?PHP					
						echo '<tr>
										<td rowspan="4" colspan="2" style="text-align:center; vertical-align:top;">
										<a href="cust_new_pic.php?from=customer">';
						if (isset($result_cust['cust_pic'])) 
							echo '<img src="'.$result_cust['cust_pic'].'" title="Customer\'s picture">';
						else {
								if ($result_cust['custsex_id'] == 2) echo '<img src="ico/custpic_f.png" title="Upload new picture" />';
								else echo '<img src="ico/custpic_m.png" title="Upload new picture" />';
						}
						echo '	</a>
										</td>
										<td>Cust No:</td>
										<td><input type="text" name="cust_no" value="'.$result_cust['cust_no'].'" tabindex="1" /></td>
										<td>Occupation:</td>
										<td><input type="text" name="cust_occup" value="'.$result_cust['cust_occup'].'" tabindex="8"/></td>
									</tr>';
						echo '<tr>
										<td>Name:</td>
										<td><input type="text" name="cust_name" value="'.$result_cust['cust_name'].'" tabindex="2" /></td>
										<td>Marital Status:</td>
										<td>
											<select name="custmarried_id" size="1" tabindex="9">';
								foreach($query_mstat as $row_mstat){
									if($row_mstat ['custmarried_id'] == $result_cust['custmarried_id']){
										echo '<option selected value="'.$row_mstat['custmarried_id'].'">'.$row_mstat['custmarried_status'].'</option>';
									}
									else echo '<option value="'.$row_mstat['custmarried_id'].'">'.$row_mstat['custmarried_status'].'</option>';
								}
								echo '	</select>
										</td>
									</tr>
									<tr>
										<td>Gender:</td>
										<td>
											<select name="custsex_id" size="1" tabindex="3">';
								foreach($query_sex as $row_sex){
									if($row_sex ['custsex_id'] == $result_cust['custsex_id']){
										echo '<option selected value="'.$row_sex['custsex_id'].'">'.$row_sex['custsex_name'].'</option>';
									}
									else echo '<option value="'.$row_sex['custsex_id'].'">'.$row_sex['custsex_name'].'</option>';
								}
								echo '</select>
										</td>
										<td>Representative:</td>
										<td><input type="text" name="cust_heir" value="'.$result_cust['cust_heir'].'" tabindex="10" /></td>
									</tr>
									<tr>
										<td>DoB:</td>
										<td><input type="text" id="datepicker" name="cust_dob" value="'.date("d.m.Y",$result_cust['cust_dob']).'" placeholder="DD.MM.YYYY" tabindex="4" /></td>
										<td>Relation:</td>
										<td><input type="text" name="cust_heirrel" value="'.$result_cust['cust_heirrel'].'" tabindex="11" /></td>
									</tr>
									<tr>';
									if ($_SESSION['fee_subscr'] > 0) echo '
										<td>Subscrip. expires:</td>
										<td><input type="text" name="cust_lastsub" value="'.date("d.m.Y",$result_cust['cust_lastsub']+31536000).'" disabled="disabled"/></td>';
									else echo '<td></td><td></td>';
							echo '<td>Address:</td>
										<td><input type="text" name="cust_address" value="'.$result_cust['cust_address'].'" placeholder="Place of Residence" tabindex="5" /></td>
										<td>Sickness:</td>
										<td>
											<select name="custsick_id" size="1" tabindex="12">';
												foreach($sql_sick as $row_sick){
													if($row_sick['custsick_id'] == $result_cust['custsick_id']){
														echo '<option selected value="'.$row_sick['custsick_id'].'">'.$row_sick['custsick_name'].'</option>';
													}
													else echo '<option value="'.$row_sick['custsick_id'].'">'.$row_sick['custsick_name'].'</option>';
												}
								echo '</select>
										</td>
									</tr>';
						echo '<tr>
										<td>Member since:</td>
										<td><input type="text" name="cust_since" value="'.date("d.m.Y", $result_cust['cust_since']).'" disabled="disabled" /></td>
										<td>Phone No:</td>
										<td><input type="text" name="cust_phone" value="'.$result_cust['cust_phone'].'" tabindex="6" /></td>
										<td>Active:</td>
										<td><input type="checkbox" name="cust_active" value="1" tabindex="13"'; 
										if ($result_cust['cust_active']==1) echo ' checked="checked"';
										echo ' />
										</td>
									</tr>
									<tr>
										<td>Updated<br/>on / by:</td>
										<td><input type="text" disabled="diabled" value="'.date("d.m.Y", $result_cust['cust_lastupd']).' / '.$result_cust['user_name'].'" /></td>
										<td>E-Mail:</td>
										<td><input type="text" name="cust_email" value="'.$result_cust['cust_email'].'" placeholder="abc@xyz.com" tabindex="7" /></td>
										<td></td>
										<td><input type="submit" name="update" value="Save Changes" tabindex="14" /></td>
									</tr>';
					?>
				</table>
				
				<!--
				<input type="button" name="membership" value="Subscription" onclick="setVisibility('content_hidden', 'block');" /> 
				-->
			</form>
			
			<!-- MIDDLE PART: Renew Subscription -->
			<?PHP if($_SESSION['fee_subscr'] > 0) include 'modules/mod_subscr.php'; ?>
			
		</div>
			
		<!-- RIGHT SIDE: Account Details -->
		<div class="content_right" style="width:40%;">			
		
			<!-- TABLE 1: Savings Account -->	
			<table id="tb_table">
				<colgroup>
					<col width="20%">
					<col width="30%">
					<col width="30%">
					<col width="20%">
				</colgroup>
				<tr>
					<th class="title" colspan="4">
						<?PHP
						if ($result_cust['cust_active'] == 1) echo
						'<a href="acc_sav_depos.php?cust='.$_SESSION['cust_id'].'">Savings Account</a> (Recent Transactions)';
						else echo 'Savings Account (Recent Transactions)';
						?>
					</th>
				</tr>
				<tr>
					<th>Date</th>
					<th>Transaction Type</th>
					<th>Amount</th>
					<th>Receipt/Slip</th>
				</tr>
			 <?PHP
			 	foreach($sql_sav as $row_sav) {
					tr_colored($color);
					echo '	<td>'.date("d.m.Y",$row_sav['sav_date']).'</td>
									<td>'.$row_sav['savtype_type'].'</td>
									<td>'.number_format($row_sav['sav_amount']).' '.$_SESSION['set_cur'].'</td>';
					if ($row_sav['savtype_id'] == 2) echo '<td>S '.$row_sav['sav_slip'].'</td>';
						else echo '<td>R '.$row_sav['sav_receipt'].'</td>';
					echo '</tr>';
				}
			
				echo '<tr class="balance">
								<td colspan="4" >Balance: '.number_format($savbalance).' '.$_SESSION['set_cur'].'</td>
							</tr>';
			 ?>
			</table>
			
			<!-- TABLE 2: Loans Account -->	
			<table id="tb_table">
				<colgroup>
					<col width="20%">
					<col width="20%">
					<col width="20%">
					<col width="20%">
					<col width="20%">
				</colgroup>
				<tr>
					<th class="title" colspan="6">Loans Account</th>
				</tr>
				<tr>
					<th>No.</th>
					<th>Status</th>
					<th>Total Repay</th>
					<th>Remaining</th>
					<th>Rate Due</th>
				</tr>
				<?PHP
				//Select all loans for current customer
				$query_loans = $functionObject->getCustomerLoans();				
				$color = 0;
				foreach($query_loans as $row_loan){					
					//Select last unpaid Due Date from LTRANS 
					$sql_ltrans = "SELECT MIN(ltrans_due) FROM ltrans, loans WHERE ltrans.loan_id = loans.loan_id AND loans.loanstatus_id = '2'
					 AND loans.loan_id = '$row_loan[loan_id]' AND ltrans_due IS NOT NULL AND ltrans_date IS NULL";
					$query_ltrans = mysql_query($sql_ltrans);
					checkSQL($query_ltrans);
					$next_due = mysql_fetch_assoc($query_ltrans);
					
					//Select Loan Balance from LTRANS
					$sql_balance = "SELECT ltrans_principaldue, ltrans_interestdue, ltrans_principal, ltrans_interest FROM ltrans, loans 
					WHERE ltrans.loan_id = loans.loan_id AND loans.loanstatus_id = '2' AND loans.loan_id = '$row_loan[loan_id]'";
					$query_balance = mysql_query($sql_balance);
					checkSQL($query_balance);
					
					$loan_balance = 0;
					$loan_paid = 0;
					while ($row_balance = mysql_fetch_assoc($query_balance)){
						$loan_paid = $loan_paid + $row_balance['ltrans_principal'] + $row_balance['ltrans_interest'];
						$loan_balance = $loan_balance + $row_balance['ltrans_interestdue'] + $row_balance['ltrans_principaldue'];
					}
					$loan_balance = $loan_balance - $loan_paid;
					
					tr_colored($color);
					echo '	<td><a href="loan.php?lid='.$row_loan['loan_id'].'">'.$row_loan['loan_no'].'</a></td>
									<td>'.$row_loan['loanstatus_status'].'</td>
									<td>'.number_format($row_loan['loan_repaytotal']).'</td>
									<td>'.number_format($loan_balance).'</td>';
					if ($row_loan['loanstatus_id'] == 2 and isset($next_due)) {
						echo '<td';
						if ($next_due['MIN(ltrans_due)'] < time()) echo ' class="warn"';
						if ($next_due['MIN(ltrans_due)'] != null) echo '>'.date("d.m.Y",$next_due['MIN(ltrans_due)']).'</td>';
						else echo '></td>';
					}
					else echo '<td></td>';
					echo '</tr>';
					}
				?>
			</table>
			
		<!-- TABLE 3: Share Account -->	
		<table id="tb_table">
			<tr>
				<th class="title" colspan="2">
					<?PHP
					if ($result_cust['cust_active'] == 1) echo
					'<a href="acc_share_buy.php?cust='.$_SESSION['cust_id'].'">Share Account</a>';
					else echo 'Share Account';
					?>
				</th>
			</tr>
			<tr>
				<th>Number of Shares</th>
				<th>Value of Shares</th>
			</tr>
			<tr>
				<td><?PHP echo $share_amount ?></td>
				<td><?PHP echo number_format($share_value).' '.$_SESSION['set_cur'] ?></td>
			</tr>
		</table>
	
	</div>
	
	</body>
	<?PHP 
	if ($share_amount == 0 && $result_cust['cust_active'] == 1)	$functionObject->showMessage('This Client owns no Shares!');
	?>
</html>