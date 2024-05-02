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
$output .= "<th>Lijst</th><th>Shipment_id</th><th>Klant</th><th>Datum</th><th>Aantal Colli</th><th>Verzonden</th>";

if($user->level && isset($zending) && !$zending->verzonden){
	$output .= "<th class='ship'>&nbsp;</th>";	
} else { 
	$output .= "<th>&nbsp;</th>";
}
$output .= "</tr>";

$c = 0;
if(count($zendingen)){
	foreach($zendingen as $zending){
		$output .= "
			<tr>
			<td>
				<a href='pages/generate/generate_pdf.php?ship_id=".$zending->ship_id."' target='_blanc'>
					<img src='images/printer.png' height='17' />
				</a> 
				<a href='pages/generate/generate_xlsx.php?ship_id=".$zending->ship_id."' target='_blanc'>
					<img src='images/excel.png' height='17' />
				</a>
			</td>
			<td>
				<a href='index.php?page=details&shipid=".$zending->ship_id."' >".$zending->ship_id."</a>
			</td>
			<td>
				<a href='index.php?page=details&shipid=".$zending->ship_id."' >".$zending->klant."</a>
			</td>
			<td>
				<a href='index.php?page=details&shipid=".$zending->ship_id."' >".date('d-m-Y',strtotime($zending->datum))."</a>
			</td>
			<td>".$zending->shipment_count."</td>
			<td>".$zending->verzonden."</td>";

		if($user->level && !$zending->verzonden){
			$output .= "<td class='ship' id='".$zending->ship_id."'><img src='images/truck.png' height='20' /></td>";	
		} 
		else { 
			$output .= "<td class='unship' id='".$zending->ship_id."'><img src='images/cross-16px.png' /></td>";
		}
		$output .= "</tr>";
	}
}
$output .= "</table>";
$output .=  "<center>Er zijn ". count($zendingen) . " resultaten weergegeven <br>";

?>

<h1>Zendingen</h1>

<?php echo $output ?>