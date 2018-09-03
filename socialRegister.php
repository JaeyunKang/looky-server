<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

$response = array("error" => FALSE);

if (isset($_POST['name']) && isset($_POST['email'])) {
	$name = $_POST['name'];
	$email = $_POST['email'];
	$password = "none";
	$gender = "2";
	$age = "0";

	if ($db->isUserExisted($email)) {
		$response["isLogin"] = TRUE;
	
		$user = $db->getUserByEmail($email);

		$response["id"] = $user["id"];
		$response["uid"] = $user["unique_id"];
		$response["user"]["name"] = $user["name"];
		$response["user"]["email"] = $user["email"];
		$response["user"]["created_at"] = $user["created_at"];
		$response["user"]["gender"] = $user["gender"];
		$response["user"]["age"] = $user["age"];
	
		echo json_encode($response);
	} else {
		$user = $db->storeUser($name, $email, $password, $gender, $age);
		if ($user) {
			$response["isLogin"] = FALSE;
	    
			$response["id"] = $user["id"];
			$response["uid"] = $user["unique_id"];
			$response["user"]["name"] = $user["name"];
			$response["user"]["email"] = $user["email"];
			$response["user"]["created_at"] = $user["created_at"];
			$response["user"]["gender"] = $user["gender"];
			$response["user"]["age"] = $user["age"];
	    
			echo json_encode($response);
		} else {
			$response["error"] = TRUE;
			$response["error_msg"] = "오류가 발생하였습니다!"; 
			echo json_encode($response);
		}
	}
} else { 
	$response["error"] = TRUE;
	$response["error_msg"] = "Required paramaters are missing!";
	echo json_encode($response);
}
?>
            
