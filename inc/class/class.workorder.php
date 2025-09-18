<?php
// Enable error reporting at the top of your script
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("class.db.php");

date_default_timezone_set("Europe/Amsterdam");

class WorkOrder {
    private $db;
    private $table_name = "vanda_work_orders";
    public $id;
    public $omschrijving;
    public $klant;
    public $opdrachtnr_klant;
    public $created;
    public $modified;
    public $leverdatum;
    public $start;
    public $end;
    public $resource1; 
    public $resources; 
    public $verpakinstructie;
    public $createdby;
    public $modifiedby;
    public $file_path;
    public $status;
    public $recurrence_type;
    public $recurrence_interval;
    public $recurrence_until;
    public $recurrence_days;

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
        (omschrijving, klant, opdrachtnr_klant, leverdatum, start, end, resources, verpakinstructie, file_path, status, created, modified,
        recurrence_type, recurrence_interval, recurrence_until, recurrence_days) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $this->created = date("Y-m-d H:i:s");
        $this->modified = date("Y-m-d H:i:s");

        $resourcesJson = json_encode($this->resources);

        if ($stmt = $this->db->link->prepare($query)) {
            if (!$stmt->bind_param(
                "sssssssssssssssi", // 16 parameters: 15 strings, 1 integer
                $this->omschrijving,
                $this->klant,
                $this->opdrachtnr_klant,
                $this->leverdatum,
                $this->start,
                $this->end,
                $resourcesJson,
                $this->verpakinstructie,
                $this->file_path,
                $this->status,
                $this->created,
                $this->modified,
                $this->recurrence_type,
                $this->recurrence_interval,
                $this->recurrence_until,
                $this->recurrence_days
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
        $resourcesJson = json_encode($this->resources);

        $query = "UPDATE " . $this->table_name . " 
                SET omschrijving = ?, klant = ?, opdrachtnr_klant = ?, 
                    leverdatum = ?, start = ?, end = ?, resources = ?, verpakinstructie = ?, 
                    file_path = ?, status = ?, modified = ?,
                    recurrence_type = ?, recurrence_interval = ?, recurrence_until = ?, recurrence_days = ?
                WHERE id = ?";

        if ($stmt = $this->db->link->prepare($query)) {
            if (!$stmt->bind_param(
                "sssssssssssisssi",
                $this->omschrijving,
                $this->klant,
                $this->opdrachtnr_klant,
                $this->leverdatum,
                $this->start,
                $this->end,
                $resourcesJson,
                $this->verpakinstructie,
                $this->file_path,
                $this->status,
                $this->modified,
                $this->recurrence_type,
                $this->recurrence_interval,
                $this->recurrence_until,
                $this->recurrence_days,
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
    $query = "SELECT 
                id, omschrijving, klant, opdrachtnr_klant, leverdatum, 
                start, end, resources, verpakinstructie, file_path, 
                created, modified, status,
                recurrence_type, recurrence_interval, recurrence_until, recurrence_days
              FROM " . $this->table_name . " 
              WHERE id = ?";

    if ($stmt = $this->db->link->prepare($query)) {
        $stmt->bind_param("i", $workOrderId);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $workOrder = $result->fetch_object();
                $workOrder->resources = json_decode($workOrder->resources, true); // Decode JSON to array

                // Optioneel: decode recurrence_days naar array als dat handig is
                // $workOrder->recurrence_days = explode(',', $workOrder->recurrence_days);

                return $workOrder;
            }
        }
    }
    return null;
}
    public function sortLink($column, $label, $currentSort, $currentOrder) {
        $nextOrder = ($currentSort === $column && $currentOrder === 'ASC') ? 'desc' : 'asc';
        return "<a href='index.php?page=workorder/showworkorders&sort=$column&order=$nextOrder'>" . $label . "</a>";
    }


    // Method to get and display all work orders
    public function getWorkorders($itemsPerPage = 10, $sortColumn = 'id', $sortOrder = 'ASC', $searchTerm = '') {
        $currentPage = isset($_GET['pagecount']) ? (int)$_GET['pagecount'] : 1;
        $currentPage = max(1, $currentPage);
    
        $offset = ($currentPage - 1) * $itemsPerPage;

        $allowedSortColumns = ['id', 'omschrijving', 'klant', 'opdrachtnr_klant', 'leverdatum', 'start', 'end', 'verpakinstructie', 'file_path', 'created', 'modified', 'status'];

        $sortColumn = isset($_GET['sort']) && in_array($_GET['sort'], $allowedSortColumns) ? $_GET['sort'] : 'id';
        $sortOrder = (isset($_GET['order']) && strtolower($_GET['order']) === 'desc') ? 'DESC' : 'ASC';
        
        $searchSQL = '';

        if (!empty($searchTerm)) {
            $searchSQL = "WHERE 
                omschrijving LIKE '%$searchTerm%' OR 
                klant LIKE '%$searchTerm%' OR 
                opdrachtnr_klant LIKE '%$searchTerm%' OR 
                leverdatum LIKE '%$searchTerm%' OR 
                verpakinstructie LIKE '%$searchTerm%' OR 
                status LIKE '%$searchTerm%' OR 
                created LIKE '%$searchTerm%' OR 
                modified LIKE '%$searchTerm%'";
        }

        $totalQuery = "SELECT COUNT(*) as total FROM " . $this->table_name . " " . $searchSQL;
        $totalResult = $this->db->link->query($totalQuery);
        $totalCount = $totalResult->fetch_assoc()['total'];
        $totalPages = ceil($totalCount / $itemsPerPage);


    
        $query = "SELECT id, omschrijving, klant, opdrachtnr_klant, leverdatum, start, end, verpakinstructie, file_path, created, modified, status 
                  FROM " . $this->table_name . " $searchSQL 
                  ORDER BY $sortColumn $sortOrder LIMIT $itemsPerPage OFFSET $offset";


        echo "<div id='filter_form_div' style='text-align: right; margin-bottom: 10px;'>
                <form method='GET' action='index.php' style='display: inline-block;'>
                    <input type='hidden' name='page' value='workorder/showworkorders'>
                    <input type='text' name='search' value='$searchTerm' placeholder='Zoek werkbonnen...' style='width: 250px;' />
                    <button type='submit'>Zoeken</button>
                </form>
              </div>";
    
        if ($result = $this->db->link->query($query)) {
            if ($result->num_rows > 0) {
                $startResult = $offset + 1;
                $endResult = min($offset + $itemsPerPage, $totalCount);
    
                echo "<table class='data-table results' cellpadding='0' cellspacing='0'>";
                echo "<tr>
                        <th class=\"ui-corner-tl\">" . $this->sortLink('id', 'ID', $sortColumn, $sortOrder) . "</th>
                        <th>" . $this->sortLink('omschrijving', 'Omschrijving', $sortColumn, $sortOrder) . "</th>
                        <th>" . $this->sortLink('klant', 'Klant', $sortColumn, $sortOrder) . "</th>
                        <th>" . $this->sortLink('opdrachtnr_klant', 'Opdrachtnr Klant', $sortColumn, $sortOrder) . "</th>
                        <th>" . $this->sortLink('leverdatum', 'Leverdatum', $sortColumn, $sortOrder) . "</th>
                        <th>" . $this->sortLink('verpakinstructie', 'Verpakinstructie', $sortColumn, $sortOrder) . "</th>
                        <th>" . $this->sortLink('status', 'Status', $sortColumn, $sortOrder) . "</th>
                        <th>" . $this->sortLink('created', 'Gemaakt', $sortColumn, $sortOrder) . "</th>
                        <th>" . $this->sortLink('modified', 'Aangepast', $sortColumn, $sortOrder) . "</th>
                        <th class=\"ui-corner-tr\">" . $this->sortLink('file_path', 'Inkoop Order', $sortColumn, $sortOrder) . "</th>
                      </tr>";
    
                while ($row = $result->fetch_assoc()) {
                    echo "<tr class=\"clickable-row\" onclick=\"window.location.href='index.php?page=workorder/editworkorder&id=" . $row['id'] . "'\" style='cursor: pointer;'>
                            <td>" . $row['id'] . "</td>
                            <td>" . $row['omschrijving'] . "</td>
                            <td>" . $row['klant'] . "</td>
                            <td>" . $row['opdrachtnr_klant'] . "</td>
                            <td>" . $row['leverdatum'] . "</td>
                            <td>" . $row['verpakinstructie'] . "</td>
                            <td>" . $row['status'] . "</td>
                            <td>" . $row['created'] . "</td>
                            <td>" . $row['modified'] . "</td>
                            <td>" . ($row['file_path'] ? "<a href='page/workorder/" . $row['file_path'] . "' target='blank'>FILE</a>" : "n.v.t.") . "</td>
                          </tr>";
                }
    
                echo "<tfoot>
                        <tr>
                            <td class=\"ui-corner-bottom\"  colspan='10'>Werkbon $startResult – $endResult van $totalCount</td>
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
        $query = "SELECT id, omschrijving AS title, start, end, resources,
                        recurrence_type, recurrence_interval, recurrence_until, recurrence_days
                FROM " . $this->table_name;

        $data = [];

        $result = $this->db->link->query($query);
        if (!$result) {
            error_log("Database query failed: " . $this->db->link->error);
            return json_encode(['error' => 'Database query failed']);
        }

        while ($row = $result->fetch_assoc()) {
            error_log("Processing row: " . print_r($row, true)); // Debugging to log

            // Validate resources
            $resources = json_decode($row['resources'], true) ?? [];
            if (empty($resources)) {
                error_log("Invalid or empty resources for ID {$row['id']}: {$row['resources']}");
                continue;
            }

            // Validate dates
            try {
                $startDT = new DateTime($row['start']);
                $endDT = new DateTime($row['end']);
                $duration = $startDT->diff($endDT);
            } catch (Exception $e) {
                error_log("Invalid date format for ID {$row['id']}: {$e->getMessage()}");
                continue;
            }

            // Recurring event
            if (!empty($row['recurrence_type']) && !empty($row['recurrence_until'])) {
                try {
                    $repeats = $this->generateRecurringDates(
                        $row['start'],
                        $row['recurrence_type'],
                        $row['recurrence_interval'],
                        $row['recurrence_until'],
                        $row['recurrence_days']
                    );

                    foreach ($repeats as $startTime) {
                        foreach ($resources as $resource) {
                            $start = new DateTime($startTime);
                            $end = clone $start;
                            $end->add($duration);

                            $data[] = [
                                'id' => hash('md5', $row['id'] . $resource . $start->format('YmdHis')),
                                'title' => $row['title'],
                                'start' => $start->format('Y-m-d H:i:s'),
                                'end' => $end->format('Y-m-d H:i:s'),
                                'resourceId' => $resource,
                                'originalId' => $row['id']
                            ];
                        }
                    }
                    error_log("Generated dates for ID {$row['id']}: " . print_r($repeats, true));
                } catch (Exception $e) {
                    error_log("Error generating recurring dates for ID {$row['id']}: {$e->getMessage()}");
                    continue;
                }
            } else {
                // Non-recurring event
                foreach ($resources as $resource) {
                    $data[] = [
                        'id' => hash('md5', $row['id'] . $resource . $startDT->format('YmdHis')),
                        'title' => $row['title'],
                        'start' => $row['start'],
                        'end' => $row['end'],
                        'resourceId' => $resource,
                        'originalId' => $row['id']
                    ];
                }
            }
        }

        $json = json_encode($data, JSON_PRETTY_PRINT);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON encoding error: " . json_last_error_msg());
            return json_encode(['error' => 'Failed to encode data']);
        }

        return $json;
    }

    private function generateRecurringDates($startDateTime, $type, $interval, $until, $days = '')
    {
        $dates = [];
        $validDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $maxIterations = 10000; // Prevent runaway loops

        // Validate inputs
        try {
            $current = new DateTime($startDateTime);
            $end = new DateTime($until);
        } catch (Exception $e) {
            error_log("Invalid date format in generateRecurringDates: {$e->getMessage()}");
            return [];
        }

        $interval = (int)($interval ?: 1); // Default to 1 if empty or 0
        $daysArray = array_filter(explode(',', $days ?? ''));
        if ($type === 'weekly' && !empty($daysArray)) {
            $daysArray = array_intersect($daysArray, $validDays);
            if (empty($daysArray)) {
                error_log("Invalid or empty days for weekly recurrence: {$days}");
                return [];
            }
        } elseif ($type === 'weekly' && empty($daysArray)) {
            $daysArray = [$current->format('D')]; // Default to start date's day
        }

        // Validate recurrence type
        if (!in_array($type, ['daily', 'weekly', 'monthly'])) {
            error_log("Invalid recurrence type: {$type}");
            return [];
        }

        $iteration = 0;
        while ($current <= $end && $iteration < $maxIterations) {
            switch ($type) {
                case 'daily':
                    $dates[] = $current->format('Y-m-d H:i:s');
                    $current->modify("+{$interval} days");
                    break;

                case 'weekly':
                    // Start at the beginning of the current week
                    $weekStart = clone $current;
                    $weekStart->modify('Monday this week'); // Ensure we start at Monday
                    $weekEnd = clone $weekStart;
                    $weekEnd->modify('+6 days'); // End of the week

                    // Only process days within the recurrence period
                    if ($weekStart <= $end) {
                        foreach ($daysArray as $day) {
                            $dayDate = clone $weekStart;
                            $dayDate->modify($day);
                            // Ensure the date is within bounds and not before the start
                            if ($dayDate >= $current && $dayDate <= $end) {
                                $dates[] = $dayDate->format('Y-m-d H:i:s');
                            }
                        }
                    }
                    $current->modify("+{$interval} weeks");
                    break;

                case 'monthly':
                    $dates[] = $current->format('Y-m-d H:i:s');
                    $current->modify("+{$interval} months");
                    break;
            }
            $iteration++;
        }

        if ($iteration >= $maxIterations) {
            error_log("Max iterations reached for recurrence type {$type}");
        }

        // Sort dates chronologically
        sort($dates);

        return $dates;
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