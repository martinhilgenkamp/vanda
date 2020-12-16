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

	function getOptionById($id) {
		$qry = "SELECT * FROM vanda_options WHERE id = '".$id."'";
		
		$res = $this->db->selectQuery($qry);

		return $res[0];
	}
}

?>