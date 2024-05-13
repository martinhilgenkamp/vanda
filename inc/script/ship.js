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
        if (!/^(?:\d+)?$/.test($('#ship_id').val())) {
            notifyUser(false,'Zendingsnummer is onjuist');
            return;
        }

        console.log('starting shipment processing');
        console.log('KLant: ' + klant);
        console.log('Ship_id: ' + ship_id);
        console.log('Barcode: ' + barcode);

        // AJAX request to submit data
        $.ajax({
            type: 'POST', // or 'GET' depending on your server-side logic
            url: 'pages/scanner/process.php', // Replace with your server endpoint
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

    // Clear shipmentnumber on select.
      $("#klant").on("change", function() {
        $('#ship_id').val("");
    }); 

    // Table function below.
    $('#openshipments').on('click', 'tr', function() {
        var klant = $(this).find('td.klant').text();
        var ship_id = $(this).find('td.id').text();

        // Fill the input fields with the selected values
        $('#klant').val(klant);
        $('#ship_id').val(ship_id);

        // Store the values in session storage
        sessionStorage.setItem('selectedKlant', klant);
        sessionStorage.setItem('selectedship_id', ship_id);

        $('#barcode').focus();
    }); // End click listener

    // Check for and retrieve stored values on page load
    var storedKlant = sessionStorage.getItem('selectedKlant');
    var storedship_id = sessionStorage.getItem('selectedship_id');

    if (storedKlant && storedship_id) {
        $('#klant').val(storedKlant);
        $('#ship_id').val(storedship_id);
    }

    // Initialize the page and set the timer and focus
    // Build the table with active shipments
    loadShipments($('#openshipments'));
    resetTimer(); 
    selectBarcode();
}); // End document ready

function notifyUser(success,message){
    // Set style for the message
    if(success){
        $('#result').removeClass('error').addClass('notice');
    } else if (message) {
        $('#result').removeClass('notice').addClass('error');
    } else {
        $('#result').removeClass('notice').removeClass('error');
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

// Get active shipments and put this in the html table
function loadShipments(selector){	
    // Setup the request for shipment
    $.ajax({
        cache: false,
        url: "pages/scanner/process.php", 
        type: "POST",
        dataType: "json",
        data: {
            task: "load"
        },
        success: function (response) {
            //  console.log(data);  DEBUGGING
            if(response.success){
                var table = buildHtmlTable(response.message);
                $(selector).html(table);
            } else {
                notifyUser(false,'Fout in het verzoek.' + response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr, status, error);
            alert("An error occurred while loading shipments.");
        }
    });	
} // End LoadShipments()

function buildHtmlTable(shipments) {
    var table = $('<table>').attr('id', 'shipmenttable'); // Add a class to the table
    var table = $('<table>').attr('class', 'data-table'); // Add a class to the table
    var tbody = $('<tbody>');
    var columns = addAllColumnHeaders(shipments, tbody);
    var activeShipment = sessionStorage.getItem('selectedship_id');

    for (var i = 0; i < shipments.length; i++) { // rijen uitzetten

      var row$ = $('<tr class="shipmentrow" />'); 
        for (var colIndex = 0; colIndex < columns.length; colIndex++) {
          var cellValue = shipments[i][columns[colIndex]];
          var colName = columns[colIndex];
          
          // debug ! console.log(columns[colIndex]);
          
          if (cellValue == null){    //set empty cells to prevent errors
              cellValue = "";
          }
          row$.append($('<td class='+colName+'>').html(cellValue));

          $(tbody).append(row$);
        }
    }
    table.append(tbody);
    return table;
    
} // end of buildHtmlTable()

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
} // End addAllColumnHeaders();