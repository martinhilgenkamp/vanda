<?php
require_once('inc/class/class.rollen.php');

$roll = new RollsManager;

if($_GET['id']){
	$id = $_GET['id'];
} elseif ($_POST['id']){
	$id = $_POST['id'];
} 

if($id){
	$vals = $roll->loadRoll($id);
	foreach(get_object_vars($vals) as $key => $val){
		$_SESSION[$key] = $val;
	}
	$_SESSION['rollid'] = $id;
	echo $roll->getRollEditForm();
} else {
	'<div><h2>Fout er is geen rolnummer opgegeven</h2></div>';
}

// DEBUG
//print_r($_SESSION);
?>