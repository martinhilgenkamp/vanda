<?php
require_once("class.db.php");

class ProductManager {
	var $db;

	function ProductManager() {
		$this->db = new DB();
	}

	function loadProducts() {
		$qry = "SELECT * FROM vanda_products ORDER BY article_desc ASC";
		
		return $this->db->selectQuery($qry);
	}

	function getByArticleNumber($articleNumber) {
		$qry = "SELECT * FROM vanda_products WHERE id = ".$articleNumber;
		
		$res = $this->db->selectQuery($qry);

		return $res[0];
	}

	function getProductsByExport($export) {
		$qry = "SELECT * FROM vanda_products WHERE export = 2 ORDER BY article_desc ASC";
		
		return $this->db->selectQuery($qry);
	}
}

?>