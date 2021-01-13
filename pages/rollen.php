<!-- include scripts for rollen functionality !-->

<?php
require_once('inc/class/class.rollen.php');

$roll = new RollsManager;

echo $roll->getRollForm();

// DEBUG
//print_r($_SESSION);
?>