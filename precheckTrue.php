<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

$response = array("error" => FALSE);

if (isset($_POST['id']) && isset($_POST['category']) && isset($_POST['subcategory'])) {
	
	$itemId = $_POST['id'];
	$category = $_POST['category'];
	$subcategory = $_POST['subcategory'];
	
	$db->setPrecheckTrue($itemId, $category, $subcategory);
	$response["error"] = FALSE;
	echo json_encode($response);

} else { 
	$response["error"] = TRUE;
	$response["error_msg"] = "Required parameters are missing!";
	echo json_encode($response);
}
?>
            
