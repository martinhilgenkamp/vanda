<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Work Order</title>
    <style>
        /* Dropdown styling */
        .autocomplete-items {
            position: absolute;
            border: 1px solid #ddd;
            border-radius: 4px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            background-color: white;
        }
        .autocomplete-item {
            padding: 10px;
            cursor: pointer;
        }
        .autocomplete-item:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <h1>Edit Work Order</h1>
    <form method="post" action="updateworkorder.php">
        <label for="omschrijving">Work Order:</label>
        
        <input type="text" id="searchBox" name="omschrijving" placeholder="Search Work Order..." autocomplete="off">

        <div id="autocompleteDropdown" class="autocomplete-items"></div>

        <!-- Add other fields for editing work orders -->

        <button type="submit">Save Changes</button>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function () {
            $('#searchBox').on('input', function () {
                const searchTerm = $(this).val();

                // Clear dropdown if input is empty
                if (searchTerm === '') {
                    $('#autocompleteDropdown').empty();
                    return;
                }

                // Make an AJAX request to fetch data
                $.get('pages/workorder/ajax.searchworkorder.php', { term: searchTerm }, function (data) {
                    // Clear the dropdown
                    $('#autocompleteDropdown').empty();

                    // Populate the dropdown with results
                    data.forEach(item => {
                        $('#autocompleteDropdown').append(
                            `<div class="autocomplete-item" data-id="${item.id}">${item.text}</div>`
                        );
                    });
                });
            });

            // Handle dropdown item click
            $(document).on('click', '.autocomplete-item', function () {
                const selectedText = $(this).text();
                $('#searchBox').val(selectedText);
                $('#autocompleteDropdown').empty(); // Clear the dropdown
            });

            // Hide dropdown when clicking outside
            $(document).click(function (e) {
                if (!$(e.target).closest('#searchBox, #autocompleteDropdown').length) {
                    $('#autocompleteDropdown').empty();
                }
            });
        });
    
    </script>


</body>
</html>