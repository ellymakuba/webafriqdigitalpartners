<?php
 
require_once 'DB_Functions.php';
require_once 'MemberDetails.php';
$db = new DB_Functions();
 
// json response array
$response = array("error" => FALSE); 
if (isset($_POST['name']) && isset($_POST['memberNO'])) { 
   $memberDetails=new MemberDetails();
    // receiving the post params
	$_POST['memberSince']='16.03.2012';
	$_POST['gender']='1';
	$_POST['dOB']='26.02.1992';
	$_POST['maritalStatus']='1';
	$_POST['lastSubscription']=strtotime(date());
	$_POST['sickness']='1';
	$_POST['active']='1';
	$_POST['nextOfKin']='';
	$_POST['lastupdated']=strtotime(date());
	$_POST['userId']='1';
	
	$memberDetails->setMemberNo($_POST['memberNo']);
	$memberDetails->setName($_POST['name']);
	$memberDetails->setAddress($_POST['address']);
	$memberDetails->setEmail($_POST['email']);
	$memberDetails->setOccupation($_POST['occupation']);
	$memberDetails->setPhoneNo($_POST['phoneNo']);
	$memberDetails->setMemberSince($_POST['memberSince']);
	$memberDetails->setGender($_POST['gender']);
	$memberDetails->setDOB($_POST['dOB']);
	$memberDetails->setMaritalStatus($_POST['maritalStatus']);
	$memberDetails->setLastSubscription($_POST['lastSubscription']);
	$memberDetails->setSickness($_POST['sickness']);
	$memberDetails->setActive($_POST['active']);
	$memberDetails->setNextOfKin($_POST['nextOfKin']);
	$memberDetails->setLastUpdated($_POST['lastupdated']);
	$memberDetails->setUserId($_POST['userId']);
 
    // check if user is already existed with the same email
    if ($db->memberAlreadyExists($memberDetails)) {
        // user already existed
        $response["error"] = TRUE;
        $response["error_msg"] = "member already exists with " . $memberDetails->getMemberNo();
        echo json_encode($response);
    } else {
        // create a new member
        $member = $db->saveNewMember($memberDetails);
        if ($member) {
            // member stored successfully
            $response["error"] = FALSE;
            $response["user"]["memberNo"] = $member->getMemberNo();
            $response["user"]["name"] = $member->getName();
            echo json_encode($response);
        } else {
            // member failed to store
            $response["error"] = TRUE;
            $response["error_msg"] = "Unknown error occurred in registration!";
            echo json_encode($response);
        }
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Please enter all the fields marked compulsory!";
    echo json_encode($response);
}
?>