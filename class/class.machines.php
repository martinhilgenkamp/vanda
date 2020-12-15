<?php
date_default_timezone_set("Europe/Amsterdam");

// prevent notifications
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", 1);
$nl = "\r\n";

class Machine {
	
	function add($post){
		global $db;
		
		// check if all variables needed are here
		if($post->persoon == '' || $post->kwaliteit == '' || $post->machine ==''){
			//incomplete entry throw error
			echo "Niet alle waardes zijn ingevuld.";
			return;
		}
		
		// check if there is no double registration.
		$time = $this->getLastTime($post);
		
		if(strtotime("-1 minutes") >= strtotime($time) || $time == ''){
			$query = "INSERT INTO `vanda_machines` (`persoon`, `kwaliteit`, `machine`, `datum`) VALUES ('".$post->persoon."', '".$post->kwaliteit."', '".$post->machine."', '". date("Y-m-d H:i:s") ."');";
			// Save the post
			if($db->query($query)){
				echo 'Opgeslagen';
			} else {
				echo 'Opslaan mislukt'.$db->error;	
			}
		} else {
			echo 'Dubbele registratie probeer het later opnieuw.';
		}
				
		$_SESSION['persoon'.$post->machine] = $post->persoon;
		$_SESSION['kwaliteit'.$post->machine] = $post->kwaliteit;
	}
	
	function getLastTime($post){
		global $db;
		
		$query = "SELECT datum FROM `vanda_machines` WHERE persoon = '".$post->persoon."' AND machine = '".$post->machine."' ORDER BY datum DESC LIMIT 1; ";
		
		//echo $query;
		
		if($result = $db->query($query)){
			$time = $result->fetch_object();
			return $time->datum;
		} else {
			echo "FOUT! ".$db->error;
			return 'error';   
		}
	}
	
	function getLastPersoon($machine){
		// Determine user based on session and db.
		global $db;
		$query = "SELECT persoon FROM `vanda_machines` WHERE machine = '".$machine."' ORDER BY datum DESC LIMIT 1; ";
		if($result = $db->query($query)){
			$result = $result->fetch_object();
		} else {
			echo "FOUT! ".$db->error;
			return 'error';   
		}
		$persoon = ($_SESSION['persoon'.$machine] ?  $_SESSION['persoon'.$machine] : $result->persoon);
		return $persoon;
	}
	
	function getLastKwaliteit ($machine){
		// Determine user based on session and db.
		global $db;
		$query = "SELECT kwaliteit FROM `vanda_machines` WHERE machine = '".$machine."' ORDER BY datum DESC LIMIT 1; ";
		if($result = $db->query($query)){
			$result = $result->fetch_object();
		} else {
			echo "FOUT! ".$db->error;
			return 'error';   
		}
		$kwaliteit = ($_SESSION['kwaliteit'.$machine] ?  $_SESSION['kwaliteit'.$machine] : $result->kwaliteit);
		return $kwaliteit;
	}
	
	function getEditForm($aantal){
		global $db;
		
		// start generating output
		$output .= "<div id=\"machineform-wrapper\">";
		$output .= "<form id=\"machineform\" name=\"machineform\"  method=\"post\" target=\"_blank\">";
		$output .= "<ul class=\"machine-header\">".$nl;
		$output .= "<li>Operator</li><li>Kwaliteit</li><li>Machine</li>".$nl;
		$output .= "</ul>".$nl;
		for ($i = 1; $i <= $aantal; $i++){			
			$output .= "<ul>".$nl;
			$output .= "<li><input type=\"text\" class=\"ui-widget ui-state-default ui-corner-all machine-input-text\" name=\"operator".$i."\" id=\"input_operator".$i."\" value=\"".$this->getLastPersoon($i)."\"></li>".$nl;
			$output .= "<li><input type=\"text\" class=\"ui-widget ui-state-default ui-corner-all machine-input-text\" name=\"kwalitet".$i."\" id=\"input_kwaliteit".$i."\" value=\"".$this->getLastKwaliteit($i)."\"></li>".$nl;
			$output .= '<input type="button" class="ui-button ui-corner-all ui-widget machinebutton"  id="machine'.$i.'" name="machine'.$i.'" value="Machine '.$i.'">';
			$output .= "</ul>".$nl;
		}
		$output .= "<input type=\"hidden\" name=\"task\" id=\"input_task\" value=\"add\" />";
		$output .= "</form>";
		$output .= "</div>";
		
		return $output;
	}
	
