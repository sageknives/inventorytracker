<?php
$response = '';
header('Content-Type: application/json');
//header('Access-Control-Allow-Origin: *');
//header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Origin: http://run.plnkr.co');
header('Access-Control-Allow-Origin: http://localhost:8000');
ini_set('session.gc_maxlifetime',10);
session_start();
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
if(isset($request->username) && isset($request->password)){
	require_once '0700_inc/common_inc.php';
	require_once '0700_inc/conn_inc.php';
	@$username = $request->username;
	@$password = $request->password;
	if(register(@$username,@$password)){
		$message = "requirements met. username:" . @$username . ", password:" . @$password;
		$response = json_encode((string)$message);
	}else{
		$response = json_encode("registration failed");
	}
}
else{
	$response = json_encode("requirements not met");
}	
echo $response;
?>