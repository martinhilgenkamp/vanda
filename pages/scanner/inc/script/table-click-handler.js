$(document).ready(function() {
    $('#data-table tbody').on('click', 'tr', function() {
        var cells = $(this).find('td');
        if (cells.length !== 2) return;

        var klant = cells.eq(0).text();
        var zending = cells.eq(1).text();

        // Fill the input fields with the selected values
        $('#klant').val(klant);
        $('#zending').val(zending);

        // Store the values in session storage
        sessionStorage.setItem('selectedKlant', klant);
        sessionStorage.setItem('selectedZending', zending);
    });

    // Check for and retrieve stored values on page load
    var storedKlant = sessionStorage.getItem('selectedKlant');
    var storedZending = sessionStorage.getItem('selectedZending');

    if (storedKlant && storedZending) {
        $('#klant').val(storedKlant);
        $('#zending').val(storedZending);
    }
});