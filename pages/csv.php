<?php
#DEBUG PURPOSE
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Noodzakelijke dingen bij elkaar rapen.
require '../vendor/autoload.php';
require_once('../inc/class/class.db.php');
date_default_timezone_set("Europe/Amsterdam");


$db = new DB();


// Load data from database
$query = stripslashes($_POST['query']);
#$query = "SELECT * FROM  vanda_production  WHERE vanda_production.datum BETWEEN '2023-04-05' AND '2024-04-05 23:59:59'  AND vanda_production.shipping_id = 0  AND vanda_production.removed = '0'  ORDER BY `datum` DESC";
if($query){
    #echo $query; // DEBUG
    $result = $db->selectQuery($query);

    if($result){
        $currentDateTime = date('Y-m-d_H-i-s');
        $fileName = 'export'.$currentDateTime. '.csv'; 
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$fileName}");
        header("Expires: 0");
        header("Pragma: public");
        
        // Open the output stream
        $fh = @fopen('php://output', 'w');

        // Build header with column names
        fputcsv($fh, array_keys((array)$result[0]), ';');
 
        // Populate table
        foreach ($result as $data) {
            fputcsv($fh, get_object_vars($data), ';');
        }


        // Close the file
        fclose($fh);

        exit;
    } else {
        // If query execution fails, handle the error (display an error message, log it, etc.)
        echo "Error executing query: " . mysqli_error($db);
    }
} else {
     // If query execution fails, handle the error (display an error message, log it, etc.)
     echo "Error executing query: EMPTY";
}
// Make sure nothing else is sent, our file is done
exit;
?>


