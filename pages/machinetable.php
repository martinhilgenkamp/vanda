<?php

require_once("inc/class/class.machines.php");

$mm = new MachineManager;

echo "<h1>Productie overzicht machines</h1>"; 
echo $mm->getTable();

?>