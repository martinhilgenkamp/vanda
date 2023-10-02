<?php 
date_default_timezone_set("Europe/Amsterdam");
require_once("../../inc/class/class.production.php");
require_once("../../inc/class/class.shipment.php");

$pm = new ProductionManager();
$sm = new ShipmentManager();

$task = isset($_POST['task']) ? $_POST['task'] : '';
$klant = isset($_POST['klant']) ? $_POST['klant'] : '';
$leverid = isset($_POST['leverid']) ? $_POST['leverid'] : '';
$barcode = isset($_POST['barcode']) ? $_POST['barcode'] : '';

// Debugging set manual values
 // $task = 'register';
 // $barcode = "F008300000000813";
 // $leverid = '2559';
 // $klant = 'test';

switch ($task){
	case 'load':
		// Load products from the database.
		$zendingen = $sm->getAllUnShippedShipments();

		echo json_encode($zendingen);
		exit;
	break;
	
	case 'register':
		if(empty($barcode)) {  
			$arr = array(
				'error' => '1',
				'message' => 'De barcode mag niet leeg zijn',
			);
			echo json_encode($arr);	
			exit;
		}

		// controleer of de barcode bestaat.
		$product = $pm->getProductByBarcode($barcode);

		if($product == null && $product->barcode == "") {
			$arr = array(
				'error' => '1',
				'message' => 'De barcode is onbekend',
			);
			echo json_encode($arr);	  
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
			
			$Shipment = $sm->GetShipment($leverid);
				if($Shipment->verzonden == 1){
					$arr = array(
						'error' => '1',
						'message' => 'Kan artikel niet toevoegen, de zending is al verzonden.',
					);
					echo json_encode($arr);	
					exit;
				} else if($Shipment->datum == date('Y-m-d H:i:s')){
					$arr = array(
						'error' => '1',
						'message' => 'Fout aanmaken dubbele zending, probeer het opnieuw.',
					);
					echo json_encode($arr);	
					exit;
				}else {
					$data = [
						"datum" => date('Y-m-d H:i:s')
					];

					if (!$sm->editShipment($data, $leverid)) {
						$arr = array(
							'error' => '1',
							'message' => 'Er is een Mysql error opgetreden tijden het aanmaken van de shipment',
						);
						echo json_encode($arr);	
						exit;
					}
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
			$arr = array(
				'error' => '1',
				'message' => 'Er is een Mysql error opgetreden tijdens het afboeken van het artikel',
			);
			echo json_encode($arr);	
			exit;
		}

		// Teller ophalen voor feedback op de scanner.
		$shippingCount = $pm->getProductionCountByShippingId($leverid);		

		if($shippingCount->aantal > 0) {
			$arr = array(
				'error' => '0',
				'message' => 'Verzonden '.$shippingCount->aantal.' collie in zending',
				'leverid' => $leverid,
				'klant' => $klant
			);
			echo json_encode($arr);	
			exit;
		} else {
			$arr = array(
				'error' => '1',
				'message' => 'Er is een Mysql error opgetreden tijdens het tellen van de verzending',
			);
			echo json_encode($arr);	
			exit;
		}		
	break;
	
	default:
		echo '{"error": "1",	
		   "message": "Geen taak opgegeven."}';
	break;	
}

?>