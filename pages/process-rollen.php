<?php
// Noodzakelijke dingen bij elkaar rapen.
session_start();
require_once('../inc/class/class.db.php');
require_once('../inc/class/class.article.php');
require_once("../inc/class/class.rollen.php");
require_once("../inc/class/class.rollship.php");

$roll = new RollsManager;
$articleManager = new ArticleManager();

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
		$roll->ProcessChildRolls($post);
	break;
	
		
		
	///////////////////////////////moet nog worden geprogrammeerd ////////////////////////////////////////	
	case 'edit':
		
		if($post->rollid){ //check if id is checked.

			$rollid = $post->rollid;
			$hidden = array('task', 'rollid'); //prevent program parameters to land in table
			$post->gewijzigd = date('Y-m-d H:i:s');
			

			$data = [];
			// Loop through post values			
			foreach($post as $col => $val) {
			  // build array
			  if($val && !in_array($col,$hidden)){
				$data[$col] = $val;
			  }
		    }

			if($roll->UpdateRoll($data, $post->rollid)){
				echo 'Opgeslagen';
			} else {
				echo 'Opslaan mislukt'.$db->error;	
			}
			
		} else {
			echo "Fout er is geen roll id opgegeven";
			exit;	
		}
	break;
		
	case 'verwijder':
		if(!$post->rollid){
			echo "FOUT! er zijn geen rollen geselecteerd";
			exit;
		} else {
			$roll->DeleteRolls($post->rollid);
			header("location:../index.php?page=rolltable");
		}
	break;
	
	case 'haalterug': 
		if(!$post->rollid){
			echo "FOUT! er zijn geen rollen geselecteerd";
			exit;
		} else {
			$roll->UnshipRolls($post->rollid);
			header("location:../index.php?page=rolltable");
		}
	break;
		
	case 'generate':
		if(!$post->rolnummer){
			echo "FOUT! er is geen rolnummer opgegegven";
			exit;
		} else {
			echo $roll->generateChildRolls($post->rolnummer,$post);	
		}
	break;
	
	///////////////////////////////moet nog worden geprogrammeerd ////////////////////////////////////////	
	case 'restore':
		if(!$post->id){
			echo "FOUT! er is geen id opgegegven";
			exit;
		} else {
			restoreArticle($post->id);	
		}
	break;
		
	case 'verzend':
		if(!$post->rollid && !$post->klant){
			echo "FOUT! er is geen id  of klant opgegegven";
			exit;
		} else {
			$roll->ShipRolls($post->rollid,$post->klant);	
			header("location:../index.php?page=rolltable");
		}
	break;
		
	case 'check':
		$rolls = $roll->loadActiveRolls($post->rolnummer);

		if(count($rolls) == 0) {
			 echo "false ".$post->rolnummer;
		} else {
			echo "true";
		}
	break;
}


function restoreArticle($id){
	
	// Load article values in to session;		
	$data = [
		"verwijderd" => 0,
	];
	$where = "`id` = ".$id;
	$wasUpdated = $articleManager->updateArticle($data, $where);
	if($wasUpdated){
		echo 'Hersteld';
	} else {
		echo 'Verzenden mislukt'.$db->error;	
	}
}

function RestoreSession(){
	$name = $_SESSION['username'];	
	session_destroy();
	session_start();
	$_SESSION['username'] = $name;
}
?>