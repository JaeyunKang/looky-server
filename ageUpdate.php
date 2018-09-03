<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

$response = array("error" => FALSE);

if (isset($_POST['userId']) && isset($_POST['year']) && isset($_POST['month']) && isset($_POST['day'])) {
	$userId = $_POST['userId'];
	$year = $_POST['year'];
	$month = $_POST['month'];
	$day = $_POST['day'];

	$db->updateAge($userId, $year, $month, $day);

	echo json_encode($response);

} else { 
	$response["error"] = TRUE;
	$response["error_msg"] = "Required parameters are missing!";
	echo json_encode($response);
}
?>
            
