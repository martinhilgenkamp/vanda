<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2015 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/Amsterdam');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/../class/PHPExcel.php';
require_once dirname(__FILE__) . '/../class/class.mysql.php';


// Get the ship id to read data.
$ship_id = $_GET['ship_id'];

class PHPExcelData extends PHPExcel{
	// Var totaal voor totaal leverings gewicht.
	public $query;
	
		// Load table data from file
    public function LoadData($ship_id) {
	    global $db;
		
		$query = "SELECT barcode, artikelnummer, gewicht,DATE_FORMAT(datum,'%m-%d-%Y') as datum, DATE_FORMAT(datum,'%H:%i') as tijd, ponummer FROM `vanda_production` LEFT JOIN vanda_options ON vanda_options.id =1 WHERE shipping_id = '".$ship_id."' ORDER BY artikelnummer, datum";		
		
		$result = $db->query($query);
		$i = 2;
		while($row = $result->fetch_assoc()){
			$rows[$i] = $row;	
			$i++;
		}		
		return $rows;		
	}// end of load data function
}

// Create new PHPExcel object
$objPHPExcel = new PHPExcelData();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Vanda Carpets")
							 ->setLastModifiedBy("Vanda Carpets")
							 ->setTitle("Vanda Carpets")
							 ->setSubject("Vanda Carpets")
							 ->setDescription("Vanda Carpets")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Vanda Carpets");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)


// Hier komt de vulling....
            ->setCellValue('A1', 'Barcode')
            ->setCellValue('B1', 'Artikelnummer')
            ->setCellValue('C1', 'Gewicht')
            ->setCellValue('D1', 'Productie Datum')
			->setCellValue('E1', 'Productie Tijd')
			->setCellValue('F1', 'PO Nummer');
			
			
$data = $objPHPExcel->LoadData($ship_id);


// Data bewerken voor export
foreach($data as $row){
	$patterns = array();
	$patterns[0] = ' PA';
	$patterns[1] = ' PP';
	$patterns[2] = ' WOL';
	$patterns[2] = ' PE';
	$patterns[2] = ' PA66';
	
	//print_r($patterns);
	$row['artikelnummer'] = str_replace(array(' WOL', ' PA', ' PP', ' PE',' PA66', ' PA6'),'',$row['artikelnummer']);
	
	$rows[] = $row;
}

$objPHPExcel->getActiveSheet()
    ->fromArray(
        $rows,  // The data to set
        NULL,        // Array values with this value will not be set
        'A2'         // Top left coordinate of the worksheet range where
                     //    we want to set these values (default is A1)
    );

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('F00830');
$objPHPExcel->getActiveSheet()->calculateColumnWidths();

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="F00830'.date('ymd').'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

