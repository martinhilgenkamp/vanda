<?php
// process.php
header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // Basic method check
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
    $om = new OptionManager();


    // Read inputs
    $action   = $_POST['action'] ?? '';
    $id       = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;

    if ($action === 'save') {
        $rolno     = trim($_POST['rolno'] ?? '');
        $quality   = trim($_POST['quality'] ?? '');
        $location  = trim($_POST['location'] ?? '');
        $processed = isset($_POST['processed']) ? (int)$_POST['processed'] : 0;
        $dateSql   = trim($_POST['date'] ?? '');

        // Validate
        $errors = [];
        if ($rolno === '')    $errors[] = 'Rolnummer is verplicht.';
        if ($quality === '')  $errors[] = 'Quality is verplicht.';
        if ($location === '') $errors[] = 'Location is verplicht.';
        if ($dateSql === '')  $errors[] = 'Datum/tijd is verplicht.';
        if ($dateSql && !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}(:\d{2})?$/', $dateSql)) {
            $errors[] = 'Datum/tijd heeft een ongeldig formaat.';
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
            $ok = $im->ModifyStock($id, [
                'rolno'     => $rolno,
                'quality'   => $quality,
                'location'  => $location,
                'processed' => $processed,
                'date'      => $dateSql
            ]);
            if (!$ok) {
                throw new Exception('Bijwerken mislukt.');
            }
            echo json_encode(['ok' => true, 'id' => $id, 'message' => "Record #$id succesvol bijgewerkt."]);
            exit;
        } else {
            // Create
            $newId = $im->InsertStock(null, $rolno, $quality, $location, $processed, $dateSql);
            if (!$newId) {
                throw new Exception('Aanmaken mislukt.');
            }
            echo json_encode(['ok' => true, 'id' => (int)$newId, 'message' => "Record #$newId succesvol aangemaakt."]);
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

    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'Onbekende actie.']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
}