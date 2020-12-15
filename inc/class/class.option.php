<?php
require_once("class.db.php");

class OptionManager {
	var $db;

	function OptionManager() {
		$this->db = new DB();
	}

	function getAllOptions() {
		$qry = "SELECT * FROM vanda_options";
		
		return $this->db->selectQuery($qry);
	}
}

?>