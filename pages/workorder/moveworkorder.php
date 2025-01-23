<?php 
date_default_timezone_set("Europe/Amsterdam");
header('Content-Type: application/json');

// Include necessary files
include_once '../../inc/class/class.db.php'; // Your database connection file
include_once '../../inc/class/class.workorder.php';



//Debug
//print_r($_POST); 


// Function to convert ISO 8601 to MySQL DATETIME
function convertToMySQLDateTime($isoDate) {
    if (!$isoDate) {
        return null; // Return null if the date is empty
    }
    try {
        $dateTime = new DateTime($isoDate);
        return $dateTime->format('Y-m-d H:i:s'); // Convert to MySQL DATETIME format
    } catch (Exception $e) {
        // Handle invalid date format
        error_log("Invalid date format: " . $isoDate);
        return null;
    }
}

// Retrieve POST values
$startISO = $_POST['start'] ?? null;
$stopISO = $_POST['stop'] ?? null;
$eventTitle = $_POST['eventtitle'] ?? null;
$resources = $_POST['resources'] ?? null;
$id = $_POST['id'] ?? null;

// Convert date values.
$startMySQL = convertToMySQLDateTime($startISO);
$stopMySQL = convertToMySQLDateTime($stopISO);

//Initiate DB connection
$db = new DB();

// Create an instance of the WorkOrder class
$workOrder = new WorkOrder($db); // Pass the $db connection object

// Call the searchWorkOrders method and output the results
echo $workOrder->MoveWorkOrder($id, $startMySQL, $stopMySQL);


?>