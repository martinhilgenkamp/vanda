<?php
require_once('PHPMailer/PHPMailerAutoload.php');
class TransportMailer extends PHPMailer
{	
	
	 function __construct()
	{
		parent::__construct();
		
		$this->isSMTP();
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$this->SMTPDebug = 0;
		//Ask for HTML-friendly debug output
		$this->Debugoutput = 'html';
		//Set the hostname of the mail server\
		$this->Host = "mail1.pruim.eu";
		//Set the SMTP port number - likely to be 25, 465 or 587
		$this->Port = 587;
		//Whether to use SMTP authentication
		$this->SMTPAuth = true;
		//Username to use for SMTP authentication
		$this->Username = "server@pruim.eu";
		//Password to use for SMTP authentication
		$this->Password = "Appelsap";
		//Set who the message is to be sent from
		$this->setFrom('rlimburg@bloemert.com', 'Vanda Carpets'); //magazijn@vandacarpets.nl
		//Set an alternative reply-to address
		$this->addReplyTo('rlimburg@bloemert.com', 'Vanda Carpets'); //magazijn@vandacarpets.nl
		
	}
	
	function BuildGetBody($ritnummer,$supplier,$type){
		$nl = "\r\n";
		
		if($supplier == "Vebe Floorcoverings BV"){
			$supplier = "Vebe PVC Afdeling";
		}
		
		$output = "<!doctype html>".$nl;
		$output .= "<html>".$nl;
		$output .= "<head>".$nl;
		$output .= "<meta charset='utf-8'>".$nl;
		$output .= "<title>Ophaalverzoek Ritnr: ".$ritnummer."</title>".$nl;
		$output .= "</head>".$nl;
		$output .= "<body>".$nl;
		$output .= "<h1>Transportverzoek Ritnr: ".$ritnummer." van ".$supplier." naar Vanda Carpets</h1>	".$nl;
		$output .= "<p>Graag ".$type." ophalen bij ".$supplier." voor Vanda Carpets</p>".$nl;
		$output .= "<p>Met vriendelijke groet,<br/>".$nl;
		$output .= "Vanda Carpets.</p>".$nl;
		$output .= "</body>".$nl;
		$output .= "</html>".$nl;
		return $output;
	}
	
	function BuildReturnBody($ritnummer,$supplier,$type){
		$nl = "\r\n";
		
		if($supplier == "Vebe Floorcoverings BV"){
			$supplier = "Vebe PVC Afdeling";
		}
		
		$output = "<!doctype html>".$nl;
		$output .= "<html>".$nl;
		$output .= "<head>".$nl;
		$output .= "<meta charset='utf-8'>".$nl;
		$output .= "<title>Ophaalverzoek Ritnr: ".$ritnummer."</title>".$nl;
		$output .= "</head>".$nl;
		$output .= "<body>".$nl;
		$output .= "<h1>Transportverzoek Ritnr: ".$ritnummer." van Vanda Carpets naar ".$supplier."</h1>	".$nl;
		$output .= "<p>Graag ".$type." ophalen bij Vanda Carpets naar ".$supplier."</p>".$nl;
		$output .= "<p>Met vriendelijke groet,<br/>".$nl;
		$output .= "Vanda Carpets.</p>".$nl;
		$output .= "</body>".$nl;
		$output .= "</html>".$nl;
		return $output;
	}
	
	function Save(){
		global $db;
		
		$query = "INSERT INTO `vanda_transportmail` (`id`, `date`, `to`,`subject`, `body`, `verstuurd`) VALUES (NULL, '".date('Y-m-d H:i:s')."', 'rlimburg@bloemert.com', '', '', '0');"; //martin@pruim.nl
		if(!$db->query($query)){
			echo "Fout bij het opslaan ".$db->error;
		}
		return $db->insert_id;
	}
	
	function UpdateStatus($id, $subject, $body, $status){
		global $db;
		$body = mysqli_real_escape_string($db,$body);
		$query = "UPDATE `vanda_transportmail` SET `body` = '".$body."', `verstuurd` = '".$status."', `subject` = '".$subject."' WHERE `vanda_transportmail`.`id` = ".$id.";";
		
		if(!$db->query($query)){
			echo "Fout bij het opslaan ".$db->error;
		} 
	}
	
}

?>