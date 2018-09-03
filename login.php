<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
 
$response = array("error" => FALSE);
 
if (isset($_POST['email']) && isset($_POST['password'])) {
    
	$email = $_POST['email'];
	$password = $_POST['password'];
 
	$user = $db->getUserByEmailAndPassword($email, $password);
 
	if ($user != false) {
		$response["id"] = $user["id"];
		$response["uid"] = $user["unique_id"];
		$response["user"]["name"] = $user["name"];
		$response["user"]["email"] = $user["email"];
		$response["user"]["created_at"] = $user["created_at"];
		$response["user"]["updated_at"] = $user["updated_at"];
		$response["user"]["gender"] = $user["gender"];
		$response["user"]["age"] = $user["age"];
		echo json_encode($response);
	} else {
		$response["error"] = TRUE;
		$response["error_msg"] = "아이디와 비밀번호를 확인해주세요!";
		$response["email"] = $email;
		$response["password"] = $password;
		echo json_encode($response);
	}
} else {
	$response["error"] = TRUE;
	$response["error_msg"] = "Required parameters are mssing!";
	$response["email"] = $email;
	$response["password"] = $password;
	echo json_encode($response);
}
?>
