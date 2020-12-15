<?php
// Needs content filtering 
// Get variables
$startdate = $_POST['startdate'];
$stopdate = $_POST['stopdate'];
$voorraadfilter = $_POST['voorraadfilter'];
$productfilter= $_POST['productfilter'];

if(isset($_GET['order'])){	$order = $_GET['order']; } else if (isset($_POST['order'])){ $order = $_POST['order']; }
if(isset($_GET['sort'])){	$sort = $_GET['sort']; } else if (isset($_POST['sort'])){ $sort = $_POST['sort']; }
if (isset($_GET["pg"])) { 	$pg  = $_GET["pg"];	} elseif (isset($_POST['pg'])){	$pg = $_POST['pg'];	} else { $pg=1;	};


// Include classes.
require_once('class/class.mysql.php');
$nl = "\r\n";
$where_array = array();

// Load products from the database.
$query = "SELECT artikelnummer FROM vanda_production GROUP BY artikelnummer ASC;";
if($result = $db->query($query)){
	while($row = $result->fetch_object()){
		$product_list[] = $row;
	}	
}
unset($query);

// Create date filter.
if($startdate && $stopdate){
	$where_array[] = "vanda_production.datum BETWEEN '".$startdate."' AND '".$stopdate." 23:59:59' "; 
	$title = "Aangepast Overzicht van ".$startdate.' tot '.$stopdate;
} else {
  	$time = strtotime(date("Y-m-d"));
  	$final = date("Y-m-d", strtotime("-1 year", $time));
  	$where_array[] = "vanda_production.datum BETWEEN '".$final."' AND '".date('Y-m-d',$time)." 23:59:59' ";
}
// Add voorraad filter to the where clause
if($voorraadfilter == 'alles'){
	// do nothing
} else if($voorraadfilter || !$voorraadfilter){
	$where_array[] = "vanda_production.shipping_id = 0 ";	
}
// Add product filter to the where clause
if($productfilter){
	$where_array[] = "vanda_production.artikelnummer = '".$productfilter."' ";	
}
// Allways prullenbak removed items
$where_array[] = "vanda_production.removed = '0' ";
//Build the where clause

// Variabelen tbv sorteren en paginatie
$order = ($order == 'DESC' ? $order = 'ASC' : $order='DESC');
$orderby= " ORDER BY ".($sort ? '`'.$sort.'` ' : '`datum` ').$order;

foreach($where_array as $part){
	if($part && $part != ''){
		$where .= ($where ? ' AND ' : ' WHERE ').$part;
	}
}

// Load products from the database.
$query = "SELECT * FROM  vanda_production ".$where.$orderby;
if($result = $db->query($query)){
	while($row = $result->fetch_object()){
		$products[] = $row;
	}	
}

// Hieronder word output gegenereerd.
$output = "<table id='product-table' class=\"ui-widget results\" cellpadding=\"0\" cellspacing=\"0\">".$nl;
$output .= "<thead class=\"table-header\">".$nl;
$output .= "<td>Label</td>
  			 <td><a href='?page=voorraad&sort=artikelnummer&order=".$order."'>Artikelnummer</a></td>
			 <td><a href='?page=voorraad&sort=barcode&order=".$order."'>Barcode</a></td>
			 <td><a href='?page=voorraad&sort=ordernr&order=".$order."'>Order Nr.</a></td>
			 <td><a href='?page=voorraad&sort=gewicht&order=".$order."'>KG/STK</a></td>
			 <td><a href='?page=voorraad&sort=kwaliteit&order=".$order."'>Kwaliteit</a></td>
			 <td><a href='?page=voorraad&sort=geleverd&order=".$order."'>Verzonden</a></td>
			 <td><a href='?page=voorraad&sort=datum&order=".$order."'>Datum</a></td>";
if($user->level){
	$output .= "<td>&nbsp;</td>";	
}
$output .= "</thead>".$nl;

if(count($products)){
	foreach($products as $product){
		if($c == 1){ $output .= "	<tr class=\"grey\">".$nl; $c = 0;} else { $output .= "<tr>".$nl; $c = $c + 1; }
		$output .= "<td>".($product->ordernr ? "<a href='inc/generate_stans.php?artikelnummer=".$product->barcode."' target='_blanc'>" : "<a href='inc/generate_label.php?artikelnummer=".$product->barcode."' target='_blanc'>")."<img src='images/printer.png' height='17'/></a></td><td>".$product->artikelnummer."</td><td>".$product->barcode."</td><td>".$product->ordernr."</td><td>".$product->gewicht."</td><td>".($product->kwaliteit ? $product->kwaliteit : '-')."</td><td>".($product->shipping_id ? '<img src="images/truck.png" height="16px" />' : '<img src="images/truck-cross.png" height="16px" />')."</td><td>".date('d-m-Y',strtotime($product->datum))."</td>".$nl;
		if($user->level){
		$output .= "<td><span class='delete-stock' id='".$product->id."'></span></td>";	
		}		
		$output .= "</tr>".$nl;
	}
}
$output .= "</table>".$nl;
?>
<form method="post" name="csvform" id="csvform" action="pages/csv.php" enctype="multipart/form-data" />
	<input type="hidden" class="query" id="query" name="query" value="<?php echo $query; ?>"  />
    <div id="csv" class="csv"><img src="images/excel_icon.gif"  /></div>
</form>

<form method="post" name="filterform" id="filterform" class="filterform" method="post">
	<h1>Product Voorraad</h1>
<div id="filterdiv">	
	<?php
    $time = strtotime(date("Y-m-d"));
    $final = date("Y-m-d", strtotime("-1 year", $time));?>
    
    <label for="productfilter">Artikelnummer:</label>
    <select name="productfilter" id="productfilter" onChange="this.form.submit()">
    	<option value="">Alles</option>
    	<?php foreach($product_list as $product){
			echo "<option value='".$product->artikelnummer."' ".($productfilter == $product->artikelnummer ? "selected='selected'" : "" ).">".$product->artikelnummer."</option>".$nl;
		}?>
    </select>
   
    <label for="startdate">Van:</label><input class="datepicker" id="startdate" name="startdate" value="<?php echo ($startdate ? $startdate :  $final); ?>" onchange="$('#filterform').submit()"/>
    <label for="startdate">Tot:</label><input class="datepicker" id="stopdate" name="stopdate" value="<?php echo ($stopdate ? $stopdate : date('Y-m-d',$time)); ?>" onchange="$('#filterform').submit()"/>
    
    <label for="voorraadfilter">Voorraad: </label>
        <select id="voorraadfilter" name="voorraadfilter" onChange="this.form.submit()">
            <option id="voorradig" value="alles" <?php echo ($voorraadfilter == 'alles' ? "selected='true'" : ""); ?>>Incl Geschiedenis</option>
            <option id="voorradig" value="voorraad" <?php echo ($voorraadfilter == 'voorraad' || !$voorraadfilter ? "selected='true'" : ""); ?>>Voorradig</option>
        </select>
    </form>
    
</div>

<?php echo $output ?>