<?php
require_once("inc/class/class.production.php");

$pm = new ProductionManager;
// Check if ship_id is provided in $_GET
if (!isset($_GET['ship_id'])) {
    // If ship_id is not provided, stop execution and display error message
    die("Zendingsnummer is niet ingegeven.");
}
// If ship_id is provided, assign it to $ship_id variable
$ship_id = $_GET['ship_id'];


// Load products from the database.
$zendingen = $pm->getProductionByShipId($ship_id);

$output = "";
$output .= "<table id='product-table' class=\"data-table\" cellpadding=\"0\" cellspacing=\"0\">";
$output .= "<tr>";
$output .= "<th class='ui-corner-tl'>Barcode</th><th>Shipment_id</th><th>Klant</th><th>Productie</th>";
if ($user->level) {
	$output .= "<th>Verzonden</th><th class='ui-corner-tr'>Geef Vrij</th>";
} else {
	$output .= "<th class='ui-corner-tr'>Verzonden</th>";
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

if ($user->level) {
	$output .= "<tr><th colspan='7' class='ui-corner-bottom'>Er zijn ". count($zendingen) . " resultaten weergegeven </th></tr>"; 
} else {
	$output .= "<tr><th colspan='6' class='ui-corner-bottom'>Er zijn ". count($zendingen) . " resultaten weergegeven </th></tr>"; 
}
$output .= "</table>";
?>
<h1>Zending Details</h1>
<a class='button article ui-button ui-corner-all ui-widget' href="index.php?page=zendingen">&lt;&lt; Terug</a>
<?php echo $output ?>