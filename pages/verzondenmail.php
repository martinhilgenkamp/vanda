<?php 
// Include classes.
require_once('inc/class/class.db.php');
$cutOffLimit = 100;
// Load products from the database.
$db = new DB();
$query = "SELECT * FROM  vanda_transportmail WHERE 1=1 ORDER BY date DESC" ;
$zendingen = $db->selectQuery($query);

unset($query);

$output = "<table id='product-table' class=\"data-table\" cellpadding=\"0\" cellspacing=\"0\">";
$output .= "<tr>";
$output .= "<th>Ritnr</th><th>Datum</th><th>Onderwerp</th><th>Bericht</th><th>Verzonden</th>";

$output .= "</tr>";

$c = 0;

foreach(array_slice($zendingen, 0, $cutOffLimit) as $zending){

	$output .= "<tr>
					<td>$zending->id</td>
					<td>$zending->date</td>
					<td>$zending->subject</td>
					<td widht='50%'>".strip_tags($zending->body)."</td>
					<td>".$zending->verstuurd."</td>
				</tr>";
}

$output .= "</table>";
$output .=  "<center>Er zijn ".$cutOffLimit." van " . count($zendingen) . " resultaten weergegeven <br>";

?>

<h1>Zendingen</h1>

<?php echo $output ?>