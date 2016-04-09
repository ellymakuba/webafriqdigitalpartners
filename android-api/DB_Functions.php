<?php
class DB_Functions {
 
    private $conn;
 
    // constructor
    function __construct() {
        require_once 'DB_Connect.php';
        // connecting to database
        $db = new Db_Connect();
        $this->conn = $db->connect();
    }
 
    // destructor
    function __destruct() {
         
    }   
 
    /**
     * Get user by email and password
     */
    public function getUserByUsernameAndPassword($username, $password) {
 		$password=sha1($password);
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE user_name = ? AND user_pw LIKE ?"); 
        $stmt->bind_param("ss", $username,$password);
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }
 
    /**
     * Check user is existed or not
     */
    public function memberAlreadyExists($memberDetails) {
        $stmt = $this->conn->prepare("SELECT cust_name from customer WHERE cust_name= ?"); 
        $stmt->bind_param("s",$memberDetails->getName()); 
        $stmt->execute(); 
        $stmt->store_result(); 
        if ($stmt->num_rows > 0) {
            // member existed 
            $stmt->close();
            return true;
        } else {
            //member did not exist
            $stmt->close();
            return false;
        }
    } 
	public function saveNewMember($memberDetails){
		$stmt=$this->conn->prepare("INSERT INTO customer(cust_no,cust_name,cust_dob,custsex_id,cust_address,cust_phone,cust_email,cust_occup,
		custmarried_id,cust_heirrel,cust_since,custsick_id,cust_lastsub,cust_active,cust_lastupd,user_id)
		 VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
		
		
		$stmt->bind_param("ssssssssssssssss",$memberDetails->getMemberNo(),$memberDetails->getName(),$memberDetails->getDOB(),
		$memberDetails->getGender(),$memberDetails->getAddress(),$memberDetails->getPhoneNo(),$memberDetails->getEmail(),
		$memberDetails->getOccupation(),$memberDetails->getMaritalStatus(),$memberDetails->getNextOfKin(),$memberDetails->getMemberSince(),
		$memberDetails->getSickness(),$memberDetails->getLastSubscription(),$memberDetails->getActive(),
		$memberDetails->getLastUpdated(),$memberDetails->getUserId());
		if($stmt->execute()){		   
		   return $memberDetails;
		   $stmt->close;
		}
		else{
		   return false;
		}
	}
}
 
?>


















