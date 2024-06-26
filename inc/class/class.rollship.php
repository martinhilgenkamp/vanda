<?php
date_default_timezone_set("Europe/Amsterdam");
require_once("class.user.php");
require_once("class.db.php");

class RollsShipment {

	// Define $where
	public $where = "";

	// Define $nl and $output
	public $nl = "\n";
	public $output = "";
	public $userManager;	
	public $db;


	function __construct() {
		$this->userManager = new UserManager();
		$this->db = new DB();
	}
	
	function makeShipment($klant){
		
		$insert = [
			"klant" => $klant,
			"datum" => date('Y-m-d H:i:s')
		];
		$newId = $this->db->insertQuery("vanda_roll_ship", $insert);

		if($newId > 0){
			return $newId;
		} else {
			echo "Fout met het bijwerken van de rol zending";
		}
	}
	
	function deleteShipment($id){
 		$query = "DELETE FROM vanda_roll_ship WHERE id ='".$id."';";
		if($this->db->insertQuery('vanda_roll_ship', $query)){
			return true;
		} else {
			echo "Fout met het bijwerken van de rol zending";
		}
		
	}
	
	
	function GetShipmentSelect(){
		$query = "SELECT * FROM vanda_roll_ship ORDER BY datum DESC;";
	}
	
	
	function showShipment(){
				
	}
	
	
	function loadFilterForm($options){
		$nl = $this->nl;
		$output = $this->output;


		$this->restoreSession();
		if (isset($_GET["free_search_rollship"])) { 	$free_search_rollship  = $_GET["free_search_rollship"];	} elseif (isset($_POST['free_search_rollship'])){	$free_search_rollship = $_POST['free_search_rollship'];	} else { $free_search_rollship='';	};		
		$user = $this->userManager->getUserByName($_SESSION['username']);
		
		$output .= "<div id='filter_form_div'>".$nl;
		$output .= "<form id='filter_form' action='index.php?page=roll-shipments' method='post'>".$nl;
		$output .= "<label for='free_search_rollship_rollship' class='left'>Zoek:</label><input type='text' name='free_search_rollship' id='input_free_search_rollship' value='".$free_search_rollship."' class='left'/><input type='submit' name='submit_search' id='submit_free_search_rollship' value='Zoek' class='left' /><input type='button' id='reset_free_form' value='Herstel' class='left'/>".$nl;
		
		// Set $options in hidden fields to keep the values
		foreach($options as $key => $val) {
			if(isset($val)){
				$output .= "<input type='hidden' id='filter_".$key."' name='".$key."' value='".$val."' />".$nl;
			}
		}
		$output .= "</form>".$nl;
		$output .= "</div>".$nl;
		
		echo $output;

		$output = "";
	}
	
