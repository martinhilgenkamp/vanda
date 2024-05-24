<?php
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', TRUE);

// Set date and load dependancies
date_default_timezone_set('Europe/Amsterdam');
require '../../vendor/autoload.php';
require_once('../../inc/class/class.db.php');

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$helper = new Sample();
if ($helper->isCli()) {
    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
    return;
}

// Check if ship_id is provided in $_GET
if (!isset($_GET['ship_id'])) {
    // If ship_id is not provided, stop execution and display error message
    die("Zendingsnummer is niet ingegeven.");
}
// If ship_id is provided, assign it to $ship_id variable
$ship_id = $_GET['ship_id'];



class ShipmentSpreadsheet extends Spreadsheet {
	// Var totaal voor totaal leverings gewicht.
	public $query;
	public $db;


    function __construct() {
		parent::__construct();
		$this->db = new DB();
	}

    public function loadData($ship_id ){
        $query = "SELECT rolnummer, deelnummer, omschrijving AS kwaliteit, ean AS Locatie, referentie, snijlengte, snijbreedte,  kleur, backing FROM `vanda_rolls` WHERE `verzonden` = ".$ship_id." AND `verwijderd` = 0";
        $result = $this->db->selectQuery($query);
       // echo $query;
        // rewrite data as an array
        $shipmentarray = [];
        foreach ($result as $stdClassObject) {
            $row_array = (array) $stdClassObject;
	        //$row_array['artikelnummer'] = str_replace(array(' WOL', ' PA', ' PP', ' PE',' PA66', ' PA6'),'',$row_array['artikelnummer']);
            $row_array['rolnummer'] = $row_array['rolnummer'].sprintf('%02d', $row_array['deelnummer']);
            unset($row_array['deelnummer']);
            
            $shipmentarray[] = $row_array;
        }
        // Return array, and header
        return($shipmentarray);
    }

}

// Create new Spreadsheet object
$spreadsheet = new ShipmentSpreadsheet();
$array = $spreadsheet->loadData($ship_id);    //TODO shipid dynamisch maken




// Set document properties
$spreadsheet->getProperties()->setCreator('Vanda Carpets')
    ->setLastModifiedBy('Vanda Carpets')
    ->setTitle('Zending-'.$ship_id)
    ->setSubject('Paklijst')
    ->setDescription('Paklijst zending'.$ship_id)
    ->setKeywords('paklijst vanda ' .$ship_id)
    ->setCategory('Paklijst');

// Add header
$spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Rolnummer')
            ->setCellValue('B1', 'Kwaliteit')
            ->setCellValue('C1', 'Referentie')
            ->setCellValue('C1', 'Locatie')
            ->setCellValue('D1', 'Zending')
            ->setCellValue('E1', 'Snijlengte')
            ->setCellValue('F1', 'Snijbreedte')
            ->setCellValue('G1', 'Kleur')
            ->setCellValue('H1', 'Backing');

// Print requested data in Excel
$spreadsheet->setActiveSheetIndex(0)
    ->fromArray(
        $array,
        NULL,
        'A2'
    );

// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Rollen');
$spreadsheet->getActiveSheet()->calculateColumnWidths();
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Vanda-Zending-'.$ship_id.'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;

?>