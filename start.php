<!DOCTYPE HTML>
<?PHP
	require 'functions.php';
	$functionObject=new functions();
	$functionObject->checkLogin();
	$functionObject->getSettings();
	$functionObject->getFees();
?>

<html>
	<!-- HTML HEAD -->
	<?PHP $functionObject->includeHead('Microfinance Management',1); ?>
	
	<!-- HTML BODY -->
	<body>
		<!-- MENU -->
		<?PHP 
				$functionObject->includeMenu(1);
		?>
		<!-- MENU MAIN -->
		<div id="menu_main">
			<a href="cust_search.php">Search Client</a>
			<a href="cust_new.php">New Client</a>
			<a href="loan_search.php">Search Loan</a>
		</div>
		
		<!-- Left Side of Dashboard -->
		<div class="content_left" style="width:50%;">
			<?PHP include $_SESSION['set_dashl']; ?>
		</div>

		<!-- Right Side of Dashboard -->
		<div  class="content_right" style="width:50%;">
			<?PHP include $_SESSION['set_dashr']; ?>
		</div>
		
		<!-- Logout Reminder Message -->
		<?PHP	$functionObject->checkLogout();	?>
		
	</body>
</html>