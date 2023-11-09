<?php
	// DEBUG
	error_reporting(E_ALL ^ E_NOTICE);
 	ini_set("display_errors", 1);

	session_start();
	date_default_timezone_set("Europe/Amsterdam");

	
	//Import PHPMailer classes into the global namespace
	//These must be at the top of your script, not inside a function
	use PHPMailer\PHPMailer\PHPMailer as PHPMailer ;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

    //require_once("../inc/class/class.transportmailer.php");
    //$tm = new TransportMailer();

	//Load Composer's autoloader
	require 'vendor/autoload.php';

    
    require_once("inc/class/class.transportmailer.php");
    $tm = new TransportMailer();

    print_r($tm)

    ?>