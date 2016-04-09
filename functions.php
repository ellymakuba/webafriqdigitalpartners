<?PHP	
class functions{
    private $conn;
	function __construct() {
		require_once 'DB_Connect.php';			
		$db=new DB_Connect();
		$this->conn=$db->connect();		
	}
public function getUserByUsernameAndPassword($username, $password){
 		$password=sha1($password);
        $stmt = $this->conn->prepare("SELECT * FROM user, ugroup WHERE user.ugroup_id = ugroup.ugroup_id AND user_name = ? AND user_pw LIKE ?");
		$data=array($username, $password);
		$stmt->execute($data);        
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
		return $stmt->fetch();
    }
/**
	* Generate unique fingerprint for every user session
	* @return string fingerprint : Unique fingerprint generated from remote server address, random string, and user agent
	*/
public function fingerprint(){
		return $fingerprint = md5($_SERVER['REMOTE_ADDR'].'jikI/20Y,!'.$_SERVER['HTTP_USER_AGENT']);
	}
	
/**
	* Check if current user is logged in
	*/
	 function checkLogin() {
		$fingerprint=$this->fingerprint();
		session_start();
		if (!isset($_SESSION['log_user']) || $_SESSION['log_fingerprint'] != $fingerprint) $this->logout();
		session_regenerate_id();
	}
	
/**
	* Check if current user logged out properly last time
	*/
	function checkLogout(){
		if ($_SESSION['logrec_logout'] == 0){
			$this->showMessage("You forgot to logout last time. Please remember to log out properly.");
			$_SESSION['logrec_logout'] = 1;
		}
	}

/**
	* Check if current user has Admin permission
	*/
	function checkPermissionAdmin() {
		if ($_SESSION['log_admin']!=='1'){
			header('Location: start.php');
			die();
		}
	}
	
/**
	* Check if current user has Delete permission
	*/
	function checkPermissionDelete() {
		if ($_SESSION['log_delete']!=='1'){
			header('Location: start.php');
			die();
		}
	}
	
/**
	* Check if current user has permission to access Reports
	*/
	function checkPermissionReport() {
		if ($_SESSION['log_report']!=='1'){
			header('Location: start.php');
			die();
		}
	}

	/**
	* Logout procedure: Delete session variables 
	* and cookies, destroy user session.
	*/
	function logout(){
		/* Delete all Session Variables */
		$_SESSION = array();
		
		/* If a session cookie was used, delete it */
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 86400, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
		}
		
		/* Finally, delete the Session */
		session_destroy();													
		
		/* Forward to logout_success.php */
		header('Location: login.php');
		die;
	}
	
/**
	* Check if an SQL statement has succeded
	*/
	function checkSQL($sqlquery){
		if (!$sqlquery) die ('SQL-Statement failed: '.mysql_error());
	}	
	
/**
	* Pushing system settings into session variables
	*/	
	public function getSettings(){
	    $stmt = $this->conn->prepare("SELECT * FROM settings");
		$stmt->execute();        
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
		while($row_settings = $stmt->fetch()){
			
			switch ($row_settings['set_id']){
				case 1:
					$_SESSION['set_msb'] = $row_settings['set_value'];
					break;
				case 2:
					$_SESSION['set_minlp'] = $row_settings['set_value'];
					break;
				case 3:
					$_SESSION['set_maxlp'] = $row_settings['set_value'];
					break;
				case 4:
					$_SESSION['set_cur'] = $row_settings['set_value'];
					break;
				case 5:
					$_SESSION['set_auf'] = $row_settings['set_value'];
					break;
				case 6:
					$_SESSION['set_deact'] = $row_settings['set_value'];
					break;
				case 7:
					$_SESSION['set_dashl'] = $row_settings['set_value'];
					break;
				case 8:
					$_SESSION['set_dashr'] = $row_settings['set_value'];
					break;
				case 9:
					$_SESSION['set_intcalc'] = $row_settings['set_value'];
					break;
				case 10:
					$_SESSION['set_maxguar'] = $row_settings['set_value'];
					break;
				case 11:
					$_SESSION['set_minmemb'] = $row_settings['set_value'];
					break;
				case 12:
					$_SESSION['set_maxpsr'] = $row_settings['set_value'];
					break;
			}
		}
	}
	
