<!DOCTYPE html>
 <head>
<title>Vanda Carpets Login</title>
<link rel="stylesheet" type="text/css" href="inc/style/style.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
</head>
<body>
<!-- Form for logging in the users -->
<h1>Login</h1>
<div class="register-form">
<?php
	if(isset($msg) & !empty($msg)){
		echo $msg;
	}
 ?>

<form action="index.php" method="POST">
    <p><label>Gebruikersnaam : </label>
	<input id="username" type="text" name="username" placeholder="username"/></p>
 
     <p><label>Wachtwoord&nbsp;&nbsp; : </label>
	 <input id="password" type="password" name="password" placeholder="password" /></p>
    
    <input class="btn register" type="submit" name="submit" value="Login" />
    </form>
</div>
<div class="error_message">
<?php 
if(is_string($user_loggedin)){
	echo $user_loggedin;	
}
?>
</div>
</body>
</html>