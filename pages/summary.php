<?php
// Include classes.
require_once('class/class.mysql.php');

// Load suppliers from the database.
$query = "SELECT * FROM vanda_suppliers ORDER BY supplier_desc ASC;";
if($result = $db->query($query)){
	while($row = $result->fetch_object()){
		$suppliers[] = $row;
	}	
}


// Load products from the database.
$query = "SELECT * FROM vanda_products ORDER BY article_desc ASC;";
if($result = $db->query($query)){
	while($row = $result->fetch_object()){
		$products[] = $row;
	}	
}

unset($query);

$today = date('y-m-d');
$period = $_GET['period'];
$beheer = $_GET['beheer'];

$selectdate = $_POST['selectdate'];
$startdate = $_POST['startdate'];
$stopdate = $_POST['stopdate'];

$periode['day'] = 'Dag';
$periode['week'] = 'Week';
$periode['month'] = 'Maand';
$periode['year'] = 'Jaar';
$periode['custom'] = 'Aangepast';
$periode = $periode[$period];

$supplier_filter = $_POST['supplier_filter'];
$product_filter = $_POST['product_filter'];

$select = "SELECT  vanda_registrations.id as id, DATE_FORMAT(vanda_registrations.date, '%Y-%m-%d') as date, vanda_suppliers.supplier_desc as supplier, vanda_products.article_no as article_no, vanda_products.article_desc as article_desc, sum(vanda_registrations.amount) as amount FROM vanda_registrations LEFT JOIN vanda_suppliers ON vanda_registrations.supplier_id = vanda_suppliers.id
LEFT JOIN vanda_products ON vanda_registrations.article_id = vanda_products.id ";

switch($period){
	case 'day':
		if($selectdate && $selectdate != ''){
			$where .= "WHERE DATE_FORMAT( date, '%Y-%m-%d' ) = '".date('Y-m-d',strtotime($selectdate))."'  AND YEAR(date) = '".date('Y')."' "; 
		} else {
			$where = "WHERE DATE_FORMAT( date, '%Y-%m-%d' ) = '".date('Y-m-d')."' AND YEAR(date) = '".date('Y')."' "; 
		}
		$order = "GROUP BY vanda_registrations.supplier_id, vanda_registrations.article_id";
		$title = $periode . ' overzicht van ' . ($selectdate ? date('d-m-Y',strtotime($selectdate)) : date('d-m-Y',strtotime($today)));; 
	break;	
	
	case 'week':
		if($selectdate && $selectdate != ''){
			$where .= " WHERE WEEKOFYEAR(date) = '". date('W',strtotime($selectdate)) ."' AND YEAR(date) = '".date('Y',strtotime($selectdate))."' ";	
		} else {
			$where = "WHERE  WEEKOFYEAR(date) = '".date('W')."' AND YEAR(date) = '".date('Y')."'"; 
		}
		$order = "GROUP BY vanda_registrations.supplier_id, vanda_registrations.article_id";
		$title = $periode . ' overzicht van week ' . ($selectdate ? date('W',strtotime($selectdate)) : date('W'));
	break;
	
	case 'month':
		if($selectdate && $selectdate != ''){
			$where = "WHERE  DATE_FORMAT( date, '%m' ) = '".date('m',strtotime($selectdate))."' AND YEAR(date) = '".date('Y',strtotime($selectdate))."' "; 
		} else {
			$where = "WHERE  DATE_FORMAT( date, '%m' ) = '".date('m')."' AND YEAR(date) = '".date('Y')."' "; 
		}
		
		$order = "GROUP BY vanda_registrations.supplier_id, vanda_registrations.article_id";
		$title = $periode . ' overzicht van maand ' . ($selectdate ? date('m',strtotime($selectdate)) : date('m'));
	break;
	
	case 'custom':
		
		if($startdate && $stopdate){
			$where = "WHERE  vanda_registrations.date BETWEEN '".$startdate."' AND '".$stopdate." 23:59:59' "; 
			$title = "Aangepast Overzicht van ".$startdate.' tot '.$stopdate;
		} else {
		  $time = strtotime(date("Y-m-d"));
		  $final = date("Y-m-d", strtotime("-1 month", $time));
		  $where = "WHERE  vanda_registrations.date BETWEEN '".$final."' AND '".date('Y-m-d',$time)." 23:59:59' "; 
		  
		  $title = "Aangepast Overzicht van ".$final.' tot '.date('Y-m-d',$time);
		}
		$order = "GROUP BY vanda_registrations.supplier_id, vanda_registrations.article_id";
	break;
	
	default:
		$select = "SELECT  vanda_registrations.id as id, vanda_registrations.date as date, vanda_suppliers.supplier_desc as supplier, vanda_products.article_no as article_no,IF(vanda_registrations.remark = '',vanda_products.article_desc,vanda_registrations.remark) article_desc, vanda_registrations.amount as amount FROM vanda_registrations LEFT JOIN vanda_suppliers ON vanda_registrations.supplier_id = vanda_suppliers.id 
LEFT JOIN vanda_products ON vanda_registrations.article_id = vanda_products.id ";
		
		// van tot mogelijk maken
		if($startdate && $stopdate){
			$where = "WHERE  vanda_registrations.date BETWEEN '".$startdate."' AND '".$stopdate." 23:59:59' "; 
			$title = "Aangepast Overzicht van ".$startdate.' tot '.$stopdate;
		} else {
		  $time = strtotime(date("Y-m-d"));
		  $final = date("Y-m-d", strtotime("-1 month", $time));
		  $where = "WHERE  vanda_registrations.date BETWEEN '".$final."' AND '".date('Y-m-d',$time)." 23:59:59' ";
		}
				
		if($supplier_filter){
			
			$where .= ($where == '' ? 'WHERE' : 'AND')." vanda_registrations.supplier_id = '".$supplier_filter."' ";	
		}
		
		// Add the filter to the where clause
		if($product_filter){
			if($where != ''){
				$where .= "AND vanda_registrations.article_id = '".$product_filter."' ";	
			} else {
				$where .= "WHERE vanda_registrations.article_id = '".$product_filter."' ";	
			}
		}
		$order = "ORDER BY vanda_registrations.date DESC LIMIT 0, 2000";
		$title = "Overzicht van de registraties";
	break;	
} 

