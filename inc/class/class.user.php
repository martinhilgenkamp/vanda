<?php
require_once("class.db.php");

class UserManager {
	var $db;

	function UserManager() {
		$this->db = new DB();
	}

	function checkLogin($data){ 
	
		if (isset($data['username']) and isset($data['password'])){
			//3.1.1 Assigning posted values to variables.
			$username = $data['username'];
			$password = $data['password'];
			
			//3.1.2 Checking the values are existing in the database or not
			$user = $this->getUser($username, $password);
			 			
			//3.1.2 If the posted values are equal to the database values, then session will be created for the user.
			if ($user){
				$_SESSION['username'] = $username;
				setcookie('username',$_SESSION['username'],time()+30*24*60*60);
			}else{
				//3.1.3 If the login credentials doesn't match, he will be shown with an error message.
				return "Ongeldige gebruikersnaam of wachtwoord.";
			}
		}
		
		//Check if cookie is there.		
		if (isset($_COOKIE['username'])){
			$user = $this->getUserByName($_COOKIE['username']);
			if ($user){
				$_SESSION['username'] = $_COOKIE['username'];				
				setcookie('username',$_COOKIE['username'],time()+30*24*60*60);
				return true;
			}else{
				//3.1.3 If the login credentials doesn't match, he will be shown with an error message.
				return "Ongeldige gebruikersnaam of wachtwoord.";
			}
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

	function getUser($username, $password) {
		$qry = "SELECT * FROM `vanda_user` WHERE username='".$username."' and password='".$password."'";
		
		$res = $this->db->selectQuery($qry);

		return count($res) > 0 ? $res[0] : null;
	}

	function getUserByName($username) {
		$qry = "SELECT * FROM vanda_user WHERE username = '".$username."'";
		
		$res = $this->db->selectQuery($qry);

		return count($res) > 0 ? $res[0] : null;
	}	
}

?>