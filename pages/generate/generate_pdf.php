<?php 
// Noodzakelijke dingen bij elkaar rapen.
require_once('../class/class.mysql.php');
require_once('../tcpdf.php');
date_default_timezone_set("Europe/Amsterdam");

// prevent notifications
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", 1);
$nl = "\r\n";


class TablePDF extends TCPDF{
	// Var totaal voor totaal leverings gewicht.
	public $totaal;
	public $query;

	
	//Page header
    public function Header() {
        $x = $this->SetX(0);
		$y = $this->SetY(0);
		
		// Logo
        $image_file = K_PATH_IMAGES.'logo.jpg';
		// Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
        $this->Image($image_file, 5, 5, '30', '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
		$this->ln();
        $this->SetFont('helvetica', 'B', 20);
		
		// draw some reference lines
		$linestyle = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => array(0, 0, 0));
		$this->Line(5, 18, 205, 18, $linestyle);
    }
	
	// Load table data from file
    public function LoadData($query, $conditions = '') {
		$result = mysql_query($query);
		if(!$result){
			die(mysql_error());	
		}
		$rows = array();
		
		// get values from database.
		while($row = mysql_fetch_assoc($result)){
			if($row['ordernr']){
				$rows[strtoupper($row['artikelnummer'] . ' - ' . $row['kwaliteit'])][] = $row;
			} else {
				$rows[strtoupper($row['artikelnummer'])][] = $row;
			}
		}	
		
		// Zet de rijen in groepen van artikelnummer.
		foreach($rows as $key => $group){
			// Werk de groep uit.
			$groep_gewicht = 0;
			foreach($group as $row){
				$return[] = array('',$row['shipping_id'],$row['barcode'],$row['ordernr'],$row['geleverd'],$row['gewicht']);	
				$groep_gewicht = $groep_gewicht + $row['gewicht'];
			}
			$return[] = array('','','','Totaal Gewicht: ',$groep_gewicht);
			$tables[$key] = $return;
			unset($groep_gewicht,$return);
		}
		return $tables;			
	}// end of load data function
	
	public function CalculateTotal($query){
		$totaal_gewicht = 0;
		$result = mysql_query($query);
		if(!$result){
			die(mysql_error());	
		}
		while($row = mysql_fetch_assoc($result)){
			$rows[] = $row;
			$totaal_gewicht = $row['gewicht'] + $totaal_gewicht;
		}
		return $totaal_gewicht;
	}
	
	public function CalculateAantal($query){
		$totaal_gewicht = 0;
		$result = mysql_query($query);
		if(!$result){
			die(mysql_error());	
		}
		while($row = mysql_fetch_assoc($result)){
			$rows[] = $row;
		}
		return count($rows);
	}
	
