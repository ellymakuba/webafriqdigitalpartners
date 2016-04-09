<!DOCTYPE HTML>
<?PHP
	require 'functions.php';
	function CryptPass($Password ) {
		return sha1($Password);
    }
	checkLogin();
	checkPermissionAdmin();
	connect();
	$user_id = 0;
	$employee = 0;
	
	//Select all users from USER
	$users = array();
	$user_names = array();
	$sql_users = "SELECT user.user_id, user.user_name, user.user_created, ugroup.ugroup_id, ugroup.ugroup_name, employee.empl_id, employee.empl_name FROM user, ugroup, employee WHERE ugroup.ugroup_id = user.ugroup_id AND user.empl_id = employee.empl_id ORDER BY user_name";
	$query_users = mysql_query($sql_users);
	checkSQL ($query_users);
	while($row_users = mysql_fetch_assoc($query_users)){
		$users[] = $row_users;
		$user_names[] = $row_users['user_name'];
	}
	
	//Select all usergroups from UGROUP
	$sql_ugroup = "SELECT ugroup_id, ugroup_name FROM ugroup";
	$query_ugroup = mysql_query($sql_ugroup);
	checkSQL($query_ugroup);
	
	// Select all employees from EMPLOYEE
	$sql_employees = "SELECT empl_id, empl_name FROM employee WHERE empl_id != 0";
	$query_employees = mysql_query($sql_employees);
	checkSQL($query_employees);

	// Select employees from EMPLOYEE who are already associated with a user 
	$sql_empl_assoc = "SELECT empl_id FROM employee WHERE empl_id != 0 AND empl_id IN (SELECT empl_id FROM user)";
	$query_empl_assoc = mysql_query($sql_empl_assoc);
	checkSQL($query_empl_assoc);
	$empl_assoc = array();
	while($row_empl_assoc = mysql_fetch_assoc($query_empl_assoc)){
		$empl_assoc[] = $row_empl_assoc['empl_id'];
	}
	
	//Set heading and variables according to selection
	if(isset($_GET['user'])){
		$user_id = sanitize($_GET['user']);
		foreach ($users as $row_user){
			if ($row_user['user_id'] == $user_id){
				$user_id = $row_user['user_id'];
				$user_name = $row_user['user_name'];
				$user_ugroup = $row_user['ugroup_id'];
				$employee = $row_user['empl_id'];
			}
		}
		$heading = "Edit User";
	}
	else $heading = "Create User";
	
	//SAVE Button
	if(isset($_POST["save_changes"])){
		//Sanitize user input
		$user_id = sanitize($_POST['user_id']);
		$user_name = sanitize($_POST['user_name']);
		$user_pw = CryptPass((sanitize($_POST['user_pw'])));
		$empl_id = sanitize($_POST['empl_id']);
		$ugroup = sanitize($_POST['ugroup']);
		if($user_id == 1) $ugroup = 1;
		$timestamp = time();
		
		if($user_id == 0){
			// Insert new user into USER
			$sql_user_ins = "INSERT INTO user (user_name, user_pw, ugroup_id, empl_id, user_created) VALUES ('$user_name', '$user_pw', '$ugroup', '$empl_id', '$timestamp')";
			$query_user_ins = mysql_query($sql_user_ins);
			checkSQL($query_user_ins);
		}
		else {
			// Update existing user
			$sql_user_upd = "UPDATE user SET user_name = '$user_name', user_pw = '$user_pw', ugroup_id = $ugroup, empl_id = $empl_id, user_created = $timestamp WHERE user_id = $user_id";
			$query_user_upd = mysql_query($sql_user_upd);
			checkSQL($query_user_upd);
		}
		header('Location:set_user.php');
	}
?>

