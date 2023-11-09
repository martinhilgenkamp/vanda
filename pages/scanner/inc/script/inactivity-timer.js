// Initialize a timer variable
let inactivityTimer;

// Function to reset the timer
function resetTimer() {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(resetPage, 5000);
}

// Function to reload the page when the timer expires
function resetPage() {
    const openshipmentsElement = document.getElementById('openshipments');
    if (openshipmentsElement) {
        loadShipments(openshipmentsElement);
    }
    console.log('Page Reloaded');
    resetTimer();
}

// Add event listeners to reset the timer when user interacts with the page
document.addEventListener("mousemove", resetTimer);
document.addEventListener("keydown", resetTimer);