	 // Colored table
    public function ColoredTable($key, $data) {
        // Header
		$w = array(50, 15, 35, 20, 35, 20, );
		$rows = 25; 
			
		// Data
        $fill = 0;
		$t=0;
		$i = 0;
		$rijen = count($data);
		$noline = 0;
				
		foreach($data as $row) {
			if($t == 0){
				 // Colors, line width and bold font
				$this->SetFillColor(50);
				$this->SetTextColor(255);
				$this->SetDrawColor(51, 51, 51);
				$this->SetLineWidth(0.1);
				$this->SetFont('', 'B');
				$this->SetFont('helvetica', '', 12);
				$this->Cell($w[0], 7, $key, 1, 0, 'C', 1);
				$this->Cell($w[1], 7, 'ID', 1, 0, 'C', 1);
				$this->Cell($w[2], 7, 'Barcode', 1, 0, 'C', 1);
				$this->Cell($w[3], 7, 'Ordernr', 1, 0, 'C', 1);
				$this->Cell($w[4], 7, 'Lever Datum', 1, 0, 'C', 1);
				$this->Cell($w[5], 7, 'Kg / Stk', 1, 0, 'R', 1);
				$this->Ln();
				// Color and font restoration
				$this->SetFillColor(225);
				$this->SetTextColor(51,51,51);
				$this->SetFont('');
			}
			
			$this->SetFont('helvetica', '', 8);
			
			// Footer
			if($i == $rijen -1){
				$colli = count($data)-1;
				$this->SetFont('dejavusans', 'B', 11, '', true);
       			$this->Cell(array_sum($w), 4, 'Totaal '.$key.' colli '.$colli.(strpos($key, ' - ') ? ' Aantal: ' : ' Gewicht: ') . $row[4].(strpos($key, ' - ') ? ' Stk' : ' Kg'), 0, 0, 'R','');
				$this->Ln();	
				// Add gewicht totaal levering
				$totaal = $totaal + $row[4];
			} else { //Normale tabel 		
				
				$bstyle = array(
				  'position' => 'L',
				  'align' => 'C',
				  'stretch' => false,
				  'fitwidth' => false,
				  'cellfitalign' => '',
				  'border' => '0',
				  'hpadding' => '0',
				  'vpadding' => 1,
				  'fgcolor' => array(0,0,0),
				  'bgcolor' => $fill, //array(255,255,255),
				  'text' => false,
				  'font' => 'helvetica',
				  'fontsize' => 8,
				  'stretchtext' => 4
			  );

				$this->Cell(1, 4, '', 'L', 0, 'C', $fill);
				$this->write1DBarcode($row[2], 'C128', '', '', $w[0]-1, 4 , '.18', $bstyle, 'T');
				//$this->Cell($w[0], 4, '', 'LR', 0, 'C', $fill);
				$this->Cell($w[1], 4, $row[1], 'LR', 0, 'C', $fill);
				$this->Cell($w[2], 4, $row[2], 'LR', 0, 'C', $fill);
				$this->Cell($w[3], 4, $row[3], 'LR', 0, 'C', $fill);
				$this->Cell($w[4], 4, $row[4], 'LR', 0, 'C', $fill);
				$this->Cell($w[5], 4, $row[5].(strpos($key, ' - ') ? ' Stk' : ' Kg'), 'LR', 0, 'R', $fill);
				$this->Ln();
			}
            $fill=!$fill;
			$t++;   // Check if pagebreak is needed.
			$i++;	// Check if last row in data
			
			// Header repeaten na aantal regels
			if($i == $rijen -1){
				$this->Cell(array_sum($w), 0, '', 'T');
				$this->Ln();
			} 
			
        }
		$this->Ln();
    }
	
	
	// Page footer
    public function Footer() {
		// draw some reference lines
		$linestyle = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => array(0, 0, 0));
		$this->Line(5, 285, 205, 285, $linestyle);
		
		
		//Print totaal leverings gewicht.
		$this->SetY(-12);
		$this->SetFont('dejavusans', 'B', 12, '', true);
		$this->Cell(190, 10, 'Totaal aantal colli '.$this->CalculateAantal($this->query).' leverings gewicht: ' .$this->CalculateTotal($this->query).' Kg/Stk', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
		$this->Ln();	

	
        // Position at 15 mm from bottom
        $this->SetY(-12);
		$this->SetX(5);
        // Set font
        $this->SetFont('helvetica', '', 12);
        // Page number
        $this->Cell(190, 10, 'Vanda Carpets B.V. pagina '.$this->getAliasNumPage().' / '.$this->getAliasNbPages(),0, false, 'L', 0, '', 0, false, 'T', 'M');
    }
	
}

// create new PDF document
$pdf = new TablePDF('portrait' , 'mm', 'a4', true, 'UTF-8', false);

// Generate query
// Get the ship id to read data.
$ship_id = $_GET['ship_id'];
$pdf->query = "SELECT * FROM `vanda_production` WHERE shipping_id = '".$ship_id."' AND removed = '0'";

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Vanda Carpets Genemuiden');
$pdf->SetTitle('Pakbon');
$pdf->SetSubject('Pakbon');
$pdf->SetKeywords('Pakbon, Levering');
$pdf->SetFont('dejavusans', '', 8, '', true);
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 20, PDF_MARGIN_RIGHT, 14);
$pdf->SetHeaderMargin(20);
$pdf->SetFooterMargin(14);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 14);
$pdf->AddPage();



$style = array(
	  'position' => 'C',
	  'align' => 'C',
	  'stretch' => true,
	  'fitwidth' => true,
	  'cellfitalign' => '',
	  'border' => 1,
	  'hpadding' => '5',
	  'vpadding' => 5,
	  'fgcolor' => array(0,0,0),
	  'bgcolor' => false, //array(255,255,255),
	  'text' => true,
	  'font' => 'helvetica',
	  'fontsize' => 8,
	  'stretchtext' => 4
  );

$x = $pdf->GetX();
$y = $pdf->GetY();

//Load tables for the shipping list.
$tables = $pdf->LoadData($pdf->query,$conditions);

foreach($tables as $key => $table){
	$pdf->ColoredTable($key, $table);	
}

$pdf->Output('label-'.$barcode.'.pdf', 'I');
?>