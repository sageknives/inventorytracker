<?php
/*
inventory tracker functions
*/
/*
Register
*/
function register($username,$password){
 return true;
}
/*
Login
*/
function login($username,$password,$token,$platform){
	$user = null;
	$hashp = hash('haval256,4', $password);
	$iConn = conn("trigger");
	$sql="CALL userLogin('$username','$hashp')";
	$result = mysqli_query($iConn, $sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
	if (mysqli_num_rows($result) > 0) {
		$user = new stdClass();
		while ($row = mysqli_fetch_assoc($result)) {
			$user->userId = $row['userId'];
			$user->userName = $row['userName'];
			$user->firstName = $row['firstName'];
			$user->lastName = $row['lastName'];
			$user->userImg =$row['userImg'];
		}
	}
 	return $user;
}

function cloneUserData($userId){
	//echo 'in clone data';
	$iConn = conn("trigger");
	$sql="CALL cloneUserData('$userId')";
	$result = mysqli_query($iConn, $sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
	if (mysqli_num_rows($result) > 0) {
		//$collections = new stdClass();
		$collection = array();
		$i = 0;
		while ($row = mysqli_fetch_assoc($result)) {
			//echo $i;
			//create a collection object
			//isset($collection[$row['collectionId']]);
			if(!isset($collection[$row['collectionId']])){
				//echo "collection id " . $row['collectionId'] . "was not set";
				$collection[$row['collectionId']] = new stdClass();
				$collection[$row['collectionId']]->id = $row['collectionId'];
				$collection[$row['collectionId']]->location = $row['location'];
				$collection[$row['collectionId']]->name = $row['collectionName'];
				$collection[$row['collectionId']]->description = $row['collectionDescription'];
				$collection[$row['collectionId']]->img = $row['collectionImg'];
				$collection[$row['collectionId']]->categories = array();
				$collection[$row['collectionId']]->users = array();
			}
			if(!isset($collection[$row['collectionId']]->users[$row['cuId']])){
				$collection[$row['collectionId']]->users[$row['cuId']] = new stdClass();
				$collection[$row['collectionId']]->users[$row['cuId']]->id = $row['cuId'];
				$collection[$row['collectionId']]->users[$row['cuId']]->userName = $row['colUserName'];
				$collection[$row['collectionId']]->users[$row['cuId']]->firstName = $row['colFirstName'];
				$collection[$row['collectionId']]->users[$row['cuId']]->lastName = $row['colLastName'];
				$collection[$row['collectionId']]->users[$row['cuId']]->img = $row['colUserImg'];
				$collection[$row['collectionId']]->users[$row['cuId']]->userType = $row['colUserType'];
			}
			if(!isset($collection[$row['collectionId']]->categories[$row['categoryId']])){
				$collection[$row['collectionId']]->categories[$row['categoryId']] = new stdClass();
				$collection[$row['collectionId']]->categories[$row['categoryId']]->id = $row['categoryId'];
				$collection[$row['collectionId']]->categories[$row['categoryId']]->name = $row['categoryName'];
				$collection[$row['collectionId']]->categories[$row['categoryId']]->description = $row['categoryDescription'];
				$collection[$row['collectionId']]->categories[$row['categoryId']]->img = $row['categoryImg'];
				$collection[$row['collectionId']]->categories[$row['categoryId']]->items = array(); 
			}
			if(!isset($collection[$row['collectionId']]->categories[$row['categoryId']]->items[$row['itemId']])){
				$collection[$row['collectionId']]->categories[$row['categoryId']]->items[$row['itemId']] = new stdClass();
				$collection[$row['collectionId']]->categories[$row['categoryId']]->items[$row['itemId']]->id = $row['itemId'];
				$collection[$row['collectionId']]->categories[$row['categoryId']]->items[$row['itemId']]->name = $row['itemName'];
				$collection[$row['collectionId']]->categories[$row['categoryId']]->items[$row['itemId']]->description = $row['itemDescription'];
				$collection[$row['collectionId']]->categories[$row['categoryId']]->items[$row['itemId']]->count = $row['itemCount'];
				$collection[$row['collectionId']]->categories[$row['categoryId']]->items[$row['itemId']]->measurement = $row['itemMeasurement'];
				$collection[$row['collectionId']]->categories[$row['categoryId']]->items[$row['itemId']]->price = $row['itemPrice'];
				$collection[$row['collectionId']]->categories[$row['categoryId']]->items[$row['itemId']]->img = $row['itemImg'];
				$collection[$row['collectionId']]->categories[$row['categoryId']]->items[$row['itemId']]->lastUpdated = $row['itemLastUpdated'];
			}
			$i++;
		}
	}
	return $collection;

}

function updateItemCount($itemId, $itemCount, $userId, $objectType, $propertyType, $isRestock){
	$iConn = conn("trigger");
	$sql="CALL updateCount('$itemId', '$itemCount', '$userId', '$objectType', '$propertyType', '$isRestock')";
	$result = mysqli_query($iConn, $sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
	if (mysqli_num_rows($result) > 0) {
		$lastUpdated = null;
		while ($row = mysqli_fetch_assoc($result)) {
			$lastUpdated = $row['lastUpdated'];
		}
	}
	return $lastUpdated;
}
function getLogs($objectType, $id){
	$iConn = conn("trigger");
	$sql="CALL getLogs('$objectType','$id')";
	$result = mysqli_query($iConn, $sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
	if (mysqli_num_rows($result) > 0) {
		$logs = [];
		$i = 0;
		while ($row = mysqli_fetch_assoc($result)) {
			$logs[$i]->firstName = $row['firstName'];
			$logs[$i]->lastName = $row['lastName'];
			$logs[$i]->userImg =$row['userImg'];
			$logs[$i]->isRestock =$row['isRestock'];
			$logs[$i]->objectValue =$row['objectValue'];
			$logs[$i]->timeStamp = $row['thisTimeStamp'];
			$i++;
		}
	}
 	return $logs;
}

function JSdate($in,$type){
    if($type=='date'){
        //Dates are patterned 'yyyy-MM-dd'
        preg_match('/(\d{4})-(\d{2})-(\d{2})/', $in, $match);
    } elseif($type=='datetime'){
        //Datetimes are patterned 'yyyy-MM-dd hh:mm:ss'
        preg_match('/(\d{4})-(\d{2})-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/', $in, $match);
    }
     
    $year = (int) $match[1];
    $month = (int) $match[2] - 1; // Month conversion between indexes
    $day = (int) $match[3];
     
    if ($type=='date'){
        return "Date($year, $month, $day)";
    } elseif ($type=='datetime'){
        $hours = (int) $match[4];
        $minutes = (int) $match[5];
        $seconds = (int) $match[6];
        return "Date($year, $month, $day, $hours, $minutes, $seconds)";    
    }
}
function funMessage(){
	return 'something_awesome';
}
/**
 * Checks log in
 */
function checkCred($phone, $password) {
	$hashp = hash('haval256,4', $password);
	$phone = htmlentities($phone);
	$sql = 'Select c.customer_id, c.phone, c.password from customer as c where c.phone =  "' . $phone . '"';

	$customer_id = '';
	$hashedPassword = null;
	$iConn = conn();
	$result = mysqli_query($iConn, $sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
	if (mysqli_num_rows($result) > 0) {

		while ($row = mysqli_fetch_assoc($result)) {
			$hashedPassword = $row['password'];
			$customer_id = $row['customer_id'];
		}
	}
	@mysqli_free_result($result);
	if ($hashedPassword == $hashp)
		return $customer_id;
	return null;
}
/**
 * Checks log in
 */
function checkValetCred($username, $password) {
	$hashp = hash('haval256,4', $password);
	$username = htmlentities($username);
	$sql = 'Select v.valet_id, v.password from valet as v where v.username =  "' . $username . '"';

	$valetId = '';
	$hashedPassword = null;
	$iConn = conn();
	$result = mysqli_query($iConn, $sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
	if (mysqli_num_rows($result) > 0) {

		while ($row = mysqli_fetch_assoc($result)) {
			$hashedPassword = $row['password'];
			$valetId = $row['valet_id'];
		}
	}
	@mysqli_free_result($result);
	if ($hashedPassword == $hashp)
		return $valetId;
	return null; 
}

/**
 * updates user tokens
 */
function updateCustomerToken($phone_token, $customer_id,$os) {

	$sql1 = 'UPDATE `device` d inner join `userdevice` ud on ud.device_id = d.device_id SET d.token="'.$phone_token.'", d.os="'.$os.'" WHERE ud.customer_id = ' .$customer_id;
	//update Reservation r
	//inner join GuestInfo g
  	//  on g.GuestID = r.GuestID 
	//set r.DateEnd = '2014-01-20' 
	//where g.GuestFName = 'JAKE'
	$result = "false";
	$iConn = conn("insert");
	try {
		/* switch autocommit status to FALSE. Actually, it starts transaction */
		$iConn -> autocommit(FALSE);

		$res = $iConn -> query($sql1);
		if ($res === false) {
			throw new Exception('Wrong SQL: ' . $sql1 . ' Error: ' . $iConn -> error);
		}
		$result = "true";
		$iConn -> commit();

	} catch (Exception $e) {
		$result = '12Transaction failed: ' . $e -> getMessage();

		$iConn -> rollback();
	}

	/* switch back autocommit status */
	$iConn -> autocommit(TRUE);

	@mysqli_free_result($res);
	return $result;
}
function updateValetToken($phone_token, $valet_id,$os) {
	$sql1 = 'UPDATE `device` d inner join `userdevice` ud on ud.device_id = d.device_id SET d.token="'.$phone_token.'", d.os="'.$os.'" WHERE ud.valet_id = ' .$valet_id;
	$result = "false";
	$iConn = conn("insert");
	try {
		/* switch autocommit status to FALSE. Actually, it starts transaction */
		$iConn -> autocommit(FALSE);

		$res = $iConn -> query($sql1);
		if ($res === false) {
			throw new Exception('Wrong SQL: ' . $sql1 . ' Error: ' . $iConn -> error);
		}
		$result = "true";
		$iConn -> commit();

	} catch (Exception $e) {
		$result = '12Transaction failed: ' . $e -> getMessage();

		$iConn -> rollback();
	}

	/* switch back autocommit status */
	$iConn -> autocommit(TRUE);

	@mysqli_free_result($res);
	return $result;
}

// gets all user info
function getUserOrderByUser($customer_id) {
	$customer_id = htmlentities($customer_id);
	$sql = 'Select c.customer_id, c.phone, c.first_name, c.last_name,d.token,d.os,v.make,v.model,v.image,v.description,v.license_number,o.order_id, o.order_state,o.valet_id,o.order_price, o.order_time, s.title, s.header,s.button_text,s.message 
FROM `orders` o
LEFT JOIN `state` s ON o.order_state = s.state_id
LEFT JOIN `vehicle` v ON o.vehicle_id = v.vehicle_id
LEFT JOIN `customer` c ON o.customer_id = c.customer_id
LEFT JOIN `userdevice` ud ON c.customer_id = ud.customer_id
LEFT JOIN `device` d ON ud.device_id = d.device_id
where o.customer_id ='. $customer_id;

	$iConn = conn();

	$result = mysqli_query($iConn, $sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
	$user = new stdClass();
	if (mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_assoc($result)) {
			$user->customer_id = $row['customer_id'];
			$user->phone = $row['phone'];
			$user->first_name = $row['first_name'];
			$user->last_name = $row['last_name'];
			$user->token = $row['token'];
			$user->os = $row['os'];
			$user->make = $row['make'];
			$user->model = $row['model'];
			$user->image = $row['image'];
			$user->description = $row['description'];
			$user->license_number = $row['license_number'];
			$user->order_id = $row['order_id'];
			$user->order_state = $row['order_state'];
			$user->valet_id = $row['valet_id'];
			$user->time = $row['order_time'];
			$user->price = $row['order_price'];	
			$user->order_time = $row['order_time'];			
			$user->title = $row['title'];
			$user->header = $row['header'];			
			$user->message = $row['message'];	
			$user->button_text = $row['button_text'];		
		}
	}
	$myresult = $result;
	@mysqli_free_result($result);
	//return $myresult;
	return $user;
}


// gets all user info
function getValetById($valet_id) {
	$valet_id = htmlentities($valet_id);
	$sql = 'Select v.valet_id, v.first_name, v.last_name, d.token, d.os  
	FROM `valet` v
	LEFT JOIN `userdevice` ud ON ud.valet_id = v.valet_id
	LEFT JOIN `device` d ON ud.device_id = d.device_id WHERE v.valet_id ='. $valet_id;

	$iConn = conn();

	$result = mysqli_query($iConn, $sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
	$valet = new stdClass();
	if (mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_assoc($result)) {
			$valet->valet_id = $row['valet_id'];
			$valet->first_name = $row['first_name'];
			$valet->last_name = $row['last_name'];
			$valet->token = $row['token'];
			$valet->os = $row['os'];	
		}
	}
	$myresult = $result;
	@mysqli_free_result($result);
	//return $myresult;
	return $valet;
}
function getUserOrderById($order_id) {
	$sql = 'Select c.customer_id, c.phone, c.first_name, c.last_name,c.android_token,c.ios_token,o.order_id, o.car_model, o.order_state, '+
	'o.valet_id,o.order_price, o.order_time, o.title, o.message from customer as c join orders as o join on o.customer_id = '+
	'c.customer_id where o.order_id = "' . $order_id . '"';

	$iConn = conn();

	$result = mysqli_query($iConn, $sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
	$user = new stdClass();
	if (mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_assoc($result)) {
			$user->customer_id = $row['customer_id'];
			$user->phone = $row['phone'];
			$user->first_name = $row['first_name'];
			$user->last_name = $row['last_name'];
			$user->android_token = $row['android_token'];
			$user->ios_token = $row['ios_token'];
			$user->car_model = $row['car_model'];
			$user->order_id = $row['order_id'];
			$user->order_state = $row['order_state'];
			$user->valet_id = $row['valet_id'];
			$user->time = $row['order_time'];
			$user->price = $row['order_price'];			
			$user->title = $row['title'];			
			$user->message = $row['message'];			
		}
	}
	$myresult = $result;
	@mysqli_free_result($result);
	//return $myresult;
	return $user;
}
/*function updateUserOrder($order_id, $order_state,$order_time, $order_price,$message,$title){
	$sql1 = 'UPDATE `order` SET `order_state`="' . $order_state . '", SET `order_time`="' . $order_time . '", SET `order_price`="' . $order_price . '", SET `message`="' . $message . '", SET `title`="' . $title . '", WHERE order_id = ' . $order_id;
	
	$result = "false";
	$iConn = conn("insert");
	try {
		/* switch autocommit status to FALSE. Actually, it starts transaction */
		/*$iConn -> autocommit(FALSE);

		$res = $iConn -> query($sql1);
		if ($res === false) {
			throw new Exception('Wrong SQL: ' . $sql1 . ' Error: ' . $iConn -> error);
		}
		$result = "true";
		$iConn -> commit();

	} catch (Exception $e) {
		$result = '12Transaction failed: ' . $e -> getMessage();

		$iConn -> rollback();
	}

	/* switch back autocommit status */
	/*$iConn -> autocommit(TRUE);

	@mysqli_free_result($res);
	return $result;
}
*/
function addUserOrder($title,$startDate,$endDate,$message,$carModel,$orderState,$orderTime,$orderPrice,$phone,$valetId){
	$sql0 = 'SELECT customer_id,android_token,ios_token FROM `customer` WHERE phone = ' . $phone;
	$order = new stdClass();	
	$iConn = conn('insert');

	try {
		/* switch autocommit status to FALSE. Actually, it starts transaction */
		$iConn -> autocommit(FALSE);
		$res = $iConn -> query($sql0);
		if ($res === false) {
			throw new Exception('Wrong SQL0: ' . $sql0 . ' Error: ' . $iConn -> error);
		}
		if (mysqli_num_rows($res) > 0) {
			while ($row = mysqli_fetch_assoc($res)) {
				$order->customer_id = $row['customer_id'];
				$order->android_token = $row['android_token'];
				$order->ios_token = $row['ios_token'];
			}
		}
		$sql1 = 'INSERT INTO orders VALUES (null,'.$title.','.$startDate.','.$endDate.','.$message.','.$carModel.','.$orderState.','.$orderTime.','.$orderPrice.','.$order->customer_id.','.$valet_id.',)';

		$res = $iConn -> query($sql1);
		if ($res === false) {
			throw new Exception('Wrong SQL1: ' . $sql1 . ' Error: ' . $iConn -> error);
		}
		$order->order_id = $iConn -> insert_id;
		$order->title = $title;
		$order->startDate = $startDate;
		$order->endDate = $endDate;
		$order->message = $message;
		$order->carModel = $carModel;
		$order->orderState = $orderState;
		$order->orderTime = $orderTime;
		$order->orderPrice = $orderPrice;
		$order->valetId = $valetId;

		$iConn -> commit();

	} catch (Exception $e) {
		echo 'Transaction failed: ' . $e -> getMessage();

		$iConn -> rollback();
	}

	/* switch back autocommit status */
	$iConn -> autocommit(TRUE);

	@mysqli_free_result($res);
	//return true;
	return $order;
}

function getValetOrders($valetId){
	$sql = 'Select c.customer_id, c.phone, c.first_name, c.last_name, o.car_model, o.order_state, o.order_price, o.order_time, o.title, o.message FROM `customer` c left join `order` o on c.customer_id = o.customer_id WHERE o.valet_id ='. $valetId;

	$iConn = conn();

	$result = mysqli_query($iConn, $sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
	$orders = array();
	$i = 0;
	if (mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_assoc($result)) {
			$orders[$i]->customer_id = $row['customer_id'];
			$orders[$i]->phone = $row['phone'];
			$orders[$i]->first_name = $row['first_name'];
			$orders[$i]->last_name = $row['last_name'];
			$orders[$i]->car_model = $row['car_model'];
			$orders[$i]->order_id = $row['order_id'];
			$orders[$i]->order_state = $row['order_state'];
			$orders[$i]->time = $row['order_time'];
			$orders[$i]->price = $row['order_price'];			
			$orders[$i]->title = $row['title'];			
			$orders[$i]->message = $row['message'];	
			$i++;		
		}
	}
	$myresult = $result;
	@mysqli_free_result($result);
	//return $myresult;
	return $orders;
}
function updateUserOrder($order_id,$state,$time,$price){
	$sql1 = 'UPDATE `orders` o SET o.order_state="'.$state.'" , o.order_time="'.$time.'" WHERE o.order_id = ' .$order_id;
	$result = "false";
	$iConn = conn("insert");
	try {
		/* switch autocommit status to FALSE. Actually, it starts transaction */
		$iConn -> autocommit(FALSE);

		$res = $iConn -> query($sql1);
		if ($res === false) {
			throw new Exception('Wrong SQL: ' . $sql1 . ' Error: ' . $iConn -> error);
		}
		$result = "true";
		$iConn -> commit();

	} catch (Exception $e) {
		$result = '12Transaction failed: ' . $e -> getMessage();

		$iConn -> rollback();
	}

	/* switch back autocommit status */
	$iConn -> autocommit(TRUE);

	@mysqli_free_result($res);
	return $result;
}
function customerUpdateOrder($customer_id, $state){
	$sql1 = 'UPDATE `orders` o SET o.order_state="'.$state.'" WHERE o.customer_id = ' .$customer_id;
	$result = "false";
	$iConn = conn("insert");
	try {
		/* switch autocommit status to FALSE. Actually, it starts transaction */
		$iConn -> autocommit(FALSE);

		$res = $iConn -> query($sql1);
		if ($res === false) {
			throw new Exception('Wrong SQL: ' . $sql1 . ' Error: ' . $iConn -> error);
		}
		$result = "true";
		$iConn -> commit();

	} catch (Exception $e) {
		$result = '12Transaction failed: ' . $e -> getMessage();

		$iConn -> rollback();
	}

	/* switch back autocommit status */
	$iConn -> autocommit(TRUE);

	@mysqli_free_result($res);
	return $result;
}


function sendNotification($token, $os, $title, $funstate, $alert,$isCustomer)
{
	if($os !== "android") return;
	$appId = 'f4a71a36';
	$curlId = 'e3488afb7d6d73095c1eabc164848880c2ed0169be7ea878';
	if ($isCustomer == false) {
		$appId = 'ab0c37a3';
		$curlId = '1f062a505ff2018977048684f818772b4aa96c5907ff9a79';
	}
 	$cmd= 'curl -u '.$curlId.': -H "Content-Type: application/json" -H "X-Ionic-Application-Id: '.$appId.'" https://push.ionic.io/api/v1/push -d \'{"tokens": ["'.$token.'"],"production": true, "notification":{ "alert":"'.$alert.'", "title": "'.$title.'", "android": {"payload": {"title": "'.$title.'"}}, "ios": {"payload": {"title": "'.$title.'"}}}}\'';
	exec($cmd,$result);
	return $result;
}




/**
 * gets the whole tree from the database for the user and turns it into a tree USING
 */
function getTodoTree($userId, $homeDir) {
	$sql = 'SELECT tl.id, tl.title, tl.parent, tl.completed, tl.due_date, tl.last_updated, ' . 'ti.desc, ti.created_by, ti.assigned_to, timg.img_src ' . 'FROM todo_list as tl ' . 'left join todo_info as ti on ti.todo_id = tl.id ' . 'left join todo_img as timg on tl.id = timg.todo_id ' . 'left join todo_tree as tt on tl.id = tt.tree_id ' . 'left join user as us on us.user_id = tt.user_id ' . 'where us.user_id = ' . $userId . ' order by tl.id asc';

	$iConn = conn();
	$result = mysqli_query($iConn, $sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
	if (mysqli_num_rows($result) > 0) {
		$count = 1;
		$wasNull = false;
		$model = array();
		$currentId = 0;
		$node = null;
		while ($row = mysqli_fetch_assoc($result)) {
			if ($currentId != $row['id']) {
				if ($row['desc'] == null) {
					$node = new Node($row['id'], $row['title'], $row['parent'], $row['completed'], $row['due_date'], $row['last_updated'], "", $row['created_by'], $row['assigned_to']);

				} else {
					$node = new Node($row['id'], $row['title'], $row['parent'], $row['completed'], $row['due_date'], $row['last_updated'], $row['desc'], $row['created_by'], $row['assigned_to']);
				}
				if ($row['img_src'] != null)
					$node -> addImage($row['img_src']);
				$currentId = $node -> getId();
				$model[] = $node;
			} else if ($row['id'] == $currentId)
				$node -> addImage($row['img_src']);

		}
		$homedir = makeTree($model, $homeDir);
	}
	@mysqli_free_result($result);
	return $homedir;
}

function getSqlData($sql, $model) {
	$iConn = conn();

	$result = mysqli_query($iConn, $sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));

	if (mysqli_num_rows($result) > 0) {
		$count = 1;
		$wasNull = false;
		$todos = array();
		$todos[] = $model;
		$model = array();
		while ($row = mysqli_fetch_assoc($result)) {

			//only gets one version of each node
			for ($i = 0; $i < count($row); $i++) {
				if ($row['lev' . (($i % 8) + 1) . 'title'] != NULL) {
					if (count($model) == 0)
						$model[] = new Node($row['lev' . (($i % 8) + 1) . 'id'], $row['lev' . (($i % 8) + 1) . 'title'], $row['lev' . (($i % 8) + 1) . 'parent'], $row['lev' . (($i % 8) + 1) . 'completed'], $row['lev' . (($i % 8) + 1) . 'duedate'], $row['lev' . (($i % 8) + 1) . 'lastupdated']);
					$found = false;
					for ($j = 0; $j < count($todos); $j++) {
						if ($todos[$j] == $row['lev' . (($i % 8) + 1) . 'id']) {
							$found = true;
							break;
						}
					}
					if (!$found) {
						$model[] = new Node($row['lev' . (($i % 8) + 1) . 'id'], $row['lev' . (($i % 8) + 1) . 'title'], $row['lev' . (($i % 8) + 1) . 'parent'], $row['lev' . (($i % 8) + 1) . 'completed'], $row['lev' . (($i % 8) + 1) . 'duedate'], $row['lev' . (($i % 8) + 1) . 'lastupdated']);
						$todos[] = $row['lev' . (($i % 8) + 1) . 'id'];
					}
				}
			}

		}

		$root = $model[0] -> getCopy();
		$homedir = makeTree($model, $root);
	}
	@mysqli_free_result($result);
	return $homedir;
}

function createSQL($value, $depth) {
	$sql = 'SELECT ';
	for ($i = 0; $i < $depth; $i++) {
		$sql .= 't' . ($i + 1) . '.id as lev' . ($i + 1) . 'id, t' . ($i + 1) . '.title as lev' . ($i + 1) . 'title, t' . ($i + 1) . '.parent  as lev' . ($i + 1) . 'parent, t' . ($i + 1) . '.completed as lev' . ($i + 1) . 'completed, t' . ($i + 1) . '.due_date as lev' . ($i + 1) . 'duedate, t' . ($i + 1) . '.last_updated as lev' . ($i + 1) . 'lastupdated';
		if ($i < $depth - 1)
			$sql .= ', ';
	}
	$sql .= ' FROM todo_list AS t1 ';
	for ($i = 0; $i < $depth - 1; $i++) {
		$sql .= 'LEFT JOIN todo_list AS t' . ($i + 2) . ' ON t' . ($i + 2) . '.parent = t' . ($i + 1) . '.id ';
	}
	$sql .= 'WHERE t1.id = ' . $value . '';

	$result = getSqlData($sql, $value);
	return $result;
}

function getModelSqlData($sql, $model) {
	$iConn = conn();

	$result = mysqli_query($iConn, $sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));

	if (mysqli_num_rows($result) > 0) {
		$count = 1;
		$wasNull = false;
		$todos = array();
		$todos[] = $model;
		$model = array();
		while ($row = mysqli_fetch_assoc($result)) {
			//only gets one version of each node
			for ($i = 0; $i < count($row); $i++) {
				if ($row['lev' . (($i % 8) + 1) . 'title'] != NULL) {
					if (count($model) == 0)
						$model[] = new Node($row['lev' . (($i % 8) + 1) . 'id'], $row['lev' . (($i % 8) + 1) . 'title'], $row['lev' . (($i % 8) + 1) . 'parent'], $row['lev' . (($i % 8) + 1) . 'completed'], $row['lev' . (($i % 8) + 1) . 'duedate'], $row['lev' . (($i % 8) + 1) . 'lastupdated']);
					$found = false;
					for ($j = 0; $j < count($todos); $j++) {
						if ($todos[$j] == $row['lev' . (($i % 8) + 1) . 'id']) {
							$found = true;
							break;
						}
					}
					if (!$found) {
						$model[] = new Node($row['lev' . (($i % 8) + 1) . 'id'], $row['lev' . (($i % 8) + 1) . 'title'], $row['lev' . (($i % 8) + 1) . 'parent'], $row['lev' . (($i % 8) + 1) . 'completed'], $row['lev' . (($i % 8) + 1) . 'duedate'], $row['lev' . (($i % 8) + 1) . 'lastupdated']);
						$todos[] = $row['lev' . (($i % 8) + 1) . 'id'];
					}
				}
			}

		}
	}
	@mysqli_free_result($result);
	return $model;
}

function createModelSQL($value, $depth) {
	$sql = 'SELECT ';
	for ($i = 0; $i < $depth; $i++) {
		$sql .= 't' . ($i + 1) . '.id as lev' . ($i + 1) . 'id, t' . ($i + 1) . '.title as lev' . ($i + 1) . 'title, t' . ($i + 1) . '.parent  as lev' . ($i + 1) . 'parent, t' . ($i + 1) . '.completed as lev' . ($i + 1) . 'completed, t' . ($i + 1) . '.dueDate as lev' . ($i + 1) . 'duedate, t' . ($i + 1) . '.lastUpdated as lev' . ($i + 1) . 'lastupdated';
		if ($i < $depth - 1)
			$sql .= ', ';
	}
	$sql .= ' FROM todo_list AS t1 ';
	for ($i = 0; $i < $depth - 1; $i++) {
		$sql .= 'LEFT JOIN todo_list AS t' . ($i + 2) . ' ON t' . ($i + 2) . '.parent = t' . ($i + 1) . '.id ';
	}
	$sql .= 'WHERE t1.id = ' . $value . '';

	$result = getModelSqlData($sql, $value);
	return $result;
}

function getIdFromTitle($title) {
	$title = htmlentities($title);
	$sql = 'Select tl.id from todo_list as tl where tl.title = "' . $title . '"';

	$iConn = conn();

	$result = mysqli_query($iConn, $sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
	$id = '';
	if (mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_assoc($result)) {
			$id = $row['id'];
		}
	}
	@mysqli_free_result($result);
	return $id;
}

function getListInfo($id) {
	$id = htmlentities($id);
	$sql = 'Select tl.id, tl.title, tl.parent, tl.completed,tl.last_updated,ti.desc, img.img_src from todo_list as tl 
left join todo_info as ti on tl.id = ti.todo_id 
left join todo_img as img on tl.id = img.todo_id 
where tl.parent = "' . $id . '" or tl.id = "' . $id . '" order by tl.id asc';
	$iConn = conn();

	$result = mysqli_query($iConn, $sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
	$parent = null;
	$view = null;
	$currentId = null;
	if (mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_assoc($result)) {
			if ($parent == null) {
				$parent = new View($row['id'], $row['title'], $row['parent'], $row['completed'], $row['desc'], $row['last_updated']);
				if ($row['img_src'] != null)
					$parent -> addImage($row['img_src']);
				$currentId = $row['id'];
			} else if ($row['id'] == $currentId) {
				if ($parent -> getId() == $currentId)
					$parent -> addImage($row['img_src']);
				else
					$view -> addImage($row['img_src']);
			} else {
				$view = new View($row['id'], $row['title'], $row['parent'], $row['completed'], $row['desc'], $row['last_updated']);
				if ($row['img_src'] != null)
					$view -> addImage($row['img_src']);
				$currentId = $row['id'];
				$parent -> addChild($view);
			}
		}
	}
	@mysqli_free_result($result);
	return $parent;
}

function makeView($view, $stepNum) {
	$contentView = '';
	$checked = '';
	if ($view != null && $view -> isCompleted())
		$checked = "checked";

	$contentView .= '<div id="view-' . $view -> getId() . '">
<h2 class="title">' . $stepNum . $view -> getTitle() . '</h2>
<section class="col12 list-block background">
	<div class="li-obj">';

	$images = $view -> getImages();
	if (count($images) > 0) {
		$contentView .= '
	<div class="li-obj-l col7">
		<img class="main-img" src="' . $images[0] . '" />
	</div>
	';
	}
	$contentView .= '<div class="li-obj-r col5">
	<header>';
	if (count($images) > 1) {
		for ($i = 0; $i < count($images); $i++) {
			$contentView .= '<img src="' . $images[$i] . '" />';
		}
		$contentView .= '<br class="clear"/>';
	}
	$contentView .= '
	<p>' . $view -> getDesc() . '</p>
</header>
	</div>
		<footer class="col5">
			<div class="obj-info col8">
				<p>5 of 7</p>
				<p>Complete</p>
			</div>
			<div class="obj-action col3">
				<label><input type="checkbox" ' . $checked . '>Mark As Done</label>
				<p class="show-info" href="comments" id="0">Comments/WorkLog</p>
			</div>
		</footer>
		<br class="clear"/>
	</div>
</section>
<div id="count0">';
	//$contentView .= makeComments($view);
	$contentView .= '
</div>
</div>
<br class="clear">
';
	return $contentView;
}

function makeComments($view) {
	$content = '
<section class="obj-updates col12 background">
	<div class="obj-comments col7">
		<h3>Comments:</h3>
		<p>4/16/2014</p>
		<p>Tim</p>
		<p>I think we should change the title of this to Sub subtitle?</p>
		<textarea rows="4"></textarea>
	</div>
	<div class="obj-log col5">
		<h3>Notifications:</h3>
		<h2 class="close">X</h2>
		<p>SubTask1 finished by Tim on 4/14/2014</p>
	</div>
</section>
';
	return $content;
}

function getViewInfo($id) {
	$id = htmlentities($id);
	$sql = 'Select tl.id, tl.title, tl.parent, tl.completed,tl.due_date,tl.last_updated,ti.desc, img.img_src from todo_list as tl join todo_info as ti on tl.id = ti.todo_id left join todo_img as img on tl.id = img.todo_id where tl.id = "' . $id . '"';
	$iConn = conn();

	$result = mysqli_query($iConn, $sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
	$view = null;
	if (mysqli_num_rows($result) > 0) {
		$count = 0;
		while ($row = mysqli_fetch_assoc($result)) {
			if ($count == 0) {
				$view = new View($row['id'], $row['title'], $row['parent'], $row['completed'], $row['desc'], $row['due_date'], $row['last_updated']);
			}
			if ($row['img_src'] != null)
				$view -> addImage($row['img_src']);

			$count++;
		}
	}
	@mysqli_free_result($result);
	return $view;
}

function getViewInfo2($id) {
	$id = htmlentities($id);
	$sql = 'Select tl.id, tl.title, tl.parent, tl.completed,tl.due_date,tl.last_updated,ti.desc,ti.created_by,ti.assigned_to,img.img_src from todo_list as tl join todo_info as ti on tl.id = ti.todo_id left join todo_img as img on tl.id = img.todo_id where tl.id = "' . $id . '"';
	$iConn = conn();

	$result = mysqli_query($iConn, $sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
	$node = null;
	if (mysqli_num_rows($result) > 0) {
		$count = 0;
		while ($row = mysqli_fetch_assoc($result)) {
			if ($count == 0) {
				$node = new Node($row['id'], $row['title'], $row['parent'], $row['completed'], $row['due_date'], $row['last_updated']);
				$node -> fullConstruct($row['desc'], $row['created_by'], $row['assigned_to']);
			}
			if ($row['img_src'] != null)
				$node -> addImage($row['img_src']);

			$count++;
		}
	}
	@mysqli_free_result($result);
	return $node;
}

function createULS($node, $firstelement, $firstId) {
	$children = $node -> getChildren();
	if (count($children) > 0) {
		$ulId = $node -> getId();
		$h4Id = $node -> getParent();
		if ($firstId) {
			$ulId = "todo-nav";
		}
		if ($h4Id == 1)
			$h4Id = 'todo-nav';
		$parentTitle = $node -> getTitle();
		if (strlen($parentTitle) > 20)
			$parentTitle = substr($parentTitle, 0, 16) . "...";
		echo '<ul id="list-' . $ulId . '" class="' . $firstelement . ' top-nav-item todolist text-nav todolistnav"><h4 class="back-navigation" href="' . $h4Id . '"><-' . $parentTitle . '<img href="' . $ulId . '" class="add-item nav-image" src="images/add.png"></h4>';
		$firstelement = '';
		$firstId = false;
		for ($i = 0; $i < count($children); $i++) {
			$childTitle = $children[$i] -> getTitle();
			if (strlen($childTitle) > 20)
				$childTitle = substr($children[$i] -> getTitle(), 0, 20) . "...";

			if (count($children[$i] -> getChildren()) > 0) {
				$completed = 'checked.png';
				$subChildren = $children[$i] -> getChildren();
				for ($j = 0; $j < count($subChildren); $j++) {
					if (!$subChildren[$j] -> isCompleted()) {
						$completed = 'blank.gif';
						break;
					}
				}
				echo '<li>
					<img class="child-status nav-image" src="images/' . $completed . '" />
					<p class="todo-navigation todo-nav-title" href="' . $children[$i] -> getId() . '">' . $childTitle . '</p>
					<p class="ajax-request todo-nav-info">
						<a href="#todolist-' . $children[$i] -> getTitle() . '">
							<img class="child-info nav-image" src="images/view.png" />
						</a>
					</p>
				</li>';
			} else {
				$completed = '';
				if ($children[$i] -> isCompleted())
					$completed = 'checked';
				echo '<li>
						<input class="css-checkbox" type="checkbox" name="todo-item" value="' . $children[$i] -> getId() . '" ' . $completed . '>
						<p class="ajax-request todo-nav-title">
							<a href="#todoview-' . $children[$i] -> getTitle() . '">' . $childTitle . '</a>
						</p>
						<img href="' . $children[$i] -> getId() . '" class="add-sub-item child-status nav-image" src="images/addsub.png" />
						<br class="clear" />
					</li>';
			}
		}
		echo '</ul>';
		for ($i = 0; $i < count($children); $i++) {
			createULS($children[$i], $firstelement, '');
		}

	}
}

/**
 * adds new todo item to db USING
 */
function addNewTodoItemto($treeId, $title, $desc, $dueDate, $lastUpdated)//,$userIds)
{
	$title = htmlentities($title);
	$treeId = htmlentities($treeId);
	$desc = htmlentities($desc);
	$sql0 = 'SELECT user_id FROM `todo_tree` WHERE tree_id = ' . $treeId;
	$sql1 = 'INSERT INTO `todo_list`(`id`, `title`, `parent`, `completed`, `due_date`, `last_updated`) VALUES (null,"' . $title . '",' . $treeId . ',0,"' . $dueDate . '","' . $lastUpdated . '")';
	$newTodoId;
	$sql2 = 'INSERT INTO `todo_info`(`id`, `desc`, `todo_id`, `created_by`, `assigned_to`) VALUES (null,"' . $desc . '",LAST_INSERT_ID(),1,0)';
	
	$iConn = conn('insert');

	try {
		$users = array();
		/* switch autocommit status to FALSE. Actually, it starts transaction */
		$iConn -> autocommit(FALSE);
		$res = $iConn -> query($sql0);
		if ($res === false) {
			throw new Exception('Wrong SQL0: ' . $sql0 . ' Error: ' . $iConn -> error);
		}
		if (mysqli_num_rows($res) > 0) {
			while ($row = mysqli_fetch_assoc($res)) {
				$users[] = $row['user_id'];
			}
		}

		$res = $iConn -> query($sql1);
		if ($res === false) {
			throw new Exception('Wrong SQL1: ' . $sql1 . ' Error: ' . $iConn -> error);
		}
		$newTodoId = $iConn -> insert_id;
		$res = $iConn -> query($sql2);
		if ($res === false) {
			throw new Exception('Wrong SQL2: ' . $sql2 . ' Error: ' . $iConn -> error);
		}
		$sql3 = 'INSERT INTO `todo_tree`(`user_id`, `tree_id`) VALUES ';
		for ($i = 0; $i < count($users); $i++) {
			$sql3 .= '(' . $users[$i] .','.$newTodoId . ')';
			if($i < count($users)-1) $sql3 .= ',';
			else $sql3 .= ';';
		}
		$res = $iConn->query($sql3);
		if($res === false) {
		  throw new Exception('Wrong SQL3: ' . $sql3 . ' Error: ' . $iConn->error);
		}

		$iConn -> commit();
		return $newTodoId;

	} catch (Exception $e) {
		echo 'Transaction failed: ' . $e -> getMessage();

		$iConn -> rollback();
	}

	/* switch back autocommit status */
	$iConn -> autocommit(TRUE);

	@mysqli_free_result($res);
	//return true;
}

/**
 * update todo item to db USING
 */
function updateTodoItemto($treeId, $title, $desc, $dueDate, $lastUpdated)//,$userIds)
{
	$title = htmlentities($title);
	$treeId = htmlentities($treeId);
	$desc = htmlentities($desc);
	$sql0 = 'SELECT user_id FROM `todo_tree` WHERE tree_id = ' . $treeId;
	$sql1 = 'UPDATE `todo_list` SET `title`="' . $title . '", `last_updated`="' . $lastUpdated . '", `due_date`="' . $dueDate . '" WHERE id = ' . $treeId;


	$sql2 = 'UPDATE `todo_info` SET `desc`="' . $desc . '" WHERE todo_id = ' . $treeId;
	
	$iConn = conn('insert');

	try {
		$users = array();
		/* switch autocommit status to FALSE. Actually, it starts transaction */
		$iConn -> autocommit(FALSE);
		$res = $iConn -> query($sql0);
		if ($res === false) {
			throw new Exception('Wrong SQL0: ' . $sql0 . ' Error: ' . $iConn -> error);
		}
		if (mysqli_num_rows($res) > 0) {
			while ($row = mysqli_fetch_assoc($res)) {
				$users[] = $row['user_id'];
			}
		}
		$res = $iConn -> query($sql1);
		if ($res === false) {
			throw new Exception('Wrong SQL1: ' . $sql1 . ' Error: ' . $iConn -> error);
		}
		$res = $iConn -> query($sql2);
		if ($res === false) {
			throw new Exception('Wrong SQL2: ' . $sql2 . ' Error: ' . $iConn -> error);
		}
		
		$iConn -> commit();
		return $treeId;

	} catch (Exception $e) {
		return 'Transaction failed: ' . $e -> getMessage();

		$iConn -> rollback();
	}

	/* switch back autocommit status */
	$iConn -> autocommit(TRUE);

	@mysqli_free_result($res);
	//return true;
}

/**
 * updates database with completed or uncompleted
 */
function updateTodoCompletion($treeId, $completed, $lastUpdated) {
	$sql1 = 'UPDATE `todo_list` SET `completed`=' . $completed . ', `last_updated`="' . $lastUpdated . '" WHERE id = ' . $treeId;
	$iConn = conn("insert");
	try {
		/* switch autocommit status to FALSE. Actually, it starts transaction */
		$iConn -> autocommit(FALSE);

		$res = $iConn -> query($sql1);
		if ($res === false) {
			throw new Exception('Wrong SQL: ' . $sql1 . ' Error: ' . $iConn -> error);
		}
		$iConn -> commit();

	} catch (Exception $e) {
		echo 'Transaction failed: ' . $e -> getMessage();

		$iConn -> rollback();
	}

	/* switch back autocommit status */
	$iConn -> autocommit(TRUE);

	@mysqli_free_result($res);
}

/**
 * updates database node with new parent
 */
function updateTodoParent($treeId, $parent, $lastUpdated) {
	$sql1 = 'UPDATE `todo_list` SET `parent`=' . $parent . ', `last_updated`="' . $lastUpdated . '" WHERE id = ' . $treeId;
	$iConn = conn("insert");
	try {
		/* switch autocommit status to FALSE. Actually, it starts transaction */
		$iConn -> autocommit(FALSE);

		$res = $iConn -> query($sql1);
		if ($res === false) {
			throw new Exception('Wrong SQL: ' . $sql1 . ' Error: ' . $iConn -> error);
		}
		$iConn -> commit();

	} catch (Exception $e) {
		echo 'Transaction failed: ' . $e -> getMessage();

		$iConn -> rollback();
	}

	/* switch back autocommit status */
	$iConn -> autocommit(TRUE);

	@mysqli_free_result($res);
}

function makeTree($tree, $root) {
	for ($i = 0; $i < count($tree); $i++) {
		if ($tree[$i] -> getParent() == $root -> getId()) {
			$child = makeTree($tree, $tree[$i]);
			$root -> addChild($child);
		}
	}
	return $root;

}

/**
 * get userId
 */
function getUserId($username) {
	$username = htmlentities($username);
	$sql = 'Select user_id from user where user_name = "' . $username . '"';
	$userId;
	$iConn = conn();
	$result = mysqli_query($iConn, $sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
	$view = null;
	if (mysqli_num_rows($result) > 0) {

		while ($row = mysqli_fetch_assoc($result)) {
			$userId = $row['user_id'];
		}
	}
	@mysqli_free_result($result);
	return $userId;
}



function checkForUpdate($id, $lastUpdate) {
	$id = htmlentities($id);
	$sql = 'Select tl.id, tl.title, tl.parent, tl.completed,tl.due_date,tl.last_updated,ti.desc,ti.created_by,ti.assigned_to,img.img_src from todo_list as tl left join todo_info as ti on tl.id = ti.todo_id  and tl.id ="' . $id . '" left join todo_img as img on tl.id = img.todo_id where tl.last_updated > "' . $lastUpdate . '"';
	$iConn = conn();

	$result = mysqli_query($iConn, $sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
	$node = null;
	if (mysqli_num_rows($result) > 0) {
		$count = 0;
		while ($row = mysqli_fetch_assoc($result)) {
			if ($count == 0) {
				$node = new Node($row['id'], $row['title'], $row['parent'], $row['completed'], $row['due_date'], $row['last_updated']);
				$node -> fullConstruct($row['desc'], $row['created_by'], $row['assigned_to']);
			}
			if ($row['img_src'] != null)
				$node -> addImage($row['img_src']);

			$count++;
		}
	}
	@mysqli_free_result($result);
	return $node;
}

/**
 * starts a session
 */
function startSession() {
	if (isset($_SESSION))
		return true;
	else
		session_start();
}
?>