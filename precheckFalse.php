<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

$response = array("error" => FALSE);

if (isset($_POST['id'])) {
	$itemId = $_POST['id'];

	$db->setPrecheckFalse($itemId);

	$response["error"] = FALSE;
	echo json_encode($response);

} else { 
	$response["error"] = TRUE;
	$response["error_msg"] = "Required parameters are missing!";
	echo json_encode($response);
}
?>
            
