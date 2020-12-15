$(document).ready(function(){
	
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
});