	function loadFilterForm($options){
		global $db;
		
		$startdate = $_POST['startdate'];
		$stopdate = $_POST['stopdate'];
		$machine_filter = $_POST['machine_filter'];
		
		$query = "SELECT machine FROM `vanda_machines` GROUP BY machine ASC; ";
		if($result = $db->query($query)){
			if($result->num_rows){
				 while ($row = $result->fetch_object()) {
					foreach ($row as $r){
						$machines[] = $r;
					}
				}
			}
		} else {
			echo "FOUT! ".$db->error;   
		}
		

		
		
		if (isset($_GET["free_search"])) { 	$free_search  = $_GET["free_search"];	} elseif (isset($_POST['free_search'])){	$free_search = $_POST['free_search'];	} else { $free_search='';	};		
		//$user = getUser($_SESSION['username']);
		$time = strtotime(date("Y-m-d"));
		$final = date("Y-m-d", strtotime("-1 week", $time));
		$output .= "<div id='filter_form_div'>".$nl;
		$output .= "<form id='filter_form' action='index.php?page=machinetable' method='post'>".$nl;
		$output .= "<label for='free_search' class='left'>Zoek:</label><input type='text' name='free_search' id='input_free_search' value='".$free_search."' class='left'/><input type='submit' name='submit_search' id='submit_free_search' value='Zoek' class='left' />".$nl;
		$output .= "<input type=\"button\" name=\"Herstel\" id=\"herstel\" value=\"Herstel\" class=\"ui-rouded-corners\" onclick=\"window.location = 'index.php?page=machinetable'\"/>";
		$output .= "<input class=\"datepicker\" id=\"startdate\" name=\"startdate\" value=\"".($startdate ? $startdate :  $final)."\" onchange=\"$('#filter_form').submit()\"/>";
        $output .= "<input class=\"datepicker\" id=\"stopdate\" name=\"stopdate\" value=\"".($stopdate ? $stopdate : date('Y-m-d',$time))."\" onchange=\"$('#filter_form').submit()\"/>";	
		
		$output .= "<select class=\"filter_select\" name=\"machine_filter\" id=\"machine_filter\" onchange=\"$('#filter_form').submit()\">";
        $output .= "<option value=\"\"".(!$machine_filter ? "selected='selected'" : '' ).">Machine</option>";
   
		foreach($machines as $machine){
            if($machine_filter == $machine){
                $output .= "	<option value='".$machine."' selected='selected'>Machine ".$machine."</option>".$nl;
            } else {
                $output .= "	<option value='".$machine."'>Machine ".$machine."</option>".$nl;
            }
        }
    	$output .= "</select>";
			
		// Set $options in hidden fields to keep the values
		foreach($options as $key => $val) {
			if(isset($val)){
				$output .= "<input type='hidden' id='filter_".$key."' name='".$key."' value='".$val."' />".$nl;
			}
		}
		$output .= "</form>".$nl;
		$output .= "</div>".$nl;
		return $output;
	}
	
