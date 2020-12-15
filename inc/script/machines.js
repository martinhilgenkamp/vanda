// Set timer variable for message box.
var timer = 1000;

$(document).ready(function(){
	$('#errorbox').hide();
	// Save Machine input on Click
	$('.machinebutton').on('click',function(){
		// FUNCTIE VOOR OPSLAAN WAARDES MACHINES
		
		var id = $(this).attr('id').substr(-1);
		var persoon = $('#input_operator'+id).val();
		var kwaliteit = $('#input_kwaliteit'+id).val();	
				
		if(persoon === ''){
			$('#errorbox').slideDown('fast');
			$('#errorbox').html('Er is geen Operator opgegeven.');
			$("#errorbox").addClass("ui-corner-all ui-state-error");
			setTimeout(function() {
						$('#errorbox').slideUp('fast');
					}, timer);
			return;	
		}
		if(kwaliteit === ''){
			$('#errorbox').slideDown('fast');
			$('#errorbox').html('Er is geen kwaliteit opgegeven.');
			$("#errorbox").addClass("ui-corner-all ui-state-error");
			setTimeout(function() {
						$('#errorbox').slideUp('fast');
					}, timer);
			return;	
		}
		
		var dataString = 'task=add&persoon='+persoon+'&kwaliteit='+kwaliteit+'&machine='+id;
		$.ajax({  
			  type: "POST",  
			  url: "pages/process-machines.php",  
			  data: dataString,  
			  success: function(result) { 
				  	$('#errorbox').slideDown('fast');
					$('#errorbox').html(result);
				  	
				  	if(result === "Opgeslagen"){
						$("#errorbox").removeClass("ui-state-error");
						$("#errorbox").addClass("ui-corner-all machine-success");
					} else {
						$("#errorbox").addClass("ui-corner-all ui-state-error");
						$("#errorbox").removeClass("machine-success");	
					}
				  
				  setTimeout(function() {
						$('#errorbox').slideUp('fast');
					}, timer);
			  },
			
			  error: function (xhr, ajaxOptions, thrownError) {
				var error = (xhr.status);
				error = error + ' ' + thrownError;
				$('#errorbox').slideDown('fast');
				$('#errorbox').html(error);
				$("#errorbox").addClass("ui-corner-all ui-state-error");  
				  setTimeout(function() {
						$('#errorbox').slideUp('fast');
					}, timer);
			  }  
		}); // end ajaxs	
					
		// Debug
		//console.log('machine '+id+' met operator '+persoon+' heeft kwaliteit '+kwaliteit+' geregistreerd');
		
		// EINDE OPSLAAN
		
	});
	
});