/**
	* Pushing fee settings into session variables
	*/
	function getFees(){
	   $stmt = $this->conn->prepare("SELECT * FROM fees");
		$stmt->execute();        
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		
		while ($row_fees = $stmt->fetch()){
			switch ($row_fees['fee_id']){
				case 1:
					$_SESSION['fee_entry'] = $row_fees['fee_value'];
					break;
				case 2:
					$_SESSION['fee_withdraw'] = $row_fees['fee_value'];
					break;
				case 3:
					$_SESSION['fee_stationary'] = $row_fees['fee_value'];
					break;
				case 4:
					$_SESSION['fee_subscr'] = $row_fees['fee_value'];
					break;
				case 5:
					$_SESSION['fee_loan'] = $row_fees['fee_value'];
					break;
				case 6:
					$_SESSION['fee_loanappl'] = $row_fees['fee_value'];
					break;
				case 7:
					$_SESSION['fee_loanfine'] = $row_fees['fee_value'];
					break;
				case 8:
					$_SESSION['fee_loaninterestrate'] = $row_fees['fee_value'];
					break;
			}
		}
	}
	
/**
	* Pushing current share value into a session variable
	*/
	function getShareValue(){
		$sql_shareval = "SELECT shareval_value FROM shareval WHERE shareval_id IN (SELECT MAX(shareval_id) FROM shareval)";
		$query_shareval = mysql_query($sql_shareval);
		checkSQL($query_shareval);
		$result_shareval = mysql_fetch_assoc($query_shareval);
		$_SESSION['share_value'] = $result_shareval['shareval_value'];
	}
	
/**
	* Sanitize and secure user input
	* @param string var : User Input
	* @return string var : Secured and sanitized User Input
	*/
	function sanitize($var) {
		if(get_magic_quotes_gpc()) $var = stripslashes($var);
		$var = htmlentities($var);
		$var = strip_tags($var);
		$var = mysql_real_escape_string($var);
		return $var;
	}
	
/**
	* Convert a number of days into UNIX timestamp seconds
	* @param int days : Number of days
	* @return int seconds : Lenght of number of days in seconds
	*/
	function convertDays($days){
		return $seconds = $days * 86400;
	}

/**
	* Convert a number of months into UNIX timestamp seconds
	* @param int months : Number of months
	* @return int seconds : Lenght of number of days in seconds
	*/	
	function convertMonths($months){
		return $seconds = $months * 2635200; // Seconds for 30.5 days
	}
	
/**
	* Check if a GET parameter with a Customer ID has been set
	* If not, return to start page.
	*/
	function getCustID(){
		if (isset($_GET['cust'])) $_SESSION['cust_id'] = $_GET['cust'];
		else header('Location: start.php');
	}
	
/**
	* Check if a GET parameter with a Loan ID has been set
	* If not, return to customer page.
	*/
	function getLoanID(){
		if (isset($_GET['lid'])) $_SESSION['loan_id'] = sanitize($_GET['lid']);
		else header('Location: customer.php?cust='.$_SESSION['cust_id']);
	}
	
/**
	* Generate HTML Header Section
	* @param string title : Page title
	* @param int endFlag : Flag to indicate whether or not to end header section.
	*/
	function includeHead($title, $endFlag = 1) {
		echo '<head>
			<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
			<meta http-equiv="Content-Script-Type" content="text/javascript">
			<meta http-equiv="Content-Style-Type" content="text/css">
			<meta name="robots" content="noindex, nofollow">
			<title>webafriq digital partners | '.$title.'</title>
			<link rel="shortcut icon" href="ico/favicon.ico" type="image/x-icon">
			<link rel="stylesheet" href="css/mangoo.css" />
			<link rel="stylesheet" href="ico/font-awesome/css/font-awesome.min.css">
			<link rel="stylesheet" href="jquery/jquery-ui-1.11.4/jquery-ui.min.css">
			<script src="jquery/jquery-2.2.1.min.js"></script>
			<script src="jquery/jquery-ui-1.11.4/jquery-ui.min.js"></script>
			<script>
				$(function() {
					$("#datepicker, #datepicker2, #datepicker3").datepicker({
						showOtherMonths: true,
						selectOtherMonths: true,
						dateFormat: \'dd.mm.yy\',
						changeMonth: true,
						changeYear: true
					});
				});
			</script>
			';
		if ($endFlag == 1) echo '</head>';
	}

