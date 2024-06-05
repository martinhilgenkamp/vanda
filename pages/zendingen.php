<?php 

require_once("inc/class/class.shipment.php");
require_once("inc/class/class.option.php");

$sm = new ShipmentManager();
$om = new OptionManager();
$options = $om->getAllOptions()[0];

if($options->shiphistory > 1){
	$history = $options->shiphistory;
}

// Load products from the database.
$zendingen = $sm->getAllShipments($history);

$output = "<table id='product-table' class=\"data-table\" cellpadding=\"0\" cellspacing=\"0\">";
$output .= "<tr>";
$output .= "<th class='ui-corner-tl'>Lijst</th><th>Shipment_id</th><th>Klant</th><th>Datum</th><th>Aantal Colli</th><th class='ui-corner-tr' colspan='2'>Verzonden</th>";


// This can be removed.
//if($user->level && isset($zending) && !$zending->verzonden){
//	$output .= "<th class='ship'>&nbsp;</th>";	
//} else { 
//	$output .= "<th>&nbsp;</th>";
//}
$output .= "</tr>";

$c = 0;
if(count($zendingen)){
	foreach($zendingen as $zending){
		$output .= "
			<tr class=\"clickable-row\" data-href=\"index.php?page=details&ship_id=".$zending->ship_id."\">
			<td>
				<a href='pages/generate/generate_pdf.php?ship_id=".$zending->ship_id."' target='_blanc'>
					<img src='images/printer.png' height='20' />
				</a> 
				<a href='pages/generate/generate_excel.php?ship_id=".$zending->ship_id."' target='_blanc'>
					<img src='images/excel.png' height='20' />
				</a>
			</td>
			<td>
				<a href='index.php?page=details&ship_id=".$zending->ship_id."' >".$zending->ship_id."</a>
			</td>
			<td>
				<a href='index.php?page=details&ship_id=".$zending->ship_id."' >".$zending->klant."</a>
			</td>
			<td>
				<a href='index.php?page=details&ship_id=".$zending->ship_id."' >".date('d-m-Y',strtotime($zending->datum))."</a>
			</td>
			<td>".$zending->shipment_count."</td>
			<td>".$zending->verzonden."</td>";

		if($user->level && !$zending->verzonden){
			$output .= "<td class='ship' id='".$zending->ship_id."'><img src='images/truck.png' height='20' /></td>";	
		} 
		else if($user->level && $zending->verzonden){ 
			$output .= "<td class='unship' id='".$zending->ship_id."'><img src='images/cross-16px.png' /></td>";
		} else if (!$zending->verzonden){
			$output .= "<td><img src='images/cross-16px.png' /></td>";
		} else {
			$output .= "<td>yes</td>";
		}
		$output .= "</tr>";
	}
}
$output .= "<tr><th colspan='7' class='ui-corner-bottom'>Er zijn ". count($zendingen) . " resultaten weergegeven </th></tr>"; 
$output .= "</table>";
?>

<h1>Zendingen</h1>

<?php echo $output ?>