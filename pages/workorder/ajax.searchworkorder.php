<?php
date_default_timezone_set("Europe/Amsterdam");
header('Content-Type: application/json');

// Include necessary files
include_once '../../inc/class/class.db.php'; // Your database connection file
include_once '../../inc/class/class.workorder.php';

//Initiate DB connection
$db = new DB();

// Create an instance of the WorkOrder class
$workOrder = new WorkOrder($db); // Pass the $db connection object

// Get the search term from the request
$term = isset($_GET['term']) ? $_GET['term'] : '';

// Call the searchWorkOrders method and output the results
echo $workOrder->searchWorkOrderCustomers($term);

?>