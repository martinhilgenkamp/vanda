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
    $value = isset($_GET['value']) ? $_GET['value'] : '';

    // Check if $machineno contains only numbers
    if (preg_match('/^[0-9]+$/', $machineno)) {
       
        // Check if we want to know the time only or all info.
        if ($value == 'tijd'){
            $data['gereed'] = $mm->getLastTimeAPI($machineno);
            $data['picked'] = $mm->getLastTimePickup($machineno);
        } else if ($value == 'kwaliteit'){ 
            $data['kwaliteit'] = $mm->getLastKwaliteit($machineno);
        } else if ($value == 'persoon'){ 
            $data['persoon'] = $mm->getLastPersoon($machineno);
        } else {
            $data['persoon'] = $mm->getLastPersoon($machineno);
            $data['kwaliteit'] = $mm->getLastKwaliteit($machineno);
            $data['last'] = $mm->getLastTimeAPI($machineno);
            $data['picked'] = $mm->getLastTimePickup($machineno);
        }
        
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
    // Takes raw data from the request
    $json = file_get_contents('php://input');

    // Converts it into a PHP object
    $postdata = json_decode($json, false);

    // Check if $machineno contains only numbers
    if (preg_match('/^[0-9]+$/', $postdata->machine) && $postdata->action == 'register') {
        $mm = new MachineManager();
        $data['persoon'] = $mm->getLastPersoon($postdata->machine);
        $data['kwaliteit'] = $mm->getLastKwaliteit($postdata->machine);
        $data['machine'] = $postdata->machine;
        $data['verwijderd'] = '0';

        // Get the current timestamp using the time function
        $currentTime = time();
        // Format the timestamp as a human-readable time
        $formattedTime = date('Y-m-d H:i:s', $currentTime);
        $data['datum'] = $formattedTime;        
        $result = $mm->gereedMachine($data);
        
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