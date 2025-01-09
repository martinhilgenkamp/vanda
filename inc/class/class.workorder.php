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
    public $omschrijving;
    public $klant;
    public $opdrachtnr_klant;
    public $omschrijving_klant;
    public $created;
    public $modified;
    public $leverdatum;
    public $start;
    public $end;
    public $resource1;
    public $resource2;
    public $verpakinstructie;
    public $opmerkingen;
    public $createdby;
    public $modifiedby;
    public $file_path;
    public $status;

    public $errors;

    public function __construct() {
      // Initialize the DB connection
      $this->db = new DB();
    
      if (!$this->db->link) {
        die("Database connection failed: " . $this->db->link->connect_error);
      }
    }

    public function createWorkOrder() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (omschrijving, klant, opdrachtnr_klant, omschrijving_klant, leverdatum, start, end, resource1, resource2, verpakinstructie, opmerkingen, file_path, status, created, modified) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
        $this->created = date("Y-m-d H:i:s");
        $this->modified = date("Y-m-d H:i:s");
    
        if ($stmt = $this->db->link->prepare($query)) {
            if (!$stmt->bind_param(
                "ssssssssiisssss",
                $this->omschrijving,
                $this->klant,
                $this->opdrachtnr_klant,
                $this->omschrijving_klant,
                $this->leverdatum,
                $this->start,
                $this->end,
                $this->resource1,
                $this->resource2,
                $this->verpakinstructie,
                $this->opmerkingen,
                $this->file_path,
                $this->status,
                $this->created,
                $this->modified
            )) {
                echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
                return false;
            }
    
            if (!$stmt->execute()) {
                echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                return false;
            }
    
            $stmt->close();
            return true;
        } else {
            echo "Prepare failed: (" . $this->db->link->errno . ") " . $this->db->link->error;
            return false;
        }
    }
    
    public function updateWorkOrder() {
        $this->modified = date("Y-m-d H:i:s");
    
        $query = "UPDATE " . $this->table_name . " 
                  SET omschrijving = ?, klant = ?, opdrachtnr_klant = ?, omschrijving_klant = ?, 
                      leverdatum = ?, start = ?, end = ?, resource1 = ?, resource2 = ?, verpakinstructie = ?, opmerkingen = ?, 
                      file_path = ?, status = ?, modified = ?
                  WHERE id = ?";
    
        if ($stmt = $this->db->link->prepare($query)) {
            if (!$stmt->bind_param(
                "ssssssssiissssi",
                $this->omschrijving,
                $this->klant,
                $this->opdrachtnr_klant,
                $this->omschrijving_klant,
                $this->leverdatum,
                $this->start,
                $this->end,
                $this->resource1,
                $this->resource2,
                $this->verpakinstructie,
                $this->opmerkingen,
                $this->file_path,
                $this->status,
                $this->modified,
                $this->id
            )) {
                echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
                return false;
            }
    
            if (!$stmt->execute()) {
                echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                return false;
            }
    
            $stmt->close();
            return true;
        } else {
            echo "Prepare failed: (" . $this->db->link->errno . ") " . $this->db->link->error;
            return false;
        }
    }
    
    public function getWorkOrderById($workOrderId) {
        // Prepare the query to fetch the work order with the specific ID, including status
        $query = "SELECT id, omschrijving, klant, opdrachtnr_klant, omschrijving_klant, leverdatum, start, end, resource1, resource2, verpakinstructie, opmerkingen, file_path, created, modified, status 
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
                    $workOrder = $result->fetch_object();
        
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
    // Method to get and display all work orders
    public function getWorkorders($itemsPerPage = 10) {
        $currentPage = isset($_GET['pagecount']) ? (int)$_GET['pagecount'] : 1;
        $currentPage = max(1, $currentPage);
    
        $offset = ($currentPage - 1) * $itemsPerPage;
    
        $totalQuery = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $totalResult = $this->db->link->query($totalQuery);
        $totalCount = $totalResult->fetch_assoc()['total'];
        $totalPages = ceil($totalCount / $itemsPerPage);
    
        $query = "SELECT id, omschrijving, klant, opdrachtnr_klant, omschrijving_klant, leverdatum, start, end, verpakinstructie, opmerkingen, file_path, created, modified, status 
                  FROM " . $this->table_name . " 
                  LIMIT $itemsPerPage OFFSET $offset";
    
        if ($result = $this->db->link->query($query)) {
            if ($result->num_rows > 0) {
                $startResult = $offset + 1;
                $endResult = min($offset + $itemsPerPage, $totalCount);
    
                echo "<table class='data-table results' cellpadding='0' cellspacing='0'>";
                echo "<tr>
                        <th class=\"ui-corner-tl\">ID</th>
                        <th>Omschrijving</th>
                        <th>Klant</th>
                        <th>Opdrachtnr Klant</th>
                        <th>Omschrijving Klant</th>
                        <th>Leverdatum</th>
                        <th>Verpakinstructie</th>
                        <th>Opmerkingen</th>
                        <th>Status</th>
                        <th>Gemaakt</th>
                        <th>Aangepast</th>
                        <th class=\"ui-corner-tr\">Inkoop Order</th>
                      </tr>";
    
                while ($row = $result->fetch_assoc()) {
                    echo "<tr class=\"clickable-row\" onclick=\"window.location.href='index.php?page=workorder/editworkorder&id=" . $row['id'] . "'\" style='cursor: pointer;'>
                            <td>" . $row['id'] . "</td>
                            <td>" . $row['omschrijving'] . "</td>
                            <td>" . $row['klant'] . "</td>
                            <td>" . $row['opdrachtnr_klant'] . "</td>
                            <td>" . $row['omschrijving_klant'] . "</td>
                            <td>" . $row['leverdatum'] . "</td>
                            <td>" . $row['verpakinstructie'] . "</td>
                            <td>" . $row['opmerkingen'] . "</td>
                            <td>" . $row['status'] . "</td>
                            <td>" . $row['created'] . "</td>
                            <td>" . $row['modified'] . "</td>
                            <td>" . ($row['file_path'] ? "<a href='page/workorder/" . $row['file_path'] . "' target='blank'>FILE</a>" : "n.v.t.") . "</td>
                          </tr>";
                }
    
                echo "<tfoot>
                        <tr>
                            <td class=\"ui-corner-bottom\"  colspan='12'>Werkbon $startResult – $endResult van $totalCount</td>
                        </tr>
                      </tfoot>";
                echo "</table>";
    
                echo "<div class='pagination'>";
                for ($page = 1; $page <= $totalPages; $page++) {
                    if ($page == $currentPage) {
                        echo "<span class='current-page'>$page</span> ";
                    } else {
                        echo "<a href='index.php?page=workorder/showworkorders&pagecount=$page' class='pagination-link'>$page</a> ";
                    }
                }
                echo "</div>";
            } else {
                echo "No work orders found.";
            }
        } else {
            echo "Query failed: (" . $this->db->link->errno . ") " . $this->db->link->error;
        }
    }
    


    public function getWorkordersJson()
    {
        $query = "SELECT id, omschrijving as title, start, end, resource1, resource2 FROM " . $this->table_name;
        $data = []; // Initialize $data as an empty array

        if ($result = $this->db->link->query($query)) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Check if resource1 is set
                    if (!empty($row['resource1'])) {
                        $data[] = [
                            'id' => $row['id'],
                            'title' => $row['title'],
                            'start' => $row['start'],
                            'end' => $row['end'],
                            'resourceId' => $row['resource1'],
                        ];
                    }

                    // Check if resource2 is set
                    if (!empty($row['resource2'])) {
                        $data[] = [
                            'id' => $row['id'],
                            'title' => $row['title'],
                            'start' => $row['start'],
                            'end' => $row['end'],
                            'resourceId' => $row['resource2'],
                        ];
                    }
                }
                return json_encode($data, JSON_PRETTY_PRINT );
            }
        }
        return json_encode($data); // Return an empty JSON array if no rows found
    }

    public function searchWorkOrderCustomers($term) {
        // Prepare the SQL query
        $query = "SELECT id, klant FROM " . $this->table_name . " WHERE klant LIKE ? LIMIT 10";
    
        if ($stmt = $this->db->link->prepare($query)) {
            $searchTerm = '%' . $term . '%';
            $stmt->bind_param("s", $searchTerm);
    
            // Execute the query
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $data = [];
                while ($row = $result->fetch_assoc()) {
                    $data[] = ['id' => $row['id'], 'text' => $row['klant']];
                }
    
                // Return the data as JSON
                return json_encode($data);
            } else {
                // Handle execution error
                return json_encode(['error' => 'Database executie fout']);
            }
        } else {
            // Handle preparation error
            return json_encode(['error' => 'Query voorbereidings fout']);
        }
    }
}
?>