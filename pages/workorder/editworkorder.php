<?php
date_default_timezone_set("Europe/Amsterdam");
require_once('inc/class/class.workorder.php');

$workorder = new Workorder();

?>

    <title>Bewerk Werkbon</title>
    <script>
        function validateForm() {
            let opdrachtnr = document.getElementById("opdrachtnr").value;
            let klant = document.getElementById("klant").value;
            let leverdatum = document.getElementById("leverdatum").value;

            // Check if opdrachtnr is numeric
            if (isNaN(opdrachtnr)) {
                alert("Opdrachtnr must be numeric.");
                return false;
            }

            // Check if klant name is too short
            if (klant.length < 3) {
                alert("Klant name must be at least 3 characters long.");
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
   
    <h1>Opdracht aanmaken</h1>
    <form action="pages/workorder/processorder.php" method="POST" onsubmit="return validateForm();" id="workorderform" enctype="multipart/form-data">
        <label for="opdrachtnr">Opdrachtnr Vanda</label><br>
        <input type="text" id="opdrachtnr" name="opdrachtnr" required maxlength="10" pattern="\d+">*<br><br>

        <label for="omschrijving">Omschrijving:</label><br>
        <textarea id="omschrijving" name="omschrijving" required minlength="10" maxlength="500"></textarea><br><br>

        <label for="klant">Klant</label><br>
        <input type="text" id="klant" name="klant" required minlength="3" maxlength="100">*<br><br>

        <label for="opdrachtnr_klant">Opdrachtnr Klant:</label><br>
        <input type="text" id="opdrachtnr_klant" name="opdrachtnr_klant" required>*<br><br>

        <label for="omschrijving_klant">Omschrijving Klant:</label><br>
        <textarea id="omschrijving_klant" name="omschrijving_klant" required></textarea><br><br>

        <label for="leverdatum">Leverdatum</label><br>
        <input type="date" id="leverdatum" name="leverdatum" required>*<br><br>

        <label for="verpakinstructie">Verpakinstructie:</label><br>
        <textarea id="verpakinstructie" name="verpakinstructie"></textarea><br><br>

        <label for="opmerkingen">Opmerkingen:</label><br>
        <textarea id="opmerkingen" name="opmerkingen"></textarea><br><br>

        <!-- File upload input -->
        <label for="file">Inkoop Order (PDF, JPG, PNG)</label><br>
        <input type="file" id="inkooporder" name="inkooporder" accept=".pdf, .jpg, .jpeg, .png"><br><br>

        <input type="submit" value="Create Work Order">
    </form>
    <?php print_R($um); ?>
</body>
</html>