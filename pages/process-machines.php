<?php
// Noodzakelijke dingen bij elkaar rapen.
session_start();
require_once('../class/class.mysql.php');
require_once("../class/class.machines.php");

$mm = new MachineManager;

// make post safe en zet in post + session
foreach($_POST as $key => $val){
	$post[$key] = $val;
	$_SESSION[$key] = $val;
}

$post = (object)$post;

if(!$post->task){
	$post-> task = '';
}

switch($post->task){
	case 'add':
		$mm->addMachine($post);
	break;
	case 'remove':
		if(!$post->id){
			echo "FOUT! er zijn geen rollen geselecteerd";
			exit;
		} else {
			$mm->Delete($post->id);	
		}
	break;
}

function RestoreSession(){
	$name = $_SESSION['username'];	
	session_destroy();
	session_start();
	$_SESSION['username'] = $name;
}

?>