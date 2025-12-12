<?php
// process.php
header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'message' => 'Method not allowed']);
        exit;
    }

    /*
     * Load required classes
     */
    require_once("../../inc/class/class.inventory.php");
    require_once("../../inc/class/class.user.php");
    require_once("../../inc/class/class.option.php");

    /*
     * Initiate classes
     */
    $im = new InventoryManager();
    $um = new UserManager;
    $om = new OptionManager;

    // Helpers
    $action = $_POST['action'] ?? '';
    $id     = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;

    // Normalize decimal input: accept "12,34" or "12.34"
    $toFloat = static function ($v) {
        if ($v === null || $v === '') return null;
        $v = str_replace([' ', ','], ['', '.'], (string)$v);
        return is_numeric($v) ? (float)$v : null;
    };

    if ($action === 'save') {
        $barcode   = trim($_POST['barcode']  ?? '');
        $relation  = trim($_POST['relation']  ?? '');
        $quality   = trim($_POST['quality']  ?? '');
        $location  = trim($_POST['location'] ?? '');
        $processed = isset($_POST['processed']) ? (int)$_POST['processed'] : 0;
        $dateSql   = trim($_POST['date'] ?? '');
        // New fields
        $lengte    = $toFloat($_POST['lengte']  ?? null);
        $breedte   = $toFloat($_POST['breedte'] ?? null);

        // Validate
        $errors = [];
        if ($barcode === '')   $errors[] = 'Rolnummer/Barcode is verplicht.';
        if ($relation === '')  $errors[] = 'Relatie is verplicht.';
        if ($quality === '')   $errors[] = 'Quality is verplicht.';
        if ($location === '')  $errors[] = 'Location is verplicht.';
        if ($dateSql === '')   $errors[] = 'Datum/tijd is verplicht.';
        if ($dateSql && !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}(:\d{2})?$/', $dateSql)) {
            $errors[] = 'Datum/tijd heeft een ongeldig formaat. (verwacht: YYYY-MM-DD HH:MM[:SS])';
        }
        // lengte/breedte optional but must be numeric if provided
        if (($_POST['lengte'] ?? '') !== '' && $lengte === null) {
            $errors[] = 'Lengte moet numeriek zijn.';
        }
        if (($_POST['breedte'] ?? '') !== '' && $breedte === null) {
            $errors[] = 'Breedte moet numeriek zijn.';
        }

        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'message' => implode(' ', $errors)]);
            exit;
        }

        // Ensure seconds present
        if (strlen($dateSql) === 16) $dateSql .= ':00';

        if ($id) {
            // Update
            $fields = [
                'barcode'   => $barcode,
                'relation'  => $relation,
                'quality'   => $quality,
                'location'  => $location,
                'processed' => $processed,
                'date'      => $dateSql
            ];
            if ($lengte !== null)  $fields['lengte']  = $lengte;
            if ($breedte !== null) $fields['breedte'] = $breedte;

            $ok = $im->ModifyStock($id, $fields);
            if (!$ok) {
                throw new Exception('Bijwerken mislukt.');
            }

            // Return fresh row (includes modified timestamp)
            $row = $im->getById($id);
            echo json_encode([
                'ok'      => true,
                'id'      => $id,
                'message' => "Record #$id succesvol bijgewerkt.",
                'row'     => $row
            ]);
            exit;
        } else {
            // Create
            // Use 0.0 if lengte/breedte omitted and your DB is NOT NULL; else pass nulls if columns accept NULL.
            $lenVal = $lengte  ?? 0.0;
            $brdVal = $breedte ?? 0.0;

            $newId = $im->InsertStock(null, $barcode, $relation, $quality, $location, $processed, $dateSql, $lenVal, $brdVal);
            if (!$newId) {
                throw new Exception('Aanmaken mislukt.');
            }

            $row = $im->getById($newId);
            echo json_encode([
                'ok'      => true,
                'id'      => (int)$newId,
                'message' => "Record #$newId succesvol aangemaakt.",
                'row'     => $row
            ]);
            exit;
        }
    }

    if ($action === 'delete') {
        if (!$id) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'message' => 'Ongeldige ID.']);
            exit;
        }
        $ok = $im->ReleaseStock($id);
        if (!$ok) throw new Exception('Verwijderen mislukt.');
        echo json_encode(['ok' => true, 'message' => "Record #$id verwijderd."]);
        exit;
    }

    if($action == 'search') {
        $barcode   = trim($_POST['barcode']  ?? '');
        $location  = trim($_POST['location'] ?? '');
        $processed = trim($_POST['processed'] ?? '');
        
        if (empty($barcode) || empty($location)) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'message' => 'Ongeldige input.']);
            exit;
        }

        $items = $im->searchInventory($barcode, $location, $processed);

        if(!$items) {
             echo json_encode(['ok' => true, 'message' => 'Geen resultaat.', JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES]);
             return;
        }

        echo json_encode(['ok' => true, 'rows' => $items]);
        exit;
    }   

    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'Onbekende actie.']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
}