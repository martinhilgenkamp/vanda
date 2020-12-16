<?php
// Include classes.
require_once("inc/class/class.supplier.php");
require_once("inc/class/class.product.php");
require_once("inc/class/class.registration.php");

$sm = new SupplierManager;
$pm = new ProductManager;
$rm = new RegistrationManager;

// Load suppliers from the database.
$suppliers = $sm->loadSuppliersIncludeDeleted();

// Load products from the database.
$products = $pm->loadProducts();

unset($query);

$today = date('y-m-d');
$period = isset($_GET['period']) ? $_GET['period'] : null;
$beheer = isset($_GET['beheer']) ? $_GET['beheer'] : null;

$selectdate = isset($_POST['selectdate']) ? $_POST['selectdate'] : null;
$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : null;
$stopdate = isset($_POST['stopdate']) ? $_POST['stopdate'] : null;

$periode['day'] = 'Dag';
$periode['week'] = 'Week';
$periode['month'] = 'Maand';
$periode['year'] = 'Jaar';
$periode['custom'] = 'Aangepast';
$periode = isset($_GET['period']) ? $periode[$period] : null;

$supplier_filter = isset($_POST['supplier_filter']) ? $_POST['supplier_filter'] : null;
$product_filter = isset($_POST['product_filter']) ? $_POST['product_filter'] : null;

$title = "Overzicht van de registraties";
switch($period) {
	case 'day':
		$title = $periode . ' overzicht van ' . ($selectdate ? date('d-m-Y',strtotime($selectdate)) : date('d-m-Y',strtotime($today)));; 
		break;	
	case 'week':
		$title = $periode . ' overzicht van week ' . ($selectdate ? date('W',strtotime($selectdate)) : date('W'));
		break;
	case 'month':
		$title = $periode . ' overzicht van maand ' . ($selectdate ? date('m',strtotime($selectdate)) : date('m'));
		break;
	case 'custom':
		if($startdate && $stopdate){
			$title = "Aangepast Overzicht van ".$startdate.' tot '.$stopdate;
		} else {
		  $time = strtotime(date("Y-m-d"));
		  $final = date("Y-m-d", strtotime("-1 month", $time));				  
		  $title = "Aangepast Overzicht van ".$final.' tot '.date('Y-m-d',$time);
		}
		break;	
}

$qry = $rm->getRegistrationOverviewByPeriodQuery($period, $periode, $today, $selectdate, $startdate, $stopdate, $supplier_filter, $product_filter);
$rows = $rm->getRegistrationOverviewByPeriod($period, $periode, $today, $selectdate, $startdate, $stopdate, $supplier_filter, $product_filter);

?>
<form method="post" name="csvform" id="csvform" action="pages/csv.php" enctype="multipart/form-data" />
	<textarea name="query" id="query">
    <?php echo $qry; ?>
    </textarea>
    
    <div id="csv" class="csv"><img src="images/excel_icon.gif"  /></div>
</form>

<form method="post" name="filterform" id="filterform" class="filterform" >
<h1><?php echo $title; ?></h1>
<?php echo $beheer; ?>
<div id="filterdiv">
	
<?php 
	switch($period){
		case 'custom':
		case '':
			$time = strtotime(date("Y-m-d"));
			$final = date("Y-m-d", strtotime("-1 month", $time));?>
			<input class="datepicker" id="startdate" name="startdate" value="<?php echo ($startdate ? $startdate :  $final); ?>" onchange="$('#filterform').submit()"/>
			<input class="datepicker" id="stopdate" name="stopdate" value="<?php echo ($stopdate ? $stopdate : date('Y-m-d',$time)); ?>" onchange="$('#filterform').submit()"/>
<?php
			break;
		default:
			$time = date("Y-m-d");?>
			<input <?php echo ($period == 'month' ? 'id="monthselect" ' : 'id="selectdate" '); ?> class="datepicker" name="selectdate" value="<?php echo ($selectdate ? 				$selectdate : $time); ?>" onchange="$('#filterform').submit()"/>
<?php 
			break;
}?>
    
    <select class="filter_select" name="supplier_filter" id="supplier_filter" onchange="$('#filterform').submit()">
        <option value="" <?php if(!$supplier_filter){ echo "selected='selected'"; } ?>>Selecteer Leverancier</option>
        <?php foreach($suppliers as $supplier){
            if($supplier_filter == $supplier->id){
                echo "	<option value='".$supplier->id."' selected='selected' >".$supplier->supplier_desc."</option>";
            } else {
                echo "	<option value='".$supplier->id."'>".$supplier->supplier_desc."</option>";
            }
        }?>
    </select>
    <select class="filter_select" name="product_filter" id="product_filter" onchange="$('#filterform').submit()">
        <option value="" <?php if(!$product_filter){ echo "selected='selected'"; } ?>>Selecteer Artikel</option>
        <?php foreach($products as $product){
            if($product_filter == $product->id){
                echo "	<option value='".$product->id."' selected='selected'>".$product->article_desc."</option>";
            } else {
                echo "	<option value='".$product->id."'>".$product->article_desc."</option>";
            }
        }?>
    </select>
    <input type="reset" class="button" id="reset" name="reset" value="Reset filters" onclick="doreset()"/>
    <input type="button" value="Ga" id="go" class="button" />
    </form>
</div>
<?php if ($user->level) {?>
<form id="deleteform" name="deleteform" action="index.php?page=summary&beheer=1" method="post">
<?php } ?>
<table class="ui-widget results" cellpadding="0" cellspacing="0">
	<thead class="ui-widget-header">
        <td>Aantal</td>
        <td>Leverancier</td>
        <td>Artikel Nummer</td>
        <td>Omschrijving</td>
        <td>Datum</td>
        <?php echo ($user->level == 1 && $period == '' ? '<td style="width: 40px;">Verwijder</td>' : ''); ?>
    </thead>
	<tbody class='ui-widget-content'>
	<?php
	if(count($rows)){
		$c = 0;
		foreach($rows as $row){
			if($row->article_desc){
				if($c == 1){ 
					echo "	<tr class=\"grey\">"; 
					$c = 0;
				} else {
					echo "	<tr>";
					$c = $c + 1;
				}			
				echo "<td>".str_replace('0.5','H',$row->amount)."</td>"."
					  <td>".$row->supplier."</td>"."
					  <td>".$row->article_no."</td>"."
					  <td>".$row->article_desc."</td>"."
					  <td>".$row->date." ".($user->level == 1 && $period == '' ? '</td>
					  <td><span class="delete" id="'.$row->id.'"></span>' : '')."</td>";
				echo "</tr>";
			}
		}
	} else {
		echo "<tr class=\"noresult\"><td colspan='5'><strong>Er zijn geen resultaten om weer te geven.</strong></td><tr>";
	}
	?>
    </tbody>
</table>
<?php 
	echo "<center>Er zijn ". count($rows) . " resultaten weergegeven <br>";
	if ($user->level) {
		echo "<input type='text' id='rowid' name='rowid' value='' />";
	} 
?>
</form>