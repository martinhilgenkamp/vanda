<?php
date_default_timezone_set("Europe/Amsterdam");
require_once("class.db.php");

class MachineManager {
	var $db;

	function __construct() {
		$this->db = new DB();
	}
	
	function addMachine($data) {
		if($data['persoon'] == '' || $data['kwaliteit'] == '' || $data['machine'] ==''){
			//incomplete entry throw error
			echo "Niet alle waardes zijn ingevuld.";
			return;
		}

		$time = $this->getLastTime($data);

		if(strtotime("-1 minutes") >= strtotime($time) || $time == '') {
			$machineId = $this->db->insertQuery("vanda_machines", $data);

			if($machineId > 0) {
				echo 'Opgeslagen';
			} else {
				echo 'Opslaan mislukt';	
			}
		}
		else {
			echo 'Dubbele registratie probeer het later opnieuw.';
		}

		$_SESSION['persoon'.$data['machine']] = $data['persoon'];
		$_SESSION['kwaliteit'.$data['machine']] = $data['kwaliteit'];
	}

	function getLastTime($data) {
		$qry = "SELECT datum FROM `vanda_machines` WHERE persoon = '".$data['persoon']."' AND machine = '".$data['machine']."' ORDER BY datum DESC LIMIT 1";
		
		$res = $this->db->selectQuery($qry);

		return $res[0]->datum;
	}
		
	function getLastPersoon($machine){
		// Determine user based on session and db.
		$qry = "SELECT persoon FROM `vanda_machines` WHERE machine = '".$machine."' ORDER BY datum DESC LIMIT 1";
		
		$res = $this->db->selectQuery($qry);

		if(isset($res[0]->persoon) || isset($_SESSION['persoon'.$machine])){
			$persoon = isset($_SESSION['persoon'.$machine]) ?  $_SESSION['persoon'.$machine] : $res[0]->persoon;
			return $persoon;
		} else {
			return;
		}
	}
	
	function getLastKwaliteit ($machine){
		// Determine user based on session and db.
		$qry = "SELECT kwaliteit FROM `vanda_machines` WHERE machine = '".$machine."' ORDER BY datum DESC LIMIT 1";
		$res = $this->db->selectQuery($qry);

		

		if(isset($_SESSION['kwaliteit'.$machine])){
			$kwaliteit = $_SESSION['kwaliteit'.$machine];
			return $kwaliteit;
		} elseif(isset($res[0]->kwaliteit)){
			$kwaliteit = $res[0]->kwaliteit;
			return $kwaliteit;
		}else {
			return;
		}
	}
	
	function getEditForm($aantal){
		$output = '';
		// start generating output
		$output .= "<div id=\"machineform-wrapper\">";
		$output .= "<form id=\"machineform\" name=\"machineform\"  method=\"post\" target=\"_blank\">";
		$output .= "<ul class=\"machine-header\">";
		$output .= "<li>Operator</li><li>Kwaliteit</li><li>Machine</li>";
		$output .= "</ul>";
		for ($i = 1; $i <= $aantal; $i++){			
			$output .= "<ul>";
			$output .= "<li><input type=\"text\" class=\"ui-widget ui-state-default ui-corner-all machine-input-text\" name=\"operator".$i."\" id=\"input_operator".$i."\" value=\"".$this->getLastPersoon($i)."\"></li>";
			$output .= "<li><input type=\"text\" class=\"ui-widget ui-state-default ui-corner-all machine-input-text\" name=\"kwalitet".$i."\" id=\"input_kwaliteit".$i."\" value=\"".$this->getLastKwaliteit($i)."\"></li>";
			$output .= '<input type="button" class="ui-button ui-corner-all ui-widget machinebutton"  id="machine'.$i.'" name="machine'.$i.'" value="Machine '.$i.'">';
			$output .= "</ul>";
		}
		$output .= "<input type=\"hidden\" name=\"task\" id=\"input_task\" value=\"add\" />";
		$output .= "</form>";
		$output .= "</div>";
		
		return $output;
	}

