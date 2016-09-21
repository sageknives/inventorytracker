<?php
$response = 'fun';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
//header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Origin: http://run.plnkr.co');
header('Access-Control-Allow-Origin: http://localhost:8000');
header('Access-Control-Allow-Origin: http://localhost:8100');
ini_set('session.gc_maxlifetime',10);
session_start();
require_once '0700_inc/jwt_helper.php';
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if (!isset($request->token)) {
	$response = json_encode("no token sent");
}

else{
	require_once '0700_inc/common_inc.php';
	require_once '0700_inc/conn_inc.php';
	$tokenObj = null;
	@$token = $request->token;
	$package = new stdClass();
	try{
		$tokenObj = JWT::decode(@$token, 'something_awesome');
	}catch(Exception $e){
		$package->error = "invalid token" + (string)$e;
	}
	if(!isset($package->error)){
		
		$package->message = "state:logged in, username:" . $tokenObj->userName;
		if($request->cmd == "updateCount"){
			$package->lastUpdated = updateItemCount($request->itemId, $request->itemCount, $tokenObj->userId, $request->objectType, $request->propertyType, $request->isRestock);
			$package->itemId = $request->itemId;
		}else if($request->cmd == "getLogs"){
			$package->logs = getLogs(3,$request->itemId);
		}else{
			$package->error = "command not recognized";
		}
		
		
	}
	$response = json_encode($package);
}
echo $response;
?>





