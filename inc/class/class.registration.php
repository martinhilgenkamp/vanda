<?php
require_once("class.db.php");

class RegistrationManager {
	var $db;

	function __construct() {
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

	function getRegistrationOverviewByPeriodQuery($period, $periode, $today, $selectdate, $startdate, $stopdate, $supplier_filter, $product_filter)
	{
		$select = "
SELECT  
	vanda_registrations.id as id, 
	DATE_FORMAT(vanda_registrations.date, '%Y-%m-%d') as date, 
	vanda_suppliers.supplier_desc as supplier, 
	vanda_products.article_no as article_no, 
	vanda_products.article_desc as article_desc, 
	sum(vanda_registrations.amount) as amount 
FROM vanda_registrations 
	LEFT JOIN vanda_suppliers 
	ON vanda_registrations.supplier_id = vanda_suppliers.id
	LEFT JOIN vanda_products 
	ON vanda_registrations.article_id = vanda_products.id ";

		$where = '';
		$order = '';
		switch($period) {
			case 'day':
				if($selectdate && $selectdate != ''){
					$where .= "WHERE DATE_FORMAT( date, '%Y-%m-%d' ) = '".date('Y-m-d',strtotime($selectdate))."'  AND YEAR(date) = '".date('Y')."' "; 
				} else {
					$where = "WHERE DATE_FORMAT( date, '%Y-%m-%d' ) = '".date('Y-m-d')."' AND YEAR(date) = '".date('Y')."' "; 
				}
				$order = "GROUP BY vanda_registrations.supplier_id, vanda_registrations.article_id";
				$title = $periode . ' overzicht van ' . ($selectdate ? date('d-m-Y',strtotime($selectdate)) : date('d-m-Y',strtotime($today)));; 
			break;	
			
			case 'week':
				if($selectdate && $selectdate != ''){
					$where .= " WHERE WEEKOFYEAR(date) = '". date('W',strtotime($selectdate)) ."' AND YEAR(date) = '".date('Y',strtotime($selectdate))."' ";	
				} else {
					$where = "WHERE  WEEKOFYEAR(date) = '".date('W')."' AND YEAR(date) = '".date('Y')."'"; 
				}
				$order = "GROUP BY vanda_registrations.supplier_id, vanda_registrations.article_id";
				$title = $periode . ' overzicht van week ' . ($selectdate ? date('W',strtotime($selectdate)) : date('W'));
			break;
			
			case 'month':
				if($selectdate && $selectdate != ''){
					$where = "WHERE  DATE_FORMAT( date, '%m' ) = '".date('m',strtotime($selectdate))."' AND YEAR(date) = '".date('Y',strtotime($selectdate))."' "; 
				} else {
					$where = "WHERE  DATE_FORMAT( date, '%m' ) = '".date('m')."' AND YEAR(date) = '".date('Y')."' "; 
				}
				
				$order = "GROUP BY vanda_registrations.supplier_id, vanda_registrations.article_id";
				$title = $periode . ' overzicht van maand ' . ($selectdate ? date('m',strtotime($selectdate)) : date('m'));
			break;
			
			case 'custom':
				
				if($startdate && $stopdate){
					$where = "WHERE  vanda_registrations.date BETWEEN '".$startdate."' AND '".$stopdate." 23:59:59' "; 
					$title = "Aangepast Overzicht van ".$startdate.' tot '.$stopdate;
				} else {
				  $time = strtotime(date("Y-m-d"));
				  $final = date("Y-m-d", strtotime("-1 month", $time));
				  $where = "WHERE  vanda_registrations.date BETWEEN '".$final."' AND '".date('Y-m-d',$time)." 23:59:59' "; 
				  
				  $title = "Aangepast Overzicht van ".$final.' tot '.date('Y-m-d',$time);
				}
				$order = "GROUP BY vanda_registrations.supplier_id, vanda_registrations.article_id";
			break;
			
			default:
				$select = "SELECT  
							vanda_registrations.id as id, 
							vanda_registrations.date as date, 
							vanda_suppliers.supplier_desc as supplier, 
							vanda_products.article_no as article_no,
							IF(vanda_registrations.remark = '',vanda_products.article_desc,vanda_registrations.remark) article_desc, 
							vanda_registrations.amount as amount 
						FROM vanda_registrations 
							LEFT JOIN vanda_suppliers 
							ON vanda_registrations.supplier_id = vanda_suppliers.id 
							LEFT JOIN vanda_products 
							ON vanda_registrations.article_id = vanda_products.id ";
				
				// van tot mogelijk maken
				if($startdate && $stopdate){
					$where = "WHERE  vanda_registrations.date BETWEEN '".$startdate."' AND '".$stopdate." 23:59:59' "; 
					$title = "Aangepast Overzicht van ".$startdate.' tot '.$stopdate;
				} else {
				  $time = strtotime(date("Y-m-d"));
				  $final = date("Y-m-d", strtotime("-1 month", $time));
				  $where = "WHERE  vanda_registrations.date BETWEEN '".$final."' AND '".date('Y-m-d',$time)." 23:59:59' ";
				}
						
				if($supplier_filter){
					
					$where .= ($where == '' ? 'WHERE' : 'AND')." vanda_registrations.supplier_id = '".$supplier_filter."' ";	
				}
				
				// Add the filter to the where clause
				if($product_filter){
					if($where != ''){
						$where .= "AND vanda_registrations.article_id = '".$product_filter."' ";	
					} else {
						$where .= "WHERE vanda_registrations.article_id = '".$product_filter."' ";	
					}
				}
				$order = "ORDER BY vanda_registrations.date DESC LIMIT 0, 2000";
				$title = "Overzicht van de registraties";
			break;	
		} 

		// Add the filter to the where clause
		if($supplier_filter){
			$where .= "AND vanda_registrations.supplier_id = '".$supplier_filter."' ";	
		}

		// Add the filter to the where clause
		if($product_filter){
			$where .= "AND vanda_registrations.article_id = '".$product_filter."' ";	
		}

		$qry = $select.$where.$order;

		return $qry;
	}

	function getRegistrationOverviewByPeriod($period, $periode, $today, $selectdate, $startdate, $stopdate, $supplier_filter, $product_filter)
	{
		$qry = $this->getRegistrationOverviewByPeriodQuery($period, $periode, $today, $selectdate, $startdate, $stopdate, $supplier_filter, $product_filter);

		return $this->db->selectQuery($qry);
	}
}

?>