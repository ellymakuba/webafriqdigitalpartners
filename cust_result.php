<!DOCTYPE HTML>
<?PHP
	require 'functions.php';
	checkLogin();
	connect();
	
	//Select from CUSTOMER
	if (isset($_POST['cust_search'])){
		$cust_search_name = sanitize($_POST['cust_search_name']);
		$cust_search_addr = sanitize($_POST['cust_search_addr']);
		$cust_search_occup = sanitize($_POST['cust_search_occup']);
		
		//Defining WHERE condition
		$where = "";
		$title = "Clients";
		if ($cust_search_name != ""){
			$where = "cust_name LIKE '%$cust_search_name%'";
			$title = $title.' named "'.ucwords($cust_search_name).'"';
		}
		if ($cust_search_name != "" AND $cust_search_addr != "") $where = $where.' AND ';
		if ($cust_search_addr != ""){
			$where = $where."cust_address LIKE '%$cust_search_addr%'";
			$title = $title.' from "'.ucwords($cust_search_addr).'"';
		}
		if (($cust_search_name != "" OR $cust_search_addr != "") AND $cust_search_occup != "") $where = $where.' AND ';
		if ($cust_search_occup != ""){
			$where = $where."cust_occup LIKE '%$cust_search_occup%'";
			$title = $title.' working as "'.ucwords($cust_search_occup).'"';
		}
		$sql_custsearch = "SELECT * FROM customer, custsex WHERE $where AND customer.custsex_id = custsex.custsex_id ORDER BY customer.cust_id";
		$query_custsearch = mysql_query($sql_custsearch);
		checkSQL ($query_custsearch);
		
		//Make array for exporting data
		$cust_exp_date = date("Y-m-d",time());
		$_SESSION['cust_export'] = array();
		$_SESSION['cust_exp_title'] = 'customers-search_'.$cust_exp_date;
	}
	else header('Location: start.php');
?>
	
<html>
	<?PHP includeHead('Customer Search Result',1) ?>	
	<body>
		<!-- MENU -->
		<?PHP includeMenu(2); ?>
		<div id="menu_main">
			<a href="cust_search.php" id="item_selected">Search</a>
			<a href="cust_new.php">New Client</a>
			<a href="cust_act.php">Active Client</a>
			<a href="cust_inact.php">Inactive Client</a>
		</div>
		
		<!-- CONTENT: Search Results -->
		<div id="content_center">
			
			<table id="tb_table">				
				<colgroup>
					<col width="10%">
					<col width="20%">
					<col width="10%">
					<col width="10%">
					<col width="20%">
					<col width="20%">
					<col width="10%">
				</colgroup>
				<tr>
					<form class="export" action="cust_export.php" method="post">
						<th class="title" colspan="7"><?PHP echo $title; ?>
						<!-- Export Button -->
						<input type="submit" name="export_cust" value="Export" />
						</th>
					</form>
				</tr>
				<tr>
					<th>Cust. No.</th>
					<th>Name</th>					
					<th>DoB</th> 
					<th>Gender</th> 
					<th>Occupation</th>
					<th>Address</th>
					<th>Phone No.</th>
				</tr>
				<?PHP		
				$color = 0;
				while ($row_custsearch = mysql_fetch_assoc($query_custsearch)){					
					//Alternating row colors
					tr_colored($color);
					echo '<td><a href="customer.php?cust='.$row_custsearch['cust_id'].'">'.$row_custsearch['cust_no'].'</a></td>
								<td>'.$row_custsearch['cust_name'].'</td>
								<td>'.date("d.m.Y",$row_custsearch['cust_dob']).'</td>
								<td>'.$row_custsearch['custsex_name'].'</td>
								<td>'.$row_custsearch['cust_occup'].'</td>
								<td>'.$row_custsearch['cust_address'].'</td>
								<td>'.$row_custsearch['cust_phone'].'</td>
							</tr>';
					
					//Prepare data for export to Excel file
					array_push($_SESSION['cust_export'], array("Cust No." => $row_custsearch['cust_no'], "Name" => $row_custsearch['cust_name'], "DoB" => date("d.m.Y", $row_custsearch['cust_dob']), "Gender" => $row_custsearch['custsex_name'], "Occupation" => $row_custsearch['cust_occup'], "Address" => $row_custsearch['cust_address'], "Phone No." => $row_custsearch['cust_phone']));
				}
				?>
			</table>
		</div>
	</body>
</html>