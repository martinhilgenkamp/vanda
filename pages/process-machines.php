<?php
// Noodzakelijke dingen bij elkaar rapen.
session_start();

require_once("../inc/class/class.machines.php");

$mm = new MachineManager;

// make post safe en zet in post + session
foreach($_POST as $key => $val){
	$post[$key] = $val;
	$_SESSION[$key] = $val;
}

$post = (object)$post;

if(!$post->task){
	$post->task = '';
}

switch($post->task){
	case 'add':
		$data = array(
			"persoon" => $post->persoon,
			"kwaliteit" => $post->kwaliteit,
			"machine" => $post->machine,
			"datum" => date("Y-m-d H:i:s"),
			"verwijderd" => "0"
		);

		$machineresult = $mm->addMachine($data);
		echo $machineresult;
	break;
	case 'remove':
		if(!isset($post->id)){
			echo "FOUT! er zijn geen rollen geselecteerd";
			exit;
		} else {
			$mm->deleteMachine($post->id);
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