<?php
// Load requirements
date_default_timezone_set("Europe/Amsterdam");
require_once('../../inc/class/class.workorder.php');

header('Content-Type: application/json');

$workorder = new WorkOrder();
$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;
echo $workorder->getWorkordersJson();

?>