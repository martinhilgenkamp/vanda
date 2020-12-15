<?php
require_once("class.mysql.php");

class task {
	
	var $id;
	var $name;
	var $description;
	var $date;
	var $adres;
	var $view;
	var $complete;
	var $removed;
	
	function __construct() {
		
	}
	
	function getToday(){
		global $db;
		// Load products from the database.
		$query = "SELECT * FROM `vanda_tasks` WHERE DATE_FORMAT( date, '%Y-%m-%d' ) = '".date('Y-m-d')."';";
		
		if($result = $db->query($query)){
			while($row = $result->fetch_object()){
				$tasks[] = $row;
			}
			return $tasks;
		} else {
			echo $db->error;
			return false;
		}			
	}
	
	function getCustom($start,$stop) {
		global $db;
		// Load products from the database.
		$query = "SELECT * FROM `vanda_tasks` WHERE DATE_FORMAT( date, '%Y-%m-%d' ) BETWEEEN '".date('Y-m-d',strtotime($start))."' AND '".date('Y-m-d',strtotime($stop))."';";
		
		if($result = $db->query($query)){
			while($row = $result->fetch_object()){
				$tasks[] = $row;
			}	
			return $tasks;
		} else {
			echo $db->error;
			return false;
		}		
	}
	
	
	function saveNew($name,$description,$date,$filename,$adres){
		
		global $db;
		$query = "INSERT INTO `vanda_tasks` (`name`, `description`, `adres`, `date`,`filename`, `status` ) VALUES ('".$name."', '".$description."', '".$adres."', '".$date."', '".$filename."', 0);";
		if(!$result = $db->query($query)){
			echo $db->error;
			return false;
		}
		return true;
	}
	
	function Process($id){
		global $db;

		$query = "UPDATE `vanda_tasks` SET `status` = '1' WHERE `vanda_tasks`.`id` = ".$id.";";
		echo $query;
		if(!$result = $db->query($query)){
			echo $db->error;
			return false;
		}
		return true;
	}
	
	function Complete($id){
		global $db;

		$query = "UPDATE `vanda_tasks` SET `status` = '2' WHERE `vanda_tasks`.`id` = ".$id.";";
		if(!$result = $db->query($query)){
			echo $db->error;
			return false;
		}
		return true;
	}
	
	function getAll(){
		global $db;
		// Load products from the database.
		$query = "SELECT * FROM `vanda_tasks` ORDER BY adres ASC, date DESC;";
		if($result = $db->query($query)){
			while($row = $result->fetch_object()){
				$tasks[] = $row;
			}	
			return $tasks;
		} else {
			echo $db->error;
			return false;
		}	
		
	}
	
	function getAllOpen($view){
		global $db;
		// Load products from the database.
		
		if($view != ''){
			$query = "SELECT * FROM `vanda_tasks` WHERE status != 2 AND adres = ".$view." ORDER BY adres ASC, date ASC;";
		} else {
			$query = "SELECT * FROM `vanda_tasks` WHERE status != 2 ORDER BY adres ASC, date ASC;";
		}
		if($result = $db->query($query)){
			while($row = $result->fetch_object()){
				$tasks[] = $row;
			}	
			return $tasks;
		} else {
			echo $db->error;
			return false;
		}	
		
	}
	
	function getExpired($view){
		global $db;
		// Load products from the database.
		if($view != ''){
			$query = "SELECT * FROM `vanda_tasks` WHERE status != 2 AND date < CURDATE() AND adres = ".$view." ;";
		} else {
			$query = "SELECT * FROM `vanda_tasks` WHERE status != 2 AND date < CURDATE() ORDER BY adres ASC , date ASC";
		}
		if($result = $db->query($query)){
			while($row = $result->fetch_object()){
				$tasks[] = $row;
			}	
			return $tasks;
		} else {
			echo $db->error;
			return false;
		}	
		
	}
	
