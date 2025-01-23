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
    public $resources;      // Hier komen de resources in. 
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
              (omschrijving, klant, opdrachtnr_klant, omschrijving_klant, leverdatum, start, end, resources, verpakinstructie, opmerkingen, file_path, status, created, modified) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $this->created = date("Y-m-d H:i:s");
    $this->modified = date("Y-m-d H:i:s");

    $resourcesJson = json_encode($this->resources); // Encode resources array as JSON

    if ($stmt = $this->db->link->prepare($query)) {
        if (!$stmt->bind_param(
            "ssssssssssssss",
            $this->omschrijving,
            $this->klant,
            $this->opdrachtnr_klant,
            $this->omschrijving_klant,
            $this->leverdatum,
            $this->start,
            $this->end,
            $resourcesJson, // Save resources as JSON
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
                  leverdatum = ?, start = ?, end = ?, resources = ?, verpakinstructie = ?, opmerkingen = ?, 
                  file_path = ?, status = ?, modified = ?
              WHERE id = ?";

    $resourcesJson = json_encode($this->resources); // Encode resources array as JSON

    if ($stmt = $this->db->link->prepare($query)) {
        if (!$stmt->bind_param(
            "sssssssssssssi",
            $this->omschrijving,
            $this->klant,
            $this->opdrachtnr_klant,
            $this->omschrijving_klant,
            $this->leverdatum,
            $this->start,
            $this->end,
            $resourcesJson, // Update resources as JSON
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

    function MoveWorkOrder($id, $startMySQL, $stopMySQL, $oldresource, $resource){
        $WorkOrderResources = $this->getWorkOrderResources($id);
    
        // Set new resources if they are different
        if(isset($oldresource) && isset($resource)){
            $resources = json_decode($WorkOrderResources->resources, true);
            $key = array_search($oldresource, $resources);
            if ($key !== false) {
                $resources[$key] = $resource;
                $WorkOrderResources->resources = json_encode($resources);
            }
        }      
    
        $SQLResource = $WorkOrderResources->resources;
        $query = "UPDATE " . $this->table_name . " 
                  SET start = ?, end = ?, resources = ?
                  WHERE id = ?";
    
        if ($stmt = $this->db->link->prepare($query)) {
            if (!$stmt->bind_param("sssi", $startMySQL, $stopMySQL, $SQLResource, $id)) {
                echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
                return false;
            }
           
            //DEBUG
            // Construct the effective query for debugging
            // $effectiveQuery = sprintf(
            //   "UPDATE %s SET start = '%s', end = '%s', resources = '%s' WHERE id = %d",
            //    $this->table_name,
            //    $startMySQL,
            //    $stopMySQL,
            //    $SQLResource,
            //    $id
            // );
            // echo "Effective query: " . $effectiveQuery;
    
            if (!$stmt->execute()) {
                echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                return false;
            }            
    
            $stmt->close();
            return json_encode(['success' => true, 'message' => 'Werkbon met success verplaatst.']);            
        } else {
            return json_encode(['success' => false, 'message' => 'Probleem bij het verplaatsen (" . $this->db->link->errno . ") " . $this->db->link->error']);
        }
    }
    
    public function getWorkOrderResources($workOrderId) {
        $query = "SELECT resources 
                  FROM " . $this->table_name . " 
                  WHERE id = ?";
    
        if ($stmt = $this->db->link->prepare($query)) {
            $stmt->bind_param("i", $workOrderId);
    
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $workOrder = $result->fetch_object();
                    return $workOrder;
                }
            }
        }
        return null;
    }

    public function getWorkOrderById($workOrderId) {
        $query = "SELECT id, omschrijving, klant, opdrachtnr_klant, omschrijving_klant, leverdatum, start, end, resources, verpakinstructie, opmerkingen, file_path, created, modified, status 
                  FROM " . $this->table_name . " 
                  WHERE id = ?";
    
        if ($stmt = $this->db->link->prepare($query)) {
            $stmt->bind_param("i", $workOrderId);
    
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $workOrder = $result->fetch_object();
                    $workOrder->resources = json_decode($workOrder->resources, true); // Decode JSON to array
                    return $workOrder;
                }
            }
        }
        return null;
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
        $query = "SELECT id, omschrijving AS title, start, end, resources FROM " . $this->table_name;
        $data = []; // Initialize $data as an empty array

        if ($result = $this->db->link->query($query)) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Decode the JSON resources array
                    $resources = json_decode($row['resources'], true);

                    // Check if resources exist and is a valid array
                    if (is_array($resources) && count($resources) > 0) {
                        foreach ($resources as $resource) {
                            $data[] = [
                                'id' => $row['id'],
                                'title' => $row['title'],
                                'start' => $row['start'],
                                'end' => $row['end'],
                                'resourceId' => $resource, // Add each resource as a separate entry
                            ];
                        }
                    }
                }
            }
            return json_encode($data, JSON_PRETTY_PRINT);
        }
        return json_encode($data); // Return an empty JSON array if no rows are found
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

    public function getStatusOptions($status) {
        //$this->status = isset($this->status) ? $this->status : 'Nieuw';

        $options = ['Nieuw', 'In behandeling', 'Afgerond', 'Verzonden', 'Verwijderd'];
        $statusOptions = '';

        foreach ($options as $option) {
            $selected = $status == $option ? 'selected' : '';
            $statusOptions .= "<option value='$option' $selected>$option</option>";
        }

        return $statusOptions;
    }


    function getAvailableResources($db, $startTime, $endTime) {
        $query = "
            SELECT u.id, u.name
            FROM vanda.users u
            WHERE u.id NOT IN (
                SELECT DISTINCT JSON_EXTRACT(r.value, '$')
                FROM vanda.vanda_work_orders wo
                CROSS JOIN JSON_TABLE(wo.resources, '$[*]' COLUMNS (value VARCHAR(255) PATH '$')) r
                WHERE wo.start < :end_time
                  AND wo.end > :start_time
            )
        ";
    
        $stmt = $db->prepare($query);
        $stmt->bindParam(':start_time', $startTime);
        $stmt->bindParam(':end_time', $endTime);
    
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return available resources
        } else {
            throw new Exception("Database query failed: " . implode(", ", $stmt->errorInfo()));
        }
    }
}


?>