<?php
require_once("class.db.php");

class OptionManager {
	var $db;

	function __construct() {
		$this->db = new DB();
	}

	function getAllOptions() {
		$qry = "SELECT * FROM vanda_options";
		
		return $this->db->selectQuery($qry);
	}

	function updateOptionRow($data) {
		return $this->db->updateQuery("vanda_options", $data, '1=1');
	}

	function getOptionById($id) {
		$qry = "SELECT * FROM vanda_options WHERE id = '".$id."'";
		
		$res = $this->db->selectQuery($qry);

		return $res[0];
	}
}

?>