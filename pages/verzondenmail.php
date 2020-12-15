<?php 
// Include classes.
require_once('class/class.mysql.php');
$nl = "\r\n";

// Load products from the database.
$query = "SELECT * FROM  vanda_transportmail ORDER BY date DESC" ;
if($result = mysql_query($query)){
	while($row = mysql_fetch_object($result)){
		$zendingen[] = $row;
	}	
}
unset($query);

$output = "<table id='product-table' class=\"ui-widget results\" cellpadding=\"0\" cellspacing=\"0\">".$nl;
$output .= "<thead class=\"ui-widget-header\">".$nl;
$output .= "<td>Ritnr</td><td>Datum</td><td>Onderwerp</td><td>Bericht</td><td>Verzonden</td>".$nl;
if($user->level && !$zending->verzonden){
	$output .= "<td class='ship'>&nbsp;</td>";	
} else { 
	$output .= "<td>&nbsp;</td>";
}
$output .= "</thead>".$nl;

if(count($zendingen)){
	foreach($zendingen as $zending){
		if($c == 1){ $output .= "	<tr class=\"grey\">".$nl; $c = 0;} else { $output .= "<tr>".$nl; $c = $c + 1; }
		$output .= "<td>$zending->id</td><td>$zending->date</td><td>$zending->subject</td><td widht='50%'>".strip_tags($zending->body)."</td><td>".$zending->verstuurd."</td>".$nl;
		$output .= "</tr>".$nl;
	}
}
$output .= "</table>".$nl;
$output .=  "<center>Er zijn ". count($zendingen) . " resultaten weergegeven <br>".$nl;

?>

<h1>Zendingen</h1>

<?php echo $output ?>