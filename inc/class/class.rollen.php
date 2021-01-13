<?php
require_once("class.rollship.php");
require_once("class.db.php");

date_default_timezone_set("Europe/Amsterdam");

// prevent notifications
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", 1);
$nl = "\r\n";

class RollsManager {

	function __construct() {
		$this->db = new DB();
	}

	function loadRoll($id){
		// Load article values in to session;		
		$query = "SELECT * FROM `vanda_rolls` WHERE `rollid` = '".$id."' LIMIT 1";		// load the article.
		$result = $this->db->selectQuery($query);
		return $result[0]; 
	}

	function loadActiveRolls($id) {
		$query = "SELECT * FROM `vanda_rolls` WHERE `rolnummer` = '".$id."' AND `verwijderd` = 0";		// load the article.
		$result = $this->db->selectQuery($query);
		
		return $result ;
	}
	
	function ProcessChildRolls($post){
	   $hidden = array('rol','width','save','username','task','id','bronlengte','bronbreedte','moederrol','custom'); //prevent program parameters to land in table
		
	   $post->ingevoerd = date('Y-m-d H:i:s');		  // Set create date time
	   $post->gewijzigd = date('Y-m-d H:i:s');		  // Set modify date time
	   $post->deelnummer = '00';	   
	   $post->verzonden = '00';
	   $post->verwijderd = '00';

	   foreach($post->rol as $key => $length){
				// Loop through post values and prepare sql query	
			   $count = 0;
			   $fields = '';

			   $post->deelnummer = $key;
			   $post->snijlengte = $length;
		   	   $post->snijbreedte = round($post->width[$key],2);
		   
				// Alleen opslaan alsd de lengte boven de 0 ligt
				$fields = [];
				if($length > 0){
				   foreach($post as $col => $val) {
					  // build array
					  if($val && !in_array($col,$hidden)){
						$fields[$col] = $val;
					  }
				   }

				   // Build query
				   $succeeded = $this->db->insertQuery("vanda_rolls", $fields);
				   //$query = "INSERT INTO `vanda_rolls` SET ".$fields;
				   //echo $query;

					if(!$succeeded){
						echo "<br> PANIEK <BR>".$db->error;
						return 'error';   
					}
				}
	   }
	  
		// Clear session values
			unset($_SESSION['rolnummer'],$_SESSION['task'],$_SESSION['verzonden'],$_SESSION['verwijderd'],$_SESSION['id'],$_SESSION['ingevoerd'],$_SESSION['rol']);
		
	   //$this->PrintRollLabel($post->rolnummer);
	   echo 'success';
	}
	
	function ShipRolls($rolls,$klant){
		global $db;
		
		$shipment = new RollsShipment;
		$id = $shipment->makeShipment($klant);
		
		
		foreach($rolls as $roll){		
			$query = "UPDATE `vanda_rolls` SET `verzonden` = '".$id."' ,`gewijzigd` =  '".date('Y-m-d H:i:s')."' WHERE `vanda_rolls`.`rollid` = ".$roll;
			if($db->query($query)){
		    } else {
				echo "<br> PANIEK <BR>".$db->error;
				return 'error';   
		    }
		}
	}
	
	function UnshipRolls($rolls){
		global $db;
		$shipment = new RollsShipment;
		//$id = $shipment->makeShipment($klant);
		
		foreach($rolls as $roll){		
			// Check of er nog rollen in de zending over blijven..
			$tmp = $this->loadRoll($roll);
			print_r($tmp);
			$shipid = $tmp['verzonden'];
			$query = "UPDATE `vanda_rolls` SET `verzonden` = '0' ,`gewijzigd` =  '".date('Y-m-d H:i:s')."' WHERE `vanda_rolls`.`rollid` = ".$roll;
			if($db->query($query)){
				$query = "SELECT * FROM `vanda_rolls` WHERE `verzonden` = '".$shipid."'";
				$result = $db->query($query);
				if(!$result->num_rows){
					$shipment->deleteShipment($shipid);
				}
		    } else {
				echo "<br> PANIEK <BR>".$db->error;
				return 'error';   
		    }
			
		}
	}
	
