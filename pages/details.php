<?php 

require_once("inc/class/class.production.php");

$pm = new ProductionManager;

// Hier moet nog een check komen of er een ship id is.
$ship_id = $_GET['shipid'];

// Load products from the database.
$zendingen = $pm->getProductionByShipId($ship_id);

$output = "";
$output .= "<table id='product-table' class=\"data-table\" cellpadding=\"0\" cellspacing=\"0\">";
$output .= "<tr>";
$output .= "<th>Barcode</th><th>Shipment_id</th><th>Klant</th><th>Productie</th><th>Verzonden</th>";
if ($user->level) {
	$output .= "<th>Geef Vrij</th>";
}
$output .= "</tr>";

if(count($zendingen)) {
	foreach($zendingen as $zending) {	
		$output .= "<tr>
						<td>".$zending->barcode."</td>
						<td>".$ship_id."</td>
						<td>".$zending->klant."</td>
						<td>".date('d-m-Y H:i',strtotime($zending->productie_tijd))."</td>
						<td>".$zending->verzend_tijd."</td>".
						($user->level == 1 
							? '	<td><span class="unship-article" id="'.$zending->id.'"></span></td>' 
							: '');
		$output .= "</tr>";
	}
} else {
	// Geef een melding dat er niets is weer te geven.
	$output .= "<tr><td colspan='5'> Deze zending bevat nog geen artikelen</td></tr>";	
}

$output .= "</table>";
$output .=  "<center>Er zijn ". count($zendingen) . " resultaten weergegeven <br>"
?>
<h1>Zending Details</h1>

<a href="index.php?page=zendingen">&lt;&lt; Terug</a>
<?php echo $output ?>