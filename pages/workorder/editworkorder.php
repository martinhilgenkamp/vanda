<?php
//Load requirements
date_default_timezone_set("Europe/Amsterdam");
require_once('inc/class/class.workorder.php');

// Initiate workorder object
$workorder = new Workorder();

// Define default edit mode
$isEditMode = false;
$existingWorkOrder = null;

//Get Current Date
$currentDate = date("Y-m-d");

// Check if there is an ID provided (for edit mode)
// TODO: check if post settings are correct

if (isset($_GET['id'])) {
    $isEditMode = true;
    $workOrderId = $_GET['id'];
    $existingWorkOrder = $workorder->getWorkOrderById($workOrderId); // Fetch existing work order details
    print_r($existingWorkOrder);

} elseif (isset($_POST)){
    $isEditMode = false;
    $existingWorkOrder = new $workorder;

$existingWorkOrder->start = isset($_POST['start']) && $_POST['start'] !== ''
    ? date('Y-m-d\TH:i', strtotime(htmlspecialchars($_POST['start'] ?? '', ENT_QUOTES, 'UTF-8')))
    : null;

$existingWorkOrder->end = isset($_POST['stop']) && $_POST['stop'] !== ''
    ? date('Y-m-d\TH:i', strtotime(htmlspecialchars($_POST['stop'] ?? '', ENT_QUOTES, 'UTF-8')))
    : null;

$existingWorkOrder->leverdatum = isset($_POST['leverdatum']) && $_POST['leverdatum'] !== ''
    ? date('Y-m-d', strtotime(htmlspecialchars($_POST['leverdatum'] ?? '', ENT_QUOTES, 'UTF-8')))
    : null;

$existingWorkOrder->omschrijving = isset($_POST['eventtitle']) && $_POST['eventtitle'] !== ''
    ? htmlspecialchars($_POST['eventtitle'] ?? '', ENT_QUOTES, 'UTF-8')
    : null;

$existingWorkOrder->resource1 = isset($_POST['resource1']) && $_POST['resource1'] !== ''
    ? htmlspecialchars($_POST['resource1'] ?? '', ENT_QUOTES, 'UTF-8')
    : null;

    
}
?>