// Add the filter to the where clause
if($supplier_filter){
	$where .= "AND vanda_registrations.supplier_id = '".$supplier_filter."' ";	
}

// Add the filter to the where clause
if($product_filter){
	$where .= "AND vanda_registrations.article_id = '".$product_filter."' ";	
}

$query = $select.$where.$order;


if($query){
	$result = $db->query($query);
	while($row = $result->fetch_assoc()){
		$rows[] = $row;
	}
}
$nl = "\r";
?>
<form method="post" name="csvform" id="csvform" action="pages/csv.php" enctype="multipart/form-data" />
	<textarea name="query" id="query">
    <?php echo $query; ?>
    </textarea>
    
    <div id="csv" class="csv"><img src="images/excel_icon.gif"  /></div>
</form>

<form method="post" name="filterform" id="filterform" class="filterform" >
<h1><?php echo $title; ?></h1>
<?php echo $beheer; ?>
<div id="filterdiv">
	
    <?php switch($period){
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
	<?php break;
		  }?>
    
    <select class="filter_select" name="supplier_filter" id="supplier_filter" onchange="$('#filterform').submit()">
        <option value="" <?php if(!$supplier_filter){ echo "selected='selected'"; } ?>>Selecteer Leverancier</option>
        <?php foreach($suppliers as $supplier){
            if($supplier_filter == $supplier->id){
                echo "	<option value='".$supplier->id."' selected='selected' >".$supplier->supplier_desc."</option>".$nl;
            } else {
                echo "	<option value='".$supplier->id."'>".$supplier->supplier_desc."</option>".$nl;
            }
        }?>
    </select>
    <select class="filter_select" name="product_filter" id="product_filter" onchange="$('#filterform').submit()">
        <option value="" <?php if(!$product_filter){ echo "selected='selected'"; } ?>>Selecteer Artikel</option>
        <?php foreach($products as $product){
            if($product_filter == $product->id){
                echo "	<option value='".$product->id."' selected='selected'>".$product->article_desc."</option>".$nl;
            } else {
                echo "	<option value='".$product->id."'>".$product->article_desc."</option>".$nl;
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
			if($row['article_desc']){
				if($c == 1){ 
					echo "	<tr class=\"grey\">".$nl; 
					$c = 0;
				} else {
					echo "	<tr>".$nl;
					$c = $c + 1;
				}			
				echo "		<td>".str_replace('0.5','H',$row['amount'])."</td>"."<td>".$row['supplier']."</td>"."<td>".$row['article_no']."</td>"."<td>".$row['article_desc']."</td>"."<td>".$row['date']." ".($user->level == 1 && $period == '' ? '</td><td><span class="delete" id="'.$row['id'].'"></span>' : '')."</td>".$nl     ;
				echo "	</tr>".$nl;
			}
		}
	} else {
		echo "<tr class=\"noresult\"><td colspan='5'><strong>Er zijn geen resultaten om weer te geven.</strong></td><tr>";
	}
	?>
    </tbody>
</table>
<?php echo "<center>Er zijn ". count($rows) . " resultaten weergegeven <br>".$nl ?>
<?php if ($user->level) {?>
	<input type="text" id="rowid" name="rowid" value="" />
</form>
<?php } ?>