/**
	* Generate Menu bar
	* @param int tab_no : Number of currently selected menu tab.
	*/
	function includeMenu($tab_no){
		echo '		
		<!-- MENU HEADER -->
		<div id="menu_header">
			<div id="menu_logout">
				<ul>
					<li>'.$_SESSION['log_user'].'
						<ul>
							<li><a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>';
		
		echo '
		<!-- MENU TABS -->
		<div id="menu_tabs"> 
			<ul>
				<li'; 
				if ($tab_no == 1) echo ' id="tab_selected"';
				echo '><a href="start.php"><i class="fa fa-tachometer fa-fw"></i> Dashboard</a></li>
				<li';
				if ($tab_no == 2) echo ' id="tab_selected"';
				echo '><a href="cust_search.php"><i class="fa fa-group fa-fw"></i> Clients</a></li>
				<li';
				if ($tab_no == 3) echo ' id="tab_selected"';
				echo '><a href="loan_search.php"><i class="fa fa-percent fa-fw"></i> Loans</a></li>
				<li';
				if ($tab_no == 4) echo ' id="tab_selected"';
				echo '><a href="books_expense.php"><i class="fa fa-calculator fa-fw"></i> Accounting</a></li>
				<li';
				if ($tab_no == 7) echo ' id="tab_selected"';
				echo '><a href="empl_curr.php"><i class="fa fa-male fa-fw"></i> Employees</a></li>';
				
				if ($_SESSION['log_report'] == 1){
					echo '<li';
					if ($tab_no == 5) echo ' id="tab_selected"';
					echo '><a href="rep_incomes.php"><i class="fa fa-line-chart fa-fw"></i> Reports</a></li>';
				}
				
				if ($_SESSION['log_admin'] == 1){
					echo '<li';
					if ($tab_no == 6) echo ' id="tab_selected"';
					echo '><a href="set_basic.php"><i class="fa fa-wrench fa-fw"></i> Settings</a></li>';
				}
			echo '</ul>
		</div>';
	}
	
/**
	* Alternate table rows background color for improved readability
	* @param int row_color : Indicator for row color
	*/
	function tr_colored(&$row_color) {
		if ($row_color == 0){ 
			echo '<tr>';
			$row_color = 1;
		}
		else {
			echo '<tr class="alt">'; 
			$row_color = 0;
		}
	}
	
