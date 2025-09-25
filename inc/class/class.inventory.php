<?php
class InventoryManager {
    /** @var mysqli */
    private $link;
    private $db;


    public function __construct() {
        $this->db = new DB();
        $this->link = $this->db->link;
    }

    /**
     * List inventory (all rows as JSON)
     */
    public function listInventory(): string {
        $stmt = $this->link->prepare("SELECT * FROM vanda_inventory");
        if (!$stmt) return json_encode([]);

        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return json_encode($rows, JSON_PRETTY_PRINT);
    }


    /**
     * Get inventory as ASSOC_ARRAY item by ID
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->link->prepare(
            "SELECT id, barcode, quality, location, processed, date
            FROM vanda_inventory WHERE id = ?"
        );
        if (!$stmt) return null;

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        return $row ?: null;
    }   

    /**
     * Insert new stock.
     * If your `id` is AUTO_INCREMENT, call without $id (i.e., $id = null).
     * If not AUTO_INCREMENT, provide an explicit $id.
     *
     * @return int Inserted row id (AUTO_INCREMENT id or provided id), or 0 on failure
     */
    public function InsertStock(?int $id, string $barcode, string $quality, string $location, int $processed, string $date): int {
        if ($id === null) {
            $sql  = "INSERT INTO vanda_inventory (barcode, quality, location, processed, date)
                     VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->link->prepare($sql);
            if (!$stmt) return 0;

            $stmt->bind_param("sssds",
                $barcode,
                $quality,
                $location,
                /* processed is tinyint(1) → bind as integer */
                $processed,
                $date
            );
        } else {
            $sql  = "INSERT INTO vanda_inventory (id, barcode, quality, location, processed, date)
                     VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->link->prepare($sql);
            if (!$stmt) return 0;

            $stmt->bind_param("isssds",
                $id,
                $barcode,
                $quality,
                $location,
                $processed,
                $date
            );
        }

        if (!$stmt->execute()) {
            $stmt->close();
            return 0;
        }

        $newId = $id ?? $stmt->insert_id;
        $stmt->close();
        return (int)$newId;
    }

    /**
     * Modify existing stock (by ID).
     * $fields is an assoc array of column => value.
     * Allowed columns: barcode, quality, location, processed, date
     */
    public function ModifyStock(int $id, array $fields): bool {
        if ($id <= 0) return false;

        // Whitelist allowed columns to avoid accidental/unsafe updates
        $allowed = ['barcode', 'quality', 'location', 'processed', 'date'];

        $setParts = [];
        $params   = [];
        $types    = "";

        foreach ($fields as $col => $val) {
            if (!in_array($col, $allowed, true)) continue;

            $setParts[] = "`$col` = ?";
            // Determine bind type
            switch ($col) {
                case 'processed':
                    $types .= "d"; // integer-like; 'i' also works but 'd' is fine in mysqli
                    $params[] = (int)$val;
                    break;
                default:
                    // barcode, quality, location, date -> strings
                    $types .= "s";
                    $params[] = (string)$val;
                    break;
            }
        }

        if (empty($setParts)) return false;

        $sql  = "UPDATE vanda_inventory SET " . implode(", ", $setParts) . " WHERE id = ?";
        $stmt = $this->link->prepare($sql);
        if (!$stmt) return false;

        $types .= "i";
        $params[] = $id;

        // bind_param requires references
        $bindParams = $this->makeRefArray($types, $params);
        call_user_func_array([$stmt, 'bind_param'], $bindParams);

        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * Release stock (delete by ID)
     */
    public function ReleaseStock(int $id): bool {
        $stmt = $this->link->prepare("DELETE FROM vanda_inventory WHERE id = ?");
        if (!$stmt) return false;

        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    /**
     * Helper to build by-ref array for call_user_func_array on bind_param
     */
    private function makeRefArray(string $types, array $params): array {
        $refs = [];
        $refs[] = $types;
        foreach ($params as $k => $v) {
            $refs[$k + 1] = &$params[$k];
        }
        return $refs;
    }
}