<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

$response = array("error" => FALSE);

if(isset($_POST['itemId'])) {
	$itemId = $_POST['itemId'];

	$data = $db->getItemLink($itemId);
	
	$response["itemLink"] = $data;
	echo json_encode($response);

} else {
	$response["error"] = TRUE;
	$response["error msg"] = "Required parameters are missing!";
	echo json_encode($response);
}


?>
