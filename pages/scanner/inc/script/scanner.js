// Docuement ready functions
$(document).ready(function(){

function clearForm(){
	$('#klant').val();
   	$('#barcode').val();
   	$('#leverid').val();
}

function selectBarcode(){
	// Select Input field barcode
	$('#barcode').focus();
}


// Initialize the page and set the timer and focus
resetTimer(); 
selectBarcode();
});