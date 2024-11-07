<?php
	// DEBUG
	error_reporting(E_ALL ^ E_NOTICE);
 	ini_set("display_errors", 1);

	session_start();
	date_default_timezone_set("Europe/Amsterdam");

	//Load Composer's autoloader
	require __DIR__.'/vendor/autoload.php';
	require_once('inc/class/class.user.php');

	$um = new UserManager();

	//Check if user logged in
	$user_loggedin = $um->checkLogin($_POST);
	if(!$user_loggedin || is_string($user_loggedin)){
		require_once('pages/loginform.php');
		exit;	
	}
	
	$user = $um->getUserByName($_SESSION['username']);	
	
	// Security on ip base
	// Function to get the client ip address
	function get_client_ip() {
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
	
	function curPageURL() {
	 $pageURL = 'http';
	 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	 $pageURL .= "://";
	 if ($_SERVER["SERVER_PORT"] != "80") {
	  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 } else {
	  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	 }
	 return $pageURL;
	}

	$ip = trim(get_client_ip());
	
	// Read ip addr file
	$handle = null;
	$ipsFileName = 'allowed_ips.txt';

	if (file_exists($ipsFileName)) {
		$handle = @fopen('allowed_ips.txt', "r"); 
	}
	if ($handle) { 
		while (!feof($handle)) { 
			$lines[] = trim(fgets($handle, 4096)); 
		} 
		fclose($handle); 
	} 

	// Check what page to load.
	$page = 'dashboard';
	if (isset($_GET['page'])){
		$page = trim($_GET['page']);
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8">
	<title>Vanda Carpets - Process Management V2</title>

	<!-- Load Stylesheets !-->
	<link rel="stylesheet" href="inc/style/style.css" type="text/css" />
	<link rel="stylesheet" href="inc/style/menu.css" type="text/css" />
	<link rel="stylesheet" href="inc/style/small.css" type="text/css" />

	<!-- jquery ui theme css laden !-->
	<link rel="stylesheet" href="inc/style/jquery-ui.min.css" type="text/css" />
	<link rel="stylesheet" href="inc/style/jquery-ui.structure.min.css" type="text/css" />
	<link rel="stylesheet" href="inc/style/jquery-ui.theme.min.css" type="text/css" />

	<!-- Load jQuery scripts and functions !-->
	<script src="inc/script/jquery.js" language="javascript" type="text/javascript"></script>
	<script src="inc/script/jquery-ui.min.js" language="javascript" type="text/javascript"></script>
	
	<!-- Load Calendar Scripts !-->
	<script src="inc/script/calendar/index.global.js"></script>


	<!-- Load Vanda Scripts !-->
	<script language="javascript" type="text/javascript" src="inc/script/functions.js"></script>
	<?php
	   if (file_exists('inc/script/'.$page.'.js')) {
		echo '<script language="javascript" type="text/javascript" src="inc/script/'.$page.'.js"></script>'; 
	   }
	?>
</head>
<body>
	<div id="wrapper">
	    <div id="header">
	        <div id="logo"><img src="images/logo.jpg" /></div>
	        <div id="topmenu">
	        	<?php require_once("pages/topmenu.php"); ?>
	        </div>
	    </div>
	    <div id="content-wrapper">
	    	<div id="errorbox"></div>
	        <div id="content">
				<?php
				   if (file_exists('pages/'.$page.'.php')) {
				   	    include('pages/'.$page.'.php'); 
				   } else {
				        include('pages/404.php'); 
				   }
	            ?>
	        </div>
	    </div>
	    <div class="clr"></div>
	</div>
	<div id="version">
		<a href="changelog.txt">
		Vanda Management v 2.0 Development - generated on: <?php echo date('H:i:s'); ?>
		</a>
	</div>
</body>
</html>
