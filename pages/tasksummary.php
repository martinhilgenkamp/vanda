<?php
// prevent notifications
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", 1);

require_once("class/class.task.php");
require_once("class/class.mysql.php");
$task = new task;

// Process posted data
if(isset($_POST)){	
	$id = mysqli_real_escape_string($db, $_POST['id']);
	$name = mysqli_real_escape_string($db, $_POST['taak']);
	$description = mysqli_real_escape_string($db, $_POST['description']);
	$date = mysqli_real_escape_string($db, $_POST['date']);
	$action = mysqli_real_escape_string($db, $_POST['action']);
	
	switch ($action) {
		case 'new':
			$task->saveNew($name,$description,$date);
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

echo "<h1>Alle Geregistreerde taken</h1>";
$task->buildTable($task->getAll());	
echo "<br /><br />";
?>	