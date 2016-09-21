<?php
$response = '';
header('Content-Type: application/json');
//header('Access-Control-Allow-Origin: *');
//header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Origin: http://run.plnkr.co');
header('Access-Control-Allow-Origin: http://localhost:8000');
header('Access-Control-Allow-Origin: http://localhost:8100');
ini_set('session.gc_maxlifetime',10);
session_start();
require_once '0700_inc/jwt_helper.php';
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
//@$token = $request->token;
if (!isset($request->token)) {
	@$phone = $request->phone;
	@$password = $request->password;
	@$phone_token = $request->phone_token;
	@$os = $request->os;
	//needs register customer
	if(@$phone !== null && @$password !== null)
	{
		require_once '0700_inc/common_inc.php';
		require_once '0700_inc/conn_inc.php';
		$customer_id = checkCred(@$phone,@$password);
		if($customer_id != null)
		{
			//echo json_encode($request->android_token);
			//return;

			$myResult = updateCustomerToken(@$phone_token, $customer_id,@$os);
			
			$token = array();
			$token['customer_id'] = $customer_id;
			$token['phone'] = $request->phone;

			echo json_encode(JWT::encode($token, 'something_awesome'));	
			return;	
		}
		else{
			$response = json_encode("Invalid Login id is null");
		}
	}
	else{
		$response = json_encode("Invalid Login no user or pass");
	}
	
}
elseif($request->action !== null){
	@$action = $request->action;
	include '0700_inc/common_inc.php';
	include '0700_inc/conn_inc.php';
	include '0700_inc/class.model_inc.php';
	@$token = JWT::decode($request->token, 'something_awesome') ;
	if(@$token->phone != ""){
		if(@$action == "carstate"){
			$user = getUserOrderByUser(@$token->customer_id);
			
			//$user->result = sendNotification($user->token, $user->os, $user->title, "checking carstate", "alert",true);
			$response = json_encode($user);
		}
		elseif(@$action == 'addorder'){
			$order = addUserOrder($request->title,$request->startDate,$request->endDate,$request->message,$request->carModel,$request->orderState,$request->orderTime,$request->orderPrice,$request->customerId,$request->valetId);
			$user = getUserOrderByUser($request->customerId);
			sendNotification($user->android_token, $user->ios_token, $user->title, "added order", "alert",false);
		}
		elseif(@$action == 'updateorder'){

			$result = customerUpdateOrder(@$token->customer_id, $request->state);
			//$result = updateUserOrder($request->order_id, $request->order_state, $request->order_time, $request->order_price, $request->message, $request->title);
			//echo json_encode($result);
			//return;
			$user = getUserOrderByUser(@$token->customer_id);
			$user->result = $result;
			$valet = getValetById($user->valet_id);
			sendNotification($valet->token, $valet->os, "Vehicle Requested", "order updated", "alert",false);
			$response = json_encode($user);
		}
		elseif(@$action == 'getordersbyvalet'){
			$result = updateUserOrder($request->order_id, $request->order_state, $request->order_time, $request->order_price, $request->message, $request->title);
			$user = getUserOrderById($request->order_id);
			sendNotification($user->android_token, $user->ios_token, $user->title, "order updated", "alert",false);
		}
		else{
			$response =json_encode("not in carstate");
		}
	}else{
		$response = json_encode("invalid token");
	}
}
else
	$response = 'No Action Requested';
	
echo $response;
?>