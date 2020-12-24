<?php 

require_once("../../inc/class/class.production.php");
require_once("../../inc/class/class.shipment.php");

$pm = new ProductionManager();
$sm = new ShipmentManager();

$task = isset($_POST['task']) ? $_POST['task'] : '';
$klant = isset($_POST['klant']) ? $_POST['klant'] : '';
$leverid = isset($_POST['leverid']) ? $_POST['leverid'] : '';
$barcode = isset($_POST['barcode']) ? $_POST['barcode'] : '';

// Debugging set manual values
//$task = 'load';
//$barcode = "F008300000000205";
//$leverid = '999';
//$klant = 'Blaaap';

switch ($task){
	case 'load':
		// Load products from the database.
		$zendingen = $sm->getAllUnShippedShipments();

		echo json_encode($zendingen);
	break;
	
	case 'register':
		// controleer of de barcode bestaat.
		$barcodeExists = $pm->getProductByBarcode($barcode);

		if(!$barcodeExists) {  
			echo '{"error": "1", "message": "De barcode is onbekend"}';	
			exit;
		}
		
		// Maak een nieuwe shipment aan in de shipment tabel, of update de oude shipment met de nieuwe datum.
		$dataShipment = [
			"ship_id" => $leverid,
			"klant" => $klant,
			"datum" => date('Y-m-d H:i:s')
		];

		$shipId = $sm->addShipment($dataShipment);
		if($shipId == 0 && $leverid != '') {
			$data = [
				"datum" => date('Y-m-d H:i:s')
			];

			if (!$sm->editShipment($data, $leverid)) {
				echo '{"error": "1", "message": "Er is een Mysql error opgetreden tijden het aanmaken van de shipment"}';
			}
		}
		
		// Als er geen leverid word mee gegeven pak de laaste ship id (nieuwe zending)
		if($leverid == ''){
			$leverid = 	$shipId;
		}
			
		// Shipment is succesvol aangemaakt nu het artikel afboeken op de shipment
		$dataProduction = [
			"shipping_id" => $leverid,
			"geleverd" => date('Y-m-d H:i:s')
		];

		$succeeded = $pm->editProductionByBarcode($dataProduction, $barcode);
		if(!$succeeded) {
			echo '{"error": "1", "message": "Er is een Mysql error opgetreden tijdens het afboeken van het artikel"}';
		}

		// Teller ophalen voor feedback op de scanner.
		$shippingCount = $pm->getProductionCountByShippingId($leverid);

		if($shippingCount->aantal > 0) {
			return array($shippingCount->aantal, $leverid, $klant);
		} else {
			echo '{"error": "1", "message": "Er is een Mysql error opgetreden tijdens het tellen van de verzending"}';
		}

		echo '{"error": "0",
				"message": "Verzonden '.$registered[0].' collie in zending",
				"leverid": "'.$registered[1].'",
				"klant": "'.$registered[2].'"}';
	break;
	
	default:
		echo '{"error": "1",	
		   "message": "Geen taak opgegeven."}';
	break;	
}

?>