<title><?php echo $isEditMode ? 'Bewerk Werkbon' : 'Opdracht aanmaken'; ?></title>
<style>
.autocomplete-items {
    display: none;
    border: 1px solid #ddd;
    border-radius: 4px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    background-color: white;
    width: calc(100% - 2px); /* Matches the input field width */
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.autocomplete-item {
    padding: 10px;
    cursor: pointer;
}

.autocomplete-item:hover {
    background-color: #f0f0f0;
}
    </style>
<script>
   $(document).ready(function () {
    // Autocomplete for 'klant' field
    $('#klant').on('input', function () {
        const searchTerm = $(this).val();

        // Clear dropdown if input is empty
        if (searchTerm === '') {
            $('#autocompleteDropdown').empty().hide();
            return;
        }

        $('#autocompleteDropdown').empty().hide();

        // Make an AJAX request to fetch data
        $.get('pages/workorder/ajax.searchworkorder.php', { term: searchTerm }, function (data) {
            // Clear the dropdown
            $('#autocompleteDropdown').empty().hide();

            // Check if data is empty
            if (!data || data.length === 0) {
                $('#autocompleteDropdown').hide(); // Hide the dropdown if no data
                return;
            }

            // Populate the dropdown with results and show it
            data.forEach(item => {
                $('#autocompleteDropdown').append(
                    `<div class="autocomplete-item" data-id="${item.id}">${item.text}</div>`
                );
            });
            $('#autocompleteDropdown').show();
        }).fail(function () {
            // Hide the dropdown in case of an error
            $('#autocompleteDropdown').empty().hide();
        });
    });

    // Handle dropdown item click
    $(document).on('click', '.autocomplete-item', function () {
        const selectedText = $(this).text();
        $('#klant').val(selectedText);
        $('#autocompleteDropdown').empty().hide(); // Clear and hide the dropdown
    });

    // Hide dropdown when clicking outside
    $(document).click(function (e) {
        if (!$(e.target).closest('#klant, #autocompleteDropdown').length) {
            $('#autocompleteDropdown').empty().hide();
        }
    });


    // Handle dropdown item click
    $(document).on('click', '.autocomplete-item', function () {
        const selectedText = $(this).text();
        $('#klant').val(selectedText);
        $('#autocompleteDropdown').empty(); // Clear the dropdown
    });

    // Hide dropdown when clicking outside
    $(document).click(function (e) {
        if (!$(e.target).closest('#klant, #autocompleteDropdown').length) {
            $('#autocompleteDropdown').empty();
        }
    });

    // Set minimum dates for 'start' and 'end' fields
    document.addEventListener("DOMContentLoaded", function () {
        // Get the current date and time
        let now = new Date();

        // Format the date and time as "YYYY-MM-DDTHH:MM" for datetime-local input
        let formattedDateTime = now.toISOString().slice(0, 16);

        // Set the min attribute of the input fields
        document.getElementById("start").min = formattedDateTime;
        document.getElementById("end").min = formattedDateTime;
    });
});

// Form validation function
function validateForm() {
    let opdrachtnr = document.getElementById("opdrachtnr").value;
    let klant = document.getElementById("klant").value;
    let leverdatum = document.getElementById("leverdatum").value;

    // Check if opdrachtnr is numeric
    if (isNaN(opdrachtnr)) {
        alert("Opdrachtnr moet numeriek zijn.");
        return false;
    }

    // Check if klant name is too short
    if (klant.length < 3) {
        alert("Klant naam moet minimaal 3 karakters bevatten.");
        return false;
    }

    // Check if the delivery date is not in the past
    let today = new Date().toISOString().split('T')[0];
    if (leverdatum < today) {
        alert("Leverdatum cannot be in the past.");
        return false;
    }

    return true; // If all validations pass
}
</script>

<h1><?php echo $isEditMode ? 'Bewerk Werkbon' : 'Opdracht aanmaken'; ?></h1>

<form action="pages/workorder/processorder.php" method="POST" onsubmit="return validateForm();" id="workorderform" enctype="multipart/form-data">

    <?php if (isset($workOrderId)): ?>
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($workOrderId ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>

    <label for="omschrijving">Omschrijving:</label><br>
    <textarea id="omschrijving" name="omschrijving" required minlength="10" maxlength="500"><?php echo $existingWorkOrder->omschrijving ? $existingWorkOrder->omschrijving : ''; ?></textarea><br><br>

    <label for="klant">Klant</label><br>
    <input type="text" id="klant" name="klant" required minlength="3" maxlength="100" 
           value="<?php echo $existingWorkOrder->klant ? htmlspecialchars($existingWorkOrder->klant ?? '', ENT_QUOTES, 'UTF-8') : ''; ?>">
    <div id="autocompleteDropdown" class="autocomplete-items"></div>
    <br><br>

    <label for="opdrachtnr_klant">Opdrachtnr Klant:</label><br>
    <input type="text" id="opdrachtnr_klant" name="opdrachtnr_klant" required 
           value="<?php echo $existingWorkOrder->opdrachtnr_klant ? htmlspecialchars($existingWorkOrder->opdrachtnr_klant ?? '', ENT_QUOTES, 'UTF-8') : ''; ?>">*<br><br>

    <label for="omschrijving_klant">Omschrijving Klant:</label><br>
    <textarea id="omschrijving_klant" name="omschrijving_klant" required><?php echo $isEditMode ? htmlspecialchars($existingWorkOrder->omschrijving_klant ?? '', ENT_QUOTES, 'UTF-8') : ''; ?></textarea><br><br>

    <label for="leverdatum">Leverdatum</label><br>
    <input type="date" id="leverdatum" name="leverdatum" required 
           value="<?php echo $existingWorkOrder->leverdatum ? htmlspecialchars($existingWorkOrder->leverdatum ?? '', ENT_QUOTES, 'UTF-8') : $currentDate; ?>">*<br><br>

    <label for="start">Start Tijd</label><br>
    <input type="datetime-local" id="start" name="start" 
           value="<?php echo $existingWorkOrder->start ? htmlspecialchars($existingWorkOrder->start ?? '', ENT_QUOTES, 'UTF-8') : $currentDate . ' 08:00:00'; ?>"><br><br>

    <label for="start">Eind Tijd</label><br>
    <input type="datetime-local" id="end" name="end" 
           value="<?php echo $existingWorkOrder->end ? htmlspecialchars($existingWorkOrder->end ?? '', ENT_QUOTES, 'UTF-8') : $currentDate . ' 17:00:00'; ?>"><br><br>

    <label for="resource1">Resource 1</label><br>
    <input type="text" id="resource1" name="resource1" 
           value="<?php echo $existingWorkOrder->resource1 ? htmlspecialchars($existingWorkOrder->resource1 ?? '', ENT_QUOTES, 'UTF-8') : ''; ?>"><br><br>

    <label for="resource2">Resource 2</label><br>
    <input type="text" id="resource2" name="resource2" 
           value="<?php echo $existingWorkOrder->resource2 ? htmlspecialchars($existingWorkOrder->resource2 ?? '', ENT_QUOTES, 'UTF-8') : ''; ?>"><br><br>

    <label for="verpakinstructie">Verpakinstructie:</label><br>
    <textarea id="verpakinstructie" name="verpakinstructie"><?php echo $existingWorkOrder->verpakinstructie ? htmlspecialchars($existingWorkOrder->verpakinstructie ?? '', ENT_QUOTES, 'UTF-8') : ''; ?></textarea><br><br>

    <label for="opmerkingen">Opmerkingen:</label><br>
    <textarea id="opmerkingen" name="opmerkingen"><?php echo $existingWorkOrder->opmerkingen ? htmlspecialchars($existingWorkOrder->opmerkingen ?? '', ENT_QUOTES, 'UTF-8') : ''; ?></textarea><br><br>

    <!-- File upload input -->
    <label for="file">Inkoop Order (PDF, JPG, PNG)</label><br>
    <input type="file" id="inkooporder" name="inkooporder" accept=".pdf, .jpg, .jpeg, .png"><br><br>

    <input type="submit" value="<?php echo $isEditMode ? 'Bewerk Werkbon' : 'Opdracht aanmaken'; ?>">
</form>