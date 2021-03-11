
				
$(document).ready(function(){
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Functies voor Rollen																																		 //
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	$("#childrollform").hide();
	$('#rollform #input_verstuur').on('click', function(){

	$.ajax({ 
		  type: "POST",  
		  url: "pages/process-rollen.php",  
		  data: {task: "check", rolnummer: $('#input_rolnummer').val() },  
		  success: function(html) { 

		if(html === 'true'){
			alert('dubbel rolnummer ingevoerd');
			$('#input_rolnummer').css('border', '1px solid red');
			$('#input_rolnummer').select();
			return;
		} else {
			$('#input_rolnummer').css('border', '1px solid #999');
		}
		
		
		if($('#input_rolnummer').val() === ''){
			alert('geen rolnummer ingevoerd');
			$('#input_rolnummer').css('border', '1px solid red');
			$('#input_rolnummer').select();
			return;	
		} else {
			$('#input_rolnummer').css('border', '1px solid #999');
			
		}
		
		if($('#input_bronlengte').val() === ''){
			alert('geen bronlengte ingevoerd');
			$('#input_bronlengte').css('border', '1px solid red');
			$('#input_bronlengte').select();
			return;	
		} else {
			$('#input_bronlengte').css('border', '1px solid #999');
		}
		
		if($('#input_snijlengte').val() === ''){
			alert('geen snijlengte ingevoerd');
			$('#input_snijlengte').css('border', '1px solid red');
			$('#input_snijlengte').select();
			return;	
		} else {
			$('#input_snijlengte').css('border', '1px solid #999');
		}
		
		if($('#input_kleur').val() === ''){
			alert('geen snijlengte ingevoerd');
			$('#input_kleur').css('border', '1px solid red');
			$('#input_kleur').select();
			return;	
		} else {
			$('#input_kleur').css('border', '1px solid #999');
		}
		
		// Als alles goed is gegaan de rollen verwerken.
		$.post( "pages/process-rollen.php", $('#rollform').serializeArray())
  			.done(function( data ) {
			$("#childrollform").html(data);
			$("#childrollform").slideDown(500);
			$("#input_task").val('add');
			$("#input_save").prop('disabled', false);
			$("#rollform-part1").slideUp(500);
			
		}).fail(function() {
    		alert( "error");
  		});
		console.log('geladen');
		 }
	//end html function on success ajax post 
	});
		
	});
	
	$('#editrollform #input_verstuur').on('click', function(){
		$.post( "pages/process-rollen.php", $('#editrollform').serializeArray())
  			.done(function( data ) {
			
			console.log(data);
			
			if(data === 'Opgeslagen'){
				window.location.href = 'index.php?page=rolltable';
			}
			
		}).fail(function() {
    		alert( "Fout bij het opslaan, probeer het opnieuw");
  		});
	});
	
	$('#editrollform #input_terug').on('click', function(){
		window.location.href = 'index.php?page=rolltable';
	});
	
	$('body').on('click','#childroll-vorige',function(){
		$("#childrollform").slideUp(500);
		$("#rollform-part1").slideDown(500);
		$("#input_task").val('generate');
		$("#input_save").prop('disabled', true);
	});	
	
	$("#rollform" ).submit(function(event) {
		event.preventDefault();
		$.post( "pages/process-rollen.php", $('#rollform').serialize())
  			.done(function( data ) {
			if(data !== 'error'){
				console.log(data);
				//getRolWindow($('#input_rolnummer').val());
				$("#frame").attr("src", 'pages/generate/generate_rol.php?rolnummer='+$('#input_rolnummer').val());
				$("#childrollform").slideUp(500);
				$('#errorbox').html('Opgeslagen'+data);

				window.location.href = 'index.php?page=rollen';
			}
			
		}).fail(function(data) {
    		alert( "error"+data);
			window.location.href = 'index.php?page=rollen';
  		});

		console.log('opgeslagen');
	});
	
	// Check if custom value is asked an clear width.
	$('#input_custom').on('change',function(){
		if($(this).prop("checked") == true){
			$('#input_snijbreedte').val('');
		}
	});
	
	// Check if custom width is active, and if so make new colum
	$('#snijbreedtes').on('change', '.snijbreedte', function(){
		if($('#input_custom').prop("checked") == true){
			console.log('hij vult m custom waardes in.');
			if(calculateRB() <= 0 ){
				// Do error
				console.log('PANIEK je snijd m in de lucht');
				$('#input_snijbreedte').closest('li').addClass('ui-state-highlight ui-corner-all');
				$('.snijbreedte').last().val(0);
				$('.snijbreedte').last().val(calculateRB);
				// Make float of 2 decimals.
				
			} else {
				$(this).clone().appendTo($(this).parent()).val('').select().attr('id',"input_snijbreedte"+$('.snijbreedte').length);
			}
		}
		
	});
	
	function calculateRB(){
		var rolbreedte = parseFloat($('#input_bronbreedte').val(),2);
		var optelbreedte = 0;
		var restbreedte = 0;
		
		$('.snijbreedte').each(function(){
			if (!isNaN(parseFloat($(this).val(),2))) {
				optelbreedte = parseFloat($(this).val(),2) + optelbreedte;
			}
		});		
		restbreedte = rolbreedte - optelbreedte;

		return restbreedte;
	}
	
	function getRolWindow(rolnummer) {
		// Controleer of het label al open staat zo ja sluiten om het boven aan te laten komen.
		//if(typeof(myWindow) !== 'undefined'){
		//	myWindow.close();
		//}
		// Nieuw label genereren met het goed artikelnummer.
		//myWindow = window.open("inc/generate_label.php?artikelnummer="+artikelnummer,'_blank',"width=1024,height=530,location=no,menubar=no,scrollbars=no,toolbar=no,fullscreen=no");

		var url = "pages/generate/generate_rol.php?rolnummer="+rolnummer;
		//var url = "https://www.google.com";
			window.open(url);	
	}
	
	// Einde van functies voor Rol //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
});