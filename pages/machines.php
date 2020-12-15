<a href="index.php?page=productie" class="ui-button ui-corner-all ui-widget" style="position: absolute"><< Productie</a>
<h1>Machine registratie</h1>

<?php
// prevent notifications
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", 1);
require_once("class/class.machines.php");
$nl = "\r\n";



$machine = new Machine;
//Define amount of machines
$machines = 8;

echo $machine->getEditForm($machines);
		
?>
	