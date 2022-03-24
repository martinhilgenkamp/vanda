// Define global parameters.
var article;
var supplier;
var amount;

// Docuement selectors
$(document).ready(function(){
		
	 // JQuery ui element initialisation
	 ProcessLayout();	 
	 $(document).on('click',".suppliers a.article", BuildAccordion);	 
	 $(document).on('click','.submit',SubmitInput);
	 $(document).on('click','.reset',function(){
		var parent = $(this).parent();
		processArticles(parent.prev('h3'))
	 });
	 
	 $(document).on('click','#go',function(){
		$('#filterform').submit();	
	 });
	
	 $('.delete').css('cursor','pointer');
	 $('.delete-stock').css('cursor','pointer');
	
	 $('#frame').css("display", "none");
	 
	 $(document).on('click', ".gettransport",function(){
		 supplier = $(this).parent().attr("id");
		 
		 var dataString = 'task=gettransport&supplier=' + supplier;
		 $.ajax({  
			  type: "POST",  
			  url: "pages/process.php",  
			  data: dataString,  
			  success: function(result) {  
				alert(result);
			  },
			  error: function (xhr, ajaxOptions, thrownError) {
				var error = (xhr.status);
				error = error + ' ' + thrownError;
				$('#errorbox').html(error);
			  }  
				});
	 });
	
	 $(document).on('click', ".returntransport",function(){
		 supplier = $(this).parent().attr("id");
		 
		 var dataString = 'task=returntransport&supplier=' + supplier;
		 $.ajax({  
			  type: "POST",  
			  url: "pages/process.php",  
			  data: dataString,  
			  success: function(result) {  
				alert(result);
			  },
			  error: function (xhr, ajaxOptions, thrownError) {
				var error = (xhr.status);
				error = error + ' ' + thrownError;
				$('#errorbox').html(error);
			  }  
				});
	 });
	
	
	 $(document).on('click',".delete",function(){
	 	var q = confirm('Weet je het zeker');
		// Functies voor deleten.
		var row   = $(this).closest('tr');
		var rowid = $(this).attr('id');
		
		$('#rowid').val(rowid);
		if(q){
			var dataString = 'task=delete&rowid=' + rowid;
			$.ajax({  
			  type: "POST",  
			  url: "pages/process.php",  
			  data: dataString,  
			  success: function(result) {  
				row.hide();
			  },
			  error: function (xhr, ajaxOptions, thrownError) {
				var error = (xhr.status);
				error = error + ' ' + thrownError;
				$('#errorbox').html(error);
			  }  
			}); 
		}
	 });
	 
	 $(document).on('click',".delete-stock",function(){
	 	var q = confirm('Weet je het zeker');
		// Functies voor deleten.
		var row   = $(this).closest('tr');
		var rowid = $(this).attr('id');
		$('#rowid').val(rowid);
		if(q){
			var dataString = 'task=delete-stock&rowid=' + rowid;
			$.ajax({  
			  type: "POST",  
			  url: "pages/process.php",  
			  data: dataString,  
			  success: function(result) {  
				row.hide();
			  },
			  error: function (xhr, ajaxOptions, thrownError) {
				var error = (xhr.status);
				error = error + ' ' + thrownError;
				$('#errorbox').html(error);
			  }  
				});
		}
	 });
	 	 
	 $(document).on('click',".ship",function(){
	 	var q = confirm('Weet je het zeker');
		// Functies voor deleten.
		var row   = $(this).closest('tr');
		var rowid = $(this).attr('id');
		
		$('#rowid').val(rowid);
		if(q){
			var dataString = 'task=ship&rowid=' + rowid;
			$.ajax({  
			  type: "POST",  
			  url: "pages/process.php",  
			  data: dataString,  
			  success: function(result) {  
				location.reload();
			  },
			  error: function (xhr, ajaxOptions, thrownError) {
				var error = (xhr.status);
				error = error + ' ' + thrownError;
				$('#errorbox').html(error);
			  }  
				});
		}
	 });
	 
	 
	 
	 $(document).on('click',".unship-article",function(){
	 	var q = confirm('Weet je het zeker');
		// Functies voor deleten.
		var row   = $(this).closest('tr');
		var rowid = $(this).attr('id');
		
		$('#rowid').val(rowid);
		if(q){
			var dataString = 'task=unship-article&rowid=' + rowid;
			$.ajax({  
			  type: "POST",  
			  url: "pages/process.php",  
			  data: dataString,  
			  success: function(result) {  
				location.reload();
			  },
			  error: function (xhr, ajaxOptions, thrownError) {
				var error = (xhr.status);
				error = error + ' ' + thrownError;
				$('#errorbox').html(error);
			  }  
				});
		}
	 });	 
	 
	 $(document).on('click',".unship",function(){
	 	var q = confirm('Weet je het zeker');
		// Functies voor deleteweet je zekern.
		var row   = $(this).closest('tr');
		var rowid = $(this).attr('id');
		
		$('#rowid').val(rowid);
		if(q){
			var dataString = 'task=unship&rowid=' + rowid;
			$.ajax({  
			  type: "POST",  
			  url: "pages/process.php",  
			  data: dataString,  
			  success: function(result) {  
				location.reload();
			  },
			  error: function (xhr, ajaxOptions, thrownError) {
				var error = (xhr.status);
				error = error + ' ' + thrownError;
				$('#errorbox').html(error);
			  }  
				});
		}
	 });
	
	
	// TAKEN WEERGAVE DENK IK 
	
	
	 $(document).on('click','.open',function(){
		$.post( "index.php?page=task", { id: $(this).attr('id'), action: "behandeling" }) 
		.done(function(data){
			location.href = window.location.href;
		})
		.fail(function( data ) {
			alert( "Fail: " + data );
		  });
		 $(this).html('behandeling')
	 });
	
	 $(document).on('click','.behandeling',function(){
		$.post( "index.php?page=task", { id: $(this).attr('id'), action: "gereed" }) 
		 .done(function(data){
			location.href = window.location.href;
			})
			.fail(function( data ) {
			alert( "Fail: " + data );
		  });
	 });
	 
	
	 //Make CSV button clickable
	 $(document).on('click','#csv',function(){
		$('#csvform').submit();
	 })
	 
	 // Datepicker initialisation
	 $("#selectdate").datepicker({
		dateFormat: 'yy-mm-dd',
		showButtonPanel: true,
		showWeek: true,
		firstDay: 1
	 });
	 
	 $('#monthselect').datepicker({
		 dateFormat: 'yy-mm-dd',
		 showButtonPanel: true,
		 showWeek: true,
		 firstDay: 1,
		 numberOfMonths: 3,
		 showCurrentAtPos: 1
	 });
	 
	 // Datepicker initialisation
	 $("#startdate").datepicker({
		numberOfMonths: 3,
		dateFormat: 'yy-mm-dd',
		showButtonPanel: true,
		defaultDate: '-1m',
		 showCurrentAtPos: 1, 
		 onClose: function( selectedDate ) {
			$( "#stopdate" ).datepicker( "option", "minDate", selectedDate );
		}	 
	 });
	 
	  // Datepicker initialisation
	 $("#stopdate").datepicker({
		numberOfMonths: 3,
		dateFormat: 'yy-mm-dd',
		defaultDate: new Date(),
		showCurrentAtPos: 1,
		 onClose: function( selectedDate ) {
			$( "#startdate" ).datepicker( "option", "maxDate", selectedDate );
		}	 
	 });	
	 
	
	$("#date").datepicker({
		dateFormat: 'yy-mm-dd',
		showButtonPanel: true,
		showWeek: true,
		firstDay: 1
	 });
	 
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	// Hieronder komen de nieuwe zaken voor het invoeren en labelen van producten en verzendingen
	//////////////////////////////////////////////////////////////////////////////////////////////
	
	// Inboek functie
	$('#insertform .article').click(function(){
        $('#insertform .ui-state-active').removeClass('ui-state-active ui-state-hover');
		
		$(this).addClass('ui-state-active ui-state-hover');
		
		$('#artikelnummer').val($(this).attr('value')); // set input value.
		$('#gewicht').attr('disabled', false);
		$('#gewicht').select();
		
	});

	$("#gewicht").on('input', function() {
		var value = $(this).val();
		if (value > 9999) {
			$(this).val(9999);
		}
	});

	$("#taskform" ).submit(function( event ) {
		if($("#name").val() === ''){
			alert("Taak moet ingevuld zijn");
			event.preventDefault();
		}
		if($("#adres").val() === ''){
			alert("Adres moet ingevuld zijn");
			event.preventDefault();
		}
		if($("#date").val() === ''){
			alert("Datum moet ingevuld zijn");
			event.preventDefault();
		}
		
		// Fileupload script
	    var file_data = $('#file').prop('files')[0];
		var form_data = new FormData();
		var file_name = $('#file').val().split('\\').pop();
			
		form_data.append('file', file_data);
		$.ajax({
			url: 'inc/upload.php', // point to server-side PHP script 
			dataType: 'text', // what to expect back from the PHP script
			cache: false,
			contentType: false,
			processData: false,
			async: false,
			data: form_data,
			type: 'post',
			success: function (response) {
				var response = $.trim(response);
				console.log(response);
				if(response === 'success' || response === 'exists'){
					//alert('upload geslaagd');
					$('#filename').val(file_name);
				};
				
				if(response === 'filetype'){
					alert('Verkeerd upload bestand.');							
				};
				
			},
			error: function (response) {
				alert(response); // display error response from the PHP script
				event.preventDefault();
			}
		});
	});
	
	
	// Productie registreer artikelen formulier.
	$('#insertform #verstuur').click(function(){
		
		// create regexp for validation
		var numberReg =  /^[0-9]{1,4}$/;
		var artikelnummer = $('#artikelnummer').val();
		var kwaliteit = $('#kwaliteit').val() != undefined ? $('#kwaliteit').val() : "";
		var gewicht = $('#gewicht').val();
		var barcode = $('#barcode').val();
		var ordernr =  $('#ordernr').val() != undefined ? $('#ordernr').val() : "";
		var task = $('#task').val();
				
		if(artikelnummer === ''){
			alert('geen artikel ingevoerd');
			return;	
		}
		if(!numberReg.test(gewicht)){
			//gewicht is niet goed
			alert('geen gewicht/aantal ingevoerd');
			return;	
		} 
		// end if nummer
		if(task === 'add-stans' && ordernr === ''){
			alert('geen ordernummer ingevoerd');
			return;	
		}		
		// Boek het artikel in de mysql database.
		$.post( "pages/process.php", { task: "insertartikel", artikelnummer: artikelnummer, kwaliteit: kwaliteit, gewicht: gewicht, ordernr: ordernr}).done(function( data ) {
			
			if(data.includes("succes")){
				if(task === 'add-stans'){
					getStansWindow(barcode,gewicht);	
				} else { 
					getLabelWindow(barcode);
				}
				console.log(data);
				$('#insertform .ui-state-active').removeClass('ui-state-active ui-state-hover'); // deactivate button
				$('#artikelnummer').val(''); // set input value.
				$('#gewicht').attr('disabled', true);
				$('#gewicht').val('');
				// Get new barcode id
				var dataString = 'task=getnewbarcode';
				$.ajax({  
					  type: "POST",  
					  url: "pages/process.php",  
					  data: dataString,  
					  success: function(result) {  
					
						$('#barcode').val(result);
					  },
					  error: function (xhr, ajaxOptions, thrownError) {
						 
						var error = (xhr.status);
						error = error + ' ' + thrownError;
						$('#errorbox').html(error);
					  }  
				});
				
			} else {
				alert('Helaas is er een fout bij het opslaan van de waarde probeer het opnieuw');	
			}
		}); //end post value
	}); // end click function
	
	
	// Stansen registreer formulier.
	$('#stansform #verstuur').click(function(){
		console.log("stans submit");
		

		// create regexp for validation
		var numberReg =  /^[0-9]{1,4}$/;
		var artikelnummer = $('#artikelnummer').val();
		var kwaliteit = $('#kwaliteit').val() != undefined ? $('#kwaliteit').val() : "";
		var gewicht = $('#gewicht').val();
		var barcode = $('#barcode').val();
		var ordernr =  $('#ordernr').val() != undefined ? $('#ordernr').val() : "";
		var task = $('#task').val();
		var colli = $('#colli').val();
				
		if(barcode === ''){
			alert('geen barcode bekend');
			return;	
		}
		if(artikelnummer === ''){
			alert('geen artikel ingevoerd');
			return;	
		}
		if(!numberReg.test(gewicht)){
			//gewicht is niet goed
			alert('geen gewicht/aantal ingevoerd');
			return;	
		} 
		// end if nummer
		if(task === 'add-stans' && ordernr === ''){
			alert('geen ordernummer ingevoerd');
			return;	
		}
		
		if(!numberReg.test(colli)){
			//Set Colli to 1 if not defined
			colli = 1;
		} 
		
		
		// Boek het artikel in de mysql database.
		$.post( "pages/process.php", { task: "insertartikel", artikelnummer: artikelnummer, kwaliteit: kwaliteit, gewicht: gewicht, ordernr: ordernr, colli: colli}).done(function( data ) {
		
			if(data.includes("succesvol")){
				//Show Etiket.
				console.log(data);
				
				// Delay page from reloading for x * 100ms seconds depending on amount of entries to add.				
				$( "#content" ).animate({
					width: "100%",
					padding: "100px",
					height: "400px"
				}, 1500 );
				$('#content').html('<center><img src="images/loading.gif" border="0"/></center>')
				
				
				//Produce the labels to print.
				getStansWindow(barcode,gewicht,colli);
				
				// Reset the form for reuse.	
				setTimeout(function(){window.location = 'index.php?page=stansen';} ,colli*100);
		
			} else {
				alert('Helaas is er een fout bij het opslaaaaan van de waarde probeer het opnieuw');
				console.log(data);
				return;
			}
		}); //end post value
			
		
	}); // end click function
	
	
	// Stans functies
	$('#stansform .article').click(function(){
		
        $('#stansform .ui-state-active').removeClass('ui-state-active ui-state-hover');
		$(this).addClass('ui-state-active ui-state-hover');
		
		$('#lengte').attr('disabled', true);
		$('#breedte').attr('disabled', true);
		
		$('#kwaliteit').val($(this).attr('value')); // set input value.
		$('#gewicht').attr('disabled', false);
		$('#gewicht').select()

	});
	
	$('#stansform #reset').click(function(){
		window.location = 'index.php?page=stansen';
	});
	
	$('#stansform #lengte,#stansform #breedte').click(function(){
	 	$('.article').slideUp();
		$('#gewicht').attr('disabled', false);
	});
	
	$('#stansform #lengte,#stansform #breedte').keyup(function(){
		$('#kwaliteit').val($('#lengte').val()+' X '+$('#breedte').val());
	});
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// End of stans part
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	// Autocomplete shipid
	if($("#leverid").length > 0){
		// Check if focus on barcode is needed
		
		
		// Select content on focus
		$("#leverid").click(function(){
			$(this).select();	
		});
		
		// Autocomplete shipid	
		$.ajax({  
			  type: "POST",  
			  url: "pages/process.php",  
			  data: { 
			  	task: 'getshipid'
			  }, 
			  success: function(data) {  
				$("#leverid").autocomplete({
				  source: data
				});
			  },
			  error: function (xhr, ajaxOptions, thrownError) {
				  debugger;
				var error = (xhr.status);
				error = error + ' ' + thrownError;
				$('#errorbox').html(error);
			  }  
		});
	} // endif leverid.length
	
	// Autocomplete barcode
	$.ajax({  
		type: "POST",  
		url: "pages/process.php", 
		data: { 
		  	task: 'getbarcode'
	  	}, 
		success: function(data) {  
			$("#barcode").autocomplete({
			  source: data
			});
		},
		error: function (xhr, ajaxOptions, thrownError) {
			var error = (xhr.status);
			error = error + ' ' + thrownError;
			$('#errorbox').html(error);
		}  
	});
});  // close document.ready()

