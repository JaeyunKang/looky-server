<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

$currentVersion = '26';

$response = array("error" => FALSE);

if (isset($_POST['currentVersion'])) {
	$appCurrentVersion = $_POST['currentVersion'];

	if($currentVersion == $appCurrentVersion) {
		$response["update"] = FALSE;
	} else {
		$response["update"] = TRUE;
	}

	$response["error"] = FALSE;
	
	echo json_encode($response);

} else { 
	$response["error"] = TRUE;
	$response["error_msg"] = "Required parameters are missing!";
	echo json_encode($response);
}
?>
            
