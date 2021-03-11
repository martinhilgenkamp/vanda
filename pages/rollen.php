<!-- include scripts for rollen functionality !-->
<h1>Rollen inboeken</h1>

<?php
require_once('inc/class/class.rollen.php');

$roll = new RollsManager;

echo $roll->getRollForm();

// DEBUG
//print_r($_SESSION);
?>