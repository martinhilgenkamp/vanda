<?php
require_once("class.db.php");

class UserManager {
	private $db;

	public $username;
	public $name;
	public $email;
	public $id;
	public $active;
	public $level;

	function __construct($username = null) {
		$this->db = new DB();
		if($username){
			$qry = "SELECT * FROM vanda_user WHERE username = '".$username."' LIMIT 1";
			if ($result = $this->db->link->query($qry)) {
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$this->id = $row['id'];
						$this->name = $row['username'];
						$this->email = $row['email'];
						$this->level = $row['level'];
						$this->active = $row['active'];
						$this->username = $row['username'];
					}
				}
			}
		}
	}

	function checkLogin($data){ 
			if (isset($data['username']) and isset($data['password'])){
			//3.1.1 Assigning posted values to variables.
			$username = $data['username'];
			$password = $data['password'];
			
			//3.1.2 Checking the values are existing in the database or not
			$this->username = $this->getUserByName($username);

			//3.1.2 If the posted values are equal to the database values, then session will be created for the user.
			if ($this->name && $this->checkCredentials($this->name, $password)){
				$_SESSION['username'] = $username;
				setcookie('username',$_SESSION['username'],time()+30*24*60*60);
			}else{
				//3.1.3 If the login credentials doesn't match, he will be shown with an error message.
				return "Ongeldige gebruikersnaam of wachtwoord.";
			}
		}
		
		//Check if cookie is there.		
		if (isset($_COOKIE['username'])){
			$this->name = $this->getUserByName($_COOKIE['username']);
			if ($this->name){
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

	function updateHash($password, $userId) {
		$hash = password_hash($password, PASSWORD_DEFAULT);
		
		/* Update the password hash on the database. */
		$data = ["password" => $hash];
		$where = 'id = '.$userId;
		$this->db->updateQuery('vanda_user', $data, $where);
	}

	function checkCredentials($user, $password) {
		if (!password_verify($password, $user->password)) {
			
			if ($password == $user->password) {
				$this->updateHash($user->password, $user->id);
				return true;
			}
			return false;
		}

		if (password_needs_rehash($user->password, PASSWORD_DEFAULT))
		{
			$this->updateHash($user->password, $user->id);
		}

		return true;
	}

	function getUser($username, $password) {
		$qry = "SELECT * FROM `vanda_user` WHERE username='".$username."' and password='".$password."'";
		
		$res = $this->db->selectQuery($qry);

		print_r($res);

		return count($res) > 0 ? $res[0] : null;
	}

	function getUserByName($username) {
		$qry = "SELECT * FROM vanda_user WHERE username = '".$username."'";
		$res = $this->db->selectQuery($qry);
		return count($res) > 0 ? $res[0] : null;
	}	

	function changePassword($username, $password, $currentPassword, &$errors) {
		$user = $this->getUserByName($username);
		if (!$this->checkCredentials($user, $currentPassword)) {
			$errors[] = "Oude wachtwoord komt niet overeen";
			return false;
		}

		$where = "username = '".$username."'";
		$data = ["password" => password_hash($password, PASSWORD_DEFAULT)];
		$this->db->updateQuery('vanda_user', $data, $where);

		return true;
	}

    function listUsers() {
        $qry = "SELECT * FROM `vanda_user` WHERE active = 1";
        
        return c($qry);
    }

    function getUserById($id) {
        $qry = "SELECT * FROM vanda_user WHERE id = ".$id;
        
        $res = $this->db->selectQuery($qry);

        return count($res) > 0 ? $res[0] : null;
    }

    function addUser($data) {
        return $this->db->insertQuery("vanda_user", $data);
    }

    function editUser($data, $id) {
        $where = "id = ".$id;

        return $this->db->updateQuery('vanda_user', $data, $where);
    }

    function deleteUser($id) {
        $where = "id = ".$id;
        $data["active"] = 0;

        return $this->db->updateQuery('vanda_user', $data, $where);
    }
}
?>