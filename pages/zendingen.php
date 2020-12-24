<?php 

require_once("inc/class/class.shipment.php");

$sm = new ShipmentManager();

// Load products from the database.
$zendingen = $sm->getAllShipments();

$output = "<table id='product-table' class=\"ui-widget results\" cellpadding=\"0\" cellspacing=\"0\">";
$output .= "<thead class=\"ui-widget-header\">";
$output .= "<td>Lijst</td><td>Shipment_id</td><td>Klant</td><td>Datum</td><td>Verzonden</td>";

if($user->level && isset($zending) && !$zending->verzonden){
	$output .= "<td class='ship'>&nbsp;</td>";	
} else { 
	$output .= "<td>&nbsp;</td>";
}
$output .= "</thead>";

$c = 0;
if(count($zendingen)){
	foreach($zendingen as $zending){
		if($c == 1) { 
			$output .= "<tr class=\"grey\">"; 
			$c = 0;
		} 
		else { 
			$output .= "<tr>"; 
			$c = $c + 1; 
		}
		$output .= "
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