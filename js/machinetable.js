$(document).ready(function(){

	//Make CSV button clickable
	 $(document).on('click','#csv',function(){
		$('#csvform').submit();
	 });
	 
	 
	
	// herstel button laten werken
	$("#herstel").on('click', function() {
		console.log('klikjeeee');
		window.location = 'index.php?page=machinetable';
	});
	
	// Alles selecteren
	$('#machine-select-all').on('click', function () {
		var checked = $(this).prop('checked');
    	$(this).closest('table').find(':checkbox').prop('checked', checked);
	});
	
	// Datepicker initialisation
	 $("#startdate").datepicker({
		numberOfMonths: 3,
		dateFormat: 'yy-mm-dd',
		showButtonPanel: true,
		defaultDate: '-1w',
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
	
	
	// Verwijder rollen
	$('#verwijder').on('click', function(){
	   if(confirm('Deze registraties verwijderen ?')){
	   	   var machineid = [];
		   
			$(".machine-checkbox").each(function(){
				// 
				if($(this).prop("checked") === true){
					machineid.push($(this).val());
				}
			}); 
		   
		   $.ajax({ 
				   type: "POST", 
				   url: "pages/process-machines.php", 
				   data: { id : machineid, task : 'remove' }, 
				   success: function(data) { 
						  $('#errorbox').html(data);
				  		  if(data === "De registraties zijn verwijderd."){
							  $("#errorbox").addClass("ui-corner-all machine-success");
							  $('#errorbox').show();
							  $('#errorbox').scroll()
						  }
					} 
			}); 
		   
		   machineid.forEach(function(entry){
			   $('#row_'+entry).slideUp();
		   });
		   
		   
		   console.log('volgende ids worden verwijderd '+machineid);
	   }
	});
	
	
	
});// End document ready