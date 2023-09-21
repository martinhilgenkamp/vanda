<?php
// prevent notifications
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", 1);

require_once("inc/class/class.task.php");

$tm = new TaskManager;

// Process posted data
if($_POST){		
	switch ($_POST['action']) {
		case 'new':
			$newTaskdata = [
				"name" => $_POST['taak'],
				"description" => $_POST['description'],
				"date" => $_POST['date'],
				"filename" => $_POST['filename'],
				"status" => 0,
			];
			$tm->addTask($newTaskdata);
			unset($_POST);
		break;
		case 'behandeling':
			$tm->processTask($id);
			exit();
		break;
		case 'gereed':
			$tm->completeTask($id);
			exit();
		break;
	} 
	
}

echo "<h1>Alle Geregistreerde taken</h1>";
$tm->buildTable($tm->getAll());	
echo "<br /><br />";
?>	