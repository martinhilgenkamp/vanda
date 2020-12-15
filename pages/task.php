<?php
// prevent notifications
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", 1);

require_once("class/class.task.php");
require_once("class/class.mysql.php");
$task = new task;
$view = $_GET['view'];

// Process posted data
if(isset($_POST)){	
	$id = mysqli_real_escape_string($db, $_POST['id']);
	$name = mysqli_real_escape_string($db, $_POST['taak']);
	$description = mysqli_real_escape_string($db, $_POST['description']);
	$date = mysqli_real_escape_string($db, $_POST['date']);
	$action = mysqli_real_escape_string($db, $_POST['action']);
	$adres = mysqli_real_escape_string($db, $_POST['adres']);
	$filename = mysqli_real_escape_string($db, $_POST['filename']);
	
	switch ($action) {
		case 'new':
			$task->saveNew($name,$description,$date,$filename,$adres);
			unset($_POST);
		break;
		case 'behandeling':
			$task->Process($id);
			exit();
		break;
		case 'gereed':
			$task->Complete($id);
			exit();
		break;
	} 
	

}

echo "<h1>Verlopen Taken</h1>";
$task->buildTable($task->getExpired($view));	
echo "<br /><br />";

echo "<h1>Open Taken</h1>";
$task->buildTable($task->getAllOpen($view));	
echo "<br /><br />";

$task->showForm();
echo "<br /><br />";


?>	