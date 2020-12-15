<?php 
// Include classes.
require_once('class/class.mysql.php');
$nl = "\r\n";

// Load products from the database.
$query = "SELECT * FROM  vanda_shipment GROUP BY datum DESC" ;
if($result = $db->query($query)){
	while($row = $result->fetch_object()){
		$zendingen[] = $row;
	}	
}

unset($query);

$output = "<table id='product-table' class=\"ui-widget results\" cellpadding=\"0\" cellspacing=\"0\">".$nl;
$output .= "<thead class=\"ui-widget-header\">".$nl;
$output .= "<td>Lijst</td><td>Shipment_id</td><td>Klant</td><td>Datum</td><td>Verzonden</td>".$nl;
if($user->level && !$zending->verzonden){
	$output .= "<td class='ship'>&nbsp;</td>";	
} else { 
	$output .= "<td>&nbsp;</td>";
}
$output .= "</thead>".$nl;

if(count($zendingen)){
	foreach($zendingen as $zending){
		if($c == 1){ $output .= "	<tr class=\"grey\">".$nl; $c = 0;} else { $output .= "<tr>".$nl; $c = $c + 1; }
		$output .= "<td><a href='inc/generate_pdf.php?ship_id=".$zending->ship_id."' target='_blanc'><img src='images/printer.png' height='17' /></a> <a href='inc/generate_xlsx.php?ship_id=".$zending->ship_id."' target='_blanc'><img src='images/excel.png' height='17' /></a></td><td><a href='index.php?page=details&shipid=".$zending->ship_id."' >".$zending->ship_id."</a></td><td><a href='index.php?page=details&shipid=".$zending->ship_id."' >".$zending->klant."</a></td><td><a href='index.php?page=details&shipid=".$zending->ship_id."' >".date('d-m-Y',strtotime($zending->datum))."</a></td><td>".$zending->verzonden."</td>".$nl;
		if($user->level && !$zending->verzonden){
			$output .= "<td class='ship' id='".$zending->ship_id."'><img src='images/truck.png' height='20' /></td>";	
		} else { 
			$output .= "<td class='unship' id='".$zending->ship_id."'><img src='images/cross-16px.png' /></td>";
		}
		$output .= "</tr>".$nl;
	}
}
$output .= "</table>".$nl;
$output .=  "<center>Er zijn ". count($zendingen) . " resultaten weergegeven <br>".$nl;

?>

<h1>Zendingen</h1>

<?php echo $output ?>