<?php
require_once("class.db.php");

class TaskManager {
	var $db;

	function __construct() {
		$this->db = new DB();
	}

	function getToday() {
		$qry = "SELECT * FROM vanda_tasks WHERE DATE_FORMAT( date, '%Y-%m-%d' ) = '".date('Y-m-d')."'";
		
		return $this->db->selectQuery($qry);
	}
	
	function getCustom($start, $stop) {
		$qry = "SELECT * FROM vanda_task WHERE DATE_FORMAT( date, '%Y-%m-%d' ) BETWEEEN '".date('Y-m-d',strtotime($start))."' AND '".date('Y-m-d',strtotime($stop))."'";
		
		return $this->db->selectQuery($qry);
	}
	
	//Old function saveNew
	function addTask($data) {
		return $this->db->insertQuery("vanda_tasks", $data);
	}

	//Old function Process
	function processTask($id){
		$data = array("status" => '1');

		return $this->db->updateQuery("vanda_tasks", $data, "id = ".(int)$id);
	}

	//Old function Complete
	function completeTask($id){
		$data = array("status" => '2');

		return $this->db->updateQuery("vanda_tasks", $data, "id = ".(int)$id);
	}	
	
	function getAll() {
		$qry = "SELECT * FROM vanda_tasks ORDER BY adres ASC, date DESC";
		
		return $this->db->selectQuery($qry);
	}
	
	function getAllOpen($view) {
		$qry = "SELECT * FROM vanda_tasks WHERE status != 2 ORDER BY adres ASC, date ASC";
		
		if($view != ''){
			$qry = "SELECT * FROM vanda_tasks WHERE status != 2 AND adres = ".$view." ORDER BY adres ASC, date ASC";
		} 
		
		return $this->db->selectQuery($qry);
	}
	
	function getExpired($view) {
		$qry = "SELECT * FROM vanda_tasks WHERE status != 2 AND date < CURDATE() ORDER BY adres ASC , date ASC";
		
		if($view != ''){
			$qry = "SELECT * FROM vanda_tasks WHERE status != 2 AND date < CURDATE() AND adres = ".$view."";
		} 
		
		return $this->db->selectQuery($qry);		
	}
	
	function showForm($action = 'new') {
		if(isset($_GET['view'])){
			$currentview = preg_replace('/[^0-9]/', '',$_GET['view']);
		} else {
			$currentview = null;
		}
		
		$output = '';
		$output .= '<div id="taskformcontainer">';
		$output .= '<form name="taskform" id="taskform" method="post" action="index.php?page=task'.($currentview ? '&view='.$currentview : '').'">';
		$output .= '<label for="name">Taak</label><input type="text" name="taak" id="name">';
		$output .= '<label for="description">Opmerking</label><input type="text" name="description" id="description">';
		
		$output .= '<Label for="adres"><span>Adres:</label><select id="adres" class="adres" name="adres">';
		$output .= '<option value="" '.($currentview == '' ? 'selected="selected"' : '').'>Adres</option>';
		$output .= '<option value="63" '.($currentview == '63' ? 'selected="selected"' : '').'>63</option>';
		$output .= '<option value="51" '.($currentview == '51' ? 'selected="selected"' : '').'>51</option>';
		$output .= '</select>';		
		$output .= '<label for="date">Datum</label><input type="text" name="date" id="date">';
		$output .= '<input type="file" id="file" name="file" />';
		$output .= '<input type="hidden" name="filename" id="filename">';
		$output .= '<input type="submit" class="button ui-button ui-corner-all ui-widget" name="opslaan" id="opslaan" value="opslaan">';
		$output .= '<input type="hidden" name="id" id="id">';
		$output .= '<input type="hidden" name="action" id="action" value="'.$action.'">';
		$output .= '</form>';
		$output .= '</div>';
		echo $output;
	}
	
	function buildTable($tasklist){		
		$lastadres = '';
		$c = 0;

		$daysofweek = array();
		$daysofweek[1] = "Maandag";
		$daysofweek[2] = "Dinsdag";
		$daysofweek[3] = "Woensdag";
		$daysofweek[4] = "Donderdag";
		$daysofweek[5] = "Vrijdag";
		$daysofweek[6] = "Zaterdag";
		$daysofweek[7] = "Zondag";
		
		$output = "";
		$output .= "<table id='product-table' class=\"data-table\" cellpadding=\"0\" cellspacing=\"0\">";
		$output .= "<tr><th>File</th><th>Taak</th><th>Opmerking</th><th>Datum</th><th>Adres</th><th>Gereed</th></tr>";

		if(count($tasklist) > 0){			
			foreach($tasklist as $row){
				if ($row->status == 0) { 
					$row->status = 'open';
				} 
				elseif ($row->status == 1) { 
					$row->status = 'behandeling'; 
				} 
				elseif ($row->status == 2) { 
					$row->status = 'gereed'; 
				}
				
				 
				if(date("Y-m-d") > $row->date){
					$output .= "<tr class=\"red\">";
				} elseif ($c == 1) {
					$output .= "<tr class=\"grey\">"; 
					$c = 0;
				} 
				else { 
					$output .= "<tr>"; 
					$c = $c + 1; 
				} // mark uneven rows
				
				if ($row->filename != '' ) {
					$row->filename = "<a href='upload/".$row->filename."' target='_blanc' />".$row->filename."</a>"; 
				} 
				else { 
					$row->filename = '';
				}

				if ($lastadres != '' && $row->adres != $lastadres) { 
					//$output .= "<tr class=\"blue-divider\"><td colspan=6>&nbsp;</td></tr>"; 
				}

				$output .= "
					<td>".$row->filename."</td>
					<td>".$row->name."</td>
					<td>".$row->description."</td>
					<td>".$daysofweek[date('N',strtotime($row->date))]." ".date("d-m",strtotime($row->date))."</td>
					<td>".$row->adres."</td>
					<td><button id='".$row->id."' class='".$row->status." ui-button ui-corner-all ui-widget'>".ucfirst($row->status)."</button></td>";
				
				$output .= "</tr>";
				$lastadres = $row->adres;
			}			
		} 
		else {
			// Geef een melding dat er niets is weer te geven.
			$output .= "<tr><td colspan='5'> Er zijn nog geen taken</td></tr>";	
		}
		$output .= "</table>";
		$output .=  "<center>Er zijn ". count($tasklist) . " taken weergegeven<br>";

		echo $output;	
	}
}

?>