<?php
require_once("class.rollship.php");
require_once("class.db.php");

date_default_timezone_set("Europe/Amsterdam");

// prevent notifications
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", 1);

class RollsManager {
	public $db;

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

	function getRollsForShipment($shipmentId) {
		$query = "SELECT * FROM `vanda_rolls` WHERE `verzonden` = ".$shipmentId;
		$result = $this->db->selectQuery($query);
		return $result;
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
						echo "<br> PANIEK <BR>";
						return 'error';   
					}
				}
	   }
	  
		// Clear session values
			unset($_SESSION['rolnummer'],$_SESSION['task'],$_SESSION['verzonden'],$_SESSION['verwijderd'],$_SESSION['id'],$_SESSION['ingevoerd'],$_SESSION['rol']);
		
	   //$this->PrintRollLabel($post->rolnummer);
	   echo 'success';
	}
	
	function UpdateRoll($data, $id) {
		$this->db->updateQuery("vanda_rolls", $data, "rollid='".$id."' LIMIT 1");
		return true;
	}

	function ShipRolls($rolls,$klant){
		
		$shipment = new RollsShipment;
		$id = $shipment->makeShipment($klant);
		
		
		foreach($rolls as $roll) {
			$data = [
				"verzonden" => $id,
				"gewijzigd" =>date('Y-m-d H:i:s')
			];
			$where = "`vanda_rolls`.`rollid` = ".$roll;
			
			if($this->db->updateQuery("vanda_rolls", $data, $where)){
		    } else {
				echo "<br> PANIEK <BR>";
				return 'error';   
		    }
		}
	}
	
	function UnshipRolls($rolls){
		$shipment = new RollsShipment;
		//$id = $shipment->makeShipment($klant);
		
		foreach($rolls as $roll){		
			// Check of er nog rollen in de zending over blijven..
			$tmp = $this->loadRoll($roll);
			print_r($tmp);
			$shipid = $tmp['verzonden'];

			$data = [
				"verzonden" => 0,
				"gewijzigd" => date('Y-m-d H:i:s')
			];
			$where = "`vanda_rolls`.`rollid` = ".$roll;


			/*	DIT STUK WERKT NIET MEER  TODO UNSHIP WERKEND MAKEN */

			if($this->db->updateQuery("vanda_rolls", $data, $where)){
				//$query = "SELECT * FROM `vanda_rolls` WHERE `verzonden` = '".$shipid."'";
				$result = $this->getRollsForShipment($shipid);
				if(count($result) == 0){
					$shipment->deleteShipment($shipid);
				}
		    } else {
				echo "<br> PANIEK <BR>";
				return 'error';   
		    }

		}
	}
	
	function DeleteRolls($rolls){
				
		foreach($rolls as $roll){
			$data = [
				"verwijderd" => 1,
				"gewijzigd" => date('Y-m-d H:i:s')
			];
			$where = "`vanda_rolls`.`rollid` = ".$roll;
			if ($this->db->updateQuery("vanda_rolls", $data, $where)){
		    } else {
				echo "FOUT!";
				return 'error';   
		    }
		}
	}
	
	
	function getEAN(){
		// Determine user based on session and db.
		$query = "SELECT ean FROM `vanda_rolls` ORDER BY ingevoerd DESC LIMIT 1; ";
		if($result = $this->db->selectQuery($query)){
			$result = $result[0];
		} else {
			echo "FOUT! ";
			return 'error';   
		}
		$ean = ($_SESSION['ean'] ?  $_SESSION['ean'] : $result->ean);
		return $ean;
	}
	
	
	function getRollForm (){	
		#Define variables
		// Check if keys exist in $_SESSION and provide default values if they don't
		$rolnummer = isset($_SESSION['rolnummer']) ? $_SESSION['rolnummer'] : '';
		$omschrijving = isset($_SESSION['omschrijving']) ? $_SESSION['omschrijving'] : 'Superflex';
		$bronlengte = isset($_SESSION['bronlengte']) ? $_SESSION['bronlengte'] : '';
		$bronbreedte = isset($_SESSION['bronbreedte']) ? $_SESSION['bronbreedte'] : '4.12';
		$snijlengte = isset($_SESSION['snijlengte']) ? $_SESSION['snijlengte'] : '40';
		$custom = isset($_SESSION['custom']) ? $_SESSION['custom'] : '1';
		$snijbreedte = isset($_SESSION['snijbreedte[0]']) ? $_SESSION['snijbreedte[0]'] : '2.06';
		$kleur = isset($_SESSION['kleur']) ? $_SESSION['kleur'] : '';
		$backing = isset($_SESSION['backing']) ? $_SESSION['backing'] : 'xx';
		$referentie = isset($_SESSION['referentie']) ?  $_SESSION['referentie'] : '';
		$verzonden = isset($_SESSION['verzonden']) ?  $_SESSION['verzonden'] : '0';
		$verwijderd = isset($_SESSION['verwijderd']) ?  $_SESSION['verwijderd'] : '0';
		$id = isset($_SESSION['id']) ?  $_SESSION['id'] : '';
		$ingevoerd = isset($_SESSION['ingevoerd']) ?  $_SESSION['ingevoerd'] : '';

		// Generate output
		$output = '<div class="roll-form-div">';
			$output .= '<form class="rollform" action="" id="rollform" enctype="multipart/form-data" method="post">';
				$output .= '<div id="rollform-part1">';
					$output .= '<ul id="rollformlist">';
						$output .= '<li>
										<label for="input_rolnummer">Rolnummer</label>
										<input id="input_rolnummer" name="rolnummer" type="text" value="'.$rolnummer.'" required/>
									</li>';
						$output .= '<li>
										<label for="input_omschrijving">Omschrijving</label>
										<input id="input_omschrijving" name="omschrijving" type="text" value="'.$omschrijving.'" required/>
									</li>';
						$output .= '<li>
										<label for="input_bronlengte">Lengte</label>
										<input id="input_bronlengte" name="bronlengte" type="text" value="'.$bronlengte.'" required/>
									</li>';
						$output .= '<li>
										<label for="input_bronbreedte">Breedte</label>
										<input id="input_bronbreedte" name="bronbreedte" type="text" value="'.$bronbreedte.'" required/>
									</li>';
						$output .= '<li>
										<label for="input_snijlengte">Snijlengte</label>
										<input id="input_snijlengte" name="snijlengte" type="text" value="'.$snijlengte.'" required/>
									</li>';
						$output .= '<li>
										<label for="input_custom">Snijbreedte Afwijkend</label>
										<input id="input_custom" name="custom" type="checkbox" value="'.$custom.'"/>
									</li>';
						$output .= '<li class="snijbreedte-li">
										<label for="input_snijbreedte">Snijbreedte</label>
										<div id="snijbreedtes"><input id="input_snijbreedte" class="snijbreedte" name="snijbreedte[]" type="text" value="'.$snijbreedte.'" required/></div>
									</li>';
						//Locatie was EAN in vorige versies
						$output .= '<li>
										<label for="input_ean">Locatie</label>
										<input id="input_ean" name="ean" type="text" value="'.$this->getEAN().'" />
									</li>';
						$output .= '<li>
										<label for="input_kleur">Kleur</label>
										<input id="input_kleur" name="kleur" type="text" value="'.$kleur.'" />
									</li>';
						$output .= '<li>
										<label for="input_backing">Backing</label>
										<input id="input_backing" name="backing" type="text" value="'.$backing.'" />
									</li>';
						$output .= '<li>
										<label for="input_referentie">Referentie</label>
										<input id="input_referentie" name="referentie" type="text" value="'.$referentie.'" />
									</li>';
					$output .= '</table>';
				$output .= '<input type="button" name="verstuur" id="input_verstuur" value="Volgende" />';
				$output .= '<input id="input_task" name="task" type="hidden" value="generate" />';
				
				$output .= '<input id="input_id" name="verzonden" value="'.$verzonden.'" type="hidden" />';
				$output .= '<input id="input_id" name="verwijderd" value="'.$verwijderd.'" type="hidden" />';
				$output .= '<input id="input_id" name="id" value="'.$id.'" type="hidden" />';
				$output .= '<input id="input_ingevoerd" name="ingevoerd" value="'.$ingevoerd.'" type="hidden" />';
				
				$output .= '</div><div class="clr"></div>';
				$output .= '<div id="childrollform"></div>';
				$output .= '</div>';
			$output .= '</form>';
			$output .= '<div><iframe id="frame"></iframe></div>';
		$output .= '</div>';
		
		
		$output .= '</ul></form></div>';
		
		return $output;
	}
	
	function getRollEditForm (){
		// Generate output
		$output ="";
		$output .= '<div class="roll-editform-div">';
		$output .= '<form class="editrollform" action="" id="editrollform" enctype="multipart/form-data" method="post">';
		$output .= '<div id="rollform-edit">';
		$output .= '<table id="rollformlist" style="margin: auto;">';
		$output .= '<tr>
						<td>Rolnummer</td>
						<td><input id="input_rolnummer" name="rolnummer" type="text" value="'.($_SESSION['rolnummer'] ?  $_SESSION['rolnummer'] : '').'" required/></td>
					</tr>';
		$output .= '<tr>
						<td>Deelnummer</td>
						<td><input id="input_deelnummer" name="deelnummer" type="text" value="'.($_SESSION['deelnummer'] ?  $_SESSION['deelnummer'] : '').'" required/></td>
					</tr>';
		$output .= '<tr>
						<td>Rolnummer</td>
						<td><input id="input_rolnummer" name="rolnummer" type="text" value="'.($_SESSION['rolnummer'] ?  $_SESSION['rolnummer'] : '').'" required/></td>
					</tr>';
		$output .= '<tr>
						<td>Omschrijving</td>
						<td><input id="input_omschrijving" name="omschrijving" type="text" value="'.($_SESSION['omschrijving'] ?  $_SESSION['omschrijving'] : '').'" required/></td>
					</tr>';
		$output .= '<tr>
						<td>Snijlengte</td>
						<td><input id="input_snijlengte" name="snijlengte" type="text" value="'.($_SESSION['snijlengte'] ?  $_SESSION['snijlengte'] : '').'" required/></td>
					</tr>';
		$output .= '<tr>
						<td>Snijbreedte</td>
						<td><input id="input_snijbreedte" name="snijbreedte" type="text" value="'.($_SESSION['snijbreedte'] ?  $_SESSION['snijbreedte'] : '').'" required/></td>
					</tr>';
		//Locatie was EAN in vorige versies
		$output .= '<tr>
						<td>Locatie</td>
						<td><input id="input_ean" name="ean" type="text" value="'.($_SESSION['ean'] ?  $_SESSION['ean'] : '').'" /></td>
					</tr>';
		$output .= '<tr>
						<td>Kleur</td>
						<td><input id="input_kleur" name="kleur" type="text" value="'.($_SESSION['kleur'] ?  $_SESSION['kleur'] : '').'" /></td>
					</tr>';
		$output .= '<tr>
						<td>Backing</td>
						<td><input id="input_backing" name="backing" type="text" value="'.($_SESSION['backing'] ?  $_SESSION['backing'] : '').'" /></td>
					</tr>';
		$output .= '<tr>
						<td>Verzonden</td>
						<td><input id="input_verzonden" name="verzonden" type="text" value="'.($_SESSION['verzonden'] ?  $_SESSION['verzonden'] : '0').'" /></td>
					</tr>';
		$output .= '<tr>
						<td>Referentie</td>
						<td><input id="input_referentie" name="referentie" type="text" value="'.($_SESSION['referentie'] ?  $_SESSION['referentie'] : '').'" /></td>
					</tr>';
		$output .= '<tr>
						<td>Verwijderd</td>
						<td><input id="input_verwijderd" name="verwijderd" type="text" value="'.($_SESSION['verwijderd'] ?  $_SESSION['verwijderd'] : '0').'" /></td>
					</tr>';
		$output .=  '</table>';
		$output .= '<input type="button" name="terug" id="input_terug" value="Terug" /><input type="button" name="verstuur" id="input_verstuur" value="Opslaan" /></li>';
		$output .= '<input id="input_task" name="task" type="hidden" value="edit" />';
		$output .= '<input id="input_id" name="rollid" value="'.($_SESSION['rollid'] ?  $_SESSION['rollid'] : '').'" type="hidden" />';
		$output .= '<br />';
		$output .= '</div><div class="clr"></div>';
		$output .= '</form></div>';

		return $output;
	}
	
	
	function generateChildRolls ($rolnummer,$post){
		
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
		$output = '<table id="chilldrolllist" class="childrolllist-input data-table">';
		$output .= '<tr><th class="ui-corner-top" colspan="4">De rol word op de volgende manier gesneden.</th></tr>';
		$output .= '<tr>
						<td>Snijbreedte</td>
						<td>'.floatval($post->snijbreedte[0]).'</td>
						<td></td>
						<td></td>
					</tr>';
		$output .= '<tr>
						<td>Bereken delen</td>
						<td colspan="3">'.floatval($sourcelength).'/'.floatval($post->snijlengte).'</td>
					</tr>';
		$output .= '<tr>
						<td>Calculating</td>
						<td colspan="3">'.floatval($post->bronbreedte).'/'.floatval($post->snijbreedte[0]).'</td>
					</tr>';
		$output .= '<tr>
						<td>Colommen</td>
						<td colspan="3">'.$colums.'</td>
					</tr>';
		$output .= '<tr>
						<td>Volle Delen</td>
						<td colspan="3">'.$volledelen.'</td>
					</tr>';
		$output .= '<tr>
						<td>Snijdelen</td>
						<td colspan="3">'.$snijdelen.'</td>
					</tr>';

		// echo "Snijbreedte: ".floatval($post->snijbreedte[0])."<br />";
		// echo "Bereken delen: ".floatval($sourcelength)."/".floatval($post->snijlengte)."<br />";
		// echo "Calculating: ".floatval($post->bronbreedte)."/".floatval($post->snijbreedte[0])."<br />";
		// echo "Colommen: ".$colums."<br />";
		// echo "Volle Delen: ".$volledelen."<br />";
		// echo "Snijdelen: ".$snijdelen."<br />";
		$output .= '<tr><th class="ui-corner-bottom" colspan="4">&nbsp;</th></tr>';
		$output .= '</table><br /><table id="chilldrolllist" class="data-table">';
		$output .= "<tr><th class='ui-corner-tl'>Rolnummer</th><th>Lengte</th><th>Breedte</th><th class='ui-corner-tr'>Mtr Verwerkt.</th>";
		$colum = 0;
		// Loop through aantal volle
		for($i = 1; $i <= $volledelen; $i++){
			
			if($colum >= $colums){
			 $colum = 0;	
			}
			
			$childnumber = sprintf("%'.02d\n", $i);		
			$output .= '<tr>
							<td>
								<label for="rolnummer-'.$childnumber.'">
									<span>Rolnummer '.$rolnummer.$childnumber.'</span>
								</label>
							</td>
							<td>
								Lengte: <input id="'.$rolnummer.$childnumber.'" name="rol['.$childnumber.']" type="text" value="'.$length.'" required/> 
							</td>
							<td>
								Breedte: <input id="'.$rolnummer.$childnumber.'" name="width['.$childnumber.']" type="text" value="'.$post->snijbreedte[$colum].'" required/> 
							</td>
							<td>
								Totaal '.$lengthsum.'mtr.
							</td>
						</tr>';
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
				$output .= '<tr>
								<td>
									<label for="rolnummer-'.$childnumber.'">
										<span>Rolnummer '.$rolnummer.$childnumber.'</span>
									</label>
								</td>
								<td>
									Lengte <input id="'.$rolnummer.$childnumber.'" name="rol['.$childnumber.']" type="text" value="'.$restlengte.'" required/> 
								</td>
								<td>
									Breedte <input id="'.$rolnummer.$childnumber.'" name="width['.$childnumber.']" type="text" value="'.$post->snijbreedte[$colum].'" required/>
								</td>
								<td>
									Totaal '.($lengthsum + $restlengte).' mtr.
								</td>
							</tr>';
				$lengthsum = $lengthsum + $restlengte;
				$i++;
				if($colums > 2){
					$colum++;
				}
			}
		}
		$output .= "<tr><th colspan='4' class='ui-corner-bottom'>&nbsp;</ht>";
		$output .= '</table>';

		$output .= '<li class="100-wide"><input type="button" name="vorige" id="childroll-vorige" value="Vorige" /><input type="submit" name="save" id="input_save" value="Opslaan" disabled="true"/></li>';
		$output .= '</ul><div class="clr"></div>';
		
		return $output;
	}
	
	function loadFilterForm($options){
		
		if (isset($_GET["free_search"])) { 	$free_search  = $_GET["free_search"];	} elseif (isset($_POST['free_search'])){	$free_search = $_POST['free_search'];	} else { $free_search='';	};		
		$sessionviewtype =  isset($_SESSION['viewtype']) ? $_SESSION['viewtype'] : '';
		
		$output  = "";
		$output .= "<div id='filter_form_div'>";
		$output .= "<form id='filter_form' action='index.php?page=rolltable' method='post'>";
		$output .= "<label for='free_search' class='left'>Zoek:</label><input type='text' name='free_search' id='input_free_search' value='".$free_search."' class='left'/><input type='submit' name='submit_search' id='submit_free_search' value='Zoek' class='left' /><input type='button' id='reset_free_form' value='Herstel' class='left'/>";
		
		//Show Locatie dropdown.
		//$output .= $this->BuildLocationDropdown();
		$output .= '<Label for="viewtype"><span>Weergave:</label><select class="viewtype" name="viewtype">';
		$output .= '<option value="0" '.($sessionviewtype == '0' ? 'selected="selected"' : '').'>Voorraad</option>';
		$output .= '<option value="1" '.($sessionviewtype == '1' ? 'selected="selected"' : '').'>Verzonden</option>';
		$output .= '<option value="2" '.($sessionviewtype == '2' ? 'selected="selected"' : '').'>Alles</option>';
		$output .= '</select>';
			
		// Set $options in hidden fields to keep the values
		foreach($options as $key => $val) {
			if(isset($val)){
				$output .= "<input type='hidden' id='filter_".$key."' name='".$key."' value='".$val."' />";
			}
		}
		$output .= "</form>";
		$output .= "</div>";
		echo $output;
	}
	
	// Get table of atricles
	function getTable($where = '', $range = array(0,20)){
		//$user = getUser($_SESSION['username']);
		//$viewtype = isset($_SESSION['viewtype']) ? $_SESSION['viewtype'] : null;
			
		$selected_roll = isset($_SESSION['selected_roll']) ? $_SESSION['selected_roll'] : null;
		unset($_SESSION['selected_roll']);
		
		$cols = array('rolnummer','deelnummer','snijbreedte','snijlengte','bronbreedte','bronlengte','omschrijving','locatie' ,'ingevoerd','gewijzigd','verzonden');
		
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
		if(isset($viewtype)) { $_SESSION['viewtype'] = $viewtype; } else { $viewtype =  isset($_SESSION['viewtype']) ? $_SESSION['viewtype']  : '' ; }
		if(isset($free_search)) { $_SESSION['free_search'] = $free_search; } else { $free_search = isset($_SESSION['free_search']) ? $_SESSION['free_search']  : ''; }
		
		// Set aantal resultaten per pagina
		$range[1] = 1000;	
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
		$link = '';
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
			($free_search ? $where_array[] = '(`rolnummer` LIKE "%' . $free_search . '%" OR `ingevoerd` LIKE "%' . $free_search . '%" OR `omschrijving` LIKE "%'. $free_search . '%" OR `ean` LIKE "%' . $free_search . '%" OR `kleur` LIKE "%' . $free_search . '%" OR `backing` LIKE "%' . $free_search . '%") ': '');		
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
		$query = "SELECT * FROM vanda_rolls ".$where.$orderby.$limit;
		
		//echo $query;
						
		// Put query in the session
		$_SESSION['query'] = $query;
		$result = $this->db->selectQuery($query);

		// WORD VOLGENS MIJ NIET MEER GEBRUIKT KOMT UIT PAGINATIE
		//$total_records = $result->num_rows;
		//$total_pages = ceil($total_records / $range[1]);	
		
		// generate output
		// Load the filter form
		echo $this->loadFilterForm($options);
		
		// Generate table header
		$output = "";
		$output .= "<form name='shipform' id='roll-shipform' action='pages/process-rollen.php' method='post'>";
		$output .= "<table class=\"data-table\">";
		$output .= "	<tr>";
		$output .= "		<th class='ui-corner-tl'><input type='checkbox' id='roll-select-all' name='roll-select-all'></th>";
		$output .= "		<th>Label</th>";
		$output .= "		<th><a href='?".$link_array['page']."&sort=rolnummer&order=".$order."&pg=".$pg."'>Rolnummer</a></th>";
		$output .= "		<th id='ordernummer_col'><a href='?".$link_array['page']."&sort=deelnummer&order=".$order."&pg=".$pg."'>Deelnummer</a></th>";
		$output .= "		<th><a href='?".$link_array['page']."&sort=snijbreedte&order=".$order."&pg=".$pg."'>Snijbreedte</a></th>";
		$output .= "		<th><a href='?".$link_array['page']."&sort=snijlengte&order=".$order."&pg=".$pg."'>Snijlengte</a></th>";
		$output .= "		<th><a href='?".$link_array['page']."&sort=omschrijving&order=".$order."&pg=".$pg."'>Omschrijving</a></th>";
		$output .= "		<th><a href='?".$link_array['page']."&sort=ean&order=".$order."&pg=".$pg."'>Locatie</a></th>";
		$output .= "		<th><a href='?".$link_array['page']."&sort=kleur&order=".$order."&pg=".$pg."'>Kleur</a></th>";
		$output .= "		<th><a href='?".$link_array['page']."&sort=backing&order=".$order."&pg=".$pg."'>Backing</a></th>";
		$output .= "		<th><a href='?".$link_array['page']."&sort=referentie&order=".$order."&pg=".$pg."'>Referentie</a></th>";
		$output .= "		<th><a href='?".$link_array['page']."&sort=ingevoerd&order=".$order."&pg=".$pg."'>Ingevoerd</a></th>";
		$output .= "		<th><a href='?".$link_array['page']."&sort=gewijzigd&order=".$order."&pg=".$pg."'>Gewijzigd</a></th>";
		$output .= "		<th class='ui-corner-tr'><a href='?".$link_array['page']."&sort=verzonden&order=".$order."&pg=".$pg."'>verzonden</a></th>";
		$output .= "	</tr>";
		
		$records = 0;
		foreach ($result as $row) {
			$row->omschrijving = (isset($row->omschrijving) && trim($row->omschrijving) != '' ? $row->omschrijving : '-');
									
			// Generate table rows
			$output .= "	<tr id='row_".$row->rollid."' class='data-table-row'>";
			$output .= "		<td><input class='roll-checkbox' type='checkbox' name='rollid[]' value='".$row->rollid."' /></td>";
		    $output .= "		<td><a href='pages/generate/generate_rol.php?rolnummer=".$row->rolnummer."' target='_blanc'><img src='images/printer.png' height='17'></a>";
			$output .= "		<td><a href='index.php?page=editroll&id=".$row->rollid."' >".$row->rolnummer."</a></td>";
			$output .= "		<td>".$row->deelnummer."</td>";
			$output .= "		<td>".$row->snijbreedte."</td>";
			$output .= "		<td>".$row->snijlengte."</td>";		
			$output .= "		<td><a href='index.php?page=editroll&id=".$row->rollid."' >".$row->omschrijving."</a></td>";
			$output .= "		<td>".$row->ean."</td>";	
			$output .= "		<td>".$row->kleur."</td>";	
			$output .= "		<td>".$row->backing."</td>";
			$output .= "		<td>".$row->referentie."</td>";
			$output .= "		<td>".date('Y-m-d',strtotime($row->ingevoerd))."</td>";
			$output .= "		<td>".date('Y-m-d',strtotime($row->gewijzigd))."</td>";
			$output .= "		<td>".$row->verzonden."</td>";
				
				
			$output .= "	</tr>";
			$records++;
		}
		
		//if($records == 0){
			$output .=  '<tr class=\'data-table-row\'>';	
			$output .=  '<th class="ui-corner-bottom" colspan="14"><strong>Er zijn '.$records.' resultaten om weer te geven</strong></td>';
			$output .=  '</tr>';
		//}
		
		// Close Table
		$output .= "</table>";
		$output .= "<input type='button' name='verwijder' id='input_verwijder' value='Verwijder' />";
		$output .= "<input type='hidden' name='task' id='task' value='verzend' />";
		// TODO Check wat deze regel deed, volgens mij niet veel.
		//$output .= "<label for='klant'>Klant: </label><input type='text' name='klant' id='roll-klant' value=''>";
		$output .= "<input type='button' name='haalterug' id='input_haalterug' value='Haal Terug' />";
		$output .= "<input type='submit' name='verzend' value='Verzend'/>";
		$output .= "</form>";
		
		
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