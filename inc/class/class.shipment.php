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

	function countShipment($shipid){
		$qry = "SELECT COUNT(*) FROM `vanda_production` WHERE shipping_id = " . $shipid;
		$res = $this->db->selectQuery($qry);
		return $res;
	}

	function getAllShipments($days = '') {
		$dateDaysAgo = date('Y-m-d', strtotime('-'.$days.' days'));
		if($days) {
			// Old query in case of emergency

			//$qry = "SELECT * FROM  vanda_shipment WHERE datum >= '" . $dateDaysAgo . "' ORDER BY datum DESC";
			$qry = "SELECT vanda_shipment.*, 
				(SELECT COUNT(*) FROM `vanda_production` 
				WHERE vanda_production.shipping_id = vanda_shipment.ship_id) as shipment_count
			FROM vanda_shipment 
			WHERE vanda_shipment.datum >= '".$dateDaysAgo."' 
			ORDER BY vanda_shipment.datum DESC;";

		} else {
			$qry = "SELECT vanda_shipment.*, 
				(SELECT COUNT(*) FROM `vanda_production` 
				WHERE vanda_production.shipping_id = vanda_shipment.ship_id) as shipment_count
			FROM vanda_shipment  
			ORDER BY vanda_shipment.datum DESC;";
		}
		return $this->db->selectQuery($qry);
	}

	function getAllShippedShipments() {
		$qry = "SELECT * FROM  vanda_shipment WHERE verzonden != 1 GROUP BY ship_id ASC";
		
		return $this->db->selectQuery($qry);
	}

	function getAllUnShippedShipments() {
		$qry = "SELECT ship_id as id, klant,(SELECT COUNT(*) FROM `vanda_production` WHERE vanda_production.shipping_id = vanda_shipment.ship_id) as Colli ,DATE_FORMAT(datum,'%d-%m-%Y') as datum FROM  vanda_shipment WHERE verzonden != 1 GROUP BY datum DESC";
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