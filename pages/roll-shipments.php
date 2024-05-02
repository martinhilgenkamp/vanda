<?php
// DEBUG
	error_reporting(E_ALL ^ E_NOTICE);
 	ini_set("display_errors", 1);

require_once('inc/class/class.rollen.php');
require_once("inc/class/class.rollship.php");
require_once("inc/class/class.option.php");

$om = new OptionManager();
$options = $om->getAllOptions()[0];

$history = '';

if($options->shiphistory > 1){
	$history = $options->shiphistory;
}

$roll = new RollsManager;
$ship = new RollsShipment;

echo $ship->showShipmentTable($history);

// DEBUG
//print_r($_SESSION);


?>