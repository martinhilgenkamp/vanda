<?php

//Let users login.
	function check_login(){ 

		if (isset($_POST['username']) and isset($_POST['password'])){
			//3.1.1 Assigning posted values to variables.
			$username = mysqli_real_escape_string($_POST['username']);
			$password = mysqli_real_escape_string($_POST['password']);
			$hash = password_hash($password, PASSWORD_DEFAULT);
			// Do the check
			checkCredentials($username,$password,$hash);
		}
		
		//Check if cookie is there.		
		if ($_COOKIE['username']){
			// Do the check
			$hash = password_hash($password, PASSWORD_DEFAULT);
			checkCredentials($username,$password, $hash);
		}
		
		//3.1.4 if the user is logged in Greets the user with message
		if (isset($_SESSION['username'])){
			$username = $_SESSION['username'];
			setcookie('username',$username,time()+30*24*60*60);
			return true;
		}else{
			return false;
		}
	}

	function checkCredentials($username, $password){
		//3.1.2 Checking the values are existing in the database or not
			$query = "SELECT * FROM `vanda_users` WHERE username='".strtolower($username)."' and password='$password'";
			$result = $db->query($query) or die(mysql_error());
			$count = $result->num_rows;
			$user - $result->fetch_object();
			$hash = password_verify($user->password);
		
			if ( password_needs_rehash ( $hash, PASSWORD_DEFAULT ) ) {
			 $newHash = password_hash( $usersPassword, PASSWORD_DEFAULT );
				/* UPDATE the user's row in `log_user` to store $newHash */
				$query = "UPDATE `vanda_users` SET `password` = '".$newHash."' WHERE `vanda_users`.`id` = ".$result->id.";";
   			}
		
			//3.1.2 If the posted values are equal to the database values, then session will be created for the user.
			if ($count == 1){
				$_SESSION['username'] = $username;
				$_SESSION['password'] = $password;
				setcookie('username',$_SESSION['username'],time()+30*24*60*60);
			}else{
				//3.1.3 If the login credentials doesn't match, he will be shown with an error message.
				return "Ongeldige gebruikersnaam of wachtwoord.";
			}
	}	
?>