<?php

class DB_Functions {
    private $conn;

    function __construct() {
        require_once 'DB_Connect.php';
        
        $db = new DB_Connect();
        $this->conn = $db->connect();
		$this->conn->set_charset("utf8");
    }

    function __destruct() {
    }

    
    public function storeUser($name, $email, $password, $gender, $age) {
        $uuid = uniqid('', true);
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"];
        $salt = $hash["salt"];

        $stmt = $this->conn->prepare("INSERT INTO users(unique_id, name, email, encrypted_password, salt, created_at, gender, age) VALUES(?, ?, ?, ?, ?, NOW(), ?, ?)");
        $stmt->bind_param("sssssss", $uuid, $name, $email, $encrypted_password, $salt, $gender, $age);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return $user;
        } else {
            return false;
        }
    }

    public function updateAge($userId, $year, $month, $day) {
	
		$stmt = $this->conn->prepare("UPDATE users SET year = ?, month = ?, day = ? WHERE id = ?");
        $stmt->bind_param("ssss", $year, $month, $day, $userId);
        $result = $stmt->execute();
        $stmt->close();
     
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function updateGender($userId, $gender) {
	
		$stmt = $this->conn->prepare("UPDATE users SET gender = ? WHERE id = ?");
        $stmt->bind_param("ss", $gender, $userId);
        $result = $stmt->execute();
        $stmt->close();
     
        if ($result) {
            return true;
        } else {
            return false;
        }
    }


 
    public function getUserByEmailAndPassword($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            $salt = $user['salt'];
            $encrypted_password = $user['encrypted_password'];
            $hash = $this->checkhashSSHA($salt, $password);

            if ($encrypted_password == $hash) {
                return $user;
            }
        } else {
            return NULL;
        }
    }

    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return $user;

        } else {
            return NULL;
        }
    }


    public function isUserExisted($email) {
        $stmt = $this->conn->prepare("SELECT email from users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    public function hashSSHA($password) { 
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }

    public function checkhashSSHA($salt, $password) { 
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
        return $hash;
    }

	public function getItemScores($gender, $category, $userId, $subcategory) {

		if ($category == "all") {
			$stmt = $this->conn->prepare("SELECT id, saved_at FROM item where gender = ? and precheck = 1 and soldout = 0 order by rand() limit 10000");
	    	$stmt->bind_param("s", $gender);
		} else if ($subcategory == "all") {
			$stmt = $this->conn->prepare("SELECT id, saved_at FROM item where gender = ? and category = ? and precheck = 1 and soldout = 0 order by rand() limit 10000");
			$stmt->bind_param("ss", $gender, $category);
		} else {
			$stmt = $this->conn->prepare("SELECT id, saved_at FROM item where gender = ? and category = ? and subcategory = ? and precheck = 1 and soldout = 0 order by rand() limit 10000");
			$stmt->bind_param("sss", $gender, $category, $subcategory);
		}

		$result = $stmt->execute();
		$stmt->bind_result($col1, $col2);

		if ($result) {
	    	$data = array();
	    	while ($stmt->fetch()) {
	        	array_push($data, array("item"=>$col1, "score"=>0, "savedAt"=>$col2));
	    	}
	    	$stmt->close();
	    	return $data;

		} else {
			$stmt->close();
			return FALSE;
		}

	}

	public function getItemName($itemId) {
		
		$stmt = $this->conn->prepare("SELECT name FROM item where id = ?");
		$stmt->bind_param("s", $itemId);
 		
		$result = $stmt->execute();
		$stmt->bind_result($col1);

		if ($result) {
	    	$data = array();
	    	$stmt->fetch();
	    	$data = $col1;
	    	$stmt->close();
	    	return $data;
    	    
		} else {
	    	$stmt->close();
    	    return false;
		}
	}

	public function getItemLink($itemId) {
		
		$stmt = $this->conn->prepare("SELECT url FROM item where id = ?");
		$stmt->bind_param("s", $itemId);
 		
		$result = $stmt->execute();
		$stmt->bind_result($col1);

		if ($result) {
	    	$data = array();
	    	$stmt->fetch();
	    	$data = $col1;
	    	$stmt->close();
	    	return $data;
    	    
		} else {
	    	$stmt->close();
    	    return false;
		}
	}

    public function storeRating($userId, $itemId, $rating, $whereFrom) {

		if ($rating == '0') {
			$stmt = $this->conn->prepare("INSERT INTO likeLog(userId, itemId, saved_at) VALUES(?, ?, NOW())");
			$stmt->bind_param("ss", $userId, $itemId);
		} else if ($rating == '1') {
			$stmt = $this->conn->prepare("INSERT INTO dislikeLog(userId, itemId, saved_at) VALUES(?, ?, NOW())");
			$stmt->bind_param("ss", $userId, $itemId);
		} else if ($rating == '2') {
			$stmt = $this->conn->prepare("INSERT INTO linkLog(userId, itemId, whereFrom, saved_at) VALUES(?, ?, ?, NOW())");
			$stmt->bind_param("sss", $userId, $itemId, $whereFrom);
		}

		$result = $stmt->execute();
		$stmt->close();

		if ($result) {
			return true;
		} else {
			return false;
		}
    }  

   
    public function filterLike($gender, $category, $userId, $subcategory) {

		$stmt = null;
		$result = null;

		if ($category == "all") {
	    	$stmt = $this->conn->prepare("SELECT itemId, name, url FROM likeLog join item on itemId=id WHERE userId=? and gender=? ORDER BY likeLog.saved_at DESC");
	    	$stmt->bind_param("ss", $userId, $gender);
		} else if ($subcategory == "all") {
	    	$stmt = $this->conn->prepare("SELECT itemId, name, url FROM likeLog join item on itemId=id WHERE userId=? and category=? and gender=? ORDER BY likeLog.saved_at DESC");
	    	$stmt->bind_param("sss", $userId, $category, $gender);
		} else {
	    	$stmt = $this->conn->prepare("SELECT itemId, name, url FROM likeLog join item on itemId=id WHERE userId=? and category=? and subcategory=? and gender=? ORDER BY likeLog.saved_at DESC");
	    	$stmt->bind_param("ssss", $userId, $category, $subcategory, $gender);
		}

		$result = $stmt->execute();
		$stmt->bind_result($col1, $col2, $col3);

		if ($result) {
	    	$data = array();
	    	while ($stmt->fetch()) {
	        	array_push($data, array("id"=>$col1, "name"=>$col2, "link"=>$col3));
	    	}
	    	$stmt->close();
	    	return $data;
    	    
		} else {
	    	$stmt->close();
    	    return false;
		}
    }
 
    public function filterChart($gender, $category, $subcategory) {

		$stmt = null;
		$result = null;

		if ($category == "all") {
	    	$stmt = $this->conn->prepare("SELECT itemId, name, price, url, count(userId) as countNum FROM likeLog join item on itemId=id WHERE itemId in (SELECT id FROM item WHERE gender=?) GROUP BY itemId ORDER BY countNum DESC LIMIT 0, 99");
	    	$stmt->bind_param("s", $gender);
		} else if ($subcategory == "all") {
	    	$stmt = $this->conn->prepare("SELECT itemId, name, price, url, count(userId) as countNum FROM likeLog join item on itemId=id WHERE itemId in (SELECT id FROM item WHERE gender=? and category=?) GROUP BY itemId ORDER BY countNum DESC LIMIT 0, 99");
	    	$stmt->bind_param("ss", $gender, $category);
		} else {
	    	$stmt = $this->conn->prepare("SELECT itemId, name, price, url, count(userId) as countNum FROM likeLog join item on itemId=id WHERE itemId in (SELECT id FROM item WHERE gender=? and category=? and subcategory=?) GROUP BY itemId ORDER BY countNum DESC LIMIT 0, 99");
	    	$stmt->bind_param("sss", $gender, $category, $subcategory);
		}

		$result = $stmt->execute();
		$stmt->bind_result($col1, $col2, $col3, $col4, $col5);

		if ($result) {
	    	$data = array();
	    	while ($stmt->fetch()) {
	        	array_push($data, array("id"=>$col1, "name"=>$col2, "price"=>$col3, "link"=>$col4, "like"=>$col5));
	    	}
	    	$stmt->close();
	    	return $data;
    	    
		} else {
	    	$stmt->close();
    	    return false;
		}
    }

    public function getLikeNum($itemId) {

		$stmt = null;
		$result = null;

		$stmt = $this->conn->prepare("SELECT itemId, count(userId) FROM likeLog WHERE itemId = ?");
		$stmt->bind_param("s", $itemId);

 		$result = $stmt->execute();
		$stmt->bind_result($col1, $col2);

		if ($result) {
	    	$data = array();
	    	$stmt->fetch();
	    	$data = $col2;
	    	$stmt->close();
	    	return $data;
    	    
		} else {
	    	$stmt->close();
    	    return false;
		}
    }
 
    public function deleteLike($userId, $itemId) {

		$stmt = $this->conn->prepare("DELETE FROM likeLog WHERE userId=? and itemId=?");
		$stmt->bind_param("ss", $userId, $itemId);
		
		$result = $stmt->execute();
		$stmt->close();

		if ($result) {
    	    return true;
		} else {
    	    return false;
		}
    }
    
    public function storeFeedback($userId, $feedback) {

		$stmt = $this->conn->prepare("INSERT INTO feedback(userId, feedback, created_at) VALUES(?, ?, NOW())");
		$stmt->bind_param("ss", $userId, $feedback);
		
		$result = $stmt->execute();
		$stmt->close();

		if ($result) {
    	    return true;
		} else {
    	    return false;
		}
    }  

    public function getPrecheckInformation($gender) {

		$stmt = $this->conn->prepare("SELECT id, url, name, price, shop, category, subcategory, gender FROM item WHERE precheck = 0 and soldout = 0 and gender = ?");
		$stmt->bind_param('s', $gender);
		$result = $stmt->execute();

		$stmt->bind_result($col1, $col2, $col3, $col4, $col5, $col6, $col7, $col8);

		if ($result) {
			$stmt->fetch();
			$data = array("id"=>$col1, "url"=>$col2, "name"=>$col3, "price"=>$col4, "shop"=>$col5, "category"=>$col6, "subcategory_hint"=>$col7, "gender"=>$col8);
			$stmt->close();
			return $data;
		} else {
			$stmt->close();
			return FALSE;
		}
	}

	public function setPrecheckFalse($itemId) {
	
		$stmt = $this->conn->prepare("UPDATE item SET precheck = 2 WHERE id = ?");
        $stmt->bind_param("s", $itemId);
        
		$result = $stmt->execute();
		$stmt->close();
     
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

	public function setPrecheckTrue($itemId, $category, $subcategory) {
	
		$stmt = $this->conn->prepare("UPDATE item SET precheck = 1, category = ?, subcategory = ?  WHERE id = ?");
        $stmt->bind_param("sss", $category, $subcategory, $itemId);
        
		$result = $stmt->execute();
		$stmt->close();
 
		if ($result) {
            return true;
        } else {
            return false;
        }
    }



}
?>
