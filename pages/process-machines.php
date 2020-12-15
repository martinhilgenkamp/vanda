<?php
// Noodzakelijke dingen bij elkaar rapen.
session_start();
require_once('../class/class.mysql.php');
require_once("../class/class.machines.php");

$machine = new Machine;

// make post safe en zet in post + session
foreach($_POST as $key => $val){
	$post[$key] = $val;
	$_SESSION[$key] = $val;
}

$post = (object) $post;


if(!$post->task){
	$post-> task = '';
}

switch($post->task){
	case 'add':
		$machine->add($post);
	break;
	case 'remove':
		if(!$post->id){
			echo "FOUT! er zijn geen rollen geselecteerd";
			exit;
		} else {
			$machine->Delete($post->id);	
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