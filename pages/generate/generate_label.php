<?php 

require_once('../../tcpdf.php');
require_once("../../inc/class/class.production.php");
require_once("../../inc/class/class.option.php");

$pm = new ProductionManager();
$om = new OptionManager();

$barcode = $_GET['artikelnummer'];
$post = $pm->getProductByBarcode($barcode);

// Get options
$options = $om->getOptionById(1);

class MYPDF extends TCPDF { }

// create new PDF document
$pdf = new MYPDF('landscape' , 'mm', array(98,205), true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Vanda Carpets Genemuiden');
$pdf->SetTitle('Label');
$pdf->SetSubject('Label');
$pdf->SetKeywords('Label, Barcode');
$pdf->SetFont('dejavusans', '', 16, '', true);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);
$pdf->SetMargins(0, 0, 0,0);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);
$pdf->SetAutoPageBreak(FALSE);
$pdf->AddPage();
    
// define barcode style
// generate barcode
$barcode = $post->barcode;
$pdf->setBarcode($barcode);

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
    'font' => 'dejavusans',
    'fontsize' => 25,
	'fontweight' => 'Bold'
);

$x = $pdf->GetX();
$y = $pdf->GetY();


$pdf->writeHTMLCell(100, 0, '5', '5', '<span style="font-size: 25px; font-weight: bold;">'.$options->bedrijfskenmerk.'</span>', 0, 1, 0, false, '', true);
$pdf->writeHTMLCell(50, 0, '150', '5', '<span style="font-size: 25px; font-weight: bold;">'.$options->ponummer.'</span>', 0, 1, 0, false, 'R', true);
$pdf->writeHTMLCell(200, 0, '5', '5', '<span style="font-size: 40px; font-weight: bold;">'.$post->gewicht.' Kg</span>', 0, 1, 0, false, 'C', true);

$pdf->write1DBarcode($barcode, 'C128', '0', '24', 120, 50, 1, $style, 'N');

$pdf->writeHTMLCell(200, 0, '5', '88', '<span style="font-size: 22px; font-weight: bold;">'.strtoupper($post->artikelnummer).'</span>', 0, 1, 0, false, 'L', true);
$pdf->writeHTMLCell(200, 0, '5', '88', '<span style="font-size: 22px; font-weight: bold;">PROD: '.date('d/m/Y H:i', strtotime($post->datum)).'</span>', 0, 1, 0, false, 'R', true);


$pdf->SetMargins(0, 0, 0,0);

// write some JavaScript code
// force print dialog
$js = 'print(true);';

// set javascript
$pdf->IncludeJS($js);

ob_end_clean();

$pdf->Output('label-'.$barcode.'.pdf', 'I');
?>