<html>
	<?PHP includeHead('Settings | Users', 0) ?>
		<script>
			function validate(form){
				fail = validateUser(form.user_name.value, <?PHP echo json_encode($user_names); ?>, <?PHP echo $user_id; ?>)
				fail += validatePw(form.user_pw.value, form.user_pw_conf.value)
				fail += validateEmployee(form.empl_id.value, <?PHP echo json_encode($empl_assoc); ?>, <?PHP echo $employee; ?>)
				if (fail == "") return true
				else { 
					alert(fail); 
					return false
				}
			}
		</script>
		<script src="functions_validate.js"></script>
	</head>
	
	<body>
		<!-- MENU -->
		<?PHP includeMenu(6); ?>
		<div id="menu_main">
			<a href="set_basic.php">Basic Settings</a>
			<a href="set_loans.php">Loan Settings</a>
			<a href="set_fees.php">Fees & Charges</a>
			<a href="set_user.php" id="item_selected">Users</a>
			<a href="set_ugroup.php">Usergroups</a>
			<a href="set_logrec.php">Log Records</a>
		</div>
		
		<!-- LEFT SIDE: Create New User Form -->
		<div class="content_left">
			<div class="content_settings" style="text-align:left; width:80%;">
				
				<p class="heading"><?PHP echo $heading; ?></p>
			
				<form action="set_user.php" method="post" onSubmit="return validate(this)">
				
					<table id="tb_set" style="margin:auto;">
						<tr>
							<td>Username</td>
							<td><input type="text" name="user_name" placeholder="Username" value="<?PHP if(isset($user_name)) echo $user_name;?>" /></td>
						</tr>
						<tr>
							<td>Password</td>
							<td><input type="password" name="user_pw" placeholder="Password" /></td>
						</tr>
						<tr>
							<td>Repeat Password</td>
							<td><input type="password" name="user_pw_conf" placeholder="Repeat Password" /></td>
						</tr>
						<tr>
							<td>Usergroup</td>
							<td class="center">
								<select name="ugroup" size="1" <?PHP if ($user_id == 1) echo ' disabled="disabled"'; ?> >
									<?PHP
									while ($row_ugroup = mysql_fetch_assoc($query_ugroup)){
											echo '<option value="'.$row_ugroup['ugroup_id'].'"';
											if (isset($user_ugroup) and $row_ugroup['ugroup_id'] == $user_ugroup) echo ' selected="selected	"';
											echo '>'.$row_ugroup['ugroup_name'].'</option>';
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td>Employee:</td>
							<td>
								<select name="empl_id" size="1">';
									<option value="0">None</option>
									<?PHP
										while ($row_employees = mysql_fetch_assoc($query_employees)){
											echo '<option value="'.$row_employees['empl_id'].'"';
											if (isset($employee) and $row_employees['empl_id'] == $employee) echo ' selected="selected	"';
											echo '>'.$row_employees['empl_name'].'</option>';
										}
									?>
								</select>
							</td>
						</tr>
					</table>
					<input type="submit" name="save_changes" value="Save Changes" />
					<input type="hidden" name="user_id" value="<?PHP echo $user_id; ?>" />
				</form>			
			</div>
		</div>
		
		<!-- RIGHT SIDE: List of Users -->
		<div class="content_right">
			<form action="set_ugroup.php" method="post">
				<table id="tb_table">				
					<colgroup>
						<col width="26%">
						<col width="26%">
						<col width="26%">
						<col width="16%">
						<col width="6%">
					</colgroup>
					<tr>
						<th class="title" colspan="5">Existing Users</th>
					</tr>
					<tr>
						<th>User Name</th>
						<th>User Group</th>
						<th>Employee</th>
						<th>Changed</th>
						<th>Edit</th> 
					</tr>
					<?PHP
					$color=0;
					foreach ($users as $row_user){					
						tr_colored($color);		//Alternating row colors
						echo '<td>'.$row_user['user_name'].'</td>
									<td>'.$row_user['ugroup_name'].'</td>
									<td><a href="employee.php?empl='.$row_user['empl_id'].'">'.$row_user['empl_name'].'</a></td>
									<td>'.date('d.m.Y',$row_user['user_created']).'</td>
									<td>
										<a href="set_user.php?user='.$row_user['user_id'].'">
											<i class="fa fa-edit fa-lg"></i>
										</a>
									</td>
								</tr>';
					}
					?>
				</table>
			</form>
		</div>
	</body>
</html>