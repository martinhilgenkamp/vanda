<!doctype html>
<html>
<head>
  <!-- Define viewport for handheld scanners !-->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=240, initial-scale=1, maximum-scale=1, user-scalable=0">
  <meta name="viewport" content="width=240, initial-scale=1, maximum-scale=1, user-scalable=0">
  <title>Vanda Carpets - Process Management</title>

  <!-- Iinclude Stylesheet !-->
  <link rel="stylesheet" href="inc/css/style.css">

  <!-- Iinclude nessecary javascripts !-->
  <script language="javascript" type="text/javascript" src="../../inc/script/jquery.js"></script>
  <script language="javascript" type="text/javascript" src="../../inc/script/scan.js"></script>
  
  <script>
	// Set the required parameters for the page to run.
	const inactivityTimeout = 1 * 60 * 1000; // 10 minutes
  </script>
  <style
  <style>
	
  </style> 
  </head>
  <body>
    <h1 id="header">Levering</h1>
    <div id="notice"></div>
    <div id="shipform-container">
    	<form id="shipform" name="shipform" method="post">	
         <ul class="mobilelist" id="shiplist">
            <li><label for="klant">Klant: </label><input type="text" id="klant" name="klant" value="" /></li>
            <li><label for="barcode">Barcode: </label><input type="text" id="barcode" name="barcode" value="" autofocus /></li>
            <li><label for="leverid">Zending: </label><input type="text" id="leverid" name="leverid" value="" readonly="true" />
            <li><center><input type="submit" onClick="SubmitShipment();" value="Verstuur"></center></li>
         </ul>
        </form>
    </div>
    <table id="excelDataTable" border="0"> 	   
    </table>
	<script src="inc/script/inactivity-timer.js"></script>
  </body>
</html>
