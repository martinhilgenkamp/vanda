<?php

class DB {

	var $host;
	var $database;
	var $user;
	var $password;
	var $link;

	var $updateResult;
	var $deleteResult;
	var $insertResult;

	var $transactionResult;

 	function __construct() {
		
		$this->host = "localhost";
       	$this->database = "vanda";
       	$this->user = "vanda";
       	
		//$this->password = "HhDDNj3z";
		$this->password = "Hwq^1Gbb5nOkuXwj";

		$this->link = new mysqli($this->host, $this->user, $this->password, $this->database);
   	}

	function selectQuery($query) {
		$selectResult = $this->link->query($query) or die("Ongeldige query: " . $query);

		$resArray = Array();
		if($selectResult){
			while($record = $selectResult->fetch_object()){
				$resArray[] = $record;
			}
		}
		return $resArray;
	}

	function selectObject($query) {
		$selectResult = $this->link->query($query) or die("Ongeldige query: " . $query);
		$resObj = $selectResult->fetch_object();
		
		return $resObj;
	}

	function updateQuery($table, $data, $where) {
		$strInsert = '';
		$strValues = '';
		$strUpdate = '';

		$arrFields = $this->listOfFields($table);

		foreach($arrFields as $field) {
			if(isset($data[$field->Field])) {
				$strInsert .= "`".$field->Field."`, ";
				$strValues .= "'".$data[$field->Field]."', ";
			}
		}

		foreach($arrFields as $field) {
			if(isset($data[$field->Field])) {
				if($data[$field->Field] == "NULL"){
					$strUpdate .= $field->Field."=".$this->sanitizeString(($data[$field->Field])).", ";
				} else {
					$strUpdate .= $field->Field."='".$this->sanitizeString(($data[$field->Field]))."', ";
				}
			}
		}

		if($strUpdate != '') {
			$strUpdate = substr($strUpdate, 0, -2);

			$qry = "UPDATE ".$table." SET ".$strUpdate." WHERE ".$where;
	
			$this->updateResult = $this->link->query($qry);

			if (!$this->updateResult){
				$this->transactionResult = false;
			}
			return true;
		}

		return false;
	}

	function sanitizeString($value) {
		return mysqli_real_escape_string($this->link, strval($value));
	}

	function deleteQuery($table, $where) {
		$qry = "DELETE FROM ".$table." WHERE ".$where;
		
		$this->deleteResult = $this->link->query($qry);

		return $this->deleteResult;
	}

	function insertQuery($table, $data) {
		$strInsert = '';
		$strValues = '';

		$arrFields = $this->listOfFields($table);
		foreach($arrFields as $field) {
			if(array_key_exists($field->Field, $data)) {
				$strInsert .= "`".$field->Field."`, ";
				$strValues .= "'".$this->sanitizeString($data[$field->Field])."', ";
			}
		}

		if($strInsert != '') {
			$strInsert = substr($strInsert, 0, -2);
			$strValues = substr($strValues, 0, -2);

			$qry = "INSERT INTO ".$table." (".$strInsert.") VALUES (".$strValues.")";


			$this->insertResult = $this->link->query($qry);

			if (!$this->insertResult){
				$errorMessage = mysqli_error($this->link);
				return $errorMessage; // Return the error message
				$this->transactionResult = false;
			}
			return $this->link->insert_id;
		}

		return 0;
	}

	function listOfFields($table){
		return $this->selectQuery("SHOW COLUMNS FROM ".$table);
	}
}
?>