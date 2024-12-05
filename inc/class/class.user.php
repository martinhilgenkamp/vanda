<?php
require_once("class.db.php");

class UserManager {
	private $db;
    private $table_name = "vanda_user";
    private $columns = "id, username, email, level, active, password, isresource";

	function __construct($username = null) {
		$this->db = new DB();
	}

	function checkLogin($data){ 
			if (isset($data['username']) and isset($data['password'])){
			$username = $data['username'];
			$password = $data['password'];
			$user = $this->getUserByName($username);

			if ($user && $this->checkCredentials($user, $password)){
				$_SESSION['username'] = $username;
				setcookie('username',$username,time()+30*24*60*60);
			}else{
				return "Ongeldige gebruikersnaam of wachtwoord.";
			}
		}
		
		if (isset($_COOKIE['username'])){
			$user = $this->getUserByName($_COOKIE['username']);
			if ($user){
				$_SESSION['username'] = $_COOKIE['username'];				
				setcookie('username',$_COOKIE['username'],time()+30*24*60*60);
				return true;
			}else{
				return "Ongeldige gebruikersnaam of wachtwoord.";
			}
		}
		
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

		// Check if password needs hashing..
		if (password_needs_rehash($user->password, PASSWORD_DEFAULT))
		{
			$this->updateHash($user->password, $user->id);
		}

		return true;
	}

	// Fetch a user by username
    function getUserByName($username) {
		$qry = "SELECT {$this->columns} FROM {$this->table_name} WHERE username = ?";
		$stmt = $this->db->link->prepare($qry);
		if ($stmt) {
			$stmt->bind_param("s", $username);
			$stmt->execute();
			$result = $stmt->get_result();
			return $result->num_rows > 0 ? (object) $result->fetch_assoc() : null;
		}
		return null;
	}

	// Fetch a user by ID
    function getUserById($id) {
        $qry = "SELECT {$this->columns} FROM {$this->table_name} WHERE id = ?";
        $stmt = $this->db->link->prepare($qry);
        
        if ($stmt) {
            $stmt->bind_param("i", $id); // Bind the parameter as an integer
            $stmt->execute(); // Execute the prepared statement
            $result = $stmt->get_result(); // Get the result set
            
            // Return the first row as an object if a result exists, otherwise return null
            return $result->num_rows > 0 ? $result->fetch_object() : null;
        }
        
        return null; // Return null if the statement could not be prepared
    }
    
	//Change password of a user
	// Change user password
    function changePassword($username, $password, $currentPassword, &$errors) {
        $user = $this->getUserByName($username);
        if (!$user || !$this->checkCredentials($user, $currentPassword)) {
            $errors[] = "Current password does not match.";
            return false;
        }
        return $this->editUser(['password' => password_hash($password, PASSWORD_DEFAULT)], $user['id']);
    }

    // List all active users with optional filtering
    function listUsers($filters = []) {
        $qry = "SELECT {$this->columns} FROM {$this->table_name} WHERE active = 1";
    
        // Apply optional filters
        if (isset($filters['isresource'])) {
            $qry .= " AND isresource = " . (int)$filters['isresource'];
        }
        // Apply ordering
        if (isset($filters['orderBy'])) {
            $qry .= " ORDER BY " . $filters['orderBy'];
        }
    
        $result = $this->db->link->query($qry);
    
        if ($result) {
            $users = [];
            while ($row = $result->fetch_object()) {
                $row->username = ucfirst($row->username);
                $users[] = $row; // Add each object to the array
            }
            return $users;
        }
    
        return []; // Return an empty array if no results
    }
    

    // Add a new user
    function addUser($data) {
        $qry = "INSERT INTO {$this->table_name} (username, email, password, level, active, isresource) 
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->link->prepare($qry);
        if ($stmt) {
            $stmt->bind_param(
                "sssiii",
                $data['username'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['level'],
                $data['active'],
                $data['isresource']
            );
            return $stmt->execute();
        }
        return false;
    }


     // Edit an existing user
	 function editUser($data, $id) {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }

        $qry = "UPDATE {$this->table_name} SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->db->link->prepare($qry);
        if ($stmt) {
            $types = str_repeat("s", count($values)) . "i";
            $values[] = $id; // Add ID for the WHERE clause
            $stmt->bind_param($types, ...$values);
            return $stmt->execute();
        }
        return false;
    }

    // Fetch users by id and username where isresource is set, and return as JSON
    function getResources() {
        $qry = "SELECT id, level, username as title FROM {$this->table_name} WHERE isresource = 1";
        $result = $this->db->link->query($qry);

        if ($result) {
            $users = [];
            while ($row = $result->fetch_object()) {
                //Determine the prefix based on the level
                $prefix = $row->level == 1 ? 'Beheerder' : ($row->level == 2 ? 'Machine' : 'Medewerker');
                
                // Add the prefix to the title
                $row->title = "{$prefix}-{$row->title}";
                unset($row->level);
                $users[] = $row; // Add each user object to the array
            }
            // Return the JSON-encoded result
            return json_encode($users, JSON_PRETTY_PRINT);
        } else {
            throw new Exception("Error fetching users: " . $this->db->link->error);
        }

        return json_encode([]); // Return an empty JSON array if no results
    }

     // Soft delete a user
    function deleteUser($id) {
        return $this->editUser(['active' => 0], $id);
    }

    
}
?>