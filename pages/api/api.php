<?php
header("Content-Type:application/json");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
//header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
//header("Access-Control-Max-Age: 3600");
//header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$req = $_SERVER['REQUEST_METHOD'];

require_once('../../inc/class/class.machines.php');
$mm = new MachineManager();


if($req == 'GET'){
    // Get the machine number from the query string
    $machineno = isset($_GET['machine']) ? $_GET['machine'] : '';

    // Check if $machineno contains only numbers
    if (preg_match('/^[0-9]+$/', $machineno)) {
       
        $data['persoon'] = $mm->getLastPersoon($machineno);
        $data['kwaliteit'] = $mm->getLastKwaliteit($machineno);
        $data['last'] = $mm->getLastTimeAPI($machineno);
        
        echo json_encode($data); 

    } else {
        // The value contains non-numeric characters
        $data = [
            'status' => 403,
            'message' => 'Not Allowed'
        
        ];
        echo json_encode($data);
        exit;
    }

} elseif($req == 'POST'){
   
    // Get the machine number from the query string
    $machineno = isset($_POST['machine']) ? $_POST['machine'] : '';
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    echo $action;

    // Check if $machineno contains only numbers
    if (preg_match('/^[0-9]+$/', $machineno) && $action == 'register') {
        $mm = new MachineManager();
        $data['persoon'] = $mm->getLastPersoon($machineno);
        $data['kwaliteit'] = $mm->getLastKwaliteit($machineno);
        $data['machine'] = $machineno;
        $data['verwijderd'] = '0';

        // Get the current timestamp using the time function
        $currentTime = time();
        // Format the timestamp as a human-readable time
        $formattedTime = date('Y-m-d H:i:s', $currentTime);
        $data['datum'] = $formattedTime;
        
        $result = $mm->addMachine($data);
        
        echo json_encode($result);


    } else {
        // The value contains non-numeric characters
        $data = [
            'status' => 403,
            'message' => 'Not Allowed'
        
        ];
        echo json_encode($data);
        exit;
    }
} else {
    $data = [
        'status' => 405,
        'message' => $req. ' Not Allowed'
    
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data); 
}

//echo json_encode(['requestmethod' =>  $req, 'status' => 'success', 'message' => 'Machine regsitratie geslaagd', 'person' => 'persoon1', 'machine' => 'machinne1', 'tijd' => '11:30:13 2024-02-05']);





?>