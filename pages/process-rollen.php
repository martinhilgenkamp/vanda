<?php
// Noodzakelijke dingen bij elkaar rapen.
session_start();
require_once('../class/class.mysql.php');
require_once("../class/class.rollen.php");
require_once("../class/class.rollship.php");

$roll = new Rolls;

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
			global $db;
			$rollid = $post->rollid;
			$hidden = array('task', 'rollid'); //prevent program parameters to land in table
			$post->gewijzigd = date('Y-m-d H:i:s');
			
			// Loop through post values			
			foreach($post as $col => $val) {
			  // build array
			  if($val && !in_array($col,$hidden)){
				  if ($count++ != 0) $fields .= ', ';
				$fields .= "`$col` = '$val'";
			  }
		    }

			$query = "UPDATE `vanda_rolls` SET ".$fields." WHERE rollid = '".$post->rollid."' LIMIT 1";
			if($db->query($query)){
				echo 'Opgeslagon';
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
		$result = $db->query("SELECT rolnummer FROM `vanda_rolls` WHERE rolnummer = '$post->rolnummer'");
		if($result->num_rows == 0) {
			 echo "false ".$post->rolnummer;
		} else {
			echo "true";
		}
	break;
}


function restoreArticle($id){
	global $db;
	// Load article values in to session;		
	$query = "UPDATE `articles` SET `verwijderd` = '0' WHERE `id` = '".$id."' LIMIT 1";		// delete the article
	if($db->query($query)){
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