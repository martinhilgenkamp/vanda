<?php

require_once("inc/class/class.production.php");

$pm = new ProductionManager();

// Needs content filtering 
// Get variables
$period = isset($_GET['period']) ? $_GET['period'] : null;
$today = date('y-m-d');
$selectdate = isset($_POST['selectdate']) ? $_POST['selectdate'] : null;
$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : null;
$stopdate = isset($_POST['stopdate']) ? $_POST['stopdate'] : null;;

$periode['day'] = 'Dag';
$periode['week'] = 'Week';
$periode['month'] = 'Maand';
$periode['year'] = 'Jaar';
$periode['custom'] = 'Aangepast';
$periode = isset($_GET['period']) ? $periode[$period] : null;

$productfilter = isset($_POST['productfilter']) ? $_POST['productfilter'] : null;
$producttype = isset($_POST['producttype']) ? $_POST['producttype'] : null;


$title = "Productie over periode:";
switch($period) {
	case 'day':
		$title = 'Productie ' . strtolower($periode) . ' overzicht van ' . ($selectdate ? date('d-m-Y',strtotime($selectdate)) : date('d-m-Y',strtotime($today)));; 
		break;	
	case 'week':
		$title = 'Productie ' . strtolower($periode) . ' overzicht van week ' . ($selectdate ? date('W',strtotime($selectdate)) : date('W'));
		break;
	case 'month':
		$title = 'Productie ' . strtolower($periode) . ' overzicht van maand ' . ($selectdate ? date('m',strtotime($selectdate)) : date('m'));
		break;
	case 'custom':
		if($startdate && $stopdate){
			$title = "Aangepast Overzicht van ".$startdate.' tot '.$stopdate;
		} else {
		  $time = strtotime(date("Y-m-d"));
		  $final = date("Y-m-d", strtotime("-1 month", $time));		
		  $startdate = $final;
		  $stopdate = date('Y-m-d',$time);
		  $title = "Aangepast Overzicht van ".$final.' tot '.date('Y-m-d',$time);
		}
		break;	
}

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

// Load products from the database.
$product_list = $pm->getArticleNumbers();
$products = $pm->getProducedProducts($period, $selectdate, $startdate, $stopdate,  $productfilter, $producttype, $order, $sort);
$query = $pm->getProducedProductsQuery($period, $selectdate, $startdate, $stopdate, $productfilter, $producttype, $order, $sort);

// Hieronder word output gegenereerd.
$output = "<table id='product-table' class=\"data-table results\" cellpadding=\"0\" cellspacing=\"0\">";
#$output .= "<thead class=\"table-header\">";
$output .= " <tr>
  			  <th class=\"ui-corner-tl\"><a href='?page=voorraad&sort=artikelnummer&order=".$order."'>Artikelnummer</a></th>
			  <th><a href='?page=voorraad&sort=kwaliteit&order=".$order."'>Kwaliteit</a></th>
			   <th><a href='?page=voorraad&sort=datum&order=".$order."'>Laatst Geproduceerd</a></th>
			  <th class=\"ui-corner-tr\"><a href='?page=voorraad&sort=geleverd&order=".$order."'>Totaal Geproduceerd</a></th>";			  

// THERE IS NO USER LEVEL, JUST IN PLACE TO REMEMBER TO BE ABLE TO USE IT..
//if($user->level == 1){
//$output .= "<th><a href='?page=voorraad&sort=datum&order=".$order."'>Laatste Productie</a></th>";
//$output .= "<th class='ui-corner-tr'>&nbsp;</th>";	
//} else {
//	$output .= "<th class='ui-corner-tr'><a href='?page=voorraad&sort=datum&order=".$order."'>Datum</a></th>";
//}
#$output .= "</thead>";

if(count($products) > 0) {
	$c = 0;
	$totaal = 0; 
	foreach($products as $product){
		if($c == 1) { 
			$output .= "<tr class=\"grey\">"; 
			$c = 0;
		} 
		else { 
			$output .= "<tr>"; 
			$c = $c + 1; 
		}

		$output .= "<td>".$product->artikelnummer."</td>
					<td>".($product->kwaliteit ? $product->kwaliteit : '-')."</td>
					<td>".date('d-m-Y',strtotime($product->datum))."</td>
					<td>".($product->totaal_gewicht ? $product->totaal_gewicht : '-')."</td>";
		// THERE IS NO USER LEVEL, JUST IN PLACE TO REMEMBER TO BE ABLE TO USE IT..
		//if($user->level == 1) {
		//	$output .= "<td><span class='delete-stock' id='".$product->id."'></span></td>";	
		//}	

		$output .= "</tr>";

		$totaal = $totaal + $product->totaal_gewicht;
	}
	if($user->level == 1){
		$output .= "<tfoot><tr><td class=\"ui-corner-bottom\" colspan='9'>Er zijn in totaal ".$totaal." producten van ".count($products)." verschillende types geproduceerd</td></tr></tfoot>";	
	} else {
		$output .= "<tfoot><tr><td class=\"ui-corner-bottom\" colspan='8'>Er zijn in totaal ".$totaal." producten van ".count($products)." verschillende types geproduceerd</td></tr></tfoot>";	
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
	<h1><?php echo $title; ?></h1>
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

		<label for="productfilter">Product type:</label>
	    <select class="ui-corner-all" name="producttype" id="producttype" onChange="this.form.submit()">
	    		<option value="" <?php echo  ($producttype == "" ? "selected='selected'" : "" );?> >Alles</option>
	    		<option value="stansen" <?php echo  ($producttype == "stansen" ? "selected='selected'" : "" );?> > Stansen</option>";
				<option value="overig" <?php echo ($producttype == "overig"? "selected='selected'" : "" );?> > Overig</option>";
	    </select>
		<?php
		
		if($period == 'custom'){
			echo "<label for=\"startdate\">Van:</label><input class=\"datepicker ui-corner-all\" id=\"startdate\" name=\"startdate\" value=\"" . ($startdate ? $startdate :  $final) . "\" onchange=\"$('#filterform').submit()\"/>";
			echo "<label for=\"startdate\">Tot:</label><input class=\"datepicker ui-corner-all\" id=\"stopdate\" name=\"stopdate\" value=\"". ($stopdate ? $stopdate : date('Y-m-d',$time)) . "\" onchange=\"$('#filterform').submit()\"/>";
		} else {
			$time = date("Y-m-d");
			echo "<input " . ($period == 'month' ? 'id="monthselect" ' : 'id="selectdate" ') . " class=\"datepicker ui-corner-all\" name=\"selectdate\" value=\"" . ($selectdate ? $selectdate : $time) . "\" onchange=\"$('#filterform').submit()\"/>";
		}	    

		?>
    </div>
</form>

<?php echo $output ?>