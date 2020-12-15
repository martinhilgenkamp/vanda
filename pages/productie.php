<?php 
// Include classes.
require_once('class/class.mysql.php');
// Get the task
$task = $_POST['task'];

switch($task){
	case 'add':
		$post = $_POST;
				
		// Set post values in vars		
		$artikelnummer = mysqli_real_escape_string ($db,$post['artikelnummer']);
		$kwaliteit = mysqli_real_escape_string ($db,$post['kwaliteit']);
		$gewicht = mysqli_real_escape_string ($db,$post['gewicht']);
		$datum = date('Y-m-d H:i:s');
		$barcode = mysqli_real_escape_string ($db,$post['barcode']);
		
		$query = "INSERT INTO `vanda_production` (`id`, `artikelnummer`, `kwaliteit`, `gewicht`, `datum`, `geleverd`, `shipping_id`, `barcode`) VALUES (NULL, '".$artikelnummer."', '".$kwaliteit."', '".$gewicht."', '".$datum."', '', '', '".$barcode."');";
		
		if($db->query($query)){
			echo "success<br /><a href='http://web01/vanda2/index.php?page=insert'>Doorgaan</a>";
				
		} else {
			echo "Er is een fout opgetreden bij het opslaan! ".mysqli_error($db);	
		} 
		
	break;
	default:
		ShowForm();
	break;		
}

function getBarID(){
	global $db;
	$query = "SELECT barcode FROM `vanda_production` ORDER BY barcode DESC LIMIT 1;";
	if($result = $db->query($query)){
		while($row = $result->fetch_object()){
			$barcode = $row->barcode;
		}	
		// Geef laatste barcode terug.
		return (int)substr($barcode, -10)+1;
	} else {
		echo "Er is een fout opgetreden bij het opslaan! ".mysqli_error($db);
	}
}

function ShowForm(){
	global $db;
	// Waarde voor barcode genereren.
	$barid = str_pad(getBarID(), 10, '0', STR_PAD_LEFT);
	$barcode = 'F00830'.$barid;
	
	// Load products from the database.
	$query = "SELECT * FROM vanda_products WHERE export = 2 ORDER BY article_desc ASC;";
	if($result = $db->query($query)){
		if($result->num_rows){
			while($row = $result->fetch_object()){
				$products[] = $row;
			}	
		}
	} 
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
<?php } // Einde show form?>
<iframe id="printFrame" width="0" height="0"/>
