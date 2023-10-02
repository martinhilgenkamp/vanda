<?php

require_once("inc/class/class.production.php");
require_once("inc/class/class.shipment.php");

$pm = new ProductionManager();
$sm = new ShipmentManager();

$task = isset($_POST['task']) ? $_POST['task'] : (isset($_GET['task']) ? $_GET['task'] : '');

$zendingen = $sm->getAllShippedShipments();

switch($task){
	case "ship":	
		// Variabele uit de post halen
		$ship_id = '';	
		
		if(isset($_GET['ship_id'])){
			$ship_id = $_POST['ship_id'];
		} else {
			$ship_id = $_GET['ship_id'];
		}
		
		$klant = $_POST['klant'];
		$barcode = $_POST['barcode'];
		
		if($klant == ''){
			echo "Geen klant ingegeven. <A HREF='pages/scanner/scanner.php'>Klik hier</a> om terug te gaan";	
			exit;
		}	
		
		// controleer of de barcode bestaat.
		$barcodeExists = $pm->getProductByBarcode($barcode);

	
		if ($barcodeExists) {
			$data = [
				"ship_id" => $ship_id,
				"klant" => $klant,
				"datum" => date('Y-m-d H:i:s')
			];

			$ShipID = $sm->addShipment($data);

			// Registreer een shipment + verander de datum naar de laastst toegevoegde waarde.
			// Check of er het een nieuwe of een bestaande shipment is.		
			if($ShipID == 0 && $ship_id != '') {
				
				$Shipment = $sm->GetShipment($ship_id);
				if($Shipment->verzonden == 1){
					echo "Kan artikel niet toevoegen de zending is al verzonden.";
				} elseif($Shipment->datum == date('Y-m-d H:i:s')){
					echo "Fout aanmaken dubbele zending, probeer het opnieuw.";
				} else {
				
					$data = [
					"datum" => date('Y-m-d H:i:s')
					];
				
					if (!$sm->editShipment($data, $ship_id)) {
						echo "Er is een fout opgetreden bij het verwerken van de zending!";
					}
				}
			}
			
			// Als er geen shipid word mee gegeven pak de laaste ship id (nieuwe zending)
			if($ship_id == ''){
				$ship_id = $ShipID;
			}
			
			// is successvol opgeslagen, nu het artikel afboeken in de voorraad lijst en de juiste ship id er aan hangen.
			$data = [
				"shipping_id" => $ship_id,
				"geleverd" => date('Y-m-d H:i:s')
			];

			$succeeded = $pm->editProductionByBarcode($data, $barcode);			
			
			if(!$succeeded){
				echo "Fout met het afboeken van het artikel.";
			}
			
			// Hier moet de teller functie komen.
			$shippingCount = $pm->getProductionCountByShippingId($ship_id);

			// Alles is verwerkt, nu weer een formulier laten zien om verder te kunnen
			showform($klant, $ship_id, $shippingCount->aantal);

			$zendingen = $sm->getAllShippedShipments();
			
			echo showSipments($zendingen);
		} else {
			echo "Geen juiste barcode ingegeven. <A HREF='pages/scanner/scanner.php'>Klik hier</a> om terug te gaan";	
		}

		break;
		
		case "select":
			$ship_id = isset($_GET['ship_id']) ? $_GET['ship_id'] : '';
			$klant = isset($_GET['klant']) ? $_GET['klant'] : '';
			
			// ship
			if($ship_id != '') {
				$_SESSION['ship_id'] = $ship_id;	
			}
			
			// klant
			if($klant != '') {
				$_SESSION['klant'] = $klant;	
			}
			
			showform($klant, $ship_id);

			echo showSipments($zendingen);
		break;

		// Standaard gewoon alleen het formulier laten zien met openstaande shipments.
		default:
			showform();
			echo showSipments($zendingen);
		break;
}

function showSipments($zendingen) {
	$output = "<table id='product-table' class=\"data-table\" cellpadding=\"0\" cellspacing=\"0\">";
	$output .= "<tr>";
	$output .= "<th>ID</th><th>Klant</th><th>Datum</th>";
	$output .= "</tr><tbody class='ui-widget-content'>";
	
	if(count($zendingen) > 0){
		foreach($zendingen as $zending){
			$output .= "<tr>
				<td class='clickable' id=".$zending->ship_id.">
					<a href='?page=ship&task=select&klant=".$zending->klant."&ship_id=".$zending->ship_id."' >".$zending->ship_id."</a>
				</td>
				<td>
					<a href='?page=ship&task=select&klant=".$zending->klant."&ship_id=".$zending->ship_id."' >".$zending->klant."</a>
				</td>
				<td>
					<a href='?page=ship&task=select&klant=".$zending->klant."&ship_id=".$zending->ship_id."' >".date('d-m-Y',strtotime($zending->datum))."</a>
				</td>
				</tr>";
		}
	}
	$output .= "</tbody></table>";
	return $output;
}

function showform($klant = null, $ship_id = null, $teller = null) { 
	
	$ship_id = $ship_id ?? $_GET['ship_id'] ?? '';
	$klant = $klant ?? $_GET['klant'] ?? '';

	?>
	<script>
	function doReset(){
		
		var klant = document.getElementById('klant');
		var ship_id = document.getElementById('ship_id');
		var barcode = document.getElementById('barcode');	
		
		barcode.value = "";	
		ship_id.value = "";	
		klant.value = ""; 
	}
	</script>
	<h1 id="header">Levering</h1>
	<div id="shipform-container">
	    <form id="shipform" name="shipform" method="post">
	        <ul class="mobilelist" id="shiplist">
	         <!-- <li><label for="ordernummer">Ordernummer: </label><input type="text" id="ordernummer" name="ordernummer" /></li> !-->
	         <li>
	         	<p>
	         		<strong><?php echo $teller; ?> artikelen geladen.</strong>
	         	</p>
	         </li>
	         <li>
	         	<label for="klant">Klant: </label>
	         	<input type="text" id="klant" name="klant" onChange="checkActive();" value="<?php echo $klant ?>" />
	         </li>
	         <li>
	            <label for="barcode">barcode: </label>
	            <input type="text" id="barcode" name="barcode" value="" autofocus />
	         </li>
	         <li>
	         	<input class="ui-button-text" type="submit" id="ship" name="verstuur" value="Verstuur" />
	         	<a href="pages/scanner/scanner.php">
	         		<input type="button" name="reset" value="Reset" id="reset" />
	         	</a>
	         </li>
	        </ul>
	        <input type="hidden" name="task" value="ship">
	        <input type="hidden" id="ship_id" name="ship_id" onChange="checkActive();" value="<?php echo $ship_id ?>" />
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
