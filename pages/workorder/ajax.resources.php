<?php
date_default_timezone_set("Europe/Amsterdam");
header('Content-Type: application/json');

// Include necessary files
include_once '../../inc/class/class.db.php'; // Your database connection file
//include_once '../../inc/class/class.workorder.php';
include_once '../../inc/class/class.user.php';

// Get the start and end times from the request
$startTime = $_GET['start'] ?? null;
$endTime = $_GET['end'] ?? null;
$ExistingResource = $_GET['ExistingResource'] ?? null;



if (!$startTime || !$endTime) {
    http_response_code(400);
    echo json_encode(['error' => 'Start and end times are required.']);
    exit;
}

$um = new UserManager();
if(!$ExistingResource){
    echo $um->getAvailableResources($startTime, $endTime); 
} else {
    echo $um->getResources();
}

?>