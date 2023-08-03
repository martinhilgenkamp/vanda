<?php
// Include classes.
require_once('inc/class/class.option.php');

$optionManager = new OptionManager();

$formWasSaved = false;
if(isset($_POST['opslaan'])){
	// Get variables.
	$data = [
		"bedrijfskenmerk" => $_POST['bedrijfskenmerk'],
		"ponummer" => $_POST['ponummer'],
		"maat1x" => $_POST['maat1x'],
		"maat1y" => $_POST['maat1y'],
		"maat2x" => $_POST['maat2x'],
		"maat2y" => $_POST['maat2y'],
		"maat3x" => $_POST['maat3x'],
		"maat3y" => $_POST['maat3y'],
		"TransportName" => $_POST['TransportName'],
		"TransportEmailAddress" => $_POST['TransportEmailAddress'],
		"TransportFromName" => $_POST['TransportFromName'],
		"TransportFromEmailAddress" => $_POST['TransportFromEmailAddress'],
		"MachineCount" => $_POST['MachineCount']
	];
	
	if (!$optionManager->updateOptionRow($data)) {
		die ('Er is een fout opgetreden met opslaan: ');
	}
	$formWasSaved = true;
 } 
 
// Load values from database
$result = $optionManager->getAllOptions()[0];

$bedrijfskenmerk = $result->bedrijfskenmerk;
$ponummer = $result->ponummer;

$maat1x = $result->maat1x;
$maat1y = $result->maat1y;

$maat2x = $result->maat2x;
$maat2y = $result->maat2y;

$maat3x = $result->maat3x;
$maat3y = $result->maat3y;

$TransportName = $result->TransportName;	
$TransportEmailAddress = $result->TransportEmailAddress;	
$TransportFromName = $result->TransportFromName;	
$TransportFromEmailAddress = $result->TransportFromEmailAddress;
$MachineCount = $result->MachineCount;	

?>
<h1>Opties</h1>
<h2>Stansen</h2>
<form id="optieform" name="optieform" method="post">
<center><div id="notice"><span><?php if ($formWasSaved) { echo('Opgeslagen'); } ?></span></div></center>
 	<ul>
    	<li><label for="bedrijfskenmerk">Bedrijfs Kenmerk:</label><input type="text" name="bedrijfskenmerk" id="bedrijfskenmerk" value="<?php echo ($bedrijfskenmerk ? $bedrijfskenmerk : '') ?>"></li>
    	<li><label for="ponummer">PO Nummer:</label><input type="text" name="ponummer" id="ponummer" value="<?php echo ($ponummer ? $ponummer : '') ?>"></li>
        <li><label for="ponummer">PO Nummer:</label><input type="text" name="ponummer" id="ponummer" value="<?php echo ($ponummer ? $ponummer : '') ?>"></li>
       
        <li><label for="ponummer">Maat1 X:</label><input type="text" name="maat1x" id="maat1x" value="<?php echo ($maat1x ? $maat1x : '') ?>"></li>
        <li><label for="ponummer">Maat1 Y:</label><input type="text" name="maat1y" id="maat1y" value="<?php echo ($maat1y ? $maat1y : '') ?>"></li>
        
        <li><label for="ponummer">Maat2 X:</label><input type="text" name="maat2x" id="maat2x" value="<?php echo ($maat2x ? $maat2x : '') ?>"></li>
        <li><label for="ponummer">Maat2 Y:</label><input type="text" name="maat2y" id="maat2y" value="<?php echo ($maat2y ? $maat2y : '') ?>"></li>
        
        <li><label for="ponummer">Maat3 X:</label><input type="text" name="maat3x" id="maat3x" value="<?php echo ($maat3x ? $maat3x : '') ?>"></li>
        <li><label for="ponummer">Maat3 Y:</label><input type="text" name="maat3y" id="maat3y" value="<?php echo ($maat3y ? $maat3y : '') ?>"></li>
    </ul>
	<h2>Logistiek</h2>
	<ul>
		<li><B>Transport E-Mail Afzender:</B></li>
		<li><label for="TransportFromName">Van:</label><input type="text" name="TransportFromName" value="<?php echo ($TransportFromName ? $TransportFromName : '') ?>"></li>
		<li><label for="TransportFromEmailAddress">E-Mail</label><input type="text" name="TransportFromEmailAddress" value="<?php echo ($TransportFromEmailAddress ? $TransportFromEmailAddress : '') ?>"></li>
		<li><B>Transport E-Mail versturen naar:</B></li>
		<li><label for="TransportName">Naar:</label><input type="text" name="TransportName" value="<?php echo ($TransportName ? $TransportName : '') ?>"></li>
		<li><label for="TransportEmailAddress">E-Mail</label><input type="text" name="TransportEmailAddress" value="<?php echo ($TransportEmailAddress ? $TransportEmailAddress : '') ?>"></li>
	</ul>
	<h2>Machines</h2>
	<ul>
		<li><B>Machine Pagina:</B></li>
		<li><label for="MachineCount">Aantal Machines:</label><input type="text" name="MachineCount" value="<?php echo ($MachineCount ? $MachineCount : '') ?>"></li>
		<li>&nbsp;</li>
		<li><label for="opslaan">Opslaan:</label><input type="submit" name="opslaan" value="Opslaan"></li>
	</ul>
	
</form>