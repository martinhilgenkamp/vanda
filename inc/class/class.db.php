<?php

class DB {

	var $host;
	var $database;
	var $user;
	var $password;
	var $link;

	var $selectResult;
	var $updateResult;
	var $deleteResult;
	var $insertResult;

	var $transactionResult;

 	function DB() {
		$this->host = "localhost";
       	$this->database = "vanda";
       	$this->user = "root";
       	$this->password = "";

		$this->link = new mysqli($this->host, $this->user, $this->password, $this->database);
   	}

	function selectQuery($query) {
		$this->selectResult = $this->link->query($query) or die("Ongeldige query: " . mysqli_error($this->link) . "<br>" . $query);

		$resArray = Array();
		if($this->selectResult){
			while($record = $this->selectResult->fetch_object()){
				$resArray[] = $record;
			}
		}

		return $resArray;
	}

	function updateQuery($table, $data, $where) {
		$strUpdate = '';

		$arrFields = $this->listOfFields($table);

		foreach($arrFields as $field) {
			if(isset($data[$field->Field])) {
				$strInsert .= $field->Field.", ";
				$strValues .= "'".$data[$field->Field]."', ";
			}
		}

		foreach($arrFields as $field) {
			if(isset($data[$field->Field])) {
				if($data[$field->Field] == "NULL"){
					$strUpdate .= $field->Field."=".addslashes($data[$field->Field]).", ";
				} else {
					$strUpdate .= $field->Field."='".addslashes($data[$field->Field])."', ";
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
			if(isset($data[$field->Field])) {
				$strInsert .= $field->Field.", ";
				$strValues .= "'".$data[$field->Field]."', ";
			}
		}

		if($strInsert != '') {
			$strInsert = substr($strInsert, 0, -2);
			$strValues = substr($strValues, 0, -2);

			$qry = "INSERT INTO ".$table." (".$strInsert.") VALUES (".$strValues.")";

			$this->insertResult = $this->link->query($qry);

			if (!$this->insertResult){
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