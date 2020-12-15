<?php
// DEBUG
	error_reporting(E_ALL ^ E_NOTICE);
 	ini_set("display_errors", 1);

require_once('class/class.rollen.php');
require_once("class/class.rollship.php");


$roll = new Rolls;
$ship = new RollsShipment;

echo $ship->showShipmentTable();

// DEBUG
//print_r($_SESSION);


?>