<?php

// // Load suppliers from the database.
// $query = "SELECT * FROM vanda_suppliers WHERE verwijderd = 0 ORDER BY volgorde DESC, supplier_desc ASC;";
// if($result =$db->query($query)){
// 	while($row = $result->fetch_object()){
// 		$resSuppliers[$row->id] = $row;
// 	}	
// }

// // Load products from the database.
// $query = "SELECT * FROM vanda_products ORDER BY article_desc ASC;";
// if($result =$db->query($query)){
// 	while($row = $result->fetch_object()){
// 		$articles[$row->id] = $row;
// 	}	
// }

// // Load products from the database.
// $query = "SELECT * FROM vanda_products ORDER BY article_desc ASC;";
// if($result =$db->query($query)){
// 	while($row = $result->fetch_object()){
// 		$products[] = $row;
// 	}	
// }

// $task = $_POST['task'];
// if(!$task){
// 	$task = $_GET['task'];
// }

// $supplier_no = mysqli_real_escape_string($db,$_POST['supplier']);
// $article_no = mysqli_real_escape_string($db,$_POST['article']);
// $remark = mysqli_real_escape_string($db,$_POST['remark']);

// if($article_no){$article = $articles[$article_no];}
// if($article_no){$supplier = $resSuppliers[$supplier_no];}

require_once("../inc/class/class.supplier.php");
require_once("../inc/class/class.product.php");
require_once("../inc/class/class.registration.php");
require_once("../inc/class/class.production.php");
require_once("../inc/class/class.shipment.php");
require_once("../inc/class/class.transportmailer.php");

$sm = new SupplierManager();
$pm = new ProductManager();
$rm = new RegistrationManager();
$prm = new ProductionManager();
$stm = new ShipmentManager();
$tm = new TransportMailer();

$resSuppliers = $sm->loadSuppliers();
$resProducts = $pm->loadProducts();

// initializing request variable
 $task = '';

if ($_POST) {
	$task = $_POST['task'];
	if(!$task){
		$task = $_GET['task'];
	}

	$supplier_no = isset($_POST['supplier']) ?  intval($_POST['supplier']) : null;
	$article_no = isset($_POST['article']) ? intval($_POST['article']) : null;
	$remark = isset($_POST['remark']) ? intval($_POST['remark']) : null;

	if($article_no) {
		$article = $pm->getByArticleNumber($article_no);
	}

	if($supplier_no) {
		$supplier = $sm->getBySupplierNumber($supplier_no);
	}
}

$nl = "\r";

