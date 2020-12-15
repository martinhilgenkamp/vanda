<?php 

require_once("inc/class/class.production.php");
require_once("inc/class/class.product.php");

$prm = new ProductionManager();
$pm = new ProductManager();

// Get the task
if ($_POST) {
	switch($_POST['task']){
		case 'add':
			$data = [
				"id" => null,
				"artikelnummer" => strtoupper($_POST['artikelnummer']),
				"kwaliteit" => $_POST['kwaliteit'],
				"gewicht" => $_POST['gewicht'],
				"datum" => date('Y-m-d H:i:s'),
				"barcode" => $_POST['barcode']
			];

			$productionId = $prm->addProduction($data);
					
			if($productionId > 0){
				echo "success<br /><a href='http://web01/vanda2/index.php?page=insert'>Doorgaan</a>";
			} else {
				echo "Er is een fout opgetreden bij het opslaan! ".mysqli_error($db);	
			} 
		break;		
	}
}

// Waarde voor barcode genereren.
$newBarcode = $prm->getNewBarcode();
$barid = str_pad($newBarcode, 10, '0', STR_PAD_LEFT);
$barcode = 'F00830'.$barid;

// Load products from the database.
$products = $pm->getProductsByExport(2);

?>
<a href="index.php?page=machines" class="ui-button ui-corner-all ui-widget" style="float: right": 0px;>Machines >></a>
<h1>Boek product in</h1>
<div id="inputform-container">
	<form id="insertform" name="insertform" method="post">
    	
        <input type="hidden" id="productiedatum" name="productiedatum" value="<?php echo date('Y-m-d H:i:s'); ?>"/>
		
        <ul class="mobilelist" id="inputlist">
		 <!-- <li><label for="ordernummer">Ordernummer: </label><input type="text" id="ordernummer" name="ordernummer" /></li> !-->
		 <li>
            	<?php 
				foreach($products as $product){
					echo "<a class=' article ui-button ui-widget ui-state-default  ui-button-text-only' value='".$product->article_no."' role='button' aria-disabled='false'><span class='ui-button-text'>".ucfirst($product->article_desc)."</span></a> ";
				};
				?>
         </li>
		 <li><label for="gewicht">Gewicht: </label><input type="text" id="gewicht" name="gewicht" disabled="disabled"/></li>
		 <li>
			<!--<div id="barcode-display"><?php echo $barcode; ?></div>!-->
		 </li>
		 <li><input type="button" class="button ui-button ui-widget ui-state-default  ui-button-text-only" id="verstuur" name="verstuur" value="Verstuur" /><input type="reset" class="button  ui-button ui-widget ui-state-default  ui-button-text-only" name="reset" value="Reset" id="reset" /></li>
		</ul>
        <input type="hidden" id="artikelnummer" name="artikelnummer" value=""/>
        <input type="hidden" id="barcode" name="barcode" value="<?php echo $barcode; ?>"/>
        <input type="hidden" name="task" value="add">
	</form>
</div>
<iframe id="printFrame" width="0" height="0"/>
