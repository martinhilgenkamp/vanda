<?php
require_once('PHPMailer/PHPMailerAutoload.php');
require_once("class.db.php");
require_once("class.option.php");

class TransportMailer extends PHPMailer
{	
	
	function __construct()
	{
		parent::__construct();
		
		$om = new OptionManager();
		$options = $om->getAllOptions()[0];

		$this->isSMTP();
		//Set SMTP options
		$this->SMTPOptions = array(
			'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
			)
		);
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$this->SMTPDebug = 0;
		//Ask for HTML-friendly debug output
		$this->Debugoutput = 'html';
		//Set the hostname of the mail server\
		$this->Host = "mx1.pruim.nl";
		//Set the SMTP port number - likely to be 25, 465 or 587
		$this->Port = 587;
		//Whether to use SMTP authentication
		$this->SMTPAuth = true;
		//Username to use for SMTP authentication
		$this->Username = "server@pruim.eu";
		//Password to use for SMTP authentication
		$this->Password = "Appelsap";
		//Set who the message is to be sent from
		//DEBUG
		$this->setFrom($options->TransportFromEmailAddress, $options->TransportFromName); //change for prod
		$this->addReplyTo($options->TransportFromEmailAddress, $options->TransportFromName); //change for prod
		//PRODUCTION
		//$this->setFrom('magazijn@vandacarpets.nl', 'Vanda Carpets'); //change for debug
		//$this->addReplyTo('magazijn@vandacarpets.nl', 'Vanda Carpets'); //change for debug
	
		$this->db = new DB();
	}
	
	function BuildGetBody($ritnummer,$supplier,$type){
		$nl = "\r\n";
		

		//Change supplier name if needed.
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
		} elseif ($supplier == "Condor Grass"){
			$supplier = "Condor Carpets afdeling grass";
		} elseif ($supplier == "Condor Carpets BV"){
			$supplier = "Condor Carpets afdeling tuft";
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
		
		$query = "INSERT INTO `vanda_transportmail` (`id`, `date`, `to`,`subject`, `body`, `verstuurd`) VALUES (NULL, '".date('Y-m-d H:i:s')."', 'martin@pruim.nl', '', '', '0');"; 
		$data = [
			"date" => date('Y-m-d H:i:s'),
			"to" => "martin@pruim.nl", //change for debug
			"verstuurd" => 0
		];

		$insertId = $this->db->insertQuery("vanda_transportmail", $data);

		if($insertId == 0){
			echo "Fout bij het opslaan ";
		}
		return $insertId;
	}
	
	function UpdateStatus($id, $subject, $body, $status){
		
		$data = [
			"verstuurd" => $status,
			"body" => $body,
			"subject" => $subject
		];
		
		$isSucceeded = $this->db->updateQuery("vanda_transportmail", $data, "`vanda_transportmail`.`id` = ".$id);
		if (!$isSucceeded) {
			echo "Fout bij het opslaan ".$db->error;
		} 
	}
	
}

?>