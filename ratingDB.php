<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

$response = array("error" => FALSE);

if (isset($_POST['userId']) && isset($_POST['itemId']) && isset($_POST['rating'])) {
	$userId = $_POST['userId'];
	$itemId = $_POST['itemId'];
	$rating = $_POST['rating'];
	$whereFrom = '0';
	
	if (isset($_POST['rating'])) {
		$whereFrom = $_POST['whereFrom'];
	}

	$user = $db->storeRating($userId, $itemId, $rating, $whereFrom);
	echo json_encode($response);

} else { 
	$response["error"] = TRUE;
	$response["error_msg"] = "Required parameters are missing!";
	echo json_encode($response);
}
?>
            
