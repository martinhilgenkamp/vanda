<?php 

require_once("inc/class/class.production.php");
require_once("inc/class/class.option.php");

$prm = new ProductionManager();
$om = new OptionManager();

// Waarde voor barcode genereren. deze word alleen gebruikt voor het eerste etiket in de weergave.
$newBarcode = $prm->getNewBarcode();
$barid = str_pad($newBarcode, 10, '0', STR_PAD_LEFT);
$barcode = 'F00830'.$barid;	

$options = $om->getAllOptions()[0];
?>
	
<h1>Registreer Stansen</h1>
<div id="inputform-container">
	<form id="stansform" name="insertform" method="post" target="_blank">
		<input type="hidden" id="productiedatum" name="productiedatum" value="<?php echo date('Y-m-d H:i:s'); ?>"/>

		<ul class="mobilelist" id="stanslist">
			<!-- <li><label for="ordernummer">Ordernummer: </label><input type="text" id="ordernummer" name="ordernummer" /></li> !-->
			<li id="stansbuttons-container">
				<a class='article ui-button ui-widget ui-corner-all' value='<?php echo $options->maat1x.'X'.$options->maat1y ; ?>' role='button' aria-disabled='false'>
					<span class='ui-button-text'><?php echo $options->maat1x.'X'.$options->maat1y ; ?></span>
				</a>
				<a class='article ui-button ui-widget ui-corner-all' value='<?php echo $options->maat2x.'X'.$options->maat2y ; ?>' role='button' aria-disabled='false'>
					<span class='ui-button-text'><?php echo $options->maat2x.'X'.$options->maat2y ; ?></span>
				</a>
				<a class='article ui-button ui-widget ui-corner-all' value='<?php echo $options->maat3x.'X'.$options->maat3y ; ?>' role='button' aria-disabled='false'>
					<span class='ui-button-text'><?php echo $options->maat3x.'X'.$options->maat3y ; ?></span>
				</a>
			</li>
			<li>
				<p>
					<strong>Anders:</strong><br>
					<label for="lengte">Lengte:</label><input type="text" class="ui-corner-all textfield" id="lengte" name="lengte" /><br> 
					<label for="breedte">Breedte: </label><input type="text" class="ui-corner-all textfield" id="breedte" name="breedte" />
				</p>
			</li>
			<li>
				<label for="gewicht">Aantal: </label><input type="text" id="gewicht" class="ui-corner-all textfield" name="gewicht" disabled="disabled"/>
			</li>
			<li>
				<label for="artikelnummer">Kwaliteit: </label><input type="text" class="ui-corner-all textfield" id="artikelnummer" name="artikelnummer"/>
			</li>
			<li>
				<label for="ordernr">Ordernr: </label><input type="text" class="ui-corner-all textfield" id="ordernr" name="ordernr"/>
			</li>
			<li>
				<label for="colli">Colli: </label><input type="text" class="ui-corner-all textfield" id="colli" name="colli"/>
			</li>
			<li>
			<!--<div id="barcode-display"><?php echo $barcode; ?></div>!-->
			</li>
			<li>
				<input type="reset" class="ui-button ui-widget ui-corner-all" name="reset" value="Reset" id="reset" />
				<input type="button" class="button ui-button ui-widget ui-corner-all" id="verstuur" name="verstuur" value="Verstuur" />
			</li>
		</ul>
		<br><br><br><br>
		<input type="hidden" id="kwaliteit" name="kwaliteit" value=""/>
		<input type="hidden" id="barcode" name="barcode" value="<?php echo $barcode; ?>"/>
		<input type="hidden" id="task" name="task" value="add-stans">
	</form>
</div>
<iframe id="printFrame" width="0" height="0"/>