	function DeleteRolls($rolls){
		global $db;
				
		foreach($rolls as $roll){		
			$query = "UPDATE `vanda_rolls` SET `verwijderd` = '1' ,`gewijzigd` =  '".date('Y-m-d H:i:s')."' WHERE `vanda_rolls`.`rollid` = ".$roll;
			if($db->query($query)){
		    } else {
				echo "FOUT!".$db->error;
				return 'error';   
		    }
		}
	}
	
	
	function getEAN(){
		// Determine user based on session and db.
		global $db;
		$query = "SELECT ean FROM `vanda_rolls` ORDER BY ingevoerd DESC LIMIT 1; ";
		if($result = $this->db->selectQuery($query)){
			$result = $result[0];
		} else {
			echo "FOUT! ".$db->error;
			return 'error';   
		}
		$ean = ($_SESSION['ean'] ?  $_SESSION['ean'] : $result->ean);
		return $ean;
	}
	
	
	function getRollForm (){
		global $db;
		
		$nl = "\n";
			
		// Check if edit and id, load the correct values for the desired id.	
		//if($_SESSION['task'] == 'edit' && $id ){
		//	$this->LoadArticle($id);		// Load article values.
		//} elseif ($_SESSION['task'] == 'add') {
		//	$this->RestoreSession();		// Clear session
		//	$_SESSION['task'] = 'add';		// Set add.
		//} else {
		//	echo 'Fout: er is geen taak of id aangeleverd';	// Throw error
		//	exit();
		//}		
	
		// Generate output
		$output = '<div class="roll-form-div">'.$nl;
		$output .= '<form class="rollform" action="" id="rollform" enctype="multipart/form-data" method="post">';
		$output .= '<div id="rollform-part1"><ul id="rollformlist">'.$nl;
		$output .= '<li><label for="rolnummer"><span>Rolnummer:</span><input id="input_rolnummer" name="rolnummer" type="text" value="'.($_SESSION['rolnummer'] ?  $_SESSION['rolnummer'] : '').'" required/></label></li>'.$nl;
		$output .= '<li><label for="omschrijving"><span>Omschrijving:</span><input id="input_omschrijving" name="omschrijving" type="text" value="'.($_SESSION['omschrijving'] ?  $_SESSION['omschrijving'] : 'Superflex').'" required/></label></li>'.$nl;
		$output .= '<li><label for="bronlengte"><span>Lengte:</span><input id="input_bronlengte" name="bronlengte" type="text" value="'.($_SESSION['bronlengte'] ?  $_SESSION['bronlengte'] : '').'" required/></label></li>'.$nl;
		$output .= '<li><label for="breedte"><span>Breedte:</span><input id="input_bronbreedte" name="bronbreedte" type="text" value="'.($_SESSION['bronbreedte'] ?  $_SESSION['bronbreedte'] : '4.12').'" required/></label></li>'.$nl;
			
		$output .= '<li><label for="snijlengte"><span>Snijengte:</span><input id="input_snijlengte" name="snijlengte" type="text" value="'.($_SESSION['snijlengte'] ?  $_SESSION['snijlengte'] : '40').'" required/></label></li>'.$nl;
		
		$output .= '<li><label for="custom"><span>Afwijkend:</span><input id="input_custom" name="custom" type="checkbox" value="'.($_SESSION['custom'] ?  $_SESSION['custom'] : '1').'"/></label></li>'.$nl;
		$output .= '<li><label for="snijbreedte"><span>Snijbreedte:</span><div id="snijbreedtes"><input id="input_snijbreedte" class="snijbreedte" name="snijbreedte[]" type="text" value="'.($_SESSION['snijbreedte[0]'] ?  $_SESSION['snijbreedte[0]'] : '2.06').'" required/></div></label></li>'.$nl;
		
		$output .= '<li><label for="ean"><span>EAN:</span><input id="input_ean" name="ean" type="text" value="'.$this->getEAN().'" /></label></li>'.$nl;
			
		$output .= '<li><label for="kleur"><span>Kleur:</span><input id="input_kleur" name="kleur" type="text" value="'.($_SESSION['kleur'] ?  $_SESSION['kleur'] : '').'" /></label></li>'.$nl;
		$output .= '<li><label for="backing"><span>Backing:</span><input id="input_backing" name="backing" type="text" value="'.($_SESSION['backing'] ?  $_SESSION['backing'] : 'xx').'" /></label></li>'.$nl;
		$output .= '<li><label for="referentie"><span>Referentie:</span><input id="input_referentie" name="referentie" type="text" value="'.($_SESSION['referentie'] ?  $_SESSION['referentie'] : '').'" /></label></li>'.$nl;

		//$output .= '<li><label for="verzonden"><span>Verzonden:</span><input id="input_verzonden" name="verzonden" type="text" value="'.($_SESSION['verzonden'] ?  $_SESSION['verzonden'] : '').'" /></label></li>'.$nl;
		//$output .= '<li><label for="verwijderd"><span>Verwijderd:</span><input id="input_verwijderd" name="verwijderd" type="text" value="'.($_SESSION['verwijderd'] ?  $_SESSION['verwijderd'] : '').'" /></label></li>'.$nl;
		
		$output .= '<li class="100-wide"><input type="button" name="verstuur" id="input_verstuur" value="Volgende" /></li>'.$nl;
		$output .= '<input id="input_task" name="task" type="hidden" value="generate" />'.$nl;
		
		$output .= '<input id="input_id" name="verzonden" value="'.($_SESSION['verzonden'] ?  $_SESSION['verzonden'] : '0').'" type="hidden" />'.$nl;
		$output .= '<input id="input_id" name="verwijderd" value="'.($_SESSION['verwijderd'] ?  $_SESSION['verwijderd'] : '0').'" type="hidden" />'.$nl;
		$output .= '<input id="input_id" name="id" value="'.($_SESSION['id'] ?  $_SESSION['id'] : '').'" type="hidden" />'.$nl;
		$output .= '<input id="input_ingevoerd" name="ingevoerd" value="'.($_SESSION['ingevoerd'] ?  $_SESSION['ingevoerd'] : '').'" type="hidden" />'.$nl;
		
		$output .= '<br />'.$nl;
		$output .= '</div><div class="clr"></div>'.$nl;
		$output .= '<div id="childrollform"></div>'.$nl;
		$output .= '</ul></form><div>
        <iframe id="frame"></iframe>
    </div></div>'.$nl;
		
		return $output;
	}
	
