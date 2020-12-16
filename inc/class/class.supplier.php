<?php
require_once("class.db.php");

class SupplierManager {
	var $db;

	function SupplierManager() {
		$this->db = new DB();
	}

	function loadSuppliers() {
		$qry = "SELECT * FROM vanda_suppliers WHERE verwijderd = 0 ORDER BY volgorde DESC, supplier_desc ASC";
		
		return $this->db->selectQuery($qry);
	}

	function loadSuppliersIncludeDeleted() {
		$qry = "SELECT * FROM vanda_suppliers ORDER BY supplier_desc ASC";
		
		return $this->db->selectQuery($qry);
	}

	function getBySupplierNumber($supplierNumber) {
		$qry = "SELECT * FROM vanda_suppliers WHERE id = ".$supplierNumber;
		
		$res = $this->db->selectQuery($qry);

		return $res[0];
	}
}

?>