// Process Layout of dynamic generated content
function ProcessLayout() {
	 $("#accordion").accordion({ 
	 	height: "400px", 
	 	beforeActivate: function(event,ui){
			ui.oldHeader.html(processArticles);
		}
	 });
	 $( "a.button" ).button(); //Create buttons from links
}

// Make the buttons active to amount menu.
function BuildAccordion() {
			article = $(this).attr('value');			//set the values in javascript parameters
			supplier = $(this).parent().attr("id");	 	//set the values in javascript parameters
			
			var dataString = 'task=accordion&article=' + article + '&supplier=' + supplier;	// Build post values	
			$.ajax({  
			  type: "POST",  
			  url: "pages/process.php",  
			  data: dataString,  
			  success: function(result) {  
				$('#'+supplier).html(result); 
				$('#'+supplier).children(".submit").append('Bevestig');
				$('#'+supplier).children(".reset").append('Terug');
				$(".amountbutton").click(function(){
					$('#amount').val($(this).html());
				});
				ProcessLayout();
			  },
			  error: function (xhr, ajaxOptions, thrownError) {
				var error = (xhr.status);
				error = error + ' ' + thrownError;
				$('#errorbox').html(error);
			  }  
			});  
			
			// NONE AJAX BASED PROCESSING
			$('#supplier').val(supplier);
			$('#article').val(article);
			
}

