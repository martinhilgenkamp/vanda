<?php
// DEBUG
	error_reporting(E_ALL ^ E_NOTICE);
 	ini_set("display_errors", 1);

require_once('inc/class/class.rollen.php');
require_once("inc/class/class.rollship.php");


$roll = new RollsManager;
$ship = new RollsShipment;

echo $ship->showShipmentTable();

// DEBUG
//print_r($_SESSION);


?>