<!-- include scripts for rollen functionality !-->

<?php
require_once('class/class.rollen.php');

$roll = new Rolls;

echo $roll->getRollForm();

// DEBUG
//print_r($_SESSION);
?>