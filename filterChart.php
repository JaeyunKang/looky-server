<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

$response = array("error" => FALSE);

if (isset($_POST['gender']) && isset($_POST['category']) && isset($_POST['subcategory'])) {

	$gender = $_POST['gender'];
	$category = $_POST['category'];
	$subcategory = $_POST['subcategory'];
    
	$data = $db->filterChart($gender, $category, $subcategory);
	$response["error"] = FALSE;
	$response["itemList"] = $data;
	echo json_encode($response);

} else { 
	$response["error"] = TRUE;
	$response["error_msg"] = "Required parameters are missing!";
	echo json_encode($response);
}
?>
            
