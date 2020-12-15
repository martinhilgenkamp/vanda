<?php

$task = ($_POST['task'] ? $_POST['task'] : $_GET['task']);

switch($task){
	case "ship":	
		// Variabele uit de post halen
		
		
		if($_GET['leverid']){
			$ship_id = mysqli_real_escape_string ($db,$_GET['leverid']);
		} else {
			$ship_id = mysqli_real_escape_string ($db,$_POST['leverid']);
		}
		
		$klant = mysqli_real_escape_string ($db,$_POST['klant']);
		$barcode = mysqli_real_escape_string ($db,$_POST['barcode']);
		
		if($klant == ''){
			echo "Geen klant ingegeven. <A HREF='scanner.php'>Klik hier</a> om terug te gaan";	
			exit;
		}	
		
		// Hier moet controle komen of de barcode bestaat.
		if(checkBarcode($barcode)){  // controleer of de barcode bestaat.

			// Registreer een shipment + verander de datum naar de laastst toegevoegde waarde.
			$query = "INSERT INTO `vanda_shipment` (`ship_id`, `klant`, `datum`) VALUES ('".$ship_id."', '".$klant."', '".date('Y-m-d H:i:s')."') ON DUPLICATE KEY UPDATE datum = '".date('Y-m-d H:i:m')."';";
			
			
			if(!$db->query($query)){
				echo "Er is een fout opgetreden bij het verwerken van de zending! ".mysqli_error($db);	
			}
			
			// Als er geen shipid word mee gegeven pak de laaste ship id (nieuwe zending)
			if($ship_id == ''){
				$ship_id = $db->insert_id;
			}
			
			// is successvol opgeslagen, nu het artikel afboeken in de voorraad lijst en de juiste ship id er aan hangen.				
			$query = "UPDATE vanda_production SET  shipping_id =  '".$ship_id."', geleverd =  '".date('Y-m-d H:i:s')."'  WHERE  barcode = '".$barcode."';" ;				
			
			if(!$db->query($query)){
				echo "Fout met het afboeken van het artikel.";
				echo mysqli_error($db);
			} else {
				
			}
			
			// Hier moet de teller functie komen.
			$query = "SELECT count(*) AS aantal FROM  vanda_production WHERE shipping_id = '$ship_id'";
			if(!$result = $db->query($query)){
				echo "Fout met het genereren van de teller";
				echo mysqli_error($db);
			} else {
				$teller = $result->fetch_array();
				$teller = $teller['aantal'];
			}
						
			// Alles is verwerkt, nu weer een formulier laten zien om verder te kunnen
			showform($klant,$ship_id,$teller);
			echo showSipments();
		} else {
			echo "Geen juise barcode ingegeven. <A HREF='scanner.php'>Klik hier</a> om terug te gaan";	
		}

		break;
		
		case "select":
			$leverid = $_GET['leverid'];
			$klant = $_GET['klant'];
			
			// ship
			if($leverid){
				$_SESSION['ship_id'] = $leverid;	
			}
			
			// klant
			if($klant){
				$_SESSION['klant'] = $klant;	
			}
			
			showform();
			echo showSipments($klant,$leverid);
		break;		
		// Standaard gewoon alleen het formulier laten zien
		default:
			showform();
			echo showSipments();
		break;
}

// Functie die controleert of er een artikel is met de barcode.
function checkBarcode($code){
	global $db;
	$query = "SELECT barcode FROM vanda_production WHERE barcode = '".$code."';";
	$query = $db->query($query);
	if($result->num_rows){
			return true;
		} else {
			// Hier moet een melding komen dat de barcode niet bestaat 
			return false;		
		}
}

// Functie om de laatst ingevulde waardes van de huidige zending te laten zien.
function getShipment($id){
	global $db;	
	$db->query($query);
}

function showSipments(){
	global $db;
	// Load products from the database.
	$query = "SELECT * FROM  vanda_shipment WHERE verzonden != 1 GROUP BY ship_id ASC" ;
	if($result = $db->query($query)){
		while($row = $result->fetch_object()){
			$zendingen[] = $row;
		}	
	}
	unset($query);
	
	$output = "<table id='product-table' class=\"ui-widget results\" cellpadding=\"0\" cellspacing=\"0\">".$nl;
	$output .= "<thead class=\"ui-widget-header\">".$nl;
	$output .= "<td>ID</td><td>Klant</td><td>Datum</td>".$nl;
	$output .= "</thead><tbody class='ui-widget-content'>".$nl;
	
	if(count($zendingen)){
		$c = 0;
		foreach($zendingen as $zending){
			if($c == 1){ 
				$output .= "<tr class=\"grey\">".$nl; $c = 0;} 
			else { 
				$output .= "<tr>".$nl; $c = $c + 1;
			}
			$output .= "<td class='clickable' id=".$zending->ship_id."><a href='?page=ship&task=select&klant=".$zending->klant."&leverid=".$zending->ship_id."' >".$zending->ship_id."</a></td><td><a href='?page=ship&task=select&klant=".$zending->klant."&leverid=".$zending->ship_id."' >".$zending->klant."</a></td><td><a href='?page=ship&task=select&klant=".$zending->klant."&leverid=".$zending->ship_id."' >".date('d-m-Y',strtotime($zending->datum))."</a></td>".$nl;
			$output .= "</tr>".$nl;
		}
	}
	$output .= "</tbody></table>".$nl;
	return $output;
}
function showform($klant = null, $leverid = null, $teller = null){ 
global $db;	
$ship_id = mysqli_real_escape_string ($db,$_POST['leverid']);
$klant = mysqli_real_escape_string ($db,$_POST['klant']);
?>
<script>
function doReset(){
	
	var klant = document.getElementById('klant');
	var leverid = document.getElementById('leverid');
	var barcode = document.getElementById('barcode');	
	
	barcode.value = "";	
	leverid.value = "";	
	klant.value = ""; 
}
</script>
<h1 id="header">Levering</h1>
<div id="shipform-container">
    <form id="shipform" name="shipform" method="post">
        <ul class="mobilelist" id="shiplist">
         <!-- <li><label for="ordernummer">Ordernummer: </label><input type="text" id="ordernummer" name="ordernummer" /></li> !-->
         <li><p><strong><?php echo $teller; ?> artikelen geladen.</strong></p></li>
         <li><label for="klant">Klant: </label><input type="text" id="klant" name="klant" onChange="checkActive();" value="<?php echo ($_POST['klant'] ? $_POST['klant'] : $_GET['klant']); ?>"/></li>
         <li>
            <label for="barcode">barcode: </label><input type="text" id="barcode" name="barcode" value=""" autofocus/>
         </li>
         <li><input class="ui-button-text" type="submit" id="ship" name="verstuur" value="Verstuur" /><a href="scanner.php"><input type="button" name="reset" value="Reset" id="reset" /></a></li>
        </ul>
        <input type="hidden" name="task" value="ship">
        <input type="hidden" id="leverid" name="leverid" onChange="checkActive();" value="<?php echo (isset($leverid) ? $leverid : $_GET['leverid']); ?>" />
   </form>
</div>

<script>
$( document ).ready(function() {
   setTimeout(function(){
  	$( "#barcode" ).focus();
  }, 3000);
});
</script>
<?php 
} // Showform() 
?>
