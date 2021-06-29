<?php
require_once("class.db.php");

class ProductionManager {
	var $db;

	function __construct() {
		$this->db = new DB();
	}

	function addProduction($data) {
		$defaultValues = [
			'removed' => 0,
			'kwaliteit' => "",
			'ordernr' => "",
			'shipping_id' => ""
		];

		foreach ($defaultValues as $key => $value) {
			if (!array_key_exists($key, $data)) {
				$data[$key] = $value;
			}
		}
		
		return $this->db->insertQuery("vanda_production", $data);
	}

	function editProduction($data, $id) {
		return $this->db->updateQuery("vanda_production", $data, "id = ". $id);
	}

	function deleteProduction($id) {
		$data = array("removed" => "1");

		return $this->db->updateQuery("vanda_production", $data, "id=".(int)$id);
	}

	function unshipArticle($id) {
		$data = array(
			"shipping_id" => "0",
			"geleverd" => "0000-00-00 00:00:00"
		);

		return $this->db->updateQuery("vanda_production", $data, "id=".(int)$id);
	}

	function getNewBarcode() {
		$qry = "SELECT barcode FROM vanda_production ORDER BY barcode DESC LIMIT 1";
		
		$res = $this->db->selectQuery($qry);
		$barcode = (int)substr($res[0]->barcode, -10) +1;

		return $barcode;
	}

	function getBarcode() {
		$qry = "SELECT barcode FROM vanda_production WHERE shipping_id = 0 ORDER BY barcode ASC";
		
		$res = $this->db->selectQuery($qry);

		$barcodes = [];
		foreach ($res as $barcode) {
			$barcodes[] = $barcode->barcode;
		}

		return $barcodes;
	}

	function getArticleNumbers() {
		$qry = "SELECT artikelnummer FROM vanda_production GROUP BY artikelnummer ASC";
		
		return $this->db->selectQuery($qry);
	}

	function getStockProductsQuery($startdate, $stopdate, $voorraadfilter, $productfilter, $order, $sort) {
		if($startdate && $stopdate){
			$where_array[] = "vanda_production.datum BETWEEN '".$startdate."' AND '".$stopdate." 23:59:59' "; 
			$title = "Aangepast Overzicht van ".$startdate.' tot '.$stopdate;
		} else {
		  	$time = strtotime(date("Y-m-d"));
		  	$final = date("Y-m-d", strtotime("-1 year", $time));
		  	$where_array[] = "vanda_production.datum BETWEEN '".$final."' AND '".date('Y-m-d',$time)." 23:59:59' ";
		}
		// Add voorraad filter to the where clause
		if($voorraadfilter == 'alles'){
			// do nothing
		} else if($voorraadfilter || !$voorraadfilter){
			$where_array[] = "vanda_production.shipping_id = 0 ";	
		}
		// Add product filter to the where clause
		if($productfilter){
			$where_array[] = "vanda_production.artikelnummer = '".$productfilter."' ";	
		}
		// Allways prullenbak removed items
		$where_array[] = "vanda_production.removed = '0' ";
		//Build the where clause

		// Variabelen tbv sorteren en paginatie
		$order = ($order == 'DESC' ? $order = 'ASC' : $order='DESC');
		$orderby= " ORDER BY ".($sort ? '`'.$sort.'` ' : '`datum` ').$order;
		$where = '';

		foreach($where_array as $part){
			if($part && $part != ''){
				$where .= ($where ? ' AND ' : ' WHERE ').$part;
			}
		}

		// Load products from the database.
		$qry = "SELECT * FROM  vanda_production ".$where.$orderby;

		return $qry;
	}

	function getStockProducts($startdate, $stopdate, $voorraadfilter, $productfilter, $order, $sort) {
		$qry = $this->getStockProductsQuery($startdate, $stopdate, $voorraadfilter, $productfilter, $order, $sort);
		
		return $this->db->selectQuery($qry);
	}

	function getProductByBarcode($barcode) {
		$qry = "SELECT * FROM vanda_production WHERE barcode = '".$barcode."' LIMIT 1";
		
		$res = $this->db->selectQuery($qry);

		return $res ? $res[0] : null;
	}

	function editProductionByBarcode($data, $barcode) {
		return $this->db->updateQuery("vanda_production", $data, "barcode = '".$barcode."'");
	}

	function getProductionCountByShippingId($shipId) {
		$qry = "SELECT count(*) AS aantal FROM  vanda_production WHERE shipping_id = '".$shipId."'";

		$res = $this->db->selectQuery($qry);

		return $res[0];
	}

	function getProductionByShipId($shipId) {
		$qry = "SELECT vp.id, vp.barcode, vp.datum AS productie_tijd, vp.geleverd AS verzend_tijd, vs.klant
				FROM `vanda_production` vp

				LEFT JOIN `vanda_shipment` vs
				ON vs.ship_id = vp.shipping_id

				WHERE vp.shipping_id = '".$shipId."'";

		return $this->db->selectQuery($qry);
	}
}

?>