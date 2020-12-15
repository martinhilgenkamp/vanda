
<h1>Dashboard</h1>
<?php
// Build button list
global $db, $nl;


// Load suppliers from the database.
$query = "SELECT * FROM vanda_suppliers WHERE verwijderd = 0 ORDER BY volgorde DESC, supplier_desc ASC;";
if($result = $db->query($query)){
	while($row = mysqli_fetch_object($result)){
		$suppliers[] = $row;
	}	
}

// Load products from the database.
$query = "SELECT * FROM vanda_products ORDER BY article_desc ASC;";
if($result = $db->query($query)){
	while($row = mysqli_fetch_object($result)){
		$products[] = $row;
	}	
}

// START WITH OUTPUT GENERATION
?>
<div id="results"></div>
<form name="registerform" id="registerform" class="registerform" action="index.php?page=register" method="post" enctype="multipart/form-data">
<div id="accordion">
<?php
	foreach($suppliers as $supplier){
		echo "	<h3>".$supplier->supplier_desc."</h3>".$nl;
		echo "	<div class=\"suppliers\" id='".$supplier->id."'>".$nl ;
		// Start listing possible articles		
		
		foreach($products as $product){
			
			// VRAAG VAN ANDRE CONDOR EX GELIJK ZETTEN AAN CONDOR 25-10-2016
			//
			//if($supplier->id == 8){ //check for belrey products
				//if($product->export == 1){
				//	echo "		<a value=\"".$product->id."\" class=\"button article\">".$product->article_desc."</a>".$nl;
				//}
			//} else
			
			if($supplier->id == 11 || $supplier->id == 12){
				if($product->id == 11){ // aangepaste product anders weergeven..
					echo "		<a value=\"".$product->id."\" class=\"button article\">Aangepast</a>".$nl;	
				}
			} elseif($supplier->id == 13){
				if($product->export == 3){
					echo "		<a value=\"".$product->id."\" class=\"button article\">".$product->article_desc."</a>".$nl;
				}
			} elseif($supplier->id == 14){
					// leeg veld doen
					echo " ";
			}else { // Check if belrey laat alleen export artikelen zien. 
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
			echo "		<a value=\"".$supplier->id."\" class=\"button gettransport\">Wagen Halen</a>".$nl;
			echo "		<a value=\"".$supplier->id."\" class=\"button returntransport\">Wagen Terug</a>".$nl;
		}
		
		echo "	</div>".$nl;	
	}
?>
</div>
<input type="hidden" name="supplier" id="supplier" value=""  />
<input type="hidden" name="article" id="article" value=""  />
<input type="hidden" name="amount_tot" id="amount_tot" value=""  />
</form>