switch($task){
	case 'buildproduct':
		foreach($resProducts as $product){
			if(($supplier_no == 11 || $supplier_no == 12) && $product->id == 11){
				// aangepaste product anders weergeven..
				echo "		<a value=\"".$product->id."\" class=\"button article\">Aangepast</a>".$nl;	
			} elseif($supplier_no == 13 && $product->export == 3){
				echo "		<a value=\"".$product->id."\" class=\"button article\">".$product->article_desc."</a>".$nl;
			} elseif($supplier_no == 14) {
				// leeg veld doen
				echo " ";
			} else { // Check if belrey laat alleen export artikelen zien. 
				if($product->export == 0){ // laat alle niet export zien
					if($product->id == 11){ // aangepaste product anders weergeven..
						echo "		<a value=\"".$product->id."\" class=\"button article\">Aangepast</a>".$nl;	
					} else {
						echo "		<a value=\"".$product->id."\" class=\"button article\">".$product->article_desc."</a>".$nl;	
					}
				}	
			}
		}	// End function to fill in products
				
		// Mail buttons toevoegen
		if($supplier->transportmail == '1'){
			echo "		<hr>".$nl;
			echo "		<a value=\"".$supplier_no."\" class=\"button gettransport\">Wagen Halen</a>".$nl;
			echo "		<a value=\"".$supplier_no."\" class=\"button returntransport\">Wagen Terug</a>".$nl;
		}
	break;
	
	case 'accordion':
?>
	 	<div id="amount-buttons">
			<span class="amountbutton button ui-button ui-widget ui-state-default ui-corner-all ui-button-text ui-button-text-only" style="display: inline-block;
			padding: .4em;">0.5</span>
			<span class="amountbutton button ui-button ui-widget ui-state-default ui-corner-all ui-button-text ui-button-text-only" style="display: inline-block;
			padding: .4em;">1</span>
			<span class="amountbutton button ui-button ui-widget ui-state-default ui-corner-all ui-button-text ui-button-text-only" style="display: inline-block;
			padding: .4em;">2</span>
			<span class="amountbutton button ui-button ui-widget ui-state-default ui-corner-all ui-button-text ui-button-text-only" style="display: inline-block;
			padding: .4em;">3</span>
			<span class="amountbutton button ui-button ui-widget ui-state-default ui-corner-all ui-button-text ui-button-text-only" style="display: inline-block;
			padding: .4em;">4</span>
			<span class="amountbutton button ui-button ui-widget ui-state-default ui-corner-all ui-button-text ui-button-text-only" style="display: inline-block;
			padding: .4em;">5</span>
			<span class="amountbutton button ui-button ui-widget ui-state-default ui-corner-all ui-button-text ui-button-text-only" style="display: inline-block;
			padding: .4em;">6</span>
			<span class="amountbutton button ui-button ui-widget ui-state-default ui-corner-all ui-button-text ui-button-text-only" style="display: inline-block;
			padding: .4em;">7</span>
			<span class="amountbutton button ui-button ui-widget ui-state-default ui-corner-all ui-button-text ui-button-text-only" style="display: inline-block;
			padding: .4em;">8</span>
			<span class="amountbutton button ui-button ui-widget ui-state-default ui-corner-all ui-button-text ui-button-text-only" style="display: inline-block;
			padding: .4em;">9</span>
			<span class="amountbutton button ui-button ui-widget ui-state-default ui-corner-all ui-button-text ui-button-text-only" style="display: inline-block;
			padding: .4em;">10</span>
			<span class="amountbutton button ui-button ui-widget ui-state-default ui-corner-all ui-button-text ui-button-text-only" style="display: inline-block;
			padding: .4em;">11</span>
			<span class="amountbutton button ui-button ui-widget ui-state-default ui-corner-all ui-button-text ui-button-text-only" style="display: inline-block;
			padding: .4em;">12</span>
			<span class="amountbutton button ui-button ui-widget ui-state-default ui-corner-all ui-button-text ui-button-text-only" style="display: inline-block;
			padding: .4em;">13</span>
			<span class="amountbutton button ui-button ui-widget ui-state-default ui-corner-all ui-button-text ui-button-text-only" style="display: inline-block;
			padding: .4em;">14</span>
			<span class="amountbutton button ui-button ui-widget ui-state-default ui-corner-all ui-button-text ui-button-text-only" style="display: inline-block;
			padding: .4em;">15</span>
		</div>
		
		<input type="text" name="amount" id="amount" class="amount" value="2"/> x 
		<?php 
			if($article->id == 11){
				echo $article->article_no . ' - '; 
				echo "<input type=\"text\" value=\"\" name=\"remark\" id=\"remark\" />";
			} else {
				echo $article->article_no . ' - ' . $article->article_desc ; 
			}
		?>
		<a type="button" class="button submit"/></a>
		<a type="button" class="button reset"/></a>

<?php
	break;
	
	case 'register':
		$data = [
			"id" => null,
			"article_id" => $article->id,
			"supplier_id" => $supplier->id,
			"amount" => $_POST['amount'],
			"remark" => ($remark) ? $remark : null,
			"date" => date('Y-m-d H:i:s'),
		];

		$registrationId = $rm->addRegistration($data);

		if($registrationId > 0) {
			$registration = $rm->getRegistrationById($registrationId);

?>
			<table id="result_table" class="ui-widget results" cellpadding="0" cellspacing="0">
	            <thead class="ui-widget-header">
	            	<td>ID</td>
	                <td>Leverancier</td>
	                <td>Artikel nr.</td>
	                <td>Artikel Omschrijving</td>
	                <td>Aantal</td>
	                <td>Tijd</td>
	            </thead>
	            <tbody class='ui-widget-content'>
            		<?php
	            	if ($registration) {
						echo '<tr>';
						echo '<td>'.$registration->id.'</td><td>'.$registration->supplier.'</td><td>'.$registration->article_no.'</td><td>'.$registration->article_desc.'</td><td>'.str_replace('0.5','H',$registration->amount).'</td><td>'.$registration->date.'</td>';
						echo '</tr>';	
					}
					?>
	            </tbody>
            </table>
<?php	
		} else {
			die('Er is een fout opgetreden, probeer het opnieuw!');	
		}
	break;
	
	case 'delete':
		$result = $rm->deleteRegistration($_POST['rowid']);
		// unset($rowid);
		// $rowid = mysqli_real_escape_string($db,$_POST['rowid']);
		// if($rowid != '') {
		// 	$query = "DELETE FROM vanda_registrations WHERE vanda_registrations.id = ".$rowid.";";	
		// 	$db->query($query) or die('fail');
		// 	echo "success";
		// }
	break;
	
	case 'delete-stock':
		if ($prm->deleteProduction($_POST['rowid'])) {
			echo "success";
		} else {
			echo "fail";
		}
		// unset($rowid);
		// $rowid = mysqli_real_escape_string($db,$_POST['rowid']);
		// if($rowid != '') {
		// 	$query = "UPDATE vanda_production SET removed = '1' WHERE id = '".$rowid."';";
		// 	$db->query($query) or die('fail'.$db->error());
		// 	echo "success";
		// }
	break;
	
	case 'ship':
		if ($stm->updateShipment('1', $_POST['rowid'])) {
			echo "success";
		} else {
			echo "fail";
		}
		// unset($rowid);
		// $rowid = mysqli_real_escape_string($db,$_POST['rowid']);
		// if($rowid != '') {
		// 	$query = "UPDATE vanda_shipment SET verzonden = '1' WHERE ship_id = '".$rowid."';";	
		// 	$db->query($query) or die('fail');
		// 	echo "success";
		// }
	break;
	
	case 'unship':
		if ($stm->updateShipment('0', $_POST['rowid'])) {
			echo "success";
		} else {
			echo "fail";
		}
		// unset($rowid);
		// $rowid = mysqli_real_escape_string($db,$_POST['rowid']);
		// if($rowid != '') {
		// 	$query = "UPDATE vanda_shipment SET verzonden = '0' WHERE ship_id = '".$rowid."';";	
		// 	$db->query($query) or die('fail');
		// 	echo "success";
		// }
	break;
	
	case 'unship-article':
		if ($prm->unshipArticle($_POST['rowid'])) {
			echo "success";
		} else {
			echo "fail";
		}

		// $rowid = mysqli_real_escape_string($db,$_POST['rowid']);
		// if($rowid != '') {
		// 	$query = "UPDATE vanda_production SET  shipping_id =  '0', geleverd =  '0000-00-00 00:00:00'  WHERE  id = '".$rowid."';" ;
		// 	$db->query($query) or die('fail'.$db->error());
		// 	echo "success";
		// }
	break;
	
	case 'insertartikel':
		$data = [
			"artikelnummer" => strtoupper($_POST['artikelnummer']),
			"kwaliteit" => $_POST['kwaliteit'],
			"gewicht" => $_POST['gewicht'],
			"datum" => date('Y-m-d H:i:s'),
			"barcode" => $_POST['barcode'],
			"ordernr" => $_POST['ordernr']
		];

		$productionId = $prm->addProduction($data);

		if($productionId > 0){
			echo "success";
		} else {
			echo "Er is een fout opgetreden bij het opslaan! Probeer het opnieuw" . $db->error();	
		}

		// $post = $_POST;	
		// $artikelnummer = strtoupper(mysqli_real_escape_string ($db,$post['artikelnummer']));
		// $kwaliteit = mysqli_real_escape_string ($db,$post['kwaliteit']);
		// $gewicht = mysqli_real_escape_string ($db,$post['gewicht']);
		// $datum = date('Y-m-d H:i:s');
		// $barcode = mysqli_real_escape_string ($db,$post['barcode']);
		// $ordernr = mysqli_real_escape_string ($db,$post['ordernr']);
		
		// $query = "INSERT INTO `vanda_production` (`id`, `artikelnummer`, `kwaliteit`, `gewicht`, `datum`, `geleverd`, `shipping_id`, `barcode`, `ordernr`) VALUES (NULL, '".$artikelnummer."', '".$kwaliteit."', '".$gewicht."', '".$datum."', '', '', '".$barcode."', '".$ordernr."');";
		
		// if($db->query($query)){
		// 	echo "success";
		// } else {
		// 	echo "Er is een fout opgetreden bij het opslaan! Probeer het opnieuw" . $db->error();	
		// }
	break;
	
	case 'getnewbarcode':
		$barcode = $prm->getNewBarcode();
		
		// $query = "SELECT barcode FROM `vanda_production` ORDER BY barcode DESC LIMIT 1;";
		// if($result =$db->query($query)){
		// 	while($row = $result->fetch_object()){
		// 		$barcode = $row->barcode;
		// 	}	
		// // Geef laatste barcode terug.
		// }
		$barid =  (int)substr($barcode, -10)+1;
		// Waarde voor barcode genereren.
		$barid = str_pad($barid, 10, '0', STR_PAD_LEFT);
		$barcode = 'F00830'.$barid;
		echo $barcode;
	break;
		
	case 'getshipid':
		header('Content-Type: application/json');

		$klanten[] = $stm->getShipmentId();

		// $query = "SELECT ship_id FROM `vanda_shipment`;";
		// if($result =$db->query($query)){
		// 	while($row = $result->fetch_array()){
		// 		$klanten[] = $row[0];
		// 	}	
		// }
		echo json_encode($klanten);
	break;
	
	case 'getbarcode':
		header('Content-Type: application/json');

		$barcode[] = $prm->getBarcode();

		// $query = "SELECT barcode FROM vanda_production WHERE shipping_id = 0 ORDER BY barcode ASC;";
		// if($result =$db->query($query)){
		// 	while($row = $result->fetch_array()){
		// 		$barcode[] = $row[0];
		// 	}	
		// }
		echo json_encode($barcode);
	break;
		
	case 'gettransport':
		$supplier_no = $_POST['supplier'];
		
		// Generate mail object and create ritnr in the database
		$ritnummer = $tm->save();
		
		// Prepare subject and messagebody
		$subject = 'Transportverzoek Ritnr: '.$ritnummer;
		$body = $tm->BuildGetBody($ritnummer,$resSuppliers[$supplier_no]->supplier_desc,$resSuppliers[$supplier_no]->transporttype);
		
		$tm->Subject = $subject;
		
		//Set who the message is to be sent to
		$tm->addAddress('expeditie@verhoek-europe.com', 'Verhoek Expeditie'); //change for debug
		
		$tm->msgHTML($body);
		//Replace the plain text body with one created manually
		$tm->AltBody = strip_tags($body);

		//send the message, check for errors
		// Function to use UpdateStatus($id, $subject, $body, $status){
		if (!$tm->send()) {
			echo "Mailer Error: " . $mail->ErrorInfo;
			$tm->UpdateStatus($ritnummer,$subject,$body, '0');
		} else {
			$tm->UpdateStatus($ritnummer,$subject,$body, '1');	
			echo 'transport onderweg van '.$resSuppliers[$supplier_no]->supplier_desc." naar Vanda met ritnummer ".$ritnummer;
		}

		// $supplier_no = mysqli_real_escape_string($db,$_POST['supplier']);

		// require_once('../class/class.transportmailer.php');
		
		// // Generate mail object and create ritnr in the database
		// $mail = new TransportMailer();
		// $ritnummer = $mail->save();
		
		// // Prepare subject ande messagebody
		
		// $subject = 'Transportverzoek Ritnr: '.$ritnummer;
		// $body = $mail->BuildGetBody($ritnummer,$resSuppliers[$supplier_no]->supplier_desc,$resSuppliers[$supplier_no]->transporttype);
		
		// $mail->Subject = $subject;
		
		// //Set who the message is to be sent to
		// $mail->addAddress('expeditie@verhoek-europe.com', 'Verhoek Expeditie');
		// //$mail->addAddress('martin@pruim.eu', 'Martin Hilgenkamp');
		
		// $mail->msgHTML($body);
		// //Replace the plain text body with one created manually
		// $mail->AltBody = strip_tags($body);

		// //send the message, check for errors
		// // Function to use UpdateStatus($id, $subject, $body, $status){
		// if (!$mail->send()) {
		// 	echo "Mailer Error: " . $mail->ErrorInfo;
		// 	$mail->UpdateStatus($ritnummer,$subject,$body, '0');
		// } else {
		// 	$mail->UpdateStatus($ritnummer,$subject,$body, '1');	
		// 	echo 'transport onerweg van '.$resSuppliers[$supplier_no]->supplier_desc." naar Vanda met ritnummer ".$ritnummer;
		// //	echo "Message sent!";
		// }
	break;
		
	case 'returntransport':
		$supplier_no = $_POST['supplier'];		
		
		$ritnummer = $tm->save();
		
		// Prepare subject and messagebody
		$subject = 'Transportverzoek Ritnr: '.$ritnummer;
		$body = $tm->BuildReturnBody($ritnummer,$resSuppliers[$supplier_no]->supplier_desc,$resSuppliers[$supplier_no]->transporttype);
		
		$tm->Subject = $subject;
		
		//Set who the message is to be sent to
		$tm->addAddress('expeditie@verhoek-europe.com', 'Verhoek Expeditie'); //change for debug

		$tm->msgHTML($body);
		//Replace the plain text body with one created manually
		$tm->AltBody = strip_tags($body);

		//send the message, check for errors
		// Function to use UpdateStatus($id, $subject, $body, $status){
		if (!$tm->send()) {
			echo "Mailer Error: " . $tm->ErrorInfo;
			$tm->UpdateStatus($ritnummer,$subject,$body, '0');
		} else {
			$tm->UpdateStatus($ritnummer,$subject,$body, '1');	
			echo 'transport onerweg van Vanda naar '.$resSuppliers[$supplier_no]->supplier_desc."Met ritnummer ".$ritnummer ;
		}

		// $supplier_no = mysqli_real_escape_string($db,$_POST['supplier']);
		// require_once('../class/class.transportmailer.php');
		
		// // Generate mail object and create ritnr in the database
		// $mail = new TransportMailer();
		// $ritnummer = $mail->save();
		
		// // Prepare subject ande messagebody
		
		// $subject = 'Transportverzoek Ritnr: '.$ritnummer;
		// $body = $mail->BuildReturnBody($ritnummer,$resSuppliers[$supplier_no]->supplier_desc,$resSuppliers[$supplier_no]->transporttype);
		
		// $mail->Subject = $subject;
		
		// //Set who the message is to be sent to
		// $mail->addAddress('expeditie@verhoek-europe.com', 'Verhoek Expeditie');
		// //$mail->addAddress('martin@pruim.eu', 'Martin Hilgenkamp');
		
		
		// $mail->msgHTML($body);
		// //Replace the plain text body with one created manually
		// $mail->AltBody = strip_tags($body);

		// //send the message, check for errors
		// // Function to use UpdateStatus($id, $subject, $body, $status){
		// if (!$mail->send()) {
		// 	echo "Mailer Error: " . $mail->ErrorInfo;
		// 	$mail->UpdateStatus($ritnummer,$subject,$body, '0');
		// } else {
		// 	$mail->UpdateStatus($ritnummer,$subject,$body, '1');	
		// 	echo 'transport onerweg van Vanda naar '.$resSuppliers[$supplier_no]->supplier_desc."Met ritnummer ".$ritnummer ;
		// //	echo "Message sent!";
		// }
	break;
	default:
	
}	// END SWITCH TASK
?>
