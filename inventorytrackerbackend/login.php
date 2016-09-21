<?php
$response = '';
header('Content-Type: application/json');
//header('Access-Control-Allow-Origin: *');
//header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Origin: http://run.plnkr.co');
header('Access-Control-Allow-Origin: http://localhost:8000');
ini_set('session.gc_maxlifetime',10);
session_start();
require_once '0700_inc/jwt_helper.php';
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
if(isset($request->username) && isset($request->password) && isset($request->token) && isset($request->platform)){
	require_once '0700_inc/common_inc.php';
	require_once '0700_inc/conn_inc.php';
	@$username = $request->username;
	@$password = $request->password;
	@$token = $request->token;
	@$platform = $request->platform;
	$user = login(@$username,@$password,@$token,@$platform);

	if($user == null){
		$response = json_encode("login failed");
	}else{
		$token = array();
		$token['userId'] = $user->userId;
		$token['userName'] = $user->userName;
		$token['token'] = @$token;
		$package = new stdClass();
		$package->secToken = JWT::encode($token, funMessage());
		$package->message = "login succeded. username:" . @$username . ", password:" . @$password . ", token:" . @$token . ", platform:" . @$platform . ", userId:" . $user->userId . ", first name:" . $user->firstName . ", last name:" . $user->lastName . ", user img:" . $user->userImg;	
		$package->user = $user;
		$response = json_encode($package);
	}
}
else{
	$response = json_encode("requirements not met");
}	
echo $response;
?>