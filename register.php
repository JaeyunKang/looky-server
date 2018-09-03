<?php

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

$response = array("error" => FALSE);

if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password'])) {
	$name = $_POST['name'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$gender = '2';
	$age = '0';

	if ($db->isUserExisted($email)) {
		$response["error"] = TRUE;
		$response["error_msg"] = "이미 등록된 이메일 입니다";
		echo json_encode($response);
	} else {
		$user = $db->storeUser($name, $email, $password, $gender, $age);
		if ($user) {
			$response["id"] = $user["id"];
			echo json_encode($response);
		} else {
			$response["error"] = TRUE;
			$response["error_msg"] = "Unknown error occurred in registration!"; 
			echo json_encode($response);
		}
	}
} else { 
	$response["error"] = TRUE;
	$response["error_msg"] = "Required parameters (name, email, password, gender or age) is missing!";
	echo json_encode($response);
}
?>
            
