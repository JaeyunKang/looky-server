<?php
  
	require_once 'include/DB_Functions.php';
	$db = new DB_Functions();

	$response = array("error" => FALSE);

	$data = $db->getPrecheckInformation("1");

  
	if($data) {
		$response["body"] = $data;
	} else {
		$response["error"] = TRUE;
	}
	echo json_encode($response);
?>

