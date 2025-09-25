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
        $sql = "SELECT id, barcode, quality, location, processed, date, modified, lengte, breedte
                FROM vanda_inventory";
        $stmt = $this->link->prepare($sql);
        if (!$stmt) return json_encode([]);

        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return json_encode($rows, JSON_PRETTY_PRINT);
    }

    /**
     * Get inventory row by ID
     */
    public function getById(int $id): ?array {
        $sql = "SELECT id, barcode, quality, location, processed, date, modified, lengte, breedte
                FROM vanda_inventory WHERE id = ?";
        $stmt = $this->link->prepare($sql);
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
     * Return new ID (or provided ID) on success, 0 on failure.
     */
    public function InsertStock(
        ?int $id,
        string $barcode,
        string $quality,
        string $location,
        int $processed,
        string $date,
        float $lengte,
        float $breedte
    ): int {
        if ($id === null) {
            $sql  = "INSERT INTO vanda_inventory
                     (barcode, quality, location, processed, date, modified, lengte, breedte)
                     VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)";
            $stmt = $this->link->prepare($sql);
            if (!$stmt) return 0;

            // s s s i s d d
            $stmt->bind_param(
                "sssisdd",
                $barcode,
                $quality,
                $location,
                $processed, // use 'i' (integer) for tinyint
                $date,
                $lengte,
                $breedte
            );
        } else {
            $sql  = "INSERT INTO vanda_inventory
                     (id, barcode, quality, location, processed, date, modified, lengte, breedte)
                     VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?)";
            $stmt = $this->link->prepare($sql);
            if (!$stmt) return 0;

            // i s s s i s d d
            $stmt->bind_param(
                "isssisdd",
                $id,
                $barcode,
                $quality,
                $location,
                $processed,
                $date,
                $lengte,
                $breedte
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
     * Allowed columns: barcode, quality, location, processed, date, lengte, breedte
     * 'modified' is set to NOW() automatically on update.
     */
    public function ModifyStock(int $id, array $fields): bool {
        if ($id <= 0) return false;

        $allowed = ['barcode', 'quality', 'location', 'processed', 'date', 'lengte', 'breedte'];

        $setParts = [];
        $params   = [];
        $types    = "";

        foreach ($fields as $col => $val) {
            if (!in_array($col, $allowed, true)) continue;

            $setParts[] = "`$col` = ?";
            switch ($col) {
                case 'processed':
                    $types   .= "i";          // integer
                    $params[] = (int)$val;
                    break;
                case 'lengte':
                case 'breedte':
                    $types   .= "d";          // double/float
                    $params[] = (float)$val;
                    break;
                default:
                    $types   .= "s";          // strings: barcode, quality, location, date
                    $params[] = (string)$val;
                    break;
            }
        }

        if (empty($setParts)) return false;

        // auto-stamp modified
        $sql  = "UPDATE vanda_inventory SET " . implode(", ", $setParts) . ", modified = NOW() WHERE id = ?";
        $stmt = $this->link->prepare($sql);
        if (!$stmt) return false;

        $types   .= "i";
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