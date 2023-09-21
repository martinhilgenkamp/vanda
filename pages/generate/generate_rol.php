<?php 
// Noodzakelijke dingen bij elkaar rapen.
require_once('../../inc/class/class.rollen.php');
require_once('../../inc/tcpdf/tcpdf.php');
date_default_timezone_set("Europe/Amsterdam");
$rollManager = new RollsManager();

// prevent notifications
error_reporting(E_ERROR | E_PARSE);
ini_set("display_errors",0);

$rolnummer= $_GET['rolnummer'];
$rollen = $rollManager->loadActiveRolls($rolnummer);
$aantal = count($rollen);

class MYPDF extends TCPDF {
    // Page footer
    public function Footer() {
		//$artikelnummer= $_GET['artikelnummer'];
		//$result = mysql_query("SELECT * FROM articles WHERE artikelnummer = '".$artikelnummer."' LIMIT 1;");
		//$post = mysql_fetch_object($result);
        // Page number
       // $this->writeHTMLCell(200, 0, '10', '80', '<span style="font-size: 18px; font-weight: bold; line-height:20px;">'.$post->opmerking.'</span>', 0, 1, 0, false, '', true);
    }
}

// create new PDF document
$pdf = new MYPDF('landscape' , 'mm', array(98,150), true, 'UTF-8', false);

// set document 
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Vanda Carpets Genemuiden');
$pdf->SetTitle('RolEtiket');
$pdf->SetSubject('Label');
$pdf->SetKeywords('Label, Barcode');
$pdf->SetFont('dejavusans', '', 16, '', true);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);
$pdf->SetMargins(0, 0, 0,0);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);
$pdf->SetAutoPageBreak(FALSE);

$style = array(
    'position' => 'C',
    'align' => 'C',
    'stretch' => true,
    'fitwidth' => true,
    'cellfitalign' => '',
    'border' => 0,
    'hpadding' => '0',
    'vpadding' => 0,
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => true,
    'font' => 'dejavusans',
    'fontsize' => 25,
	'fontweight' => 'Bold',
    'stretchtext' => 4
);

$substyle = array(
    
    'stretch' => true,
    'fitwidth' => true,
    
    'border' => 0,
    
    'fgcolor' => array(0,0,0),
    //'bgcolor' => array(120,0,120), //array(255,255,255),
    'text' => true,
    'font' => 'dejavusans',
    'fontsize' => 15,
	'fontweight' => 'Bold',
    'stretchtext' => 4
);

$substylenotext = array(
    
    'stretch' => true,
    'fitwidth' => true,
    
    'border' => 0,
    
    'fgcolor' => array(0,0,0),
    //'bgcolor' => array(120,0,120), //array(255,255,255),
    'text' => false,
    'font' => 'dejavusans',
    'fontsize' => 15,
	'fontweight' => 'Bold',
    'stretchtext' => 4
);

// Repeat page function to the set amount of labels
for($i = 0; $i < $aantal;$i++){
	$pdf->AddPage();
	$rolcode = $rollen[$i]->rolnummer.sprintf('%02d',$rollen[$i]->deelnummer);
	
	$pdf->setBarcode($rolcode);
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	$pdf->write1DBarcode($rolcode, 'C128', '0', '5', '700' , 20, 0.6, $style, 'N');
	$linestyle = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => array(0, 0, 0));
	$pdf->Line(0, 23, 150, 23, $linestyle);
	$pdf->writeHTMLCell(150, 10, '0', '23', '<span style="font-size: 25px; font-weight: bold;">'.$rollen[$i]->omschrijving.'</span>', 0, 1, 0, false, 'C', true);
	$pdf->Line(0, 34, 150, 33, $linestyle);

	
	$pdf->writeHTMLCell(50, 0, '0', '35', '<span style="font-size: 15px; font-weight: bold;">Lengte: '.$rollen[$i]->snijlengte.'</span>', 0, 1, 0, false, 'L', true);
	$pdf->writeHTMLCell(50, 0, '50', '35', '<span style="font-size: 15px; font-weight: bold;">Breedte: '.$rollen[$i]->snijbreedte.'</span>', 0, 1, 0, false, 'C', true);
	$pdf->writeHTMLCell(40, 0, '110', '35', '<span style="font-size: 15px; font-weight: bold;">M2: '.round(($rollen[$i]->snijlengte*$rollen[$i]->snijbreedte),2).'</span>', 0, 1, 0, false, 'L', true);
	
	$pdf->write1DBarcode($rollen[$i]->snijlengte, 'C128', '5', '43', '50' , 10, 0.4, $substylenotext, 'L');
	$pdf->write1DBarcode($rollen[$i]->snijbreedte, 'C128', '55', '43', '50' , 10, 0.4, $substylenotext, 'C');
	$pdf->write1DBarcode(round(($rollen[$i]->snijbreedte*$rollen[$i]->snijlengte),2), 'C128', '110', '43', '50' , 10, 0.4, $substylenotext, 'R');
	
	$pdf->Line(0, 61, 150, 61, $linestyle);
	
	//Locatie was EAN in vorige versies
	$pdf->writeHTMLCell(35, 0, '0', '62', '<span style="font-size: 18px; font-weight: bold;">Loc:</span>', 0, 1, 0, false, 'L', true);
	$pdf->write1DBarcode($rollen[$i]->ean, 'C128', '20', '62', '50' , 15, 0.4, $substyle, 'L');
	
	
	$pdf->writeHTMLCell(50, 0, '100', '64', '<span style="font-size: 15px; font-weight: bold;">Kleur: '.$rollen[$i]->kleur.'</span>', 0, 1, 0, false, 'R', true);
	$pdf->writeHTMLCell(50, 0, '100', '74', '<span style="font-size: 15px; font-weight: bold;">Backing: '.$rollen[$i]->backing.'</span>', 0, 1, 0, false, 'R', true);
	
	$pdf->SetMargins(0, 0, 0,0);
}

// force print dialog
$js .= 'print(true);';

// set javascript
$pdf->IncludeJS($js);

$pdf->Output('label-'.$barcode.'.pdf', 'I');
?>