	function getRollEditForm (){
		global $db;
		
		$nl = "\n";
		// Generate output
		$output .= '<div class="roll-editform-div">'.$nl;
		$output .= '<form class="editrollform" action="" id="editrollform" enctype="multipart/form-data" method="post">';
		$output .= '<div id="rollform-edit"><ul id="rollformlist">'.$nl;
		$output .= '<li><label for="rolnummer"><span>Rolnummer:</span><input id="input_rolnummer" name="rolnummer" type="text" value="'.($_SESSION['rolnummer'] ?  $_SESSION['rolnummer'] : '').'" required/></label></li>'.$nl;
		$output .= '<li><label for="deelnummer"><span>Deelnummer:</span><input id="input_deelnummer" name="deelnummer" type="text" value="'.($_SESSION['deelnummer'] ?  $_SESSION['deelnummer'] : '').'" required/></label></li>'.$nl;
		$output .= '<li><label for="omschrijving"><span>Omschrijving:</span><input id="input_omschrijving" name="omschrijving" type="text" value="'.($_SESSION['omschrijving'] ?  $_SESSION['omschrijving'] : '').'" required/></label></li>'.$nl;
		$output .= '<li><label for="snijlengte"><span>Snijengte:</span><input id="input_snijlengte" name="snijlengte" type="text" value="'.($_SESSION['snijlengte'] ?  $_SESSION['snijlengte'] : '').'" required/></label></li>'.$nl;
		$output .= '<li><label for="snijbreedte"><span>Snijreedte:</span><input id="input_snijbreedte" name="snijbreedte[]" type="text" value="'.($_SESSION['snijbreedte'] ?  $_SESSION['snijbreedte'] : '').'" required/></label></li>'.$nl;
		$output .= '<li><label for="ean"><span>EAN:</span><input id="input_ean" name="ean" type="text" value="'.($_SESSION['ean'] ?  $_SESSION['ean'] : '').'" /></label></li>'.$nl;
		$output .= '<li><label for="kleur"><span>Kleur:</span><input id="input_kleur" name="kleur" type="text" value="'.($_SESSION['kleur'] ?  $_SESSION['kleur'] : '').'" /></label></li>'.$nl;
		$output .= '<li><label for="backing"><span>Backing:</span><input id="input_backing" name="backing" type="text" value="'.($_SESSION['backing'] ?  $_SESSION['backing'] : '').'" /></label></li>'.$nl;
		$output .= '<li><label for="verzonden"><span>Verzonden:</span><input id="input_verzonden" name="verzonden" type="text" value="'.($_SESSION['verzonden'] ?  $_SESSION['verzonden'] : '0').'" /></label></li>'.$nl;
		$output .= '<li><label for="referentie"><span>Referentie:</span><input id="input_referentie" name="referentie" type="text" value="'.($_SESSION['referentie'] ?  $_SESSION['referentie'] : '').'" /></label></li>'.$nl;
		$output .= '<li><label for="verwijderd"><span>Verwijderd:</span><input id="input_verwijderd" name="verwijderd" type="text" value="'.($_SESSION['verwijderd'] ?  $_SESSION['verwijderd'] : '0').'" /></label></li>'.$nl;
		$output .= '<li class="100-wide"><input type="button" name="terug" id="input_terug" value="Terug" /><input type="button" name="verstuur" id="input_verstuur" value="Opslaan" /></li>'.$nl;
		$output .= '<input id="input_task" name="task" type="hidden" value="edit" />'.$nl;
		$output .= '<input id="input_id" name="rollid" value="'.($_SESSION['rollid'] ?  $_SESSION['rollid'] : '').'" type="hidden" />'.$nl;
		$output .= '<br />'.$nl;
		$output .= '</div><div class="clr"></div>'.$nl;
		$output .= '</ul></form></div>'.$nl;

		return $output;
	}
	
	
	function generateChildRolls ($rolnummer,$post){
		global $db;

		//$output .= '<form class="childrollform" action="pages/process-rollen.php" id="childrollform" enctype="multipart/form-data" method="post"><ul id="childrollformlist">'.$nl;
		// bereken hoeveel rollen er uit kunnen
		
		$sourcelength = (floatval($post->bronlengte));
		
		
		
		// Als er een variabele sbijbreedte is opgegeven hier rekening mee houden.
		if($post->custom){
			$colums = count($post->snijbreedte);
		} else {
			$colums = round(floatval($post->bronbreedte) / floatval($post->snijbreedte[0]));
		}
		
	
		
		// Berekenen hoeveel volle rollen er gemaakt kunnen worden.
		$vollerollen = floor(floatval($sourcelength) / floatval($post->snijlengte));
		$volledelen = $vollerollen * $colums;
		
		// Bereken de restlengte voor de laatste rol.
		$restlengte = floatval($sourcelength) - (floatval($vollerollen) * floatval($post->snijlengte));
		$snijdelen = $colums;
		
		$rows = $volledelen + $snijdelen;		
		$length = $post->snijlengte;
		$lengthsum = 0;

		
		//DEBUGGING
		//print_r($post);
		//echo "<hr>";
		echo "Snijbreedte: ".floatval($post->snijbreedte[0])."<br />";
		echo "Bereken delen: ".floatval($sourcelength)."/".floatval($post->snijlengte)."<br />";
		echo "Calculating: ".floatval($post->bronbreedte)."/".floatval($post->snijbreedte[0])."<br />";
		echo "Colommen: ".$colums."<br />";
		echo "Volle Delen: ".$volledelen."<br />";
		echo "Snijdelen: ".$snijdelen."<br />";
		
		//echo '<h2>Rolnummer: ' . $rolnummer . "word berekend op " . ($colums - 1) . " keer in de lengte snijden om de ".floatval($post->snijlengte)." wat resulteert in  " . $rows . " rollen.</h2>".$nl ;
			
		$output .= '<ul id="chilldrolllist" />';
		$colum = 0;
		// Loop through aantal volle
		for($i = 1; $i <= $volledelen; $i++){
			
			if($colum >= $colums){
			 $colum = 0;	
			}
			
			$childnumber = sprintf("%'.02d\n", $i);		
			$output .= '<li><label for="rolnummer-'.$childnumber.'"><span>Rolnummer '.$rolnummer.$childnumber.' lengte:</span><input id="'.$rolnummer.$childnumber.'" name="rol['.$childnumber.']" type="text" value="'.$length.'" required/> Breedte: <input id="'.$rolnummer.$childnumber.'" name="width['.$childnumber.']" type="text" value="'.$post->snijbreedte[$colum].'" required/> totaal '.$lengthsum.'mtr.</label></li>'.$nl;
			$lengthsum = $lengthsum + $length;
			if($colums > 2){
				$colum++;
			}
		}
		// Creeer het aantal kortere delen
		for($j = 1; $j <= $snijdelen; $j++){
			if($restlengte >0){		//kijken of restlengte geen 0 is
				if($colum >= $colums){ // Loop throug custom width
				 $colum = 0;	
				}
				$childnumber = sprintf("%'.02d\n", $i);	
				$output .= '<li><label for="rolnummer-'.$childnumber.'"><span>Rolnummer '.$rolnummer.$childnumber.' lengte:</span><input id="'.$rolnummer.$childnumber.'" name="rol['.$childnumber.']" type="text" value="'.$restlengte.'" required/> Breedte: <input id="'.$rolnummer.$childnumber.'" name="width['.$childnumber.']" type="text" value="'.$post->snijbreedte[$colum].'" required/> totaal '.($lengthsum + $restlengte).' mtr.</label></li>'.$nl;
				$lengthsum = $lengthsum + $restlengte;
				$i++;
				if($colums > 2){
					$colum++;
				}
			}
		}
		$output .= '<li class="100-wide"><input type="button" name="vorige" id="childroll-vorige" value="Vorige" /><input type="submit" name="save" id="input_save" value="Opslaan" disabled="true"/></li>'.$nl;
		$output .= '</ul><div class="clr"></div>'.$nl;
		
		return $output;
	}
	
	
	
	
	function loadFilterForm($options){
		if (isset($_GET["free_search"])) { 	$free_search  = $_GET["free_search"];	} elseif (isset($_POST['free_search'])){	$free_search = $_POST['free_search'];	} else { $free_search='';	};		
		//$user = getUser($_SESSION['username']);
		
		$output .= "<div id='filter_form_div'>".$nl;
		$output .= "<form id='filter_form' action='index.php?page=rolltable' method='post'>".$nl;
		$output .= "<label for='free_search' class='left'>Zoek:</label><input type='text' name='free_search' id='input_free_search' value='".$free_search."' class='left'/><input type='submit' name='submit_search' id='submit_free_search' value='Zoek' class='left' /><input type='button' id='reset_free_form' value='Herstel' class='left'/>".$nl;
		
		// Check if user has rights
		//if($user->level == 1){
			//$output .= "<label for='voorraadLijst' class='left'>Voorraadlijst: </label><a href='pages/generate_balans.php' target='_blank'><image src='images/pdf.png' class='left' name='voorraadLijst'/></a>".$nl;
		//}
		
		//Show Locatie dropdown.
		//$output .= $this->BuildLocationDropdown();
		$output .= '<Label for="viewtype"><span>Weergave:</label><select class="viewtype" name="viewtype">'.$nl;
		$output .= '<option value="0" '.($_SESSION['viewtype'] == '0' ? 'selected="selected"' : '').'>Voorraad</option>'.$nl;
		$output .= '<option value="1" '.($_SESSION['viewtype'] == '1' ? 'selected="selected"' : '').'>Verzonden</option>'.$nl;
		$output .= '<option value="2" '.($_SESSION['viewtype'] == '2' ? 'selected="selected"' : '').'>Alles</option>'.$nl;
		$output .= '</select>'.$nl;
			
		// Set $options in hidden fields to keep the values
		foreach($options as $key => $val) {
			if(isset($val)){
				$output .= "<input type='hidden' id='filter_".$key."' name='".$key."' value='".$val."' />".$nl;
			}
		}
		$output .= "</form>".$nl;
		$output .= "</div>".$nl;
		echo $output;
	}
	
