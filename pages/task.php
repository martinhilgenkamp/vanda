<?php
require_once("inc/class/class.task.php");

$tm = new TaskManager();

$view = isset($_GET['view']) ? $_GET['view'] : '';

// Process posted data
if ($_POST) {
	switch ($_POST['action']) {
		case 'new':
			$data = [
				"name" => $_POST['taak'],
				"description" => $_POST['description'],
				"adres" => $_POST['adres'],
				"date" => $_POST['date'],
				"filename" => $_POST['filename'],
				"status" => 0,
			];

			$tm->addTask($data);
			unset($_POST);
		break;
		case 'behandeling':
			$tm->processTask($_POST['id']);
			exit();
		break;
		case 'gereed':
			$tm->completeTask($_POST['id']);
			exit();
		break;
	} 
}

echo "<h1>Open Taken</h1>";
$tm->buildTable($tm->getAllOpen($view));	
echo "<br /><br />";

$tm->showForm();
echo "<br /><br />";

?>	