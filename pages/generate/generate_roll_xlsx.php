<?php
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/Amsterdam');

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
// Get the ship id to read data.
$ship_id = $_GET['ship_id'];

class PHPExcelData extends Spreadsheet{
	// Var totaal voor totaal leverings gewicht.
	public $query;

	function __construct() {
		parent::__construct();
		$this->db = new DB();
	}
	
		// Load table data from file
    public function LoadData($ship_id) {
	    global $db;
		
		$query = "SELECT rolnummer, deelnummer, omschrijving AS kwaliteit, ean AS Locatie, referentie, snijlengte, snijbreedte,  kleur, backing FROM `vanda_rolls` WHERE `verzonden` = ".$ship_id." AND `verwijderd` = 0";	
		
		
		
		if($result = $this->db->selectQuery($query)){
			
		} else {
			echo "query failed" .  mysqli_error($db);
		}
		$i = 2;
		
		foreach($result as $data){
			$row = (array)$data;
			$row['rolnummer'] = $row['rolnummer'].sprintf('%02d', $row['deelnummer']);
			unset($row['deelnummer']);
			$rows[$i] = $row;	
			$i++;
		}		
		return $rows;		
	}// end of load data function
}

// Create new PHPExcel object
$objPHPExcel = new PHPExcelData();

// Set document properties
$objPHPExcel->getProperties()->setCreator('Vanda Carpets')
			->setLastModifiedBy('Vanda Carpets')
			->setTitle('Zending-'.$ship_id)
			->setSubject('Paklijst')
			->setDescription('Paklijst zending'.$ship_id)
			->setKeywords('paklijst vanda ' .$ship_id)
			->setCategory('Paklijst');


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Rolnummer')
            ->setCellValue('B1', 'Kwaliteit')
            ->setCellValue('C1', 'Referentie')
			->setCellValue('C1', 'Locatie')
            ->setCellValue('D1', 'Snijlengte')
			->setCellValue('E1', 'Snijbreedte')
			->setCellValue('F1', 'Kleur')
			->setCellValue('F1', 'Backing');
			
			
$data = $objPHPExcel->LoadData($ship_id);


$objPHPExcel->getActiveSheet()
    ->fromArray(
        $data,  // The data to set
        NULL,        // Array values with this value will not be set
        'A2'         // Top left coordinate of the worksheet range where
                     //    we want to set these values (default is A1)
    );

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Rollen');
$objPHPExcel->getActiveSheet()->calculateColumnWidths();

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


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

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Xlsx');
$objWriter->save('php://output');
exit;

