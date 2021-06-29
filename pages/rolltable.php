<?php
// DEBUG
	error_reporting(E_ALL ^ E_NOTICE);
 	ini_set("display_errors", 1);

require_once('inc/class/class.rollen.php');


$roll = new RollsManager;

echo $roll->getTable();

// DEBUG
//print_r($_SESSION);


?>