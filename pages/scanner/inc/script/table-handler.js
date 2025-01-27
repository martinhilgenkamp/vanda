$(document).ready(function() {
    $('#openshipments').on('click', 'tr', function() {
        var klant = $(this).find('td.klant').text();
        var ship_id = $(this).find('td.id').text();

        // Fill the input fields with the selected values
        $('#klant').val(klant);
        $('#ship_id').val(ship_id);

        // Store the values in session storage
        sessionStorage.setItem('selectedKlant', klant);
        sessionStorage.setItem('selectedship_id', ship_id);

        // prevent OSK popup
        $('#barcode').prop("readonly", true);  
        $('#barcode').focus();
        setTimeout(function() {
            $('#barcode').prop("readonly", false);
        }, 100);
        
    }); // End click listener

    // Check for and retrieve stored values on page load
    var storedKlant = sessionStorage.getItem('selectedKlant');
    var storedship_id = sessionStorage.getItem('selectedship_id');

    if (storedKlant && storedship_id) {
        $('#klant').val(storedKlant);
        $('#ship_id').val(storedship_id);
    }
// end doc ready
});

// Get active shipments and put this in the html table
function loadShipments(selector){	
    // Setup the request for shipment
    $.ajax({
        cache: false,
        url: "process.php", 
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