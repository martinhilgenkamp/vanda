// Docuement ready functions
$(document).ready(function(){
    // Do the shipment on submit
    $('#shipform').submit(function(e) {
        e.preventDefault(); // Prevent the form from submitting traditionally

        // Get form values
        var klant = $('#klant').val();
        var ship_id = $('#ship_id').val();
        var currentTime = new Date().toLocaleString();
        var task = 'ship';
        var barcode = $('#barcode').val();

        // Verify "klant" has normal characters (letters and spaces)
        if (!/^[A-Za-z0-9\s]+$/.test(klant)) {
            $('#result')
            notifyUser(false,'Een klant mag alleen letters spaties en nummers bevatten');
            return;
        }
        // Verify barcode
        if (/^[A-Z0-9]{15}$/.test(barcode)) {
            notifyUser(false,'Barcode is onjuist');
            return;
        }
        // Verify "ship_id" is an integer or empty
        //if (!/^(?:\d+)?$/.test($('#ship_id').val())) {
        //    notifyUser(false,'Zendingsnummer is onjuist');
        //    return;
        //}

        console.log('starting shipment processing');
        console.log('KLant: ' + klant);
        console.log('Ship_id: ' + ship_id);
        console.log('Barcode: ' + barcode);

        // AJAX request to submit data
        $.ajax({
            type: 'POST', // or 'GET' depending on your server-side logic
            url: 'process.php', // Replace with your server endpoint
            data: {
                klant: klant,
                ship_id: ship_id,
                time: currentTime,
                task: task,
                barcode: barcode
            },
            success: function(response) {
                notifyUser(response.success,response.message)
                loadShipments($('#openshipments'));
                // Fill in shipment id on pageload to prevent new shipments when adding new shipment.
                if(response.shipment){
                    $('#ship_id').val(response.shipment);
                }
                selectBarcode();
            },
            error: function(xhr, status, error) {
                notifyUser(false,'Error: ' + error)
                loadShipments($('#openshipments'));
                selectBarcode();
            }
        });

        
    });

    // Clear barcode on select.
    $("#barcode").on("focus", function() {
        $(this).val("");
    }); 
    // Initialize the page and set the timer and focus
    // Build the table with active shipments
    loadShipments($('#openshipments'));
    resetTimer(); 
    selectBarcode();
});

function notifyUser(success,message){
    // Set style for the message
    if(success){
        $('#result').removeClass('error').addClass('notice');
    } else {
        $('#result').removeClass('notice').addClass('error');
    }
    // Return the message
    $('#result').text(message);
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