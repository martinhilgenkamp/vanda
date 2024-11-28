<?php

require_once('../../inc/class/class.workorder.php');
require_once('../../inc/class/class.user.php');



// Directory where files will be uploaded
$uploadDir = 'uploads/';

// Helper function to sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $workOrder = new WorkOrder();

    // Sanitize form inputs
    $workOrder->opdrachtnr = sanitize($_POST['opdrachtnr']);
    $workOrder->omschrijving = sanitize($_POST['omschrijving']);
    $workOrder->klant = sanitize($_POST['klant']);
    $workOrder->opdrachtnr_klant = sanitize($_POST['opdrachtnr_klant']);
    $workOrder->omschrijving_klant = sanitize($_POST['omschrijving_klant']);
    $workOrder->leverdatum = sanitize($_POST['leverdatum']);
    $workOrder->verpakinstructie = sanitize($_POST['verpakinstructie']);
    $workOrder->opmerkingen = sanitize($_POST['opmerkingen']);
    $workOrder->start = sanitize($_POST['start']);
    $workOrder->end = sanitize($_POST['end']);
    $workOrder->resource1 = sanitize($_POST['resource1']); 
    $workOrder->resource2 = sanitize($_POST['resource2']); 
    $workOrder->id = isset($_POST['id']) ? sanitize($_POST['id']) : null;

     // Initialize an error array
     $errors = [];
     $workOrder->errors= $errors;
 

     
     // Server-side validation
     if (!is_numeric($workOrder->opdrachtnr)) {
         $errors[] = "Opdrachtnr must be numeric.";
     }
 
     if (strlen($workOrder->klant) < 3) {
         $errors[] = "Klant name must be at least 3 characters long.";
     }
 
    // if (date('Y-m-d', strtotime($workOrder->leverdatum)) < date('Y-m-d')) {
    //     $errors[] = "Leverdatum cannot be in the past.";
    // }
    
     // Handle file upload
     if (!empty($_FILES['file']['name'])) {
         $fileName = basename($_FILES['file']['name']);
         $fileTmpName = $_FILES['file']['tmp_name'];
         $fileSize = $_FILES['file']['size'];
         $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
         $uploadFilePath = $uploadDir . $fileName;
 
         // Validation for file type and size
         if (in_array($fileType, ['pdf', 'jpg', 'jpeg', 'png']) && $fileSize <= 2000000) {
             if (move_uploaded_file($fileTmpName, $uploadFilePath)) {
                 $workOrder->file_path = $uploadFilePath; // Store the file path in the WorkOrder object
             } else {
                 echo "Error uploading file.";
             }
         } else {
             echo "Invalid file type or size. Only PDF, JPG, JPEG, and PNG files under 2MB are allowed.";
         }
     } else {
         $workOrder->file_path = null; // No file uploaded
     }
 
     // Check for errors before proceeding
     if (count($errors) > 0) {
         foreach ($errors as $error) {
             echo "<p style='color:red;'>$error</p>";
         }
     } else {
         // Try to insert into database
         if($workOrder->id != 0 ){
            if ($workOrder->updateWorkOrder()) {
                echo "Work order updated successfully.";
            } else {
                echo "Failed to update work order.";
            }
         } else {
            if ($workOrder->createWorkOrder()) {
                echo "<p style='color:green;'>Work order created successfully!</p>";
            } else {
                echo "<p style='color:red;'>Error creating work order.</p>";
            }
        }
     }
}
   

?>