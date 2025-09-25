//Initial init
inventory_in = true;

let typingTimer, dismissTimer;
const TYPING_IDLE_MS   = 600;
const DISMISS_AFTER_MS = 600;

//Functions loading after DOM has finished initilizing
  document.onreadystatechange = function () {
    if (document.readyState == "complete") {
        checkin = document.getElementById("checkin");
        checkout = document.getElementById("checkout");

        checkin.addEventListener("click", function() {enableButton(0)});
        checkout.addEventListener("click", function() {enableButton(1)});
        
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
            $('#barcode').removeAttr('disabled');
            $('#location').prop('disabled', true)
            $('#quality').prop('disabled', true)
            $('#barcode').focus();
        });

        //Handle move next field
        $(document)
        .on('input', '#barcode, #location', function () {
            clearTimeout(typingTimer); clearTimeout(dismissTimer);
            const val = this.value.trim(); if (!val) return;

            typingTimer = setTimeout(() => {
            dismissTimer = setTimeout(() => { NEXT[this.id]?.(); }, DISMISS_AFTER_MS);
            }, TYPING_IDLE_MS);
        })
        .on('keydown', '#barcode, #location', function (e) {
            if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(typingTimer); clearTimeout(dismissTimer);
            NEXT[this.id]?.();
            }
        });
        
        $('#barcode').focus();
    }
 }

 //Function to change the active tab
function enableButton(button_id) {
    if(button_id === 0) {
        inventory_in = true;
        checkin.classList.add("button_selected");
        checkout.classList.remove("button_selected");
    } else {
        inventory_in = false;
        checkin.classList.remove("button_selected");
        checkout.classList.add("button_selected");
    }
}

const NEXT = {
  barcode:  () => switchBarLoc(),
  location:  () => switchLocQua(),
};

//Switch barcode and location
function switchBarLoc () {
    $('#location').removeAttr('disabled');
    $('#location').focus();
    $('#barcode').prop('disabled', true)
}

//Switch location to quality
function switchLocQua () {
    $('#quality').removeAttr('disabled');
    $('#quality').focus();
    $('#location').prop('disabled', true)
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