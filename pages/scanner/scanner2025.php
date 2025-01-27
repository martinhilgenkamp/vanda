<?php
require_once("../../inc/class/class.option.php");
$om = new OptionManager();

$options = $om->getAllOptions()[0];

// TODO de timer variabel maken middels optie pagina

?>

<!DOCTYPE html>
<html>
<head>
  <!-- Define viewport for handheld scanners !-->
  <meta charset="UTF-8">
  <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=0">
  <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=0">
  <title>Vanda Carpets - Process Management</title>

  <!-- Iinclude Stylesheet !-->
  <link rel="stylesheet" href="css/style.css">

  <!-- Adding required scripts !-->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="inc/script/inactivity-timer.js"></script>
  <script src="inc/script/table-handler.js"></script>
  <script src="inc/script/scanner.js"></script>
  <script>
	// Set the required parameters for the page to run.
  //const inactivityTimeout = 10 * 10 * 1000; // 10 minutes
  //inactivityTimeout = 10 * 1000; // 5 minutes
  // TODO bovenstaande timer naar variable
  </script>
</head>
<body>
    <!-- div for user feedback !-->
    <div id="result"></div>
    
    <!-- the actual form !-->
    <div id="shipform-container">
    	<form id="shipform" name="shipform" method="post">	
         <ul class="mobilelist" id="shiplist">
            <li><label for="klant">Klant: </label><input type="text" id="klant" name="klant" placeholder="Klant"/></li>
            <li><label for="barcode">Barcode: </label><input type="text" id="barcode" name="barcode" placeholder="Barcode" inputmode="none"/></li>
            <li><label for="ship_id">Zending: </label><input type="text" id="ship_id" name="ship_id"  placeholder="Zending" readonly="true" />
            <li><button type="submit">Submit</button><button type="reset">Herstel</button></li>
         </ul>
        </form>
    </div>
    
    <!-- div for open shipments !-->
    <div id="openshipments">
        Open zendingen verzamelen...
    </div>    
</body>
</html>
