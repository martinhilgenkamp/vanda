<?php 
require_once("inc/class/class.production.php");
require_once("inc/class/class.product.php");

$prm = new ProductionManager();
$pm = new ProductManager();

// Waarde voor barcode genereren.
$newBarcode = $prm->getNewBarcode();
$barid = str_pad($newBarcode, 10, '0', STR_PAD_LEFT);
$barcode = 'F00830'.$barid;

// Load products from the database.
$products = $pm->getProductsByExport(2);
?>
<div class="navigation-header"><a href="index.php?page=machines" class="ui-button ui-corner-all ui-widget pageswitch" >Machine Pagina >></a></div>
<h1>Boek product in</h1>
<div class="clr"></div>

<div id="inputform-container">
	<form id="insertform" name="insertform" method="post">
    	
        <input type="hidden" id="productiedatum" name="productiedatum" value="<?php echo date('Y-m-d H:i:s'); ?>"/>
		
        <ul class="mobilelist" id="inputlist">
		 <!-- <li><label for="ordernummer">Ordernummer: </label><input type="text" id="ordernummer" name="ordernummer" /></li> !-->
		 <li>
            	<?php 
				foreach($products as $product){
					echo "<a class='article ui-button ui-widget ui-state-default ui-state-default ui-corner-all ui-button-text-only' value='".$product->article_no."' role='button' aria-disabled='false'><span class='ui-button-text'>".ucfirst($product->article_desc)."</span></a> ";
				};
				?>
         </li>
		 <hr>
		 <li><label for="gewicht">Gewicht: </label><input type="number" id="gewicht" class="ui-corner-all textfield" name="gewicht" max="9999" disabled="disabled" maxlength=4 /></li>
		 <li>
			<!--<div id="barcode-display"><?php echo $barcode; ?></div>!-->
		 </li>
		 <hr>
		 <li><input type="button" class="button ui-button ui-widget ui-corner-all ui-state-default  ui-button-text-only" id="verstuur" name="verstuur" value="Verstuur" />
		 
		 <input type="reset" class="button ui-corner-all ui-button ui-widget ui-state-default  ui-button-text-only" name="reset" value="Reset" id="reset" /></li>
		</ul>

        <input type="hidden" id="artikelnummer" name="artikelnummer" value=""/>
        <input type="hidden" id="barcode" name="barcode" value="<?php echo $barcode; ?>"/>
        <input type="hidden" name="task" value="add">
	</form>
</div>
