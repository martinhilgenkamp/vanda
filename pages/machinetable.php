<?php
// DEBUG
	error_reporting(E_ALL ^ E_NOTICE);
 	ini_set("display_errors", 1);

require_once('class/class.machines.php');


$machine = new Machine;
echo "<h1>Productie overzicht machines</h1>"; 
echo $machine->getTable();

// DEBUG
//print_r($_SESSION);


?>