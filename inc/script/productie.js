$(document).ready(function(){
	
	// Set timer variable for message box.
	var timer = 5000;

	
	// Alles selecteren
	$('#roll-select-all').on('click', function () {
    	console.log('klikje');
		var checked = $(this).prop('checked');
    	$(this).closest('table').find(':checkbox').prop('checked', checked);
	});
	
	
	// Filter aanpassing
	$('.viewtype').change(function(){
		$('#filter_form').submit();
	});
	
	// Reset functie
	$('#reset_free_form').click(function(){
		$('#filter_form').trigger("reset");
	});
	
	// Verwijder rollen
	$('#roll-shipform #input_verwijder').on('click', function(){
	   if(confirm('Deze rollen verwijderen ?')){
	    $('#roll-shipform #task').val('verwijder');
	    $('#roll-shipform ').submit();
	   }
	});
	
	// Haal rollen uit een zending
	$('#roll-shipform #input_haalterug').on('click', function(){
	   if(confirm('Deze rollen terugboeken naar voorraad ?')){
	    $('#roll-shipform #task').val('haalterug');
	    $('#roll-shipform ').submit();
	   }
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
		var result = ''; 

		if(artikelnummer === '')
		{
			result = 'geen artikel ingevoerd';
		} 
		else if(!numberReg.test(gewicht))
		{
			//gewicht is niet goed
			result = 'geen gewicht of aantal ingevoerd';	
		} 
		// STANSEN PAGINA (Moet naar andere JS)
		//else if(task === 'add-stans' && ordernr === ''){
		//	result = 'geen ordernummer ingevoerd';
		//}
		
		//$('#errorbox').slideDown('fast');
		//$('#errorbox').html(result);
		
		if(result.includes("geen"))
		{
			$("#errorbox").addClass("ui-corner-all ui-state-error");
			$("#errorbox").removeClass("machine-success");
			$('#errorbox').slideDown('fast');
			$('#errorbox').html(result);	
			return;  // Stop script from processing further
		} else {
			$("#errorbox").removeClass("ui-state-error");
			$("#errorbox").addClass("ui-corner-all machine-success");
		}

		// Boek het artikel in de mysql database.
		var poststring = 'task=insertartikel&artikelnummer='+artikelnummer+'&kwaliteit'+kwaliteit+'&gewicht='+gewicht+'&ordernr='+ordernr;
		$.ajax({
			type: "POST",  
			url: "pages/process.php",  
			data: poststring,
		    success: function(result) {  
					if(result.includes("succes")){
						$('#errorbox').slideDown('fast');
						$('#errorbox').html(result);
					
						if(task === 'add-stans'){
							getStansWindow(barcode,gewicht);	
						} else { 
							getLabelWindow(barcode);
						}
						console.log(result);
					}
					setTimeout(function() {
						$('#errorbox').slideUp('fast');
					}, timer);

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
				
			}, error: function (xhr, ajaxOptions, thrownError) {
				var error = (xhr.status);
				error = error + ' ' + thrownError;
				$('#errorbox').slideDown('fast');
				$('#errorbox').html(error);
				$("#errorbox").addClass("ui-corner-all ui-state-error");  
				  setTimeout(function() {
						$('#errorbox').slideUp('fast');
					}, timer*2);
			  }
		}); //end post value
	}); // end click function

});


