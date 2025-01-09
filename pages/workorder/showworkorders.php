<?php
date_default_timezone_set("Europe/Amsterdam");
require_once('inc/class/class.workorder.php');
$db = new DB();
$workorder = new Workorder($db);
$workorder->getWorkorders(20);
?>