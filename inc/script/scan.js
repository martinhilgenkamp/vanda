// Docuement ready functions
$(document).ready(function(){
	// Select Input field barcode
	$('#barcode').focus();
	
	$( "#shipform" ).submit(function( event ) {
  			event.preventDefault();
			$('#barcode').val('');
	});
	
	$('#klant').keypress(function(){
		$('#leverid').val('');
	});

	// Build the table with active shipments
	LoadShipments('#shipmenttable');			
}); // Close Document ready


// Handle the shipment
function SubmitShipment() {
	// Setup the request for shipment
	$.ajax({
		cache: false,
		url: "../../pages/scanner/scanproc.php", 
		type: "POST",
		data: {
			"task": "register",
			"klant": $('#klant').val(),
			"barcode": $('#barcode').val(),
			"leverid": $('#leverid').val()
		},
		success: function (data) {				
			//Notify user 		
			$('#notice').html(data.message);
			if(data.error == 1){
				$('#notice').removeClass("notice");
				$('#notice').addClass("error");
			} else {
				$('#notice').removeClass("error");
				$('#notice').addClass("notice");
			}
			
			// Set leverid input on new shipment and fill table
			if(data.leverid !== $("#leverid").val()){
				$("#leverid").val(data.leverid);
				$('#excelDataTable').empty();			
				LoadShipments('#shipmenttable');
			}
			
			selectBarcode();
		},
		error: function (data, error) {
			console.log(error);
			alert(data);	
		}
	});	
}
// End SubmitShipment()


// Get Counter for shipping MOET HIER NOG KOMEN!!!


// Get active shipments and put this in the html table
function LoadShipments(){	
	 // Setup the request for shipment
	$.ajax({
		cache: false,
		url: "../../pages/scanner/scanproc.php", 
		type: "POST",
		dataType: "json",
		data: {
			task: "load"
		},
		success: function (data) {
			buildHtmlTable(data,'#excelDataTable');
		},
		error: function (data) {
			console.log(data);
			alert(data);
		}
	});	
		
	selectBarcode(); 
} // End LoadShipments()

//////////////////////////////////////////////////////////
// Hieronder komen output zaken
/////////////////////////////////////////////////////////

// Builds the HTML Table out of shipments.
function buildHtmlTable(shipments,selector) {
  var columns = addAllColumnHeaders(shipments, selector);
  for (var i = 0; i < shipments.length; i++) { // rijen uitzetten
    var row$ = $('<tr class="shipmentrow" />'); 
	  for (var colIndex = 0; colIndex < columns.length; colIndex++) {
	    var cellValue = shipments[i][columns[colIndex]];
		var colName = columns[colIndex];
		
	    // debug ! console.log(columns[colIndex]);
		
        if (cellValue == null){    //set empty cells to prevent errors
		    cellValue = "";
	    }
		
		if(shipments[i][columns[colIndex]] == 'klant'){
			row$.append($('<td class='+colName+'/>').html('<a class="klantnaam">'+cellValue+'</a>'));
		} else {
			row$.append($('<td class='+colName+'/>').html(cellValue));
		}
        $(selector).append(row$);
      }
  }
	
	
 $('.shipmentrow').on("click",function(){ 
  $('#leverid').val($(this).children("td:first").text());	//set leverid
  $('#klant').val($(this).children(".klant").text());	//set Klant
  selectBarcode(); 
 });
} // end buildHTMLTable()



// get the colomn headers and put these in the table
function addAllColumnHeaders(shipments, selector) {
  var columnSet = [];
  var headerTr$ = $('<tr/>');
  for (var i = 0; i < shipments.length; i++) {
    var rowHash = shipments[i];
    for (var key in rowHash) {
      if ($.inArray(key, columnSet) == -1) {
        columnSet.push(key);
        headerTr$.append($('<th/>').html(key));
      }
    }
  }
  $(selector).append(headerTr$);
  return columnSet;
}


function clearForm(){
	$('#klant').val();
   	$('#barcode').val();
   	$('#leverid').val();
}

function selectBarcode(){
	// Select Input field barcode
	$('#barcode').focus();
}
