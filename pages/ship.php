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
		if($_GET['leverid']){
			$ship_id = $_GET['leverid'];
		} else {
			$ship_id = $_POST['leverid'];
		}
		
		$klant = $_POST['klant'];
		$barcode = $_POST['barcode'];
		
		if($klant == ''){
			echo "Geen klant ingegeven. <A HREF='scanner.php'>Klik hier</a> om terug te gaan";	
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

			$shipId = $sm->addShipment($data);

			// Registreer een shipment + verander de datum naar de laastst toegevoegde waarde.			
			if($shipId == 0 && $ship_id != '') {
				$data = [
					"datum" => date('Y-m-d H:i:s')
				];

				if (!$sm->editShipment($data, $ship_id)) {
					echo "Er is een fout opgetreden bij het verwerken van de zending!";
				}
			}
			
			// Als er geen shipid word mee gegeven pak de laaste ship id (nieuwe zending)
			if($ship_id == ''){
				$ship_id = $shipId;
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
			
			echo showSipments($zendingen);
		} else {
			echo "Geen juiste barcode ingegeven. <A HREF='scanner.php'>Klik hier</a> om terug te gaan";	
		}

		break;
		
		case "select":
			$leverid = isset($_GET['leverid']) ? $_GET['leverid'] : '';
			$klant = isset($_GET['klant']) ? $_GET['klant'] : '';
			
			// ship
			if($leverid != '') {
				$_SESSION['ship_id'] = $leverid;	
			}
			
			// klant
			if($klant != '') {
				$_SESSION['klant'] = $klant;	
			}
			
			showform($klant, $leverid);

			echo showSipments($zendingen);
		break;		
		// Standaard gewoon alleen het formulier laten zien
		default:
			showform();
			echo showSipments($zendingen);
		break;
}

function showSipments($zendingen) {

	$output = "<table id='product-table' class=\"ui-widget results\" cellpadding=\"0\" cellspacing=\"0\">";
	$output .= "<thead class=\"ui-widget-header\">";
	$output .= "<td>ID</td><td>Klant</td><td>Datum</td>";
	$output .= "</thead><tbody class='ui-widget-content'>";
	
	if(count($zendingen) > 0){
		$c = 0;
		foreach($zendingen as $zending){
			if($c == 1){ 
				$output .= "<tr class=\"grey\">"; $c = 0;} 
			else { 
				$output .= "<tr>"; $c = $c + 1;
			}
			$output .= "<td class='clickable' id=".$zending->ship_id."><a href='?page=ship&task=select&klant=".$zending->klant."&leverid=".$zending->ship_id."' >".$zending->ship_id."</a></td><td><a href='?page=ship&task=select&klant=".$zending->klant."&leverid=".$zending->ship_id."' >".$zending->klant."</a></td><td><a href='?page=ship&task=select&klant=".$zending->klant."&leverid=".$zending->ship_id."' >".date('d-m-Y',strtotime($zending->datum))."</a></td>";
			$output .= "</tr>";
		}
	}
	$output .= "</tbody></table>";
	return $output;
}

function showform($klant = null, $leverid = null, $teller = null) { 
	$leverid = isset($leverid) ? $leverid : isset($_GET['leverid']) ? $_GET['leverid'] : '';
	$klant = isset($klant) ? $klant : isset($_GET['klant']) ? $_GET['klant'] : '';

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
	        <input type="hidden" id="leverid" name="leverid" onChange="checkActive();" value="<?php echo $leverid ?>" />
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
