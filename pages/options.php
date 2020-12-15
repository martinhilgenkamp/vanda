<?php
// Include classes.
require_once('class/class.mysql.php');

if(isset($_POST['opslaan'])){
	// Get variables.
	$bedrijfskenmerk = mysql_real_escape_string($_POST['bedrijfskenmerk']);
	$ponummer = mysql_real_escape_string($_POST['ponummer']);
	$maat1x = mysql_real_escape_string($_POST['maat1x']);
	$maat1y = mysql_real_escape_string($_POST['maat1y']);
	$maat2x = mysql_real_escape_string($_POST['maat2x']);
	$maat2y = mysql_real_escape_string($_POST['maat2y']);
	$maat3x = mysql_real_escape_string($_POST['maat3x']);
	$maat3y = mysql_real_escape_string($_POST['maat3y']);
	
	$query = "UPDATE vanda_options SET ponummer = '".$ponummer."', bedrijfskenmerk = '".$bedrijfskenmerk."', maat1x = '".$maat1x."', maat1y = '".$maat1y."', maat2x = '".$maat2x."', maat2y = '".$maat2y."', maat3x = '".$maat3x."', maat3y = '".$maat3y."' WHERE id = '1'";
	mysql_query($query) or die ('Er is een fout opgetreden met opslaan: '. mysql_error());	
 } else  {
	 // Load values from database
	$query = "SELECT * FROM `vanda_options` WHERE id = '1'";
	$result = mysql_query($query) or die ('Er is een fout opgetreden met laden van de gegevens: '. mysql_error());
	$result = mysql_fetch_object($result);
	
	$bedrijfskenmerk = $result->bedrijfskenmerk;
	$ponummer = $result->ponummer;
	
	$maat1x = $result->maat1x;
	$maat1y = $result->maat1y;
	
	$maat2x = $result->maat2x;
	$maat2y = $result->maat2y;
	
	$maat3x = $result->maat3x;
	$maat3y = $result->maat3y;	
 }

?>
<h1>Opties</h1>
<form id="optieform" name="optieform" method="post">
 	<ul>
    	<li><label for="bedrijfskenmerk">Bedrijfs Kenmerk:</label><input type="text" name="bedrijfskenmerk" id="bedrijfskenmerk" value="<?php echo ($bedrijfskenmerk ? $bedrijfskenmerk : '') ?>"</li>
    	<li><label for="ponummer">PO Nummer:</label><input type="text" name="ponummer" id="ponummer" value="<?php echo ($ponummer ? $ponummer : '') ?>"</li>
        <li><label for="ponummer">PO Nummer:</label><input type="text" name="ponummer" id="ponummer" value="<?php echo ($ponummer ? $ponummer : '') ?>"</li>
       
        <li><label for="ponummer">Maat1 X:</label><input type="text" name="maat1x" id="maat1x" value="<?php echo ($maat1x ? $maat1x : '') ?>"</li>
        <li><label for="ponummer">Maat1 Y:</label><input type="text" name="maat1y" id="maat1y" value="<?php echo ($maat1y ? $maat1y : '') ?>"</li>
        
        <li><label for="ponummer">Maat2 X:</label><input type="text" name="maat2x" id="maat2x" value="<?php echo ($maat2x ? $maat2x : '') ?>"</li>
        <li><label for="ponummer">Maat2 Y:</label><input type="text" name="maat2y" id="maat2y" value="<?php echo ($maat2y ? $maat2y : '') ?>"</li>
        
        <li><label for="ponummer">Maat3 X:</label><input type="text" name="maat3x" id="maat3x" value="<?php echo ($maat3x ? $maat3x : '') ?>"</li>
        <li><label for="ponummer">Maat3 Y:</label><input type="text" name="maat3y" id="maat3y" value="<?php echo ($maat3y ? $maat3y : '') ?>"</li>
       
        <li><label for="opslaan">Opslaan:</label><input type="submit" name="opslaan" value="Opslaan"></li>
    </ul>
</form>