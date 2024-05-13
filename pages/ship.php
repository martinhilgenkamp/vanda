<?php
// Handle includes
require_once("inc/class/class.production.php");
require_once("inc/class/class.shipment.php");
require_once("inc/class/class.option.php");

$pm = new ProductionManager();
$sm = new ShipmentManager();


$task = isset($_POST['task']) ? $_POST['task'] : (isset($_GET['task']) ? $_GET['task'] : '');
$zendingen = $sm->getAllShippedShipments();

switch($task){


		// Standaard gewoon alleen het formulier laten zien met openstaande shipments.
		default:
			showform();
			//echo showSipments($zendingen);
		break;
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
		<div id="result"></div>
	    <form id="shipform" name="shipform" method="post">
	        <ul class="mobilelist" id="shiplist">
	         <!-- <li><label for="ordernummer">Ordernummer: </label><input type="text" id="ordernummer" name="ordernummer" /></li> !-->
	         <li>
	         	<label for="klant">Klant: </label>
	         	<input type="text" id="klant" name="klant" onChange="checkActive();" value="<?php echo $klant ?>" />
	         </li>
	         <li>
	            <label for="barcode">barcode: </label>
	            <input type="text" id="barcode" name="barcode" value="" autofocus />
	         </li>
			 <li>
				<label for="ship_id">Zending Nr: </label>
			 	<input type="Text" id="ship_id" name="ship_id" onChange="checkActive();" value="<?php echo $ship_id ?>" />
			 </li>
	         <li>
	         	<input class="ui-button-text" type="submit" id="ship" name="verstuur" value="Verstuur" />
	         	<a href="pages/scanner/scanner.php">
	         		<input type="button" name="reset" value="Reset" id="reset" />
	         	</a>
	         </li>
	        </ul>
	        <input type="hidden" name="task" value="ship">
	   </form>
	</div>
	 <!-- div for open shipments !-->
	 <div id="openshipments">
        Open zendingen verzamelen...
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


function returnMessage($success, $message, $shipment = null, $debug = null){
	// Create a response array
	$response = array(
		'success' => $success,
		'message' => $message,
		'shipment' => $shipment,
		'debug' => $debug
	);
	// Send the response as JSON
	//header('Content-Type: application/json');
	print_r($response); //json_encode($response);
} // ReturnMessage()
?>
