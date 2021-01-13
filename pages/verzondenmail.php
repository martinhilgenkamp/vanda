<?php 
// Include classes.
require_once('inc/class/class.db.php');
$nl = "\r\n";

// Load products from the database.
$db = new DB();
$query = "SELECT * FROM  vanda_transportmail WHERE 1=1 ORDER BY date DESC" ;
$zendingen = $db->selectQuery($query);

unset($query);

$output = "<table id='product-table' class=\"ui-widget results\" cellpadding=\"0\" cellspacing=\"0\">".$nl;
$output .= "<thead class=\"ui-widget-header\">".$nl;
$output .= "<td>Ritnr</td><td>Datum</td><td>Onderwerp</td><td>Bericht</td><td>Verzonden</td>".$nl;

$output .= "</thead>".$nl;

$c = 0;

foreach($zendingen as $zending){
	$c = $c + 1;
	if($c % 2 == 0){ 
			$output .= "	<tr class=\"grey\">".$nl;
	} else {
		$output .= "<tr>".$nl;
	}

	$output .= "<td>$zending->id</td><td>$zending->date</td><td>$zending->subject</td><td widht='50%'>".strip_tags($zending->body)."</td><td>".$zending->verstuurd."</td>".$nl;
	$output .= "</tr>".$nl;
}

$output .= "</table>".$nl;
$output .=  "<center>Er zijn ". count($zendingen) . " resultaten weergegeven <br>".$nl;

?>

<h1>Zendingen</h1>

<?php echo $output ?>