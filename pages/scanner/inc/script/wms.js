//Initial init
inventory_in = true;

let typingTimer, dismissTimer;
const TYPING_IDLE_MS   = 600;
const DISMISS_AFTER_MS = 600;

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
            
            if (!/^[A-Z0-9]{15}$/.test(barcode)) {
                notifyUser(false,'Barcode is onjuist');
                console.log("nope");
                return;
            }
        })

        //Reset the entire form
        $('#inventory_in').on('reset', function (e) {
            $('#barcode_in').removeAttr('disabled');
            $('#location_in').prop('disabled', true)
            $('#quality_in').prop('disabled', true)
            $('#barcode_in').focus();
        });
      

        // ===== INVENTORY OUT HANDLING =====
        $('#inventory_out').submit(function(e) {
            e.preventDefault();
            
        })

        //Reset the entire form
        $('#inventory_out').on('reset', function (e) {
            $('#barcode_out').removeAttr('disabled');
            $('#location_out').prop('disabled', true)
            $('#barcode_out').focus();
        });

        // ===== SHARED functions =====
        
        //Handle move next field
        $(document)
        .on('input', '#barcode_in, #location_in, #barcode_out' , function () {
            clearTimeout(typingTimer); clearTimeout(dismissTimer);
            const val = this.value.trim(); if (!val) return;

            typingTimer = setTimeout(() => {
            dismissTimer = setTimeout(() => { NEXT[this.id]?.(); }, DISMISS_AFTER_MS);
            }, TYPING_IDLE_MS);
        })
        .on('keydown', '#barcode_in, #location_in, #barcode_out', function (e) {
            if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(typingTimer); clearTimeout(dismissTimer);
            NEXT[this.id]?.();
            }
        });
        
        $('#barcode_in').focus();
    }
 }

 //Function to change the active tab
function enableButton(button_id) {
    if(button_id === 0) {
        inventory_in = true;
        checkin.classList.add("button_selected_green");
        checkout.classList.remove("button_selected_red");
        $('#inventory_out').hide();
        $('#inventory_in').show();
        $('#barcode_in').focus();
    } else {
        inventory_in = false;
        checkin.classList.remove("button_selected_green");
        checkout.classList.add("button_selected_red");
        $('#inventory_out').show();
        $('#inventory_in').hide();
        $('#barcode_out').focus();
    }
}

const NEXT = {
    barcode_in:  () => switchBarLocIn(),
    location_in:  () => switchLocQuaIn(),
    barcode_out: () => switchBarLocOut(),
};

//Switch barcode and location
function switchBarLocIn () {
    $('#location_in').removeAttr('disabled');
    $('#location_in').focus();
    $('#barcode_in').prop('disabled', true)
}

//Switch location to quality
function switchLocQuaIn () {
    $('#quality_in').removeAttr('disabled');
    $('#quality_in').focus();
    $('#location_in').prop('disabled', true)
}

function switchBarLocOut () {
    $('#location_out').removeAttr('disabled');
    $('#location_out').focus();
    $('#barcode_out').prop('disabled', true)
}

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