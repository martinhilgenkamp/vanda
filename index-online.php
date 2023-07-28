<?php
	// DEBUG
	error_reporting(E_ALL ^ E_NOTICE);
 	ini_set("display_errors", 1);
	
	date_default_timezone_set('Europe/Amsterdam');
	
	session_start();

	// Login functions
	require('pages/loginfunctions.php');
	require_once('class/class.mysql.php');

	
	//Check if user logged in
	$user_loggedin =	check_login();
	  if(!$user_loggedin || is_string($user_loggedin)){
		require_once('pages/loginform.php');
		exit;	
	}
	
	$user = getUser($_SESSION['username']);	
	
	// Security on ip base
	// Function to get the client ip address
	function get_client_ip() {
		$ipaddress = '';
		if ($_SERVER['HTTP_CLIENT_IP'])
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if($_SERVER['HTTP_X_FORWARDED_FOR'])
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if($_SERVER['HTTP_X_FORWARDED'])
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if($_SERVER['HTTP_FORWARDED_FOR'])
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if($_SERVER['HTTP_FORWARDED'])
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if($_SERVER['REMOTE_ADDR'])
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
	$handle = @fopen('allowed_ips.txt', "r"); 
	 if ($handle) { 
		while (!feof($handle)) { 
			$lines[] = trim(fgets($handle, 4096)); 
		} 
		fclose($handle); 
	 } 
	 
	 // Set newline var
	$nl = "\n";

	// Check what page to load.
	$page = trim($_GET['page']);
	if($page == ''){
		$page = 'dashboard';	
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
<meta http-equiv="Cache-Control" content="no-store" />
<title>Vanda Carpets - Process Management</title>

<!-- Load Stylesheets !-->
<link rel="stylesheet" href="js/jquery_ui/css/smoothness/jquery-ui-1.10.1.custom.css" type="text/css" />
<link rel="stylesheet" href="css/style.css" type="text/css" />
<link rel="stylesheet" href="css/menu.css" type="text/css" />
<link rel="stylesheet" href="css/small.css" type="text/css" />


<!-- Load Scripts !-->
<script src="js/jquery.js" language="javascript" type="text/javascript"></script>
<script src="js/jquery_ui/js/jquery-ui-1.10.1.custom.js" language="javascript" type="text/javascript"></script>

<!-- Fucntionality Script !-->
<script language="javascript" type="text/javascript" src="js/functions.js"></script>
<script language="javascript" type="text/javascript">
$(document).ready(function(){
	
		//prevent form submission on enter for heftruck
			$(window).keydown(function(event){
			if(event.keyCode == 13) {
			  event.preventDefault();
			  return false;
			}
		  });	
});
</script>
<?php
   if (file_exists('js/'.$page.'.js')) {
	echo '<script language="javascript" type="text/javascript" src="js/'.$page.'.js"></script>'; 
   }
?>

<link rel="apple-touch-icon" sizes="57x57" href="images/icon/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="images/icon/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="images/icon/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="images/icon/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="images/icon/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="images/icon/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="images/icon/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="images/icon/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="images/icon/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="images/icon/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="images/icon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="images/icon/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="images/icon/favicon-16x16.png">
<link rel="manifest" href="images/icon/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="images/icon/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">

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
               require_once('pages/'.$page.'.php'); 
            ?>
        </div>
    </div>
    <div class="clr"></div>
</div>
<div id="version">
<a href="changelog.txt">
Vanda Management v 2.0 Master  - generated on: <?php echo date('H:i:s'); ?>
</a>
</div>
</body>
</html>

