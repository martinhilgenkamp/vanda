<?php
// DEBUG
	error_reporting(E_ALL ^ E_NOTICE);
 	ini_set("display_errors", 1);

require_once('class/class.rollen.php');


$roll = new Rolls;

echo $roll->getTable();

// DEBUG
//print_r($_SESSION);


?>