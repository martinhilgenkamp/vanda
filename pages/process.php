<?php
date_default_timezone_set("Europe/Amsterdam");
	
// Include classes.
require_once('../class/class.mysql.php');
$task = $_POST['task'];
if(!$task){
	$task = $_GET['task'];
}

// Load suppliers from the database.
$query = "SELECT * FROM vanda_suppliers WHERE verwijderd = 0 ORDER BY volgorde DESC, supplier_desc ASC;";
if($result =$db->query($query)){
	while($row = $result->fetch_object()){
		$suppliers[$row->id] = $row;
	}	
}

// Load products from the database.
$query = "SELECT * FROM vanda_products ORDER BY article_desc ASC;";
if($result =$db->query($query)){
	while($row = $result->fetch_object()){
		$articles[$row->id] = $row;
	}	
}

// Load products from the database.
$query = "SELECT * FROM vanda_products ORDER BY article_desc ASC;";
if($result =$db->query($query)){
	while($row = $result->fetch_object()){
		$products[] = $row;
	}	
}



$supplier_no = mysqli_real_escape_string($db,$_POST['supplier']);
$article_no = mysqli_real_escape_string($db,$_POST['article']);
$remark = mysqli_real_escape_string($db,$_POST['remark']);

if($article_no){$article = $articles[$article_no];}
if($article_no){$supplier = $suppliers[$supplier_no];}

$nl = "\r";


