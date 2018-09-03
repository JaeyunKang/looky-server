<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

$response = array("error" => FALSE);

if (isset($_POST['gender']) && isset($_POST['category']) && isset($_POST['userId']) && isset($_POST['subcategory'])) {

	$gender = $_POST['gender'];
	$category = $_POST['category'];
	$userId = $_POST['userId'];
	$subcategory = $_POST['subcategory'];

	$data = $db->filterLike($gender, $category, $userId, $subcategory);
	$response["itemList"] = $data;
	echo json_encode($response);

} else { 
	$response["error"] = TRUE;
	$response["error_msg"] = "Required parameters are missing!";
	echo json_encode($response);
}
?>
            