	function showForm($action = 'new'){
		$nl = "\r\n";
		$output .= '<div id="taskformcontainer">';
		$output .= '<form name="taskform" id="taskform" method="post" action="index.php?page=task">'.$nl;
		$output .= '<label for="name">Taak</label><input type="text" name="taak" id="name">'.$nl;
		$output .= '<label for="description">Opmerking</label><input type="text" name="description" id="description">'.$nl;
		
		$output .= '<Label for="adres"><span>Adres:</label><select id="adres" class="adres" name="adres">'.$nl;
		$output .= '<option value="" '.($_SESSION['viewtype'] == '' ? 'selected="selected"' : '').'>Adres</option>'.$nl;
		$output .= '<option value="63" '.($_SESSION['viewtype'] == '63' ? 'selected="selected"' : '').'>63</option>'.$nl;
		$output .= '<option value="51" '.($_SESSION['viewtype'] == '51' ? 'selected="selected"' : '').'>51</option>'.$nl;
		$output .= '</select>'.$nl;		
		$output .= '<label for="date">Datum</label><input type="text" name="date" id="date">'.$nl;
		$output .= '<input type="file" id="file" name="file" />'.$nl;
		$output .= '<input type="hidden" name="filename" id="filename">'.$nl;
		$output .= '<input type="submit" class="button ui-button ui-corner-all ui-widget" name="opslaan" id="opslaan" value="opslaan">'.$nl;
		$output .= '<input type="hidden" name="id" id="id">'.$nl;
		$output .= '<input type="hidden" name="action" id="action" value="'.$action.'">'.$nl;
		$output .= '</form>'.$nl;
		$output .= '</div>'.$nl;
		echo $output;
	}
	
	
	function buildTable($tasklist){
		$nl = "\r\n";
		
		$daysofweek = array();
		$daysofweek[1] = "Maandag";
		$daysofweek[2] = "Dinsdag";
		$daysofweek[3] = "Woensdag";
		$daysofweek[4] = "Donderdag";
		$daysofweek[5] = "Vrijdag";
		$daysofweek[6] = "Zaterdag";
		$daysofweek[7] = "Zondag";
		
		$output .= "<table id='product-table' class=\"ui-widget results\" cellpadding=\"0\" cellspacing=\"0\">".$nl;
		$output .= "<thead class=\"ui-widget-header\">".$nl;
		$output .= "<td>File</td><td>Taak</td><td>Opmerking</td><td>Datum</td><td>Adres</td><td>Gereed</td></thead>".$nl;

		if(count($tasklist)){
			// er zijn taken om te laten zien.
			
			foreach($tasklist as $row){
				if($row->status == 0){ $row->status = 'open';} elseif ($row->status == 1){ $row->status = 'behandeling'; } elseif ($row->status == 2){ $row->status = 'gereed'; };
				if($c == 1){ $output .= "	<tr class=\"grey\">".$nl; $c = 0;} else { $output .= "<tr>".$nl; $c = $c + 1; } // mark uneven rows
				if($row->filename != '' ){$row->filename = "<a href='upload/".$row->filename."' target='_blanc' />".$row->filename."</a>"; } else {$row->filename = '';};
				if($lastadres != '' && $row->adres != $lastadres){ $output .= "<tr class=\"blue-divider\"><td colspan=6>&nbsp;</td></tr>"; }
				$output .= "<td>".$row->filename."</td><td>".$row->name."</td><td>".$row->description."</td><td>".$daysofweek[date('N',strtotime($row->date))]." ".date("d-m",strtotime($row->date))."</td><td>".$row->adres."</td><td><button id='".$row->id."' class='".$row->status." ui-button ui-corner-all ui-widget'>".ucfirst($row->status)."</button></td>".$nl;
				
				$output .= "</tr>".$nl;
				$lastadres = $row->adres;
			}
			
			
		} else {
			// Geef een melding dat er niets is weer te geven.
			$output .= "<tr><td colspan='5'> Er zijn nog geen taken</td></tr>";	
		}
		$output .= "</table>".$nl;
		$output .=  "<center>Er zijn ". count($tasklist) . " taken weergegeven<br>".$nl;

		echo $output;	
	}
}// End class


?>