switch($task){
	case 'buildproduct':
	foreach($products as $product){
			// VRAAG VAN ANDRE CONDOR EX GELIJK ZETTEN AAN CONDOR 25-10-2016
			//
			//if($supplier_no == 8){ //check for belrey products
				//if($product->export == 1){
				//echo "		<a value=\"".$product->id."\" class=\"button article\">".$product->article_desc."</a>".$nl;
				//}
			//} else
			if($supplier_no == 11 || $supplier_no == 12){
				if($product->id == 11){ // aangepaste product anders weergeven..
					echo "		<a value=\"".$product->id."\" class=\"button article\">Aangepast</a>".$nl;	
				}
			} elseif($supplier_no == 13){
				if($product->export == 3){
					echo "		<a value=\"".$product->id."\" class=\"button article\">".$product->article_desc."</a>".$nl;
				}
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
		if($suppliers[$supplier_no]->transportmail == '1'){
			echo "		<hr>".$nl;
			echo "		<a value=\"".$supplier_no."\" class=\"button gettransport\">Wagen Halen</a>".$nl;
			echo "		<a value=\"".$supplier_no."\" class=\"button returntransport\">Wagen Terug</a>".$nl;
		}
	break;
	
	case 'accordion':?>

<!-- Oude aantal functie 		
<select name="amount" id="amount">
		<option value='0.5' "<?php/*  ($i == $article->default_amount ? 'selected="selected" ' : '' ); */?>">H</option>";
	<?php 
		/* for($i=1; $i<26; $i++ ){
			echo "<option value='".$i."' ".($i == $article->default_amount ? 'selected="selected" ' : '' ).">".$i."</option>";
		} */
	?>
</select>
!-->
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
	$article = mysqli_real_escape_string($db,$_POST['article']);
	$supplier = mysqli_real_escape_string($db,$_POST['supplier']);
	$amount = mysqli_real_escape_string($db,$_POST['amount']);
	$remark = mysqli_real_escape_string($db,$_POST['remark']);
	
	if($remark == 'undefined'){ unset($remark); }
	
	if($remark){
		$query = "INSERT INTO vanda_registrations (id,article_id,supplier_id,amount,remark,date) VALUES (NULL ,".$article.",".$supplier.",".$amount.",'".$remark."','".date('Y-m-d H:i:s')."');";
	} else {
		$query = "INSERT INTO vanda_registrations (id,article_id,supplier_id,amount,date) VALUES (NULL ,".$article.",".$supplier.",".$amount.",'".date('Y-m-d H:i:s')."');";
	}		
		if($db->query($query)){
			$select = "SELECT  vanda_registrations.id as id, vanda_registrations.date as date, vanda_suppliers.supplier_desc as supplier, vanda_products.article_no as article_no,IF(vanda_registrations.remark = '',vanda_products.article_desc,vanda_registrations.remark) article_desc, vanda_registrations.amount as amount FROM vanda_registrations LEFT JOIN vanda_suppliers ON vanda_registrations.supplier_id = vanda_suppliers.id 
LEFT JOIN vanda_products ON vanda_registrations.article_id = vanda_products.id ORDER BY vanda_registrations.date DESC LIMIT 0,2";
			if($result =$db->query($select)){
				while($row = $result->fetch_object()){
					$rows[] = $row;
				}	
			}
			// Hier komt de output voor de statusbalk.
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
				foreach($rows as $row){
					echo '<tr>';
					echo '<td>'.$row->id.'</td><td>'.$row->supplier.'</td><td>'.$row->article_no.'</td><td>'.$row->article_desc.'</td><td>'.str_replace('0.5','H',$row->amount).'</td><td>'.$row->date.'</td>';
					echo '</tr>';	
				}
				?>
            </tbody>
            </table>
			<?php	
		} else {
			die('Er is een fout opgetreden! probeer het opnieuw'.$db->error());	
		}
	
	break;
	
	case 'delete':
		unset($rowid);
		$rowid = mysqli_real_escape_string($db,$_POST['rowid']);
		if($rowid != '') {
			$query = "DELETE FROM vanda_registrations WHERE vanda_registrations.id = ".$rowid.";";	
			$db->query($query) or die('fail');
			echo "success";
		}
	break;
	
	case 'delete-stock':
		unset($rowid);
		$rowid = mysqli_real_escape_string($db,$_POST['rowid']);
		if($rowid != '') {
			$query = "UPDATE vanda_production SET removed = '1' WHERE id = '".$rowid."';";
			$db->query($query) or die('fail'.$db->error());
			echo "success";
		}
	break;
	
	case 'ship':
		unset($rowid);
		$rowid = mysqli_real_escape_string($db,$_POST['rowid']);
		if($rowid != '') {
			$query = "UPDATE vanda_shipment SET verzonden = '1' WHERE ship_id = '".$rowid."';";	
			$db->query($query) or die('fail');
			echo "success";
		}
	break;
	
	case 'unship':
		unset($rowid);
		$rowid = mysqli_real_escape_string($db,$_POST['rowid']);
		if($rowid != '') {
			$query = "UPDATE vanda_shipment SET verzonden = '0' WHERE ship_id = '".$rowid."';";	
			$db->query($query) or die('fail');
			echo "success";
		}
	break;
	
	case 'unship-article':
		$rowid = mysqli_real_escape_string($db,$_POST['rowid']);
		if($rowid != '') {
			$query = "UPDATE vanda_production SET  shipping_id =  '0', geleverd =  '0000-00-00 00:00:00'  WHERE  id = '".$rowid."';" ;
			$db->query($query) or die('fail'.$db->error());
			echo "success";
		}
	break;
	
	case 'insertartikel':
		// Set post values in vars	
		$post = $_POST;	
		$artikelnummer = strtoupper(mysqli_real_escape_string ($db,$post['artikelnummer']));
		$kwaliteit = mysqli_real_escape_string ($db,$post['kwaliteit']);
		$gewicht = mysqli_real_escape_string ($db,$post['gewicht']);
		$datum = date('Y-m-d H:i:s');
		$barcode = mysqli_real_escape_string ($db,$post['barcode']);
		$ordernr = mysqli_real_escape_string ($db,$post['ordernr']);
		
		$query = "INSERT INTO `vanda_production` (`id`, `artikelnummer`, `kwaliteit`, `gewicht`, `datum`, `geleverd`, `shipping_id`, `barcode`, `ordernr`) VALUES (NULL, '".$artikelnummer."', '".$kwaliteit."', '".$gewicht."', '".$datum."', '', '', '".$barcode."', '".$ordernr."');";
		
		if($db->query($query)){
			echo "success";
		} else {
			echo "Er is een fout opgetreden bij het opslaan! Probeer het opnieuw" . $db->error();	
		}
		
	break;
	
	case 'getnewbarcode':
	$query = "SELECT barcode FROM `vanda_production` ORDER BY barcode DESC LIMIT 1;";
		if($result =$db->query($query)){
			while($row = $result->fetch_object()){
				$barcode = $row->barcode;
			}	
		// Geef laatste barcode terug.
		}
		$barid =  (int)substr($barcode, -10)+1;
		// Waarde voor barcode genereren.
		$barid = str_pad($barid, 10, '0', STR_PAD_LEFT);
		$barcode = 'F00830'.$barid;
		echo $barcode;
	break;
		
	case 'getshipid':
		header('Content-Type: application/json');
		$query = "SELECT ship_id FROM `vanda_shipment`;";
		if($result =$db->query($query)){
			while($row = $result->fetch_array()){
				$klanten[] = $row[0];
			}	
		}
		echo json_encode($klanten);
	break;
	
	case 'getbarcode':
		header('Content-Type: application/json');
		$query = "SELECT barcode FROM vanda_production WHERE shipping_id = 0 ORDER BY barcode ASC;";
		if($result =$db->query($query)){
			while($row = $result->fetch_array()){
				$barcode[] = $row[0];
			}	
		}
		echo json_encode($barcode);
	break;
		
	case 'gettransport':
		$supplier_no = mysqli_real_escape_string($db,$_POST['supplier']);
		require_once('../class/class.transportmailer.php');
		
		// Generate mail object and create ritnr in the database
		$mail = new TransportMailer();
		$ritnummer = $mail->save();
		
		// Prepare subject ande messagebody
		
		$subject = 'Transportverzoek Ritnr: '.$ritnummer;
		$body = $mail->BuildGetBody($ritnummer,$suppliers[$supplier_no]->supplier_desc,$suppliers[$supplier_no]->transporttype);
		
		$mail->Subject = $subject;
		
		//Set who the message is to be sent to
		$mail->addAddress('expeditie@verhoek-europe.com', 'Verhoek Expeditie');
		//$mail->addAddress('martin@pruim.eu', 'Martin Hilgenkamp');
		
		$mail->msgHTML($body);
		//Replace the plain text body with one created manually
		$mail->AltBody = strip_tags($body);

		//send the message, check for errors
		// Function to use UpdateStatus($id, $subject, $body, $status){
		if (!$mail->send()) {
			echo "Mailer Error: " . $mail->ErrorInfo;
			$mail->UpdateStatus($ritnummer,$subject,$body, '0');
		} else {
			$mail->UpdateStatus($ritnummer,$subject,$body, '1');	
			echo 'transport onerweg van '.$suppliers[$supplier_no]->supplier_desc." naar Vanda met ritnummer ".$ritnummer;
		//	echo "Message sent!";
		}

	break;
		
	case 'returntransport':
		$supplier_no = mysqli_real_escape_string($db,$_POST['supplier']);
		require_once('../class/class.transportmailer.php');
		
		// Generate mail object and create ritnr in the database
		$mail = new TransportMailer();
		$ritnummer = $mail->save();
		
		// Prepare subject ande messagebody
		
		$subject = 'Transportverzoek Ritnr: '.$ritnummer;
		$body = $mail->BuildReturnBody($ritnummer,$suppliers[$supplier_no]->supplier_desc,$suppliers[$supplier_no]->transporttype);
		
		$mail->Subject = $subject;
		
		//Set who the message is to be sent to
		$mail->addAddress('expeditie@verhoek-europe.com', 'Verhoek Expeditie');
		//$mail->addAddress('martin@pruim.eu', 'Martin Hilgenkamp');
		
		
		$mail->msgHTML($body);
		//Replace the plain text body with one created manually
		$mail->AltBody = strip_tags($body);

		//send the message, check for errors
		// Function to use UpdateStatus($id, $subject, $body, $status){
		if (!$mail->send()) {
			echo "Mailer Error: " . $mail->ErrorInfo;
			$mail->UpdateStatus($ritnummer,$subject,$body, '0');
		} else {
			$mail->UpdateStatus($ritnummer,$subject,$body, '1');	
			echo 'transport onerweg van Vanda naar '.$suppliers[$supplier_no]->supplier_desc."Met ritnummer ".$ritnummer ;
		//	echo "Message sent!";
		}
	break;
	default:
	
}	// END SWITCH TASK
?>
