<?php
require_once("class.db.php");

class ShipmentManager {
	var $db;

	function __construct() {
		$this->db = new DB();
	}

	function addShipment($data) {
		return $this->db->insertQuery("vanda_shipment", $data);
	}

	function editShipment($data, $id){
		return $this->db->updateQuery("vanda_shipment", $data, "ship_id = ".(int)$id);
	}

	function updateShipment($ship, $id) {
		$data = array("verzonden" => $ship);

		return $this->db->updateQuery("vanda_shipment", $data, "ship_id = ".(int)$id);
	}

	function getShipmentId() {
		$qry = "SELECT ship_id FROM vanda_shipment";
		
		$res = $this->db->selectQuery($qry);

		$shipmentIds = [];
		foreach ($res as $shipment) {
			$shipmentIds[] = $shipment->ship_id;
		}

		return $shipmentIds;
	}

	function getAllShipments() {
		$qry = "SELECT * FROM  vanda_shipment ORDER BY datum DESC";
		
		return $this->db->selectQuery($qry);
	}

	function getAllShippedShipments() {
		$qry = "SELECT * FROM  vanda_shipment WHERE verzonden != 1 GROUP BY ship_id ASC";
		
		return $this->db->selectQuery($qry);
	}

	function getAllUnShippedShipments() {
		$qry = "SELECT ship_id as id, klant, DATE_FORMAT(datum,'%d-%m-%Y') as datum FROM  vanda_shipment WHERE verzonden != 1 GROUP BY ship_id ASC";

		return $this->db->selectQuery($qry);
	}

	function getExistingShipmentsById($id) {
		$qry = "SELECT * FROM vanda_production WHERE shipping_id = '".$id."' AND removed = '0'";

		return $this->db->selectQuery($qry); 
	}

	function GetShipment($id){
		$qry = "SELECT * FROM vanda_shipment WHERE ship_id = '".$id."'";
		return $this->db->selectObject($qry);
	}
}

?>