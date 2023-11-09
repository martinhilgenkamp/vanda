<?php 
require '../../vendor/autoload.php';
require_once("../../inc/class/class.production.php");
require_once("../../inc/class/class.option.php");



$pm = new ProductionManager();
$om = new OptionManager();

$barcode = $_GET['artikelnummer'];
$colli = $_GET['colli'];


// Get options
$options = $om->getOptionById(1);

class MYPDF extends TCPDF { }

// create new PDF document
$pdf = new MYPDF('portrait' , 'mm', array(210,297), true, 'UTF-8', true);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Vanda Carpets Genemuiden');
$pdf->SetTitle('Label');
$pdf->SetSubject('Label');
$pdf->SetKeywords('Label, Barcode');
$pdf->SetFont('dejavusans', '', 16, '', true);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);
$pdf->SetMargins(10, 10, 10,10);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);
$pdf->SetAutoPageBreak(FALSE);


if(!$colli > 1 || !isset($colli)){
 $colli = 1;
}

for($c = 0; $c < $colli; $c++){
	// chceck if product is listed in DB and genreate code
	if($post = $pm->getProductByBarcode($barcode)){
	
	$pdf->AddPage();    
	// define barcode style
	// generate barcode
	
	
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
		'fontsize' => 35,
		'fontweight' => 'Bold',
		'stretchtext' => 4
	);

	$x = $pdf->GetX();
	$y = $pdf->GetY();

	// (width, height, margin-left, margin-top)
	$pdf->writeHTMLCell(150, 0, '10', '10', '<span style="font-size: 20px; font-weight: bold;">'.$options->bedrijfskenmerk.'</span>', 0, 1, 0, false, '', true);
	$pdf->writeHTMLCell(150, 0, '50', '10', '<span style="font-size: 20px; font-weight: bold;">'.strtoupper($post->artikelnummer).'</span>', 0, 1, 0, false, 'R', true);
	$pdf->writeHTMLCell(150, 0, '10', '20', '<span style="font-size: 20px; font-weight: bold;">'.$post->kwaliteit.'</span>', 0, 1, 0, false, 'L', true);
	$pdf->writeHTMLCell(150, 0, '50', '20', '<span style="font-size: 20px; font-weight: bold;">'.$post->gewicht.' Stk</span>', 0, 1, 0, false, 'R', true);

	$pdf->write1DBarcode($barcode, 'C128', '0', '35', 200, 80, 1, $style, 'N');

	$pdf->writeHTMLCell(150, 0, '10', '120', '<span style="font-size: 18px; font-weight: bold;">Ordernr: '.$post->ordernr.'</span>', 0, 1, 0, false, 'L', true);
	$pdf->writeHTMLCell(150, 0, '10', '130', '<span style="font-size: 18px; font-weight: bold;">PROD: '.date('d/m/Y H:i', strtotime($post->datum)).'</span>', 0, 1, 0, false, 'L', true);


	$pdf->SetMargins(0, 0, 0,0);
	
	
	$barid = (int)substr($barcode, -10) + 1;
	$barcode = 'F00830'.$barid;
	unset($post);
	}
}


// write some JavaScript code
// force print dialog
//$js = 'print(true);';

// set javascript
//$pdf->IncludeJS($js);

ob_end_clean();

$pdf->Output('label-'.$barcode.'.pdf', 'I');

?>