/**
	* Generate a Javascript alert message
	* @param string text : Message text
	*/
	function showMessage($text) {
		echo '<script language=javascript>
						alert(\''.$text.'\')
					</script>';
	}
	
/**	
	* Calculate a given customer's savings account balance
	* @return int sav_balance : Current savings account balance for given customer
	*/
	function getSavingsBalance($cust_id){
		$stmt=$this->conn->prepare("SELECT savbal_balance FROM savbalance WHERE cust_id =':cust'");
		$param=array(":cust"=>$cust_id);
		$stmt->execute($param);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		return $stmt->fetch();
	}
	function updateCustomer($cust_no,$cust_name,$cust_dob,$custsex_id,$cust_address,$cust_phone,$cust_email,$cust_occup,$cust_active,$timestamp)
	{
		 $stmt=$this->conn->prepare("UPDATE customer SET cust_no = ':cust_no', cust_name = ':cust_name', cust_dob = :cust_dob,
		 custsex_id = :custsex_id,
		 cust_address = ':cust_address', cust_phone = ':cust_phone', cust_email = ':cust_email', cust_occup = ':cust_occup',
		 custmarried_id = $custmarried_id, cust_heir = '$cust_heir', cust_heirrel = '$cust_heirrel', custsick_id = $custsick_id,
		 cust_active = ':cust_active', cust_lastupd = ':timestamp', user_id =':sessionLogID' WHERE cust_id =':custIdSession'");
		 
		 $params=array(":cust_no"=>$cust_no,":cust_name"=>$cust_name,":cust_dob"=>$cust_dob,":custsex_id"=>$custsex_id,
		 ":cust_address"=>$cust_address,":cust_phone"=>$cust_phone,":cust_email"=>$cust_email,":cust_occup"=>$cust_occup,
		 ":cust_active"=>$cust_active,":timestamp"=>$timestamp,"sessionLogID"=>$_SESSION['log_id'],"custIdSession"=>$_SESSION['cust_id']);
		$stmt->execute($params);
	}	
/**
	* Update savings account balance for SPECIFIC customer
	*/
	function updateSavingsBalance($cust_id){
	    $timestamp = time();
		$sql_cust = "SELECT cust_id FROM customer WHERE cust_id = $cust_id";
		$query_cust = mysql_query($sql_cust);
		if(mysql_num_rows($query_cust)>0){
			$sql_savbal_upd = "UPDATE savbalance SET savbal_balance = (SELECT SUM(sav_amount) FROM savings WHERE cust_id = $cust_id) 
			WHERE cust_id = $cust_id";
			$query_savbal_upd = mysql_query($sql_savbal_upd);
			checkSQL($query_savbal_upd);
		}
		else{
			$sql_savings = "SELECT SUM(sav_amount) FROM savings WHERE cust_id = $cust_id";
	        $query_savings = mysql_query($sql_savings);
			$total_savings=mysql_fetch_row($query_savings);
	
			$sql_savbal = "INSERT INTO savbalance (cust_id, savbal_balance, savbal_date, savbal_created, user_id) VALUES ('$cust_id', 
			'$total_savings', '$timestamp', '$timestamp', '1')";
			$query_savbal = mysql_query($sql_savbal);
			checkSQL($query_savbal);
		}
	}

/**
	* Update savings account balance for ALL customers
	*/
	function updateSavingsBalanceAll(){
		$sql_savbal_upd_all = "UPDATE savbalance SET savbalance.savbal_balance = (SELECT SUM(savings.sav_amount) FROM savings WHERE savings.cust_id = savbalance.cust_id)";
		$query_savbal_upd_all = mysql_query($sql_savbal_upd_all);
		checkSQL($query_savbal_upd_all);
	}	
	
/**
	* Calculate current customer's share account balance
	* @return int share_balace : Current share account balance
	*/
	function getShareBalance($cust_id){
		$sql_sharebal = "SELECT share_amount, share_value FROM shares WHERE cust_id = $cust_id";
		$query_sharebal = mysql_query($sql_sharebal);
		checkSQL($query_sharebal);
		$sharebal = array("amount" => "0", "value" => "0");
		while($row_sharebal = mysql_fetch_assoc($query_sharebal)){
			$sharebal['amount'] = $sharebal['amount'] + $row_sharebal['share_amount'];
			$sharebal['value'] = $sharebal['value'] + $row_sharebal['share_value'];
		}
		return $sharebal;
	}
	
/**
	* Get current customer's details
	* @return array result_cust : Associative array with the details of the current customer
	*/
	function getCustomer($custID){
		$stmt=$this->conn->prepare("SELECT * FROM customer LEFT JOIN custsex ON customer.custsex_id = custsex.custsex_id LEFT JOIN custmarried 
		ON customer.custmarried_id = custmarried.custmarried_id LEFT JOIN custsick ON customer.custsick_id = custsick.custsick_id
		LEFT JOIN user ON customer.user_id = user.user_id WHERE cust_id =:customer");
		$param=array(":customer"=>$custID);
		$stmt->execute($param);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		return $stmt->fetch();
	}
	function getMaritalStatus(){
		$stmt=$this->conn->prepare("SELECT * FROM custmarried");
		$stmt->execute();
		$resultSet=$stmt->fetchALL();	
		return $resultSet;
	}
	function getSickness(){
		$stmt=$this->conn->prepare("SELECT * FROM custsick");
		$stmt->execute();
		$resultSet=$stmt->fetchALL();	
		return $resultSet;
	}
	function getGender(){
		$stmt=$this->conn->prepare("SELECT * FROM custsex");
		$stmt->execute();
		$resultSet=$stmt->fetchALL();	
		return $resultSet;
	}
	function getShares(){
		$stmt=$this->conn->prepare("SELECT * FROM shares WHERE cust_id = ':custIdSession'");
		$params=array(":custIdSession"=>$_SESSION['cust_id']);
		$stmt->execute($params);
		$resultSet=$stmt->fetchALL();	
		return $resultSet;
	}
	function getLastFiveRecentSavings(){
		$stmt=$this->conn->prepare("SELECT * FROM savings, savtype WHERE savings.savtype_id = savtype.savtype_id AND cust_id = ':custIdSession'
		ORDER BY sav_date DESC, sav_id DESC LIMIT 5");
		$params=array(":custIdSession"=>$_SESSION['cust_id']);
		$stmt->execute($params);
		$resultSet=$stmt->fetchALL();	
		return $resultSet;
	}
	function getCustomerLoans(){
		$stmt=$this->conn->prepare("SELECT * FROM loans, loanstatus WHERE loans.loanstatus_id = loanstatus.loanstatus_id 
		AND cust_id = ':custIdSession'");
		$params=array(":custIdSession"=>$_SESSION['cust_id']);
		$stmt->execute($params);
		$resultSet=$stmt->fetchALL();	
		return $resultSet;
	}
	
/**
	* Get all customers except current one
	* @return array query_custother : Array with the result of the SQL query
	*/
	function getCustOther(){
		$sql_custother = "SELECT * FROM customer LEFT JOIN custsex ON custsex.custsex_id = customer.custsex_id WHERE cust_id NOT IN (0, $_SESSION[cust_id]) ORDER BY cust_id";
		$query_custother = mysql_query($sql_custother);
		checkSQL($query_custother);
		
		return $query_custother;
	}

/**
	* Get all active customers
	* @return array query_custact : Array with the result of the SQL query
	*/
	function getCustAct(){
		$sql_custact = "SELECT * FROM customer LEFT JOIN custsex ON custsex.custsex_id = customer.custsex_id WHERE cust_id != 0 AND cust_active = 1 ORDER BY cust_id";
		$query_custact = mysql_query($sql_custact);
		checkSQL($query_custact);
		
		return $query_custact;
	}

/**
	* Get all inactive customers
	* @return array query_custinact : Array with the result of the SQL query
	*/
	function getCustInact(){
		$sql_custinact = "SELECT * FROM customer LEFT JOIN custsex ON custsex.custsex_id = customer.custsex_id WHERE cust_id != 0 AND cust_active != 1 ORDER BY cust_id";
		$query_custinact = mysql_query($sql_custinact);
		checkSQL($query_custinact);
		
		return $query_custinact;
	}

/**
	* Get current employee
	* @return array result_empl :  Associative array with the details of the current employee
	*/
	function getEmployee($empl_id){
		$sql_empl = "SELECT * FROM employee LEFT JOIN user ON employee.empl_id = user.empl_id WHERE employee.empl_id = $empl_id";
		$query_empl = mysql_query($sql_empl);
		checkSQL($query_empl);
		$result_empl = mysql_fetch_assoc($query_empl);
		
		return $result_empl;
	}
		
/**
	* Get all current employees
	* @return array query_emplcurr : Array with the result of the SQL query
	*/
	function getEmplCurr(){
		$timestamp = time();
		$sql_emplcurr = "SELECT * FROM employee LEFT JOIN emplsex ON employee.emplsex_id = emplsex.emplsex_id LEFT JOIN emplmarried ON employee.emplmarried_id = emplmarried.emplmarried_id WHERE empl_id != 0 AND (empl_out > $timestamp OR empl_out IS NULL) ORDER BY empl_id";
		$query_emplcurr = mysql_query($sql_emplcurr);
		checkSQL($query_emplcurr);
		
		return $query_emplcurr;
	}

/**
	* Get all past employees
	* @return array query_emplpast : Array with the result of the SQL query
	*/
	function getEmplPast(){
		$timestamp = time();
		$sql_emplpast = "SELECT * FROM employee LEFT JOIN emplsex ON employee.emplsex_id = emplsex.emplsex_id LEFT JOIN emplmarried ON employee.emplmarried_id = emplmarried.emplmarried_id WHERE empl_id != 0 AND empl_out < $timestamp ORDER BY empl_id";
		$query_emplpast = mysql_query($sql_emplpast);
		checkSQL($query_emplpast);
		
		return $query_emplpast;
	}
	public function getCustomerLastSubscription($last_subscr){
        $stmt = $this->conn->prepare("SELECT * FROM customer WHERE cust_active=? AND cust_lastsub < ? ORDER BY cust_lastsub, cust_id");
		$data=array(1,$last_subscr);
		$stmt->execute($data);        
        $resultSet=$stmt->fetchALL();
		return $resultSet;
    }
	public function getLoanDefaulters($timestamp){
        $stmt = $this->conn->prepare("SELECT * FROM ltrans LEFT JOIN loans ON ltrans.loan_id = loans.loan_id LEFT JOIN customer 
		ON loans.cust_id = customer.cust_id WHERE ltrans_due <= :time AND ltrans_date IS :lastDate AND loanstatus_id = :loanStatus 
		ORDER BY ltrans_due");
		$params = array( ':time'=>$timestamp, ':lastDate'=>'NULL', ':loanStatus'=>'2');
		$stmt->execute($params);        
        $resultSet=$stmt->fetchALL();
		return $resultSet;
    }
}	
?>