	// Get table of atricles
	function getTable($where = '', $range = array(0,20)){
		global $db;
		//$user = getUser($_SESSION['username']);
		
		$startdate = $_POST['startdate'];
		$stopdate =  $_POST['stopdate'];
			
		$cols = array('rolnummer','deelnummer','snijbreedte','snijlengte','bronbreedte','bronlengte','omschrijving','ingevoerd','gewijzigd','verzonden');
		
		// Variabelen definieren
		if(isset($_GET['page'])){	$page = $_GET['page']; } else if (isset($_POST['page'])){ $page = $_POST['page']; }
		if(isset($_GET['order'])){	$order = $_GET['order']; } else if (isset($_POST['order'])){ $order = $_POST['order']; }
		if(isset($_GET['sort'])){	$sort = $_GET['sort']; } else if (isset($_POST['sort'])){ $sort = $_POST['sort']; }
		if (isset($_GET["pg"])) { 	$pg  = $_GET["pg"];	} elseif (isset($_POST['pg'])){	$pg = $_POST['pg'];	} else { $pg=1;	};
		if (isset($_GET["free_search"])) { 	$free_search  = $_GET["free_search"];	} elseif (isset($_POST['free_search'])){	$free_search = $_POST['free_search'];	} else { $free_search='';	};
		if (isset($_GET["machine_filter"])) { 	$machine_filter  = $_GET["machine_filter"];	} elseif (isset($_POST['machine_filter'])){	$machine_filter = $_POST['machine_filter'];	} else { $machine_filter ='';	};
		
		
		// filter formulier waardes in een sessie zetten.
		if(isset($page)) { $_SESSION['page'] = $page; } else { $page = $_SESSION['page']; }
		if(isset($sort)) { $_SESSION['sort'] = $sort; } else { $sort = $_SESSION['sort']; } 	 
		if(isset($pg)) { $_SESSION['pg'] = $pg; } else { $pg = $_SESSION['pg']; }
		if(isset($order)) { $_SESSION['order'] = $order; } else { $order = $_SESSION['order']; } 
		if(isset($free_search)) { $_SESSION['free_search'] = $free_search; } else { $free_search = $_SESSION['free_search']; }
		if(isset($machine_filter)) { $_SESSION['machine_filter'] = $machine_filter; } else { $machine_filter = $_SESSION['machine_filter']; }
		
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
			($free_search ? $where_array[] = '(`persoon` LIKE "%' . $free_search . '%" OR `kwaliteit` LIKE "%' . $free_search . '%"': '');		
		}
		
		
		// van tot mogelijk maken
		if($startdate && $stopdate){
			$where = "WHERE  vanda_machines.datum BETWEEN '".$startdate."' AND '".$stopdate." 23:59:59' "; 
			$title = "Aangepast Overzicht van ".$startdate.' tot '.$stopdate;
		} else {
		  $time = strtotime(date("Y-m-d"));
		  $final = date("Y-m-d", strtotime("-1 month", $time));
		  $where = "WHERE  vanda_machines.datum BETWEEN '".$final."' AND '".date('Y-m-d',$time)." 23:59:59' ";
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
		
		//DEBUG
		//echo $query;
						
		// Put query in the session
		$_SESSION['query'] = $query;
	
		
		// Tel het aantal waardes en bepaal hoeveel paginas er moeten komen
		$result = $db->query($query);
		$total_records = $result->num_rows;
		$total_pages = ceil($total_records / $range[1]);
		$nl = "\n";
		
		
		
		// generate output
		
		//Create excel icon
		$output .= "<form method=\"post\" name=\"csvform\" id=\"csvform\" action=\"pages/csv.php\" enctype=\"multipart/form-data\" />";
		$output .= "<textarea name=\"query\" id=\"query\">";
		$output .= $query;
		$output .= "</textarea>";
		$output .= "<div id=\"csv\" class=\"csv\"><img src=\"images/excel_icon.gif\" style=\"margin-top: -0px;\" /></div>";
		$output .= "</form>";
		
		// Load the filter form
		$output .= $this->loadFilterForm($options);
		
		// Generate table header
			$output .= "<table class=\"data-table\">".$nl;
			$output .= "	<tr>".$nl;
			$output .= "		<th><input type='checkbox' id='machine-select-all' name='machine-select-all'></th>".$nl;
			$output .= "		<th><a href='?".$link_array['page']."&sort=id&order=".$order."&pg=".$pg."'>ID</a></th>".$nl;
			$output .= "		<th><a href='?".$link_array['page']."&sort=persoon&order=".$order."&pg=".$pg."'>Operator</a></th>".$nl;
			$output .= "		<th><a href='?".$link_array['page']."&sort=kwaliteit&order=".$order."&pg=".$pg."'>Kwaliteit</a></th>".$nl;
			$output .= "		<th><a href='?".$link_array['page']."&sort=machine&order=".$order."&pg=".$pg."'>Machine</a></th>".$nl;
			$output .= "		<th><a href='?".$link_array['page']."&sort=datum&order=".$order."&pg=".$pg."'>Datum</a></th>".$nl;
			$output .= "	</tr>".$nl;
		
		
			$records = 0;
			while($row = $result->fetch_object()){
			// Generate table rows
			$output .= "	<tr id='row_".$row->id."' class='data-table-row'>".$nl;
			$output .= "		<td><input class='machine-checkbox' type='checkbox' name='machineid[]' value='".$row->id."' /></td>".$nl;
			$output .= "		<td>".$row->id."</td>".$nl;
			$output .= "		<td>".$row->persoon."</td>".$nl;
			$output .= "		<td>".$row->kwaliteit."</td>".$nl;
			$output .= "		<td> Machine ".$row->machine."</td>".$nl;		
			$output .= "		<td>".date('Y-m-d H:i:s',strtotime($row->datum))."</td>".$nl;		
			$output .= "	</tr>".$nl;
			$records++;
		}
		
		if($records == 0){
			$output .=  '<tr class=\'data-table-row\'>'.$nl;	
			$output .=  '<td colspan="16"><strong>Er zijn geen resultaten om weer te geven</strong></td>'.$nl;
			$output .=  '</tr>'.$nl;
		}
		
		// Close Table
		$output .= "</table>";
		$output .= "<input type=\"button\" id=\"verwijder\" value=\"Verwijder\" />";
		
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
		
		$result->close();
		return $output;
	
}
	
	function Delete($ids){
		global $db;
				
		foreach($ids as $id){		
			$query = "UPDATE `vanda_machines` SET `verwijderd` = '1' WHERE `vanda_machines`.`id` = ".$id;
			if($db->query($query)){
				echo "De registraties zijn verwijderd.";
		    } else {
				echo "FOUT! ".$db->error;
				return 'error';   
		    }
		}
		
		$this->RestoreSession();
	}
	
	
	
	
	// RESET SESSION VALUES	
	function RestoreSession(){
		$name = $_SESSION['username'];
		
		session_destroy();
		session_start();
		$_SESSION['username'] = $name;
		
	}
	
}

?>