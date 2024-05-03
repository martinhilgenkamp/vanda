$(document).ready(function() {
    $(".clickable-row td:not(:last-child) td:not(:first-child)").click(function() {
        console.log('hallo wereld');
        window.location.href = $(this).closest('tr').data("href");
    });
});
