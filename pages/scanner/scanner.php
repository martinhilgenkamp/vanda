<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=240, initial-scale=1, maximum-scale=1, user-scalable=0">
  <meta name="viewport" content="width=240, initial-scale=1, maximum-scale=1, user-scalable=0">
  <title>Vanda Carpets - Process Management</title>
  <!-- Iinclude nessecary javascripts !-->
  <script language="javascript" type="text/javascript" src="../../inc/script/jquery.js"></script>
  <script language="javascript" type="text/javascript" src="../../inc/script/scan.js"></script>
  <style>
	  * {
		margin: 0px;
		padding: 0px;
		font-family:  Arial;
	  }
	  
	  body, table {
		width: 240px; 
	    font-size: 0.9em;
	  }
	  
	  h1 {
		  text-align: center;
	  }
	  
	  label{
			display: inline-block;
			width: 100px;
			font-size: 0.8em;	
		}
	  
	  #notice{
		  width: 210px;
		  margin: 5px auto;
		  
	  }
	  
	  .notice {
		  	border: 1px solid #6879FF;
		  	background: #C8CEFF;
	  }
	  
	  .error {
		 	border: 1px solid #FF8D82;
		  	background: #FFD4A4;
	  }

		input[type=text] {
			display: inline-block;
			margin: 5px 0px;;
			width: 130px;	
		}
	  
	  input[type=submit] {
		  margin: 5px auto;
	  }
	  
	  table tr:hover{
		  cursor:  pointer;
		  background: #ccc;
		  text-align:  center;
	  }
	  
	  table tr td{
	   text-align:  center;
	  }
	
  </style> 
  </head>
  <body>
    <h1 id="header">Levering</h1>
    <div id="notice"></div>
    <div id="shipform-container">
    	<form id="shipform" name="shipform" method="post">	
         <ul class="mobilelist" id="shiplist">
            <li><label for="klant">Klant: </label><input type="text" id="klant" name="klant" value=""/></li>
            <li><label for="barcode">barcode: </label><input type="text" id="barcode" name="barcode" value="" autofocus/></li>
            <li><center><input type="submit" onClick="SubmitShipment();" value="Verstuur"></center></li>
         </ul>
         <input type="text" id="leverid" name="leverid" value="" />
        </form>
    </div>
    
    <table id="excelDataTable" border="0">
    
    
    </table>
  </body>
</html>