	// Get table of atricles
	function getTable($where = '', $range = array(0,20)){
		global $db;
		//$user = getUser($_SESSION['username']);
		$viewtype = isset($_SESSION['viewtype']) ? $_SESSION['viewtype'] : null;
			
		$selected_roll = isset($_SESSION['selected_roll']) ? $_SESSION['selected_roll'] : null;
		unset($_SESSION['selected_roll']);
		
		$cols = array('rolnummer','deelnummer','snijbreedte','snijlengte','bronbreedte','bronlengte','omschrijving','ingevoerd','gewijzigd','verzonden');
		
		// Variabelen definieren
		if(isset($_GET['page'])){	$page = $_GET['page']; } else if (isset($_POST['page'])){ $page = $_POST['page']; }
		if(isset($_GET['order'])){	$order = $_GET['order']; } else if (isset($_POST['order'])){ $order = $_POST['order']; }
		if(isset($_GET['sort'])){	$sort = $_GET['sort']; } else if (isset($_POST['sort'])){ $sort = $_POST['sort']; }
		if (isset($_GET["pg"])) { 	$pg  = $_GET["pg"];	} elseif (isset($_POST['pg'])){	$pg = $_POST['pg'];	} else { $pg=1;	};
		if (isset($_GET["viewtype"])) { 	$viewtype  = $_GET["viewtype"];	} elseif (isset($_POST['viewtype'])){	$viewtype = $_POST['viewtype'];	} else { /*doe niks*/	};
		if (isset($_GET["free_search"])) { 	$free_search  = $_GET["free_search"];	} elseif (isset($_POST['free_search'])){	$free_search = $_POST['free_search'];	} else { $free_search='';	};
		
		
		// filter formulier waardes in een sessie zetten.
		if(isset($page)) { $_SESSION['page'] = $page; } else { $page = $_SESSION['page']; }
		if(isset($sort)) { $_SESSION['sort'] = $sort; } else { $sort = $_SESSION['sort']; } 	 
		if(isset($pg)) { $_SESSION['pg'] = $pg; } else { $pg = $_SESSION['pg']; }
		if(isset($order)) { $_SESSION['order'] = $order; } else { $order = $_SESSION['order']; } 
		if(isset($viewtype)) { $_SESSION['viewtype'] = $viewtype; } else { $viewtype = $_SESSION['viewtype']; }
		if(isset($free_search)) { $_SESSION['free_search'] = $free_search; } else { $free_search = $_SESSION['free_search']; }
		
		// Set aantal resultaten per pagina
		$range[1] = 5000;	
		$range[0] = ($pg-1) * $range[1];
		
		if(!in_array($sort,$cols)){
			$sort = 'rolnummer';
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
			($free_search ? $where_array[] = '(`rolnummer` LIKE "%' . $free_search . '%" OR `ingevoerd` LIKE "%' . $free_search . '%" OR `omschrijving` LIKE "%' . $free_search . '%" OR `kleur` LIKE "%' . $free_search . '%" OR `backing` LIKE "%' . $free_search . '%") ': '');		
		}
		
		
		// Hide verwijderde items from dashboard
		if($viewtype == "1"){ 
		  $where_array[] = '`verzonden` != "0" ';
		} elseif($viewtype == "2") {
		  // doe niets
		} else {
		  $where_array[] = '`verzonden` = "0" ';
		}
		
		// Alleen niet verwijderde rollen laten zien
		$where_array[] = '`verwijderd` = "0" ';
		
		//Build the where clause
		foreach($where_array as $part){
			if($part && $part != ''){
				$where .= ($where ? ' AND ' : ' WHERE ').$part;
			}
		}
			
		//Query om te tellen hoeveel waarden er in de tabel zitten.
		$query = "SELECT * FROM vanda_rolls ".$where.$orderby;
		
		//echo $query;
						
		// Put query in the session
		$_SESSION['query'] = $query;
		
		// Tel het aantal waardes en bepaal hoeveel paginas er moeten komen
		$result = $this->db->selectQuery($query);
		$total_records = $result->num_rows;
		$total_pages = ceil($total_records / $range[1]);
		$nl = "\n";
		
		
		
		// generate output
		// Load the filter form
		echo $this->loadFilterForm($options);
		
		// Generate table header
		$output .= "<form name='shipform' id='roll-shipform' action='pages/process-rollen.php' method='post'>";
		$output .= "<table class=\"data-table\">".$nl;
		$output .= "	<tr>".$nl;
		$output .= "		<th><input type='checkbox' id='roll-select-all' name='roll-select-all'></th>".$nl;
		$output .= "		<th>Label</th>".$nl;
		$output .= "		<th><a href='?".$link_array['page']."&sort=rolnummer&order=".$order."&pg=".$pg."'>Rolnummer</a></th>".$nl;
		$output .= "		<th id='ordernummer_col'><a href='?".$link_array['page']."&sort=deelnummer&order=".$order."&pg=".$pg."'>Deelnummer</a></th>".$nl;
		$output .= "		<th><a href='?".$link_array['page']."&sort=snijbreedte&order=".$order."&pg=".$pg."'>Snijbreedte</a></th>".$nl;
		$output .= "		<th><a href='?".$link_array['page']."&sort=snijlengte&order=".$order."&pg=".$pg."'>Snijlengte</a></th>".$nl;
		$output .= "		<th><a href='?".$link_array['page']."&sort=omschrijving&order=".$order."&pg=".$pg."'>Omschrijving</a></th>".$nl;
		$output .= "		<th><a href='?".$link_array['page']."&sort=kleur&order=".$order."&pg=".$pg."'>Kleur</a></th>".$nl;
		$output .= "		<th><a href='?".$link_array['page']."&sort=backing&order=".$order."&pg=".$pg."'>Backing</a></th>".$nl;
		$output .= "		<th><a href='?".$link_array['page']."&sort=referentie&order=".$order."&pg=".$pg."'>Referentie</a></th>".$nl;
		$output .= "		<th><a href='?".$link_array['page']."&sort=ingevoerd&order=".$order."&pg=".$pg."'>Ingevoerd</a></th>".$nl;
		$output .= "		<th><a href='?".$link_array['page']."&sort=gewijzigd&order=".$order."&pg=".$pg."'>Gewijzigd</a></th>".$nl;
		$output .= "		<th><a href='?".$link_array['page']."&sort=verzonden&order=".$order."&pg=".$pg."'>verzonden</a></th>".$nl;
		$output .= "	</tr>".$nl;
		
		
		$records = 0;
		foreach ($result as $row) {
			$row->omschrijving = (isset($row->omschrijving) && trim($row->omschrijving) != '' ? $row->omschrijving : '-');
									
			// Generate table rows
			$output .= "	<tr id='row_".$row->rollid."' class='data-table-row'>".$nl;
			$output .= "		<td><input class='roll-checkbox' type='checkbox' name='rollid[]' value='".$row->rollid."' /></td>".$nl;
		    $output .= "		<td><a href='pages/generate/generate_rol.php?rolnummer=".$row->rolnummer."' target='_blanc'><img src='images/printer.png' height='17'></a>";
			$output .= "		<td>".$row->rolnummer."</td>".$nl;
			$output .= "		<td>".$row->deelnummer."</td>".$nl;
			$output .= "		<td>".$row->snijbreedte."</td>".$nl;
			$output .= "		<td>".$row->snijlengte."</td>".$nl;		
			$output .= "		<td><a href='index.php?page=editroll&id=".$row->rollid."' >".$row->omschrijving."</a></td>".$nl;
			$output .= "		<td>".$row->kleur."</td>".$nl;	
			$output .= "		<td>".$row->backing."</td>".$nl;
			$output .= "		<td>".$row->referentie."</td>".$nl;
			$output .= "		<td>".date('Y-m-d',strtotime($row->ingevoerd))."</td>".$nl;
			$output .= "		<td>".date('Y-m-d',strtotime($row->gewijzigd))."</td>".$nl;
			$output .= "		<td>".$row->verzonden."</td>".$nl;
				
				
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
		$output .= "<input type='button' name='verwijder' id='input_verwijder' value='Verwijder' />";
		$output .= "<input type='hidden' name='task' id='task' value='verzend' />";
		$output .= "<label for='klant'>Klant: </label><input type='text' name='klant' id='roll-klant' value=''>";
		$output .= "<input type='button' name='haalterug' id='input_haalterug' value='Haal Terug' />";
		$output .= "<input type='submit' name='verzend' value='Verzend'/>";
		$output .= "</form>";
		
		
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
		
		//$result->close();
		return $output;
	
}
	
	
	// RESET SESSION VALUES	
	function RestoreSession(){
		$name = $_SESSION['username'];
		$referentie = $_SESSION['referentie'];
		session_destroy();
		session_start();
		$_SESSION['username'] = $name;
		$_SESSION['referentie'] = $referentie;
	}
	
}

?>