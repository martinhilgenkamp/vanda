<?php
// TODO  check if shipment is shipped before adding colli.
// TODO  this page needs to be merged with ship.php in a neat matter.


// Handle includes
require_once("../../inc/class/class.production.php");
require_once("../../inc/class/class.shipment.php");
require_once("../../inc/class/class.option.php");

$pm = new ProductionManager();
$sm = new ShipmentManager();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //print_r($_POST);
    
    $klant = $_POST['klant'];
    $ship_id = $_POST['ship_id'];
    $time = $_POST['time'];
    $task = $_POST['task'];
    $barcode = $_POST['barcode'];

    // Fixed values for debugging
    //$klant = 'Pruim';
    //$task = 'ship';
    //$ship_id = '';
    //$barcode = 'F008300000000350';
    
   

    switch ($task){
        case 'load':
            // Load products from the database.
            $message = $sm->getAllUnShippedShipments();
            $success = true;
            returnMessage($success, $message);
        break;

        case 'latest':
            // Load products from the database.
            $message = $sm->getAllUnShippedShipments();
            $success = true;
            returnMessage($success, $message);
        break;

        case 'ship':
            // controleer of de barcode bestaat en niet verzonden is returns barcode object
            $barcodeExists = $pm->getProductShipmentByBarcode($barcode);
            print_r($barcodeExists);
            if ($barcodeExists) {
                // Check for current or new shipment.
                if($ship_id) {
                    $Shipment = $sm->GetShipment($ship_id);
                    //$debug[] = get_object_vars($Shipment);
                    if($Shipment->verzonden == 1){
                        $message = "Kan artikel niet toevoegen de zending is al verzonden.";
                        $success = false;
                    } elseif($Shipment->datum == date('Y-m-d H:i:s')){
                        $message = "Fout aanmaken dubbele zending, probeer het opnieuw.";
                        $success = false;
                    } else {
                        $data = [
                        "datum" => date('Y-m-d H:i:s')
                        ];
                        if (!$sm->editShipment($data, $ship_id)) {
                            $message = "Fout bij het verwerken van de zending.";
                            $success = false;
                        } else { 
                           // eventueel handlers als shipment successvol is
                        }
                    }
                }  else {
                    //Hier moet iets komen om nieuwe shipments te maken.
                    $data = [
                        "klant" => $klant,
                        "verzonden" => '0',
                        "datum" => date('Y-m-d H:i:s')
                    ];

                    $ShipID = $sm->addShipment($data);
                    if($ShipID){
                        // Set id to ship_id var for further processing.
                        $ship_id = $ShipID;
                    } else {
                        $message = "Fout met maken van zending.";
                        $success = false;
                    }
                }
                
                // Phase 2
                // Shipment is successvol opgeslagen, nu het artikel afboeken in de voorraad lijst en de juiste ship id er aan hangen.
                $data = [
                    "shipping_id" => $ship_id,
                    "geleverd" => date('Y-m-d H:i:s')
                ];

                if (!$pm->editProductionByBarcode($data, $barcode)) {
                    $message = "Fout met het afboeken van het artikel.";
                    $success = false;
                } 

                // Hier moet de teller functie komen.
			    $shippingCount = $pm->getProductionCountByShippingId($ship_id);
                
                $message = "Success " . $shippingCount->aantal . ' colli in zending'; 
                $shipment = $ship_id;
                $success = true;
            } else {
                $message = "Onjuiste Barcode.";
                $success = false;
            }
            returnMessage($success, $message, $ship_id, $debug);
        break;

        

    } // end taskswitch
    // Determine the success or failure of the server-side processing
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo "Method not allowed";
}

function returnMessage($success, $message, $shipment = null, $debug = null){
        // Create a response array
        $response = array(
            'success' => $success,
            'message' => $message,
            'shipment' => $shipment,
            'debug' => $debug
        );
        // Send the response as JSON
        header('Content-Type: application/json');
        echo json_encode($response);
}
?>