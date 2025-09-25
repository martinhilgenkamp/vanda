  document.onreadystatechange = function () {
    if (document.readyState == "complete") {
        checkin = document.getElementById("checkin");
        checkout = document.getElementById("checkout");

        checkin.addEventListener("click", function() {enableButton(0)});
        checkout.addEventListener("click", function() {enableButton(1)});
    }
 }

function enableButton(button_id) {
    if(button_id === 0) {
        checkin.classList.add("button_selected");
        checkout.classList.remove("button_selected");
    } else {
        checkin.classList.remove("button_selected");
        checkout.classList.add("button_selected");
    }
}


$('#inventort_in').submit(function(e) {
    e.preventDefault();
    console.log("hallo");
})