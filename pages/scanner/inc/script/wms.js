//Initial init
inventory_in = true;
cleared_loc = true;
cleared_qua = true;
cleared_rea = true;

//Functions loading after DOM has finished initilizing
  document.onreadystatechange = function () {
    if (document.readyState == "complete") {
        //Initial init
        checkin = document.getElementById("checkin");
        checkout = document.getElementById("checkout");

        //IN-OUT switch
        checkin.addEventListener("click", function() {enableButton(0)});
        checkout.addEventListener("click", function() {enableButton(1)});
        
        // ===== INVENTORY IN HANDLING =====
        $('#inventory_in').submit(function(e) {
            e.preventDefault();

            //Clear notifications
            notifyUser(false);

            //Retrieve values from from the form
            const barcode = $("#barcode_in").val()?.trim() || "";
            const locationVal = $("#location_in").val()?.trim() || "";
            const relationVal = $("#relation_in").val()?.trim() || "";
            const qualityVal = $("#quality_in").val()?.trim() || "";
            const lengthVal = $("#length_in").val()?.trim() || "";
            const widthVal = $("#width_in").val()?.trim() || "";
            
            if (!/^[A-Z0-9]{15}$/.test(barcode)) {
                notifyUser(false,'Barcode is onjuist');
                //return;
            }
        
            // AJAX request to submit data
            $.ajax({
                type: 'POST',
                url: '../inventory/process.php',
                data: {
                    action: "save",
                    id: "",
                    barcode: barcode,
                    relation: relationVal,
                    quality: qualityVal,
                    lengte: lengthVal,
                    breedte: widthVal,
                    location: locationVal,
                    date: nowSqlTimestamp(),
                    processed: "0"
                },
                success: function(response) {
                    if(response)
                    notifyUser(true, "Scannen succesvol")
                    // Create table, then add rows as elements and attach click in one line
                    const $results = $('#resultsIn');
                    const $tbody = $results.find('tbody');
                    $('<tr>')
                        .append(`
                        <td>${barcode}</td>
                        <td>${relationVal}</td>
                        <td>${locationVal}</td>
                        <td>${qualityVal}</td>                   
                        <td>${lengthVal}</td>
                        <td>${widthVal}</td>
                        `).prependTo($tbody)

                        if($tbody.find("tr").length > 5) {
                            $tbody.find("tr").last().remove();
                        }
                    
            },
                error: function(xhr, status, error) {
                    notifyUser(false,'Error: ' + error)

                }
            });

            $('#inventory_in')[0].reset();

            $("#location_in").val(locationVal);
            $("#quality_in").val(qualityVal);
            $("#relation_in").val(relationVal);
            cleared_loc = false;
            cleared_qua = false;
            cleared_rea = false;
        })

        //Reset the entire form
        $('#inventory_in').on('reset', function (e) {
            clearIn();
        });

        // ===== INVENTORY OUT HANDLING =====
        $('#inventory_out').submit(function(e) {
            e.preventDefault();

            //Clear result table and notifications
            $('#resultsOut').empty();
            notifyUser(false);

            //Retrieve values from from the form
            const barcode = $("#barcode_out").val()?.trim() || "";
            const locationVal = $("#location_out").val()?.trim() || "";

            // AJAX request to submit data
            $.ajax({
                type: 'POST',
                url: '../inventory/process.php',
                data: {
                    action: "search",
                    barcode: barcode,
                    location: locationVal,
                    processed: 0,
                },
                success: function(response) {
                    //No results
                    if(response.message === "Geen resultaat.") {
                        notifyUser(true, response.message);
                    }
                    //Single response immidate checkout
                    else if(response.rows.length === 1) {
                        let singleOutput = response.rows[0];
                        checkOut(singleOutput.id, singleOutput.barcode, singleOutput.relation, singleOutput.location, singleOutput.date, singleOutput.quality, singleOutput.lengte, singleOutput.breedte);
                    } 
                    //Show multiple collisons
                    else {
                    // Create table, then add rows as elements and attach click in one line
                        const $results = $('#resultsOut').append(`
                        <table id="shipmenttable">
                            <thead>
                            <tr>
                                <th>Barcode</th><th>Relatie</th><th>Locatie</th><th>Kwaliteit</th>
                                <th>Lengte</th><th>Breedte</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        `);

                        const $tbody = $results.find('tbody');

                        response.rows.forEach(r => {
                        // Build <tr>, attach click with the known values, then append
                        $('<tr>')
                            .attr('id', r.id)
                            .append(`
                            <td>${r.barcode}</td>
                            <td>${r.relation}</td>
                            <td>${r.location}</td>
                            <td>${r.quality}</td>                   
                            <td>${r.lengte}</td>
                            <td>${r.breedte}</td>
                            `)
                            .on('click', () => checkOut(r.id, r.barcode, r.relation, r.location, r.date, r.quality, r.lengte, r.breedte)) // one-liner
                            .appendTo($tbody);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    notifyUser(false,'Error: ' + error)

                }
            });
            //Clear input after submit
            $('#inventory_out')[0].reset();
        })

        //Reset the entire form
        $('#inventory_out').on('reset', function (e) {
            clearOut();
        });

        // ===== SHARED functions =====
        
        //Add event listeners to handle switch to next fields, does it on: 'enter' and with a timeout.
        $(document)
        .on('input', '#barcode_in, #location_in, #relation_in, #quality_in, #length_in, #barcode_out, #location_out' , function () {
            const val = this.value.trim(); if (!val) return;

            //Clear input if value was previously set
            if(this.id === 'location_in' && !cleared_loc) {
                let index = this.value.length - 1;
                this.value = this.value.substring(index);
                cleared_loc = true;
            }

            if(this.id === 'relation_in' && !cleared_qua) {
                let index = this.value.length - 1;
                this.value = this.value.substring(index);
                cleared_qua = true;
            }

            if(this.id === 'quality_in' && !cleared_rea) {
                let index = this.value.length - 1;
                this.value = this.value.substring(index);
                cleared_rea = true;
            }
        })
        .on('keydown', '#barcode_in, #location_in, #relation_in, #quality_in, #length_in, #barcode_out, #location_out', function (e) {
            //Detect enter key, move to next
            if (e.key === 'Enter') {
            e.preventDefault();
            NEXT[this.id]?.();
            }
        });
        
        $('#barcode_in').focus();
    }
 }

 //Function to change the active tab
 //IN and OUT forms are different elements with their own ID so values they cannot be mixed
function enableButton(button_id) {
    if(button_id === 0) {
        inventory_in = true;
        clearOut();
        $('#inventory_out')[0].reset();
        checkin.classList.add("button_selected_green");
        checkout.classList.remove("button_selected_red");
        $('#inventory_out').hide();
        $('#inventory_in').show();
        $('#resultsOut').hide();
        $('#resultsIn').show();
        $('#barcode_in').focus();
    } else {
        inventory_in = false;
        clearIn();
        $('#inventory_in')[0].reset();
        checkin.classList.remove("button_selected_green");
        checkout.classList.add("button_selected_red");
        $('#inventory_out').show();
        $('#inventory_in').hide();
        $('#resultsOut').show();
        $('#resultsIn').hide();
        $('#barcode_out').focus(); 
    }
}

//Next handling when field is filled.
const NEXT = {
    barcode_in:  () => switchBarLocIn(),
    location_in:  () => switchLocReaIn(),
    relation_in: () => switchReaQuaIn(),
    barcode_out: () => switchBarLocOut(),
    location_out: () => switchLocSubOut(),
    quality_in: () => switchQuaLenIn(),
    length_in: () => switchLenWitIn(),
};

// ===== Button switch functions =====
//Switches input fields of the forms on the page
function switchBarLocIn () {
    $('#location_in').focus();
}

function switchBarLocOut () {
    $('#location_out').focus();
}

function switchLocSubOut() {
    $('#inventory_out').trigger('submit');
}

function switchLocReaIn () {
    $('#relation_in').focus();
}

function switchReaQuaIn () {
    $('#quality_in').focus();
}

function switchQuaLenIn () {
    $('#length_in').focus();
}

function switchLenWitIn () {
    $('#width_in').focus();
}

function nowSqlTimestamp() {
    const d = new Date();
    const pad = (n) => String(n).padStart(2, "0");
    return (
        d.getFullYear() + "-" +
        pad(d.getMonth() + 1) + "-" +
        pad(d.getDate()) + " " +
        pad(d.getHours()) + ":" +
        pad(d.getMinutes()) + ":" +
        pad(d.getSeconds())
    );
}

function clearIn() {
    $('#barcode_in').focus();
}

function clearOut() {
    $('#results').empty();
    $('#barcode_out').focus();
}

function writeResult(result){
    $('#results').html(result);
}

//Function to checkout items
function checkOut(id, barcode, relation, location, date, quality, lengte, breedte) {
    let permission = confirm(barcode + " uitscannen?");
    if(permission){
        $.ajax({
            type: 'POST',
            url: '../inventory/process.php',
            data: {
                action: "save",
                id: id,
                barcode: barcode,
                relation: relation,
                location: location,
                quality: quality,
                processed: 1,
                date: date,
                lengte: lengte,
                breedte: breedte,
            },
            success: () => {
                $('#' + id).remove();
                notifyUser(true, barcode + " uitgescanned");
            },
            error: function(xhr, status, error) {
            notifyUser(false,'Error: ' + error)
            }
        });
    }
}

// ===== notify function =====
function notifyUser(success,message){
    // Set style for the message
    if(success){
        $('#result').removeClass('error').addClass('notice');
    } else if (message) {
        $('#result').removeClass('notice').addClass('error');
    } else {
        $('#result').removeClass('notice').removeClass('error');
        $('#result').empty();
    }

    // Return the message
    $('#result').text(message);
}