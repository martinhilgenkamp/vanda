<!DOCTYPE html>
 <head>
<title>Vanda Carpets Login</title>
<link rel="stylesheet" type="text/css" href="inc/style/style.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
</head>
<body>
<!-- Form for logging in the users -->

<div class="register-form centered">
<?php
	if(isset($msg) & !empty($msg)){
		echo $msg;
	}
 ?>

	<form action="index.php" method="POST">
		<div class="login"> 
			<div class="login-header">
				<h1>Login</h1>
			</div>
			<div class="login-body">
				<div class="labels">
					<label>Gebruikersnaam:</label>
					<label>Wachtwoord:</label>
				</div>
				<div class="inputfields">
					<input id="username" type="text" name="username" placeholder="gebruikersnaam"/>
					<input id="password" type="password" name="password" placeholder="wachtwoord" />
				</div>
			</div>
			<div class="login-footer">
				<input class="btn register" type="submit" name="submit" value="Login" />
			</div>
			<div class="error_message">
			<?php 
				if(is_string($user_loggedin)){
					echo $user_loggedin;	
				}
			?>
			</div>
		</div>
	</form>
</div>

</body>
</html>