// Register Input in the database
function SubmitInput() {
		var parent = $(this).parent();
		amount = $('#amount').val();
		supplier = $('#supplier').val();
		article = $('#article').val();
		remark = $('#remark').val();
		
		// Do the database transaction
		var dataString = 'task=register&supplier=' + supplier + '&amount=' + amount + '&article=' + article + '&remark=' + remark;	// Build post values	
			$.ajax({  
			  type: "POST",  
			  url: "pages/process.php",  
			  data: dataString,  
			  success: function(result) {  
					$('#results').html(result);
					$('#results').slideDown();
					  var dataString = 'task=buildproduct&supplier=' + supplier;	// Build post values
					  $.ajax({  
					  type: "POST",  
					  url: "pages/process.php",  
					  data: dataString,  
					  success: function(result) {  
						parent.html(result);
						ProcessLayout();
					  },
					  error: function (xhr, ajaxOptions, thrownError) {
						var error = (xhr.status);
						error = error + ' ' + thrownError;
						$('#errorbox').html(error);
					  }  
					});   
			  },
			  error: function (xhr, ajaxOptions, thrownError) {
				var error = (xhr.status);
				error = error + ' ' + thrownError;
				$('#errorbox').html(error);
			  }  
			});	
		processArticles(parent.prev('h3').attr('id'));  
			
}

