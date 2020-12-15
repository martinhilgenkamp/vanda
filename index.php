<?php
	// DEBUG
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');
	date_default_timezone_set('Europe/Amsterdam');
	
	// Setup DB connection
	require_once("class/class.mysql.php");

	session_start();

	// check if user is logged in
	// Login functions
	//require('inc/loginfunctions.php');
	//require_once('class/class.mysql.php');

	//Check if user logged in
	//$user_loggedin =	check_login();
	//  if(!$user_loggedin || is_string($user_loggedin)){
	//	require_once('inc/loginform.php');
	//	exit;	
	//}


	// Begin output page
	 // Set newline var
	$nl = "\n";

	// Check what page to load.
	$page = trim($_GET['page']);
	if($page == ''){
		$page = 'dashboard';	
	}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Vanda Carpets - Process Management V2</title>

<!-- Load Stylesheets !-->
<link rel="stylesheet" href="css/style.css" type="text/css" />
<link rel="stylesheet" href="css/menu.css" type="text/css" />
<link rel="stylesheet" href="css/small.css" type="text/css" />

<!-- jquery ui theme css laden !-->
<link rel="stylesheet" href="css/jquery-ui.min.css" type="text/css" />
<link rel="stylesheet" href="css/jquery-ui.structure.min.css" type="text/css" />
<link rel="stylesheet" href="css/jquery-ui.theme.min.css" type="text/css" />

<!-- Load jQuery scripts and functions !-->
<script src="js/jquery.js" language="javascript" type="text/javascript"></script>
<script src="js/jquery-ui.min.js" language="javascript" type="text/javascript"></script>


<!-- Load Vanda Scripts !-->
<script language="javascript" type="text/javascript" src="js/functions.js"></script>
<?php
   if (file_exists('js/'.$page.'.js')) {
	echo '<script language="javascript" type="text/javascript" src="js/'.$page.'.js"></script>'; 
   }
?>
</head>
<body>
<div id="wrapper">
    <div id="header">
        <div id="logo"><img src="images/logo.jpg" /></div>
        <div id="topmenu">
        	<?php require_once("topmenu.php"); ?>
        </div>
    </div>
    <div id="content-wrapper">
    	<div id="errorbox"></div>
        <div id="content">
			<?php
			   if (file_exists('pages/'.$page.'.php')) {
			   	require_once('pages/'.$page.'.php'); 
			   } else {
				   echo '<div class="ui-state-error ui-corner-all" style="padding: 0 1em; font-size: 1.2em">
							<p>
								<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em; top: .4em"></span>
								<strong>404</strong> De gevraagde pagina bestaat niet.</p>
						</div>';
			   }
            ?>
        </div>
    </div>
    <div class="clr"></div>
</div>
<div id="version">
<a href="changelog.txt">
Vanda Management v 2.0 - generated on: <?php echo date('H:i:s'); ?>
</a>
</div>
<div id=".debug">
	
	<?php
	// Debugging
	print_r($_SESSION);
	echo "<hr>";
	print_r($_POST);
	?>
</div>
</body>
</html>
