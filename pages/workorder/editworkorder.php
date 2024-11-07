<?php
//Load requirements
date_default_timezone_set("Europe/Amsterdam");
require_once('inc/class/class.workorder.php');

// Initiate workorder object
$workorder = new Workorder();

// Define default edit mode
$isEditMode = false;
$existingWorkOrder = null;

// Check if there is an ID provided (for edit mode)
if (isset($_GET['id'])) {
    $isEditMode = true;
    $workOrderId = $_GET['id'];
    $existingWorkOrder = $workorder->getWorkOrderById($workOrderId); // Fetch existing work order details
    print_r($existingWorkOrder);
}

?>

<title><?php echo $isEditMode ? 'Bewerk Werkbon' : 'Opdracht aanmaken'; ?></title>
<script>
    // Get Current Date and set minimum
    document.addEventListener("DOMContentLoaded", function() {
    // Get the current date and time
    let now = new Date();

    // Format the date and time as "YYYY-MM-DDTHH:MM" for datetime-local input
    let formattedDateTime = now.toISOString().slice(0, 16);

    // Set the min attribute of the input field
    document.getElementById("start").min = formattedDateTime;
    document.getElementById("end").min = formattedDateTime;


    // Do Form Validation
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
            alert("Klant naam moet minimaal 3 karakters bevatten");
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

    <?php
    // Add id if in edit mode.
    if ($isEditMode): 
    ?>
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($workOrderId); ?>">
    <?php endif; ?>


    <label for="opdrachtnr">Opdrachtnr Vanda</label><br>
    <input type="text" id="opdrachtnr" name="opdrachtnr" required maxlength="10" pattern="\d+" value="<?php echo $isEditMode ? htmlspecialchars($existingWorkOrder['opdrachtnr']) : ''; ?>">*<br><br>

    <label for="omschrijving">Omschrijving:</label><br>
    <textarea id="omschrijving" name="omschrijving" required minlength="10" maxlength="500"><?php echo $isEditMode ? htmlspecialchars($existingWorkOrder['omschrijving']) : ''; ?></textarea><br><br>

    <label for="klant">Klant</label><br>
    <input type="text" id="klant" name="klant" required minlength="3" maxlength="100" value="<?php echo $isEditMode ? htmlspecialchars($existingWorkOrder['klant']) : ''; ?>">*<br><br>

    <label for="opdrachtnr_klant">Opdrachtnr Klant:</label><br>
    <input type="text" id="opdrachtnr_klant" name="opdrachtnr_klant" required value="<?php echo $isEditMode ? htmlspecialchars($existingWorkOrder['opdrachtnr_klant']) : ''; ?>">*<br><br>

    <label for="omschrijving_klant">Omschrijving Klant:</label><br>
    <textarea id="omschrijving_klant" name="omschrijving_klant" required><?php echo $isEditMode ? htmlspecialchars($existingWorkOrder['omschrijving_klant']) : ''; ?></textarea><br><br>

    <label for="leverdatum">Leverdatum</label><br>
    <input type="date" id="leverdatum" name="leverdatum" required value="<?php echo $isEditMode ? htmlspecialchars($existingWorkOrder['leverdatum']) : ''; ?>">*<br><br>

    <label for="start">Start Tijd</label><br>
    <input type="datetime-local" id="start" name="start" value="<?php echo $isEditMode ? htmlspecialchars($existingWorkOrder['start']) : ''; ?>"><br><br>

    <label for="start">Eind Tijd</label><br>
    <input type="datetime-local" id="end" name="end" value="<?php echo $isEditMode ? htmlspecialchars($existingWorkOrder['end']) : ''; ?>"><br><br>

    <label for="machine">Machine Nummer</label><br>
    <input type="text" id="machine" name="machine" value="<?php echo $isEditMode ? htmlspecialchars($existingWorkOrder['machine']) : ''; ?>"><br><br>

    <label for="verpakinstructie">Verpakinstructie:</label><br>
    <textarea id="verpakinstructie" name="verpakinstructie"><?php echo $isEditMode ? htmlspecialchars($existingWorkOrder['verpakinstructie']) : ''; ?></textarea><br><br>

    <label for="opmerkingen">Opmerkingen:</label><br>
    <textarea id="opmerkingen" name="opmerkingen"><?php echo $isEditMode ? htmlspecialchars($existingWorkOrder['opmerkingen']) : ''; ?></textarea><br><br>

    <!-- File upload input -->
    <label for="file">Inkoop Order (PDF, JPG, PNG)</label><br>
    <input type="file" id="inkooporder" name="inkooporder" accept=".pdf, .jpg, .jpeg, .png"><br><br>

    <input type="submit" value="Create Work Order">
</form>