// Get the articles for regeneration of the buttons when scrolling through suppliers
function processArticles(onderdeel) {
		  if(!onderdeel && onderdeel != null){
		  	var currelement = $(this);
		  } else {
			var currelement = $('#' + onderdeel);
		  }	
		  var supplier_no = currelement.next('div').attr('id')
		  
		  var dataString = 'task=buildproduct&supplier=' + supplier_no;	// Build post values
		  $.ajax({  
		  type: "POST",  
		  url: "pages/process.php",  
		  data: dataString,  
		  success: function(result) {  
			currelement.next('div').html(result);
			ProcessLayout();
		  },
		  error: function (xhr, ajaxOptions, thrownError) {
			var error = (xhr.status);
			error = error + ' ' + thrownError;
			$('#errorbox').html(error);
		  }  
		});  		
}

function doreset(){
	$('#product_filter').val('');
	$('#supplier_filter').val('');
	$('#startdate').val('');
	$('#stopdate').val('');
	$('#filterform').submit();	
}

function SubmitProduct(){
		
}

/*****************************************************
** Functie om de labels te genereren				**
*****************************************************/

function getLabelWindow(artikelnummer){
	// Controleer of het label al open staat zo ja sluiten om het boven aan te laten komen.
	//if(typeof(myWindow) !== 'undefined'){
	//	myWindow.close();
	//}
	// Nieuw label genereren met het goed artikelnummer.
	//myWindow = window.open("inc/generate_label.php?artikelnummer="+artikelnummer,'_blank',"width=1024,height=530,location=no,menubar=no,scrollbars=no,toolbar=no,fullscreen=no");
	
	var loc = window.location.href;
	var dir = loc.substring(0, loc.lastIndexOf('/'));
	var url = dir+"/pages/generate/generate_label.php?artikelnummer="+artikelnummer;
	window.open(url);
	
	$('#printFrame').attr('src', url);
    $('#printFrame').load(function() {
        var frame = document.getElementById('printFrame');
        if (!frame) {
            alert("Error: Can't find printing frame.");
            return;
        }
        frame = frame.contentWindow;
        frame.focus();
    });
}

function getStansWindow(artikelnummer,aantal,colli){
	// Controleer of het label al open staat zo ja sluiten om het boven aan te laten komen.
	//if(typeof(myWindow) !== 'undefined'){
	//	myWindow.close();
	//}
	// Nieuw label genereren met het goed artikelnummer.
	//myWindow = window.open("inc/generate_label.php?artikelnummer="+artikelnummer,'_blank',"width=1024,height=530,location=no,menubar=no,scrollbars=no,toolbar=no,fullscreen=no");
	
	// Aantal op 1 zetten als deze niet is aangegeven.
	if (aantal === undefined) {
          aantal = 1;
    } 
	
	var loc = window.location.href;
	var dir = loc.substring(0, loc.lastIndexOf('/'));
	var url = dir+"/pages/generate/generate_stans.php?artikelnummer="+artikelnummer+"&aantal="+aantal+"&colli="+colli;
	
	window.open(url);
	
	//$('#printFrame').attr('src', url);
    //$('#printFrame').load(function() {
    //    var frame = document.getElementById('printFrame');
    //    if (!frame) {
    //        alert("Error: Can't find printing frame.");
    //        return;
    //    }
    //    frame = frame.contentWindow;
    //    frame.focus();
    //});
}