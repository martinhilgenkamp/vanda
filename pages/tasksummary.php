<?php
// prevent notifications
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", 1);

require_once("inc/class/class.task.php");

$task = new TaskManager;

// Process posted data
if(isset($_POST)){	
	$id = $_POST['id'];
	$name = $_POST['taak'];
	$description = $_POST['description'];
	$date = $_POST['date'];
	$action = $_POST['action'];
	
	switch ($action) {
		case 'new':
			$newTaskdata = [
				"name" => $name,
				"description" => $description,
				"date" => $date
			];
			$task->addTask($newTaskdata);
			unset($_POST);
		break;
		case 'behandeling':
			$task->processTask($id);
			exit();
		break;
		case 'gereed':
			$task->completeTask($id);
			exit();
		break;
	} 
	
}

echo "<h1>Alle Geregistreerde taken</h1>";
$task->buildTable($task->getAll());	
echo "<br /><br />";
?>	