	function getAllMachines() {
		$qry = "SELECT machine FROM `vanda_machines` GROUP BY machine ASC";

		return $this->db->selectQuery($qry);
	}
	
	function loadFilterForm($options){		
		$startdate = '';
		$stopdate =  '';
		$machine_filter = '';

		if ($_POST) {
			$startdate = $_POST['startdate'];
			$stopdate =  $_POST['stopdate'];
			$machine_filter = $_POST['machine_filter'];
		}
		
		$machines = $this->getAllMachines();
				
		if (isset($_GET["free_search"])) { 	
			$free_search  = $_GET["free_search"];	
		} 
		elseif (isset($_POST['free_search'])) {	
			$free_search = $_POST['free_search'];	
		} 
		else { 
			$free_search='';	
		};		

		$time = strtotime(date("Y-m-d"));
		$final = date("Y-m-d");

		$output = "";
		$output .= "<div id='filter_form_div'>";
		$output .= "<form id='filter_form' action='index.php?page=machinetable' method='post'>";
		$output .= "<input type='text' name='free_search' id='input_free_search' value='".$free_search."' class='left ui-widget ui-text ui-corner-all'/><input type='submit' name='submit_search' id='submit_free_search' value='Zoek' class='leftui-widget ui-button ui-corner-all' />";
		//$output .= "<input type=\"button\" name=\"Herstel\" id=\"herstel\" value=\"Herstel\" class=\"ui-widget ui-button ui-corner-all\" onclick=\"window.location = 'index.php?page=machinetable'\"/>";
		$output .= "<input class=\"datepicker ui-corner-all\" id=\"startdate\" name=\"startdate\" value=\"".($startdate ? $startdate :  $final)."\" onchange=\"$('#filter_form').submit()\"/>";
        $output .= "<input class=\"datepicker ui-corner-all\" id=\"stopdate\" name=\"stopdate\" value=\"".($stopdate ? $stopdate : date('Y-m-d',$time))."\" onchange=\"$('#filter_form').submit()\"/>";	
		
		$output .= "<select class=\"filter_select ui-corner-all\" name=\"machine_filter\" id=\"machine_filter\" onchange=\"$('#filter_form').submit()s\">";
        $output .= "<option value=\"\"".(!$machine_filter ? "selected='selected'" : '' ).">Machine</option>";
   
		foreach($machines as $machine){
            if($machine_filter == $machine->machine){
                $output .= "<option value='".$machine->machine."' selected='selected'>Machine ".$machine->machine."</option>";
            } else {
                $output .= "<option value='".$machine->machine."'>Machine ".$machine->machine."</option>";
            }
        }
    	$output .= "</select>";
			
		// Set $options in hidden fields to keep the values
		foreach($options as $key => $val) {
			if(isset($val)){
				$output .= "<input type='hidden' id='filter_".$key."' name='".$key."' value='".$val."' />";
			}
		}
		$output .= "</form>";
		$output .= "</div>";
		return $output;
	}
	
	// Get table of atricles
	function getTable($where = '', $range = array(0,20)){	
		$startdate = '';
		$stopdate =  '';

		if ($_POST) {
			$startdate = $_POST['startdate'];
			$stopdate =  $_POST['stopdate'];
		}
			
		$cols = array('rolnummer','deelnummer','snijbreedte','snijlengte','bronbreedte','bronlengte','omschrijving','ingevoerd','gewijzigd','verzonden');
		
		// Variabelen definieren
		if (isset($_GET['page'])) {	
			$page = $_GET['page']; 
		} 
		else if (isset($_POST['page'])) { 
			$page = $_POST['page']; 
		}

		if (isset($_GET['order'])) {	
			$order = $_GET['order']; 
		} 
		else if (isset($_POST['order'])) { 
			$order = $_POST['order']; 
		}

		if (isset($_GET['sort'])) {	
			$sort = $_GET['sort']; 
		} 
		else if (isset($_POST['sort'])) { 
			$sort = $_POST['sort']; 
		}

		$pg = 1;
		if (isset($_GET["pg"])) { 	
			$pg  = $_GET["pg"];	
		} 
		elseif (isset($_POST['pg'])) {	
			$pg = $_POST['pg'];	
		}

		$free_search = '';
		if (isset($_GET["free_search"])) { 	
			$free_search  = $_GET["free_search"];	
		} 
		elseif (isset($_POST['free_search'])) {	
			$free_search = $_POST['free_search'];	
		}

		$machine_filter = '';
		if (isset($_GET["machine_filter"])) { 	
			$machine_filter  = $_GET["machine_filter"];	
		} 
		elseif (isset($_POST['machine_filter'])) {	
			$machine_filter = $_POST['machine_filter'];	
		}
				
		// filter formulier waardes in een sessie zetten.
		if (isset($page)) { 
			$_SESSION['page'] = $page; 
		} 
		else { 
			$page = $_SESSION['page']; 
		}

		if (isset($sort)) { 
			$_SESSION['sort'] = $sort; 
		} 
		else { 
			$sort = isset($_SESSION['sort']) ? $_SESSION['sort'] : ''; 
		} 	

		if (isset($pg)) { 
			$_SESSION['pg'] = $pg; 
		} 
		else { 
			$pg = $_SESSION['pg']; 
		}

		if (isset($order)) { 
			$_SESSION['order'] = $order; 
		} 
		else { 
			$order = isset($_SESSION['order']) ? $_SESSION['order'] : ''; 
		} 

		if (isset($free_search)) { 
			$_SESSION['free_search'] = $free_search; 
		} 
		else { 
			$free_search = $_SESSION['free_search']; 
		}

		if (isset($machine_filter)) { 
			$_SESSION['machine_filter'] = $machine_filter; 
		} 
		else { 
			$machine_filter = $_SESSION['machine_filter']; 
		}
		
		// Set aantal resultaten per pagina
		$range[1] = 5000;	
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
		
		$link = '';
		foreach($link_array as $part){
			if($part){ // controleer link delen op waarde
				$link .= ($link == '' ? '?' : '&').$part;
			}
		}
		
		// Variabelen voor t limiet
		$limit = " LIMIT ".$range[0].",".$range[1];
		
		// build where query
		$where_array = array();
		//($filter_locatie ? $where_array[] = '`locatie` LIKE "%' . $filter_locatie . '%" ': '');
		
		if($free_search && $free_search != ''){
			($free_search ? $where_array[] = '(`persoon` LIKE "%' . $free_search . '%" OR `kwaliteit` LIKE "%' . $free_search . '%")': '');		
		}
		
		
		// van tot mogelijk maken
		if($startdate && $stopdate){
			$where = "WHERE  vanda_machines.datum BETWEEN '".$startdate."' AND '".$stopdate." 23:59:59' "; 
			$title = "Aangepast Overzicht van ".$startdate.' tot '.$stopdate;
		} else {
		  $time = strtotime(date("Y-m-d"));
		  $where = "WHERE  vanda_machines.datum BETWEEN '".date('Y-m-d',$time)." 00:00:00"."' AND '".date('Y-m-d',$time)." 23:59:59' ";
		}
		
		
		// Alleen niet verwijderde registraties laten zien
		$where_array[] = '`verwijderd` = "0" ';
		
		if($machine_filter != ''){
			$where_array[] = 'machine = "'.$machine_filter.'"';
		}
	
		//Build the where clause
		foreach($where_array as $part){
			if($part && $part != ''){
				$where .= ($where ? ' AND ' : ' WHERE ').$part;
			}
		}
			
		//Query om te tellen hoeveel waarden er in de tabel zitten.
		$query = "SELECT * FROM vanda_machines ".$where.$orderby;
						
		// Put query in the session
		$_SESSION['query'] = $query;
	
		// Tel het aantal waardes en bepaal hoeveel paginas er moeten komen
		$result = $this->db->selectQuery($query);
		$total_records = count($result);
		$total_pages = ceil($total_records / $range[1]);
		$nl = "\n";	
		
		// generate output
		
		//Create excel icon
		$output = "";
		$output .= "<form method=\"post\" name=\"csvform\" id=\"csvform\" action=\"pages/csv.php\" enctype=\"multipart/form-data\" />";
		$output .= "<textarea name=\"query\" id=\"query\">";
		$output .= $query;
		$output .= "</textarea>";
		$output .= "<div id=\"csv\" class=\"csv\"><img src=\"images/excel_icon.gif\" style=\"margin-top: -0px;\" /></div>";
		$output .= "</form>";
		
		// Load the filter form
		$output .= $this->loadFilterForm($options);
		
		// Generate table header
		$output .= "<table class=\"data-table\">";
		$output .= "	<tr>";
		$output .= "		<th class='ui-corner-tl'><input type='checkbox' id='machine-select-all' name='machine-select-all'></th>";
		$output .= "		<th>Rij</th>";
		$output .= "		<th><a href='?".$link_array['page']."&sort=id&order=".$order."&pg=".$pg."'>ID</a></th>";
		$output .= "		<th><a href='?".$link_array['page']."&sort=persoon&order=".$order."&pg=".$pg."'>Operator</a></th>";
		$output .= "		<th><a href='?".$link_array['page']."&sort=kwaliteit&order=".$order."&pg=".$pg."'>Kwaliteit</a></th>";
		$output .= "		<th><a href='?".$link_array['page']."&sort=machine&order=".$order."&pg=".$pg."'>Machine</a></th>";
		$output .= "		<th class='ui-corner-tr'><a href='?".$link_array['page']."&sort=datum&order=".$order."&pg=".$pg."'>Datum</a></th>";
		$output .= "	</tr>";
		
		$records = 0;
		foreach ($result as $row) {
			// Generate table rows
			$records++;
			$output .= "	<tr id='row_".$row->id."' class='data-table-row'>";
			$output .= "		<td><input class='machine-checkbox' type='checkbox' name='machineid[]' value='".$row->id."' /></td>";
			$output .= "		<td>".$records."</td>";
			$output .= "		<td>".$row->id."</td>";
			$output .= "		<td>".$row->persoon."</td>";
			$output .= "		<td>".$row->kwaliteit."</td>";
			$output .= "		<td> Machine ".$row->machine."</td>";		
			$output .= "		<td>".date('Y-m-d H:i:s',strtotime($row->datum))."</td>";		
			$output .= "	</tr>";
		}
		
		if($records == 0) {
			$output .=  '<tr class=\'data-table-row\'>';	
			$output .=  	'<td colspan="16"><strong>Er zijn geen resultaten om weer te geven</strong></td>';
			$output .=  '</tr>';
		}
		else {
			$output .=  "<tr>";	
			$output .=  	"<th class='ui-corner-bl ui-corner-br' colspan='7'>Totaal ".$records." registraties.</th>";
			$output .=  "</tr>";
		}
		
		// Close Table
		$output .= "</table>";
		$output .= "<input type=\"button\" id=\"verwijder\" value=\"Verwijder\" />";
		
		/***********************************************************
		// Paginatie Staat nu uit maar kan aangezet worden		   *
		************************************************************
		
		$output .= "<div id='pagination'>";
		for ($i=1; $i<=$total_pages; $i++) { 
			// Generate link for pagination.	
			$output .= "<a href='?";
			
			$output .= "&pg=".$i."'><span>".$i."</span></a> "; 
		}; 
		$output .="</div>";
		*/
		
		return $output;	
	}

	function deleteMachine($ids){
		$deleteIds = implode(', ', $ids); 
		$data = array("verwijderd" => "1");

		var_dump("<pre>");
		var_dump($deleteIds);
		//$this->db->updateQuery("vanda_machines", $data, "id IN (".$deleteIds.")");

		$this->RestoreSession();
	}
	
	// RESET SESSION VALUES	
	function RestoreSession(){
		$name = $_SESSION['username'];
		
		session_destroy();
		session_start();
		$_SESSION['username'] = $name;
	}

	function executeQuery($qry) {
		return $this->db->selectQuery($qry);
	}
}

?>