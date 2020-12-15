<?php
require_once("class.db.php");

class RegistrationManager {
	var $db;

	function RegistrationManager() {
		$this->db = new DB();
	}

	function addRegistration($data) {
		return $this->db->insertQuery("vanda_registrations", $data);
	}

	function getRegistrationById($id) {
		$qry = "
SELECT  
	vr.id as id, 
	vr.date as date, 
	vs.supplier_desc as supplier, 
	vp.article_no as article_no,
	IF(vr.remark = '', vp.article_desc, vr.remark) article_desc, 
	vr.amount as amount 
FROM vanda_registrations vr
	LEFT JOIN vanda_suppliers vs
	ON vr.supplier_id = vs.id 
	LEFT JOIN vanda_products vp
	ON vr.article_id = vp.id 
WHERE vr.id = ".$id;
		
		$res = $this->db->selectQuery($qry);

		return $res[0];
	}

	function deleteRegistration($id) {
		return $this->db->deleteQuery("vanda_registrations", "id = ".$id);
	}
}

?>