<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Printer Test</title>
<script language="javascript">
	function isLoaded()
	{
	  var pdfFrame = window.frames["pdf"];
	  pdfFrame.focus();
	  pdfFrame.print();
	}
</script>	
	
</head>

<body>
	<h1>Printer test pagina</h1>
	<p>Hieronder word een iframe met de pdf getoond</p>
	<iframe id="pdf" name="pdf" src="inc/generate_label.php?artikelnummer=F008300000021919"></iframe>
</body>
</html>
