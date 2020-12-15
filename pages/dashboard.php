<?php

require_once("inc/class/class.supplier.php");
require_once("inc/class/class.product.php");

$sm = new SupplierManager();
$pm = new ProductManager();

$resSuppliers = $sm->loadSuppliers();
$products = $pm->loadProducts();

?>

<h1>Dashboard</h1>
<div id="results"></div>
<form name="registerform" id="registerform" class="registerform" action="index.php?page=register" method="post" enctype="multipart/form-data">
	<div id="accordion">
	<?php

		foreach($resSuppliers as $supplier) {
			echo "	<h3>".$supplier->supplier_desc."</h3>";
			echo "	<div class=\"suppliers\" id='".$supplier->id."'>";
			
			//Articles
			foreach($products as $product){
				if($supplier->id == 11 || $supplier->id == 12){
					if($product->id == 11){ // aangepaste product anders weergeven..
						echo "		<a value=\"".$product->id."\" class=\"button article\">Aangepast</a>";	
					}
				} elseif($supplier->id == 13){
					if($product->export == 3){
						echo "		<a value=\"".$product->id."\" class=\"button article\">".$product->article_desc."</a>";
					}
				} elseif($supplier->id == 14){
						// leeg veld doen
						echo " ";
				}else { // Check if belrey laat alleen export artikelen zien. 
					if($product->export == 0){ // laat alle niet export zien
						if($product->id == 11){ // aangepaste product anders weergeven..
							echo "		<a value=\"".$product->id."\" class=\"button article\">Aangepast</a>";	
						} else {
							echo "		<a value=\"".$product->id."\" class=\"button article\">".$product->article_desc."</a>";	
						}
					}	
				}
			}	// End function to fill in products
			
			// Mail buttons toevoegen
			if($supplier->transportmail == '1'){
				echo "		<hr>";
				echo "		<a value=\"".$supplier->id."\" class=\"button gettransport\">Wagen Halen</a>";
				echo "		<a value=\"".$supplier->id."\" class=\"button returntransport\">Wagen Terug</a>";
			}
			
			echo "	</div>";	
		}
	?>
	</div>
	<input type="hidden" name="supplier" id="supplier" value=""  />
	<input type="hidden" name="article" id="article" value=""  />
	<input type="hidden" name="amount_tot" id="amount_tot" value=""  />
</form>