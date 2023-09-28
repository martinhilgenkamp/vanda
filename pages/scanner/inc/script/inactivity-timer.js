// Initialize a timer variable
let inactivityTimer;

// Function to reset the timer
function resetTimer() {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(reloadPage, inactivityTimeout);
}

// Function to reload the page when the timer expires
function reloadPage() {
    // You can customize this part with your desired reload action
    location.reload();
}

// Add event listeners to reset the timer when user interacts with the page
document.addEventListener("mousemove", resetTimer);
document.addEventListener("keydown", resetTimer);

