<?php
	// prevent notifications
	error_reporting(E_ALL ^ E_NOTICE);
	ini_set("display_errors", 0);
	date_default_timezone_set("Europe/Amsterdam");

	//start mysqli connection.
	$db = new mysqli('localhost', 'root', '@ppels@p', 'vanda');

	// throw error if connection failed.
	if($db->connect_errno > 0){
		die('Unable to connect to database [' . $db->connect_error . ']');
	}

	
?>