<?php
date_default_timezone_set("Europe/Amsterdam");
require_once('inc/class/class.workorder.php');
$db = new DB();
$workorder = new Workorder($db);
<<<<<<< HEAD
$sort = $_GET['sort'] ?? 'id';
$order = $_GET['order'] ?? 'asc';
$searchTerm = $_GET['search'] ?? '';
$workorder->getWorkorders(20, $sort, $order, $searchTerm);
=======
$workorder->getWorkorders(20);
>>>>>>> ramon
