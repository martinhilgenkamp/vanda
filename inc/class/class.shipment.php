<?php
require_once("class.db.php");

class ShipmentManager {
	var $db;

	function ShipmentManager() {
		$this->db = new DB();
	}

	function addShipment($data) {
		return $this->db->insertQuery("vanda_shipment", $data);
	}

	function editShipment($data, $id){
		return $this->db->updateQuery("vanda_shipment", $data, "id = ".(int)$id);
	}

	function updateShipment($ship, $id) {
		$data = array("verzonden" => $ship);

		return $this->db->updateQuery("vanda_shipment", $data, "id=".(int)$id);
	}

	function getShipmentId() {
		$qry = "SELECT ship_id FROM vanda_shipment";
		
		$res = $this->db->selectQuery($qry);

		return $res[0];
	}
}

?>