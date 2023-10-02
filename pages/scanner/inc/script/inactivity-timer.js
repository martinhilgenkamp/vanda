// Initialize a timer variable
let inactivityTimer;

// Function to reset the timer
function resetTimer() {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(resetPage, inactivityTimeout);
}

// Function to reload the page when the timer expires
function resetPage() {
    // You can customize this part with your desired reload action
    loadShipments($('#openshipments'));
    selectBarcode();
    
    // Check for and retrieve stored values on page load
    //sessionStorage.removeItem('selectedKlant');
    //sessionStorage.removeItem('selectedZending');
    //$('#klant').val('');
    //$('#zending').val('');
    

}

// Add event listeners to reset the timer when user interacts with the page
document.addEventListener("mousemove", resetTimer);
document.addEventListener("keydown", resetTimer);

