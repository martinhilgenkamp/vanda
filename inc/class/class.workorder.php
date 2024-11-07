<?php
// Enable error reporting at the top of your script
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set("Europe/Amsterdam");

class WorkOrder {
    private $db;
    private $table_name = "vanda_work_orders";

    public $id;
    public $opdrachtnr;
    public $omschrijving;
    public $klant;
    public $opdrachtnr_klant;
    public $omschrijving_klant;
    public $created;
    public $modified;
    public $leverdatum;
    public $start;
    public $end;
    public $machine;
    public $verpakinstructie;
    public $opmerkingen;
    public $createdby;
    public $modifiedby;
    public $file_path;

    public $errors;

    public function __construct() {
      // Initialize the DB connection
      $this->db = new DB(); // Assuming DB is your database connection class
    
      if (!$this->db->link) {
        die("Database connection failed: " . $this->db->link->connect_error);
      }
    }

    public function createWorkOrder() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (opdrachtnr, omschrijving, klant, opdrachtnr_klant, omschrijving_klant, leverdatum, start, end, machine, verpakinstructie, opmerkingen, file_path, created, modified) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $this->created = date("Y-m-d H:i:s");
        $this->modified = date("Y-m-d H:i:s");
        
        // Prepare the statement
        if ($stmt = $this->db->link->prepare($query)) {

            // Bind the parameters
            if (!$stmt->bind_param(
                "ssssssssssssss",
                $this->opdrachtnr,
                $this->omschrijving,
                $this->klant,
                $this->opdrachtnr_klant,
                $this->omschrijving_klant,
                $this->leverdatum,
                $this->start,
                $this->end,
                $this->machine,
                $this->verpakinstructie,
                $this->opmerkingen,
                $this->file_path,
                $this->created,
                $this->modified
            )) {
                echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
                return false;
            }

            // Execute the statement
            if (!$stmt->execute()) {
                echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                return false;
            }

            // Close the statement
            $stmt->close();
            return true;
        } else {
            echo "Prepare failed: (" . $this->db->link->errno . ") " . $this->db->conn->error;
            return false;
        }
    }
    
    public function updateWorkOrder() {
        // Update the modified date
        $this->modified = date("Y-m-d H:i:s");
        
        print_r($this);


        // SQL update query
        $query = "UPDATE " . $this->table_name . " 
                  SET opdrachtnr = ?, omschrijving = ?, klant = ?, opdrachtnr_klant = ?, omschrijving_klant = ?, 
                      leverdatum = ?, start = ?, end = ?, machine = ?, verpakinstructie = ?, opmerkingen = ?, file_path = ?, 
                      file_path = ?, modified = ? 
                  WHERE id = ?";
    
        // Prepare the statement
        if ($stmt = $this->db->link->prepare($query)) {
            // Bind the parameters
            if (!$stmt->bind_param(
                "ssssssssssssssi",
                $this->opdrachtnr,
                $this->omschrijving,
                $this->klant,
                $this->opdrachtnr_klant,
                $this->omschrijving_klant,
                $this->leverdatum,
                $this->start,
                $this->end,
                $this->machine,
                $this->verpakinstructie,
                $this->opmerkingen,
                $this->file_path,
                $this->created,
                $this->modified,
                $this->$id
            )) {
                echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
                return false;
            }
    
            // Execute the statement
            if (!$stmt->execute()) {
                echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                return false;
            }
    
            // Close the statement
            $stmt->close();
            return true;
        } else {
            echo "Prepare failed: (" . $this->db->link->errno . ") " . $this->db->link->error;
            return false;
        }
    }

    public function getWorkOrderById($workOrderId) {
        // Prepare the query to fetch the work order with the specific ID
        $query = "SELECT id, opdrachtnr, omschrijving, klant, opdrachtnr_klant, omschrijving_klant, leverdatum, start, end, machine, verpakinstructie, opmerkingen, file_path, created, modified 
                  FROM " . $this->table_name . " 
                  WHERE id = ?";
    
        // Prepare the statement
        if ($stmt = $this->db->link->prepare($query)) {
            // Bind the parameter (assuming $workOrderId is an integer)
            $stmt->bind_param("i", $workOrderId);
    
            // Execute the query
            if ($stmt->execute()) {
                // Get the result
                $result = $stmt->get_result();
    
                // Check if a work order was found
                if ($result->num_rows > 0) {
                    // Fetch the data as an associative array
                    $workOrder = $result->fetch_assoc();
    
                    // Close the statement and return the work order data
                    $stmt->close();
                    return $workOrder;
                } else {
                    // No work order found with that ID
                    $stmt->close();
                    return null;
                }
            } else {
                // Handle execution error
                echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                $stmt->close();
                return null;
            }
        } else {
            // Handle preparation error
            echo "Prepare failed: (" . $this->db->link->errno . ") " . $this->db->link->error;
            return null;
        }
    }

    // Method to get and display all work orders
    public function getWorkorders() {
        $query = "SELECT id, opdrachtnr, omschrijving, klant, opdrachtnr_klant, omschrijving_klant, leverdatum, start, end, verpakinstructie, opmerkingen, file_path, created, modified FROM " . $this->table_name;

        if ($result = $this->db->link->query($query)) {
            if ($result->num_rows > 0) {
                // Start displaying the table
                echo "<table class='data-table results' cellpadding='0' cellspacing='0'>";
                echo "<tr>
                        <th class='ui-corner-tl'>ID</th>
                        <th>Opdrachtnr</th>
                        <th>Omschrijving</th>
                        <th>Klant</th>
                        <th>Opdrachtnr Klant</th>
                        <th>Omschrijving Klant</th>
                        <th>Leverdatum</th>
                        <th>Verpakinstructie</th>
                        <th>Opmerkingen</th>
                        <th>Gemaakt</th>
                        <th>Aangepast</th>
                        <th class='ui-corner-tr'>Inkoop Order</th>
                      </tr>";

                // Fetch and display each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row['id'] . "</td>
                            <td>" . $row['opdrachtnr'] . "</td>
                            <td>" . $row['omschrijving'] . "</td>
                            <td>" . $row['klant'] . "</td>
                            <td>" . $row['opdrachtnr_klant'] . "</td>
                            <td>" . $row['omschrijving_klant'] . "</td>
                            <td>" . $row['leverdatum'] . "</td>
                            <td>" . $row['verpakinstructie'] . "</td>
                            <td>" . $row['opmerkingen'] . "</td>
                            <td>" . $row['created'] . "</td>
                            <td>" . $row['modified'] . "</td>
                            <td>" . ($row['file_path'] ? "<a href='pages/workorder/".$row['file_path']."' target='blank'>FILE</a>" : "n.v.t." ) . "</td>
                          </tr>";
                }
                echo "<tfoot><tr><td class='ui-corner-bottom' colspan='12'>" . $result->num_rows ." resultaten weergegeven</td></tr></tfoot>";
                echo "</table>";
            } else {
                echo "No work orders found.";
            }
        } else {
            echo "Query failed: (" . $this->db->link->errno . ") " . $this->db->link->error;
        }
    }



    public function getWorkordersJson(){
        $query = "SELECT id, opdrachtnr as title, start, end, machine as resourceId FROM " . $this->table_name;
        $data = []; // Initialize $data as an empty array
        if ($result = $this->db->link->query($query)) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                return json_encode($data);
            }
        }
    }
}
?>