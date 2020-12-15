<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", 1);

// Include classes.
require_once('../class/class.mysql.php');

$query = stripslashes ($_POST['query']);

$fileName = 'export.csv'; 
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header('Content-Description: File Transfer');
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename={$fileName}");
header("Expires: 0");
header("Pragma: public");
$fh = @fopen( 'php://output', 'w' );

// Load data from database
$results = array();
$result = $db->query($query);
while($row = $result->fetch_assoc()){
		$results[] = $row;
	}

// Build header
$keys = array_keys($results[0]);
fputcsv($fh, $keys , ';');
 
// Populate table
foreach ( $results as $data ) {
    
    fputcsv($fh, $data, ';');
}
// Close the file
fclose($fh);
// Make sure nothing else is sent, our file is done
exit;



?>