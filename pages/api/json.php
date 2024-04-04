
<?php
// Retrieve the raw POST data
$jsonData = file_get_contents('php://input');
// Decode the JSON data into a PHP associative array
$data = json_decode($jsonData, true);
// Check if decoding was successful
if ($data !== null) {
   // Access the data and perform operations
   $machine = $data['machine'];
   $action = $data['action'];
   // Perform further processing or respond to the request

   echo "Machine " . $machine . " actie " . $action;
} else {
   // JSON decoding failed
   http_response_code(400); // Bad Request
   echo "Invalid JSON data";
}
?>