<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

$response = array("error" => FALSE);

if (isset($_POST['userId']) && isset($_POST['gender'])) {
	$userId = $_POST['userId'];
	$gender = $_POST['gender'];

	$db->updateGender($userId, $gender);

	echo json_encode($response);

} else { 
	$response["error"] = TRUE;
	$response["error_msg"] = "Required parameters are missing!";
	echo json_encode($response);
}
?>
            
