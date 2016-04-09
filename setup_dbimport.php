<!DOCTYPE HTML>
<?PHP
session_start();
require 'functions.php';

// Script Configuration
 $fileSQL = 'database/webafriqdigitalpatners.sql';
$progressFilename = $fileSQL.'_filepointer'; 	// Temporary file for progress
$errorFilename = $fileSQL.'_error'; 					// Temporary file for errors
$maxRuntime = 2;															// Should be less than script execution time limit
$deadline = time()+ $maxRuntime; 

// Database connection
$db_connect = mysql_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_pass']);
if(!$db_connect) die('Could not connect to host '.$_SESSION['db_host'].': '.mysql_showMessage());
$db_select = mysql_select_db($_SESSION['db_name']);
if(!$db_select) die('Could not select database '.$_SESSION['db_name'].': '.mysql_showMessage());

// Open import file
($fp = fopen($fileSQL, 'r')) OR die('Failed to open file:'.$fileSQL);

// Check for previous errors
if(file_exists($errorFilename) ){
  echo 'Previous error encountered: '.file_get_contents($errorFilename);
  //die'Previous error encountered: '.file_get_contents($errorFilename);
}

// Go to previous file position
$filePosition = 0;
if(file_exists($progressFilename) ){
	$filePosition = file_get_contents($progressFilename);
  fseek($fp, $filePosition);
}

// Execute SQL commands line by line
$queryCount = 0;
$query = '';
while(time() < $deadline AND ($line = fgets($fp, 1024000))){
    
	// Skipping comment lines
	if(substr($line,0,2)=='--' OR trim($line)=='' ){
    continue;
   }

	$query .= $line;
	if(substr(trim($query),-1)==';' ){
		if(!mysql_query($query) ){
			$error = 'Error performing query <strong>' . $query . ' : ' . mysql_showMessage();
			file_put_contents($errorFilename, $error."\n");
			exit;
		}
		$query = '';
		file_put_contents($progressFilename, ftell($fp)); // Save current file pointer position 
		$queryCount++;
	}
}

// Check if EoF was reached
if(feof($fp)){
	unlink($progressFilename);
	unlink($errorFilename);
	if ($_SESSION['db_type'] == 2) header ('Location:setup_makeconf.php');
	else header('Location:setup_admin.php');
}
else{
	$amount = ftell($fp).'/'.filesize($fileSQL);
	$percentage = (round(ftell($fp)/filesize($fileSQL), 2)*100);
}
?>

<html>
	<?PHP includeHead('Microfinance Management', 0) ?>	
		<meta http-equiv="refresh" content="<?PHP echo ($maxRuntime+1); ?>">
		<link rel="stylesheet" type="text/css" href="css/setup.css" />
	</head>
	<body>
		<div class="content_center">
			<img src="ico/mangoo_l.png" style="width:380px; margin-top:3em; margin-bottom:2em;"/>
			<p class="heading">mangoO Setup Assistant</p>
			<div class="setup">
				<p>Database Import</p>
				<div class="progress">
					<div class="progress back"></div>
					<div class="progress over" style="width:<?PHP echo $percentage;?>%;"></div>
				</div>
				<p id="message" style="position:relative; top:-33px;"><?PHP echo $percentage;?>%</p>
				<p id="message">Please wait for import to complete!<br/>Do not leave this page!</p>
			</div>
		</div>
	</body>
</html>