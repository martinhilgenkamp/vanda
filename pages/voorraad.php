<?php

require_once("inc/class/class.production.php");

$pm = new ProductionManager();

// Needs content filtering 
// Get variables
$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : null;
$stopdate = isset($_POST['stopdate']) ? $_POST['stopdate'] : null;;
$voorraadfilter = isset($_POST['voorraadfilter']) ? $_POST['voorraadfilter'] : null;
$productfilter = isset($_POST['productfilter']) ? $_POST['productfilter'] : null;

$order = null;
if(isset($_GET['order'])) {	
	$order = $_GET['order']; 
} 
else if (isset($_POST['order'])) { 
	$order = $_POST['order']; 
}

$sort = null;
if (isset($_GET['sort'])) {	
	$sort = $_GET['sort']; 
} 
else if (isset($_POST['sort'])) { 
	$sort = $_POST['sort']; 
}

$pg=1;
if (isset($_GET["pg"])) { 
	$pg  = $_GET["pg"];	
} 
elseif (isset($_POST['pg'])) {	
	$pg = $_POST['pg'];	
}

$where_array = array();

// Load products from the database.
$product_list = $pm->getArticleNumbers();
$products = $pm->getStockProducts($startdate, $stopdate, $voorraadfilter, $productfilter, $order, $sort);
$query = $pm->getStockProductsQuery($startdate, $stopdate, $voorraadfilter, $productfilter, $order, $sort);

// Hieronder word output gegenereerd.
$output = "<table id='product-table' class=\"data-table results\" cellpadding=\"0\" cellspacing=\"0\">";
#$output .= "<thead class=\"table-header\">";
$output .= " <tr>
			  <th class='ui-corner-tl'>Label</td>
  			  <th><a href='?page=voorraad&sort=artikelnummer&order=".$order."'>Artikelnummer</a></th>
			  <th><a href='?page=voorraad&sort=barcode&order=".$order."'>Barcode</a></th>
			  <th><a href='?page=voorraad&sort=ordernr&order=".$order."'>Order Nr.</a></th>
			  <th><a href='?page=voorraad&sort=gewicht&order=".$order."'>KG/STK</a></th>
			  <th><a href='?page=voorraad&sort=kwaliteit&order=".$order."'>Kwaliteit</a></th>
			  <th><a href='?page=voorraad&sort=geleverd&order=".$order."'>Verzonden</a></th>";			  
if($user->level == 1){
	$output .= "<th><a href='?page=voorraad&sort=datum&order=".$order."'>Datum</a></th>";
	$output .= "<th class='ui-corner-tr'>&nbsp;</th>";	
} else {
	$output .= "<th class='ui-corner-tr'><a href='?page=voorraad&sort=datum&order=".$order."'>Datum</a></th>";
}
#$output .= "</thead>";

if(count($products) > 0) {
	$c = 0;
	foreach($products as $product){
		if($c == 1) { 
			$output .= "<tr class=\"grey\">"; 
			$c = 0;
		} 
		else { 
			$output .= "<tr>"; 
			$c = $c + 1; 
		}

		$output .= "<td>".($product->ordernr ? 
						"<a href='pages/generate/generate_stans.php?artikelnummer=".$product->barcode."' target='_blanc'>" : 
						"<a href='pages/generate/generate_label.php?artikelnummer=".$product->barcode."' target='_blanc'>").
							"<img src='images/printer.png' height='17'/>
						</a>
					</td>
					<td>".$product->artikelnummer."</td>
					<td>".$product->barcode."</td>
					<td>".$product->ordernr."</td>
					<td>".$product->gewicht."</td>
					<td>".($product->kwaliteit ? $product->kwaliteit : '-')."</td>
					<td>".($product->shipping_id ? 
						'<img src="images/truck.png" height="16px" />' : 
						'<img src="images/truck-cross.png" height="16px" />')."
					</td>
					<td>".date('d-m-Y',strtotime($product->datum))."</td>";

		if($user->level == 1) {
			$output .= "<td><span class='delete-stock' id='".$product->id."'></span></td>";	
		}	

		$output .= "</tr>";
	}
	if($user->level == 1){
		$output .= "<tfoot><tr><td class=\"ui-corner-bottom\" colspan='9'>".count($products)." resultaten weergegeven</td></tr></tfoot>";	
	} else {
		$output .= "<tfoot><tr><td class=\"ui-corner-bottom\" colspan='8'>".count($products)." resultaten weergegeven</td></tr></tfoot>";	
	}
	
	
}
else {
	if($user->level == 1){
		$output .= "<tr><td class=\"ui-corner-bottom\" colspan='9'>Geen resultaten</td></tr>";
	} else {
		$output .= "<tr><td class=\"ui-corner-bottom\" colspan='8'>Geen resultaten</td></tr>";
	}
	
}

$output .= "</table>";
?>

<form method="post" name="csvform" id="csvform" action="pages/csv.php" enctype="multipart/form-data" />
	<input type="hidden" class="query" id="query" name="query" value="<?php echo $query; ?>"  />
    <div id="csv" class="csv"><img src="images/excel.png"  /></div>
</form>

<form method="post" name="filterform" id="filterform" class="filterform" method="post">
	<h1>Product Voorraad</h1>
	<div id="filterdiv">	
		<?php
		    $time = strtotime(date("Y-m-d"));
		    $final = date("Y-m-d", strtotime("-1 year", $time));
	    ?>
	    
	    <label for="productfilter">Artikelnummer:</label>
	    <select class="ui-corner-all" name="productfilter" id="productfilter" onChange="this.form.submit()">
	    	<option value="">Alles</option>
	    	<?php 
	    		foreach($product_list as $product) {
					echo "<option value='".$product->artikelnummer."' ".($productfilter == $product->artikelnummer ? "selected='selected'" : "" ).">".$product->artikelnummer."</option>";
				}
			?>
	    </select>
	   
	    <label for="startdate">Van:</label><input class="datepicker ui-corner-all" id="startdate" name="startdate" value="<?php echo ($startdate ? $startdate :  $final); ?>" onchange="$('#filterform').submit()"/>
	    <label for="startdate">Tot:</label><input class="datepicker ui-corner-all" id="stopdate" name="stopdate" value="<?php echo ($stopdate ? $stopdate : date('Y-m-d',$time)); ?>" onchange="$('#filterform').submit()"/>
	    
	    <label for="voorraadfilter">Voorraad: </label>
        <select class="ui-corner-all" id="voorraadfilter" name="voorraadfilter" onChange="this.form.submit()">
            <option id="voorradig" value="alles" <?php echo ($voorraadfilter == 'alles' ? "selected='true'" : ""); ?>>Incl Geschiedenis</option>
            <option id="voorradig" value="voorraad" <?php echo ($voorraadfilter == 'voorraad' || !$voorraadfilter ? "selected='true'" : ""); ?>>Voorradig</option>
        </select>
    </div>
</form>

<?php echo $output ?>