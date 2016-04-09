<!DOCTYPE HTML>
<?PHP
	session_start();
	require 'functions.php';
	$functionObject=new functions();	
	
	if(isset($_POST['login'])){		
		// Sanitize user input
		$username = $_POST['log_user'];
		$password = $_POST['log_pw'];
		$fingerprint=$functionObject->fingerprint();
		// Select user details from USER
		$user=$functionObject->getUserByUsernameAndPassword($username, $password);
		if($user){			
			// Define Session Variables for this User
			$_SESSION['log_user'] = $username;
			$_SESSION['log_time'] = time();
			$_SESSION['log_id'] = $user['user_id'];
			$_SESSION['log_ugroup'] = $user['ugroup_name'];
			$_SESSION['log_admin'] = $user['ugroup_admin'];
			$_SESSION['log_delete'] = $user['ugroup_delete'];
			$_SESSION['log_report'] = $user['ugroup_report'];
			$_SESSION['log_fingerprint'] = $fingerprint;
			
			
			
			// Forward to start.php
			header('Location: start.php');
		}
		else $functionObject->showMessage('Authentification failed!\nWrong Username and/or Password!');
	}
?>
<html>
	<?PHP $functionObject->includeHead('Microfinance Management') ?>	
	
	<body>
		<div class="content_center" style="width:100%; margin-top:15em">
		
		<!-- LEFT SIDE: mangoO Logo -->
		<div class="content_left" style="width:50%; padding-right:5em; text-align:right;">
			<img src="logo.jpg" style="width:75%;">
		</div>
		
		<!-- RIGHT SIDE: Login Form -->
		<div class="content_right" style="width:50%; padding-left:5em; text-align:left;">
			
			<p class="heading" style="margin:0; text-align:left;">Please login...</p>
			
			<form action="login.php" method="post">
				<table id="tb_fields" style="margin:0; border-spacing:0em 1.25em;">
					<tr>
						<td>
							<input type="text" name="log_user"  placeholder="Username" />
						</td>
					</tr>
					<tr>
						<td>
							<input type="password" name="log_pw" placeholder="Password" />
						</td>
					</tr>
					<tr>
						<td>
							<input type="submit" name="login" value="Login">
						</td>
					</tr>
				</table>
			</form>
			
		</div>
		
	</body>
</html>