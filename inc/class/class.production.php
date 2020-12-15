<?php
require_once("class.db.php");

class ProductionManager {
	var $db;

	function ProductionManager() {
		$this->db = new DB();
	}

	function addProduction($data) {
		return $this->db->insertQuery("vanda_production", $data);
	}

	function editProduction($data, $id){
		return $this->db->updateQuery("vanda_production", $data, "id = ". $id);
	}

	function deleteProduction($id){
		$data = array("removed" => "1");

		return $this->db->updateQuery("vanda_production", $data, "id=".(int)$id);
	}

	function unshipArticle($id){
		$data = array(
			"shipping_id" => "0",
			"geleverd" => "0000-00-00 00:00:00"
		);

		return $this->db->updateQuery("vanda_production", $data, "id=".(int)$id);
	}

	function getNewBarcode() {
		$qry = "SELECT barcode FROM vanda_production ORDER BY barcode DESC LIMIT 1";
		
		$res = $this->db->selectQuery($qry);

		return $res[0]->barcode;
	}

	function getBarcode() {
		$qry = "SELECT barcode FROM vanda_production WHERE shipping_id = 0 ORDER BY barcode ASC";
		
		$res = $this->db->selectQuery($qry);

		return $res[0];
	}
}

?>