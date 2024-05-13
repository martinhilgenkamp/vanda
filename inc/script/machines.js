// Set timer variable for message box.
var timer = 5000;
var messagetimer;

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
				  	
				  	if(result.includes("opgeslagen")){
						$("#errorbox").removeClass("ui-state-error");
						$("#errorbox").addClass("machine-success");	
						$("#errorbox").addClass("ui-corner-all machine-success");
					} else {
						$("#errorbox").addClass("ui-corner-all ui-state-error");
						$("#errorbox").removeClass("machine-success");	
					}
					checkAndUpdateBackground();
			  },			
			  error: function (xhr, ajaxOptions, thrownError) {
				var error = (xhr.status);
				error = error + ' ' + thrownError;
				$('#errorbox').slideDown('fast');
				$('#errorbox').html(error);
				$("#errorbox").addClass("ui-corner-all ui-state-error");  
			  }  
		}); // end ajaxs

		checkAndUpdateBackground();
		
		clearTimeout(messagetimer);
		messagetimer = setTimeout(function() {
			$('#errorbox').slideUp('fast');
		}, timer);
	});
	
	
	 // Function to check and update the background color
	 function checkAndUpdateBackground() {
        var currentTime = new Date().getTime();
		
        // Select all <ul> elements with class machine-ul
        $(".machine-ul").each(function() {
            // Get the time from the tijd attribute of the current <ul> element
            var machine =  $(this).attr("machine");
			fetchDataFromServer(machine)
			 .done(function(data){
					console.log(data);
					
					// Set the current kwaliteit if not active.
					if (!$('#input_kwaliteit'+machine).is(":focus")) {
						$('#input_kwaliteit'+machine).val(data.kwaliteit);
					}
					
					var parts = data.tijd.split(" ");
					// Extract the time part
					var timePart = parts[1];
					//Set time for user
					$('#tijd' + machine).val(timePart);
					// Set time for script
					$('#machine' + machine).attr('tijd', data.tijd);
					$('#machine' + machine).attr('picked', data.picked);

					var tijdAttr = $('#machine' + machine).attr("tijd");
					var pickedAttr = $('#machine' + machine).attr("picked");
					var tijd = new Date(tijdAttr).getTime();
					var Timediff = currentTime - tijd;

					//console.log("Machine" + machine +  " : " + tijdAttr + " - " + tijd + " = " + Timediff);

					// Check if the time difference is less than 5 minutes (300000 milliseconds)
					if (Timediff >= 30000000) {
						$('#machine' + machine).removeClass("red-background");
						$('#machine' + machine).removeClass("yellow-background");
						$('#machine' + machine).removeClass("green-background");
					
					} else if (Timediff >= 300000) {
						$('#machine' + machine).addClass("red-background");
						$('#machine' + machine).removeClass("yellow-background");
						$('#machine' + machine).removeClass("green-background");
						
					} else if (Timediff >= 60000) {
						$('#machine' + machine).addClass("yellow-background");
						$('#machine' + machine).removeClass("red-background");
						$('#machine' + machine).removeClass("green-background");
						
					} else {
						$('#machine' + machine).removeClass("yellow-background");
						$('#machine' + machine).removeClass("red-background");
						$('#machine' + machine).addClass("green-background");
						
					}
			});
        });
    }

		// Function to fetch data from the server
		function fetchDataFromServer(id) {
			// Implement this function to fetch data from your server
			// and return the data containing timestamps for each <ul> element
			// Example:
			return $.ajax({
				url: "pages/api/api.php?machine="+id,
				type: "GET",
				dataType: "json"
			});


		}

	// Call the function initially
    checkAndUpdateBackground();

    // Set up a loop to periodically recheck the time difference every 10 seconds
    setInterval(checkAndUpdateBackground, 10000);


});