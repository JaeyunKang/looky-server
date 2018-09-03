<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

$response = array("error" => FALSE);

if (isset($_POST['userId']) && isset($_POST['feedback'])) {
	$userId = $_POST['userId'];
	$feedback = $_POST['feedback'];

	$db->storeFeedback($userId, $feedback);
	echo json_encode($response);

} else { 
	$response["error"] = TRUE;
	$response["error_msg"] = "Required parameters are missing!";
	echo json_encode($response);
}
?>
            