	function showShipmentTable($history = ''){
		$link = "";
		$where = "";
		$sort = "";
		$order = "";

		$user = $this->userManager->getUserByName($_SESSION['username']);
		$cols = array('id','klant','datum');
		
		// Variabelen definieren
		if(isset($_GET['page'])){	$page = $_GET['page']; } else if (isset($_POST['page'])){ $page = $_POST['page']; }
		if(isset($_GET['order'])){	$order = $_GET['order']; } else if (isset($_POST['order'])){ $order = $_POST['order']; }
		if(isset($_GET['sort'])){	$sort = $_GET['sort']; } else if (isset($_POST['sort'])){ $sort = $_POST['sort']; }
		if (isset($_GET["pg"])) { 	$pg  = $_GET["pg"];	} elseif (isset($_POST['pg'])){	$pg = $_POST['pg'];	} else { $pg=1;	};
		if (isset($_GET["free_search_rollship"])) { 	$free_search_rollship  = $_GET["free_search_rollship"];	} elseif (isset($_POST['free_search_rollship'])){	$free_search_rollship = $_POST['free_search_rollship'];	} else { $free_search_rollship='';	};
		
		
		// filter formulier waardes in een sessie zetten.
		if(isset($page)) { $_SESSION['page'] = $page; } else { $page = $_SESSION['page']; }
		if(isset($sort)) { $_SESSION['sort'] = $sort; } else { $sort = $_SESSION['sort']; } 	 
		if(isset($pg)) { $_SESSION['pg'] = $pg; } else { $pg = $_SESSION['pg']; }
		if(isset($order)) { $_SESSION['order'] = $order; } else { $order = $_SESSION['order']; } 
		if(isset($free_search_rollship)) { $_SESSION['free_search_rollship'] = $free_search_rollship; } else { $free_search_rollship = $_SESSION['free_search_rollship']; }
		
		// Set aantal resultaten per pagina
		$range[1] = 2500;	
		$range[0] = ($pg-1) * $range[1];
		
		if(!in_array($sort,$cols)){
			$sort = 'datum';
		}
		
		// Save Variabelen voor zoek formulier.
		$options = array('page' => $page, 'order' => $order, 'sort' => $sort, 'pg' => $pg);
						
		// Variabelen tbv sorteren en paginatie
		$order = ($order == 'DESC' ? $order = 'ASC' : $order='DESC');
		$orderby= " ORDER BY ".($sort ? '`'.$sort.'` ' : '`ingevoerd` ').$order;
		
		//Link opbouwen voor paginatie en sortering klikken
		$link_array['page'] = ($page ? "page=".$page : '');
		$link_array['sort'] = "sort=".$sort;
		$link_array['order'] = "order=".$order;
		$link_array['pg'] = "pg=".$pg;		
		
		foreach($link_array as $part){
			if($part){ // controleer link delen op waarde
				$link .= ($link == '' ? '?' : '&').$part;
			}
		}
		
		// Variabelen voor t limiet
		$limit = " LIMIT ".$range[0].",".$range[1];
		
		// build where query
		$where_array = array();
		
		if($free_search_rollship && $free_search_rollship != ''){
			($free_search_rollship ? $where_array[] = '(`id` LIKE "%' . $free_search_rollship . '%" OR `klant` LIKE "%' . $free_search_rollship . '%" OR `datum` LIKE "%' . $free_search_rollship . '%") ': '');		
		}

		if($history){
			$dateDaysAgo = date('Y-m-d', strtotime('-'.$history.' days'));
			$where_array[] = "vanda_roll_ship.datum >= '".$dateDaysAgo."'";
		}
		
		
		//Build the where clause
		foreach($where_array as $part){
			if($part && $part != ''){
				$where .= ($where ? ' AND ' : ' WHERE ').$part;
			}
		}
			
		//Query om te tellen hoeveel waarden er in de tabel zitten.
		$query = "SELECT * FROM vanda_roll_ship ".$where.$orderby;
		
		// Put query in the session
		$_SESSION['query'] = $query;
		
		// Tel het aantal waardes en bepaal hoeveel paginas er moeten komen
		$result = $this->db->selectQuery($query);
		$total_records = count($result);
		$total_pages = ceil($total_records / $range[1]);
		$nl = $this->nl;
		$output = $this->output;
		
		
		// generate output
		// Load the filter form
		echo $this->loadFilterForm($options);
		
		// Generate table header
		$output .= "<table class=\"data-table\">".$nl;
		$output .= "	<tr>".$nl;
		$output .= "		<th class='ui-corner-tl' >&nbsp;</th>".$nl;
		$output .= "		<th><a href='?".$link_array['page']."&sort=id&order=".$order."&pg=".$pg."'>ID</a></th>".$nl;
		$output .= "		<th><a href='?".$link_array['page']."&sort=klant&order=".$order."&pg=".$pg."'>Klant</a></th>".$nl;
		$output .= "		<th class='ui-corner-tr'><a href='?".$link_array['page']."&sort=datum&order=".$order."&pg=".$pg."'>Datum</a></th>".$nl;
		$output .= "	</tr>".$nl;

		$records = 0;
		foreach($result as $row) {
			$row->klant = (isset($row->klant) && trim($row->klant) != '' ? $row->klant : '-');
									
			// Generate table rows
			$output .= "	<tr id='row_".$row->id."' class='data-table-row'>".$nl;
			$output .= "		<td><a href='pages/generate/generate_roll_pdf.php?ship_id=".$row->id."' target='_blanc'><img src='images/printer.png' height='20'></a> <a href='pages/generate/generate_roll_excel.php?ship_id=".$row->id."' target='_blanc'><img src='images/excel.png' height='20'></a></td>".$nl;
			$output .= "		<td>".$row->id."</td>".$nl;
			$output .= "		<td>".$row->klant."</td>".$nl;
			$output .= "		<td>".$row->datum."</td>".$nl;
			$output .= "	</tr>".$nl;
			$records++;
		}
		
		//if($records == 0){
			$output .=  '<tr class=\'data-table-row\'>'.$nl;	
			$output .=  '<th class="ui-corner-bottom" colspan="4"><strong>Er zijn '.$records.' resultaten om weer te geven</strong></td>'.$nl;
			$output .=  '</tr>'.$nl;
		//}
		
		// Close Table
		$output .= "</table>";
		
		
		/***********************************************************
		// Paginatie Staat nu uit maar kan aangezet worden		   *
		************************************************************
		
		$output .= "<div id='pagination'>".$nl;
		for ($i=1; $i<=$total_pages; $i++) { 
			// Generate link for pagination.	
			$output .= "<a href='?";
			
			$output .= "&pg=".$i."'><span>".$i."</span></a> "; 
		}; 
		$output .="</div>";
		*/
		
		return $output;
	}
	
	function restoreSession(){
		$savesession = array('username','order','sort','free_search_rollship');
		// Loop through session
		foreach($_SESSION as $key => $val){
			if(!in_array($key,$savesession)){
				unset($_SESSION[$key]);
			}
		}
	}
}




?>