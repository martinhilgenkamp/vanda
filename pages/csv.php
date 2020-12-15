<?php

require_once("../inc/class/class.machines.php");

$mm = new MachineManager;

$fileName = 'export.csv'; 
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header('Content-Description: File Transfer');
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename={$fileName}");
header("Expires: 0");
header("Pragma: public");
$fh = @fopen('php://output', 'w');

// Load data from database
$query = stripslashes($_POST['query']);
$results = $mm->executeQuery($query);

// Build header
fputcsv($fh, array_keys((array)$results[0]), ';');
 
// Populate table
foreach ($results as $data) {
    fputcsv($fh, get_object_vars($data), ';');
}

// Close the file
fclose($fh);

// Make sure nothing else is sent, our file is done
exit;
?>