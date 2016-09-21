<?php
	require_once '0700_inc/common_inc.php';
	require_once '0700_inc/conn_inc.php';
	$userId = 1;
	$package = new stdClass();
	$package->message = "state:logged in, username: Sage";
	$package->groups = cloneUserData($userId);
	$package->user = login("sage","gatzke",1,1);
		
	$response = json_encode($package);
	echo($response);
?>