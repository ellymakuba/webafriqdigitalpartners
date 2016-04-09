<?php
class MemberDetails{
	private $name;
	private	$address;
	private	$email;
	private	$occupation;
	private	$phoneNo;	
	private	$memberSince;
	private	$gender;
	private	$dOB;
	private	$maritalStatus;
	private $lastSubscription;
	private $memberNo;
	private $sickness;
	private $active;
	private $nextOfKin;
	private $lastUpdated;
	private $userId;
	
	public function getName(){
	  return $this->name;
	}
	public function setName($name){
		$this->name=$name;
	}
	
	public function getAddress(){
	  return $this->address;
	}
	public function setAddress($address){
		$this->address=$address;
	}
	
	
	public function getEmail(){
	  return $this->email;
	}
	public function setEmail($email){
		$this->email=$email;
	}
	
	
	public function getOccupation(){
	  return $this->occupation;
	}
	public function setOccupation($occupation){
		$this->occupation=$occupation;
	}
	
	
	public function getPhoneNo(){
	  return $this->phoneNo;
	}
	public function setPhoneNo($phoneNo){
		$this->phoneNo=$phoneNo;
	}
	
	
	public function getMemberSince(){
	  return $this->memberSince;
	}
	public function setMemberSince($memberSince){
		$this->memberSince=$memberSince;
	}
	
	
	public function getGender(){
	  return $this->gender;
	}
	public function setGender($gender){
		$this->gender=$gender;
	}
	
	
	public function getDOB(){
	  return $this->dOB;
	}
	public function setDOB($dOB){
		$this->dOB=$dOB;
	}
	
	
	public function getMaritalStatus(){
	  return $this->maritalStatus;
	}
	public function setMaritalStatus($maritalStatus){
		$this->maritalStatus=$maritalStatus;
	}
	
	public function getLastSubscription(){
	  return $this->lastSubscription;
	}
	public function setLastSubscription($lastSubscription){
		$this->lastSubscription=$lastSubscription;
	}
	
	public function getMemberNo(){
	  return $this->memberNo;
	}
	public function setMemberNo($memberNo){
		$this->memberNo=$memberNo;
	}
	
	public function getSickness(){
	  return $this->sickness;
	}
	public function setSickness($sickness){
		$this->sickness=$sickness;
	}
	
	public function getActive(){
	  return $this->active;
	}
	public function setActive($active){
		$this->sickness=$active;
	}
	
	public function getNextOfKin(){
	  return $this->nextOfKin;
	}
	public function setNextOfKin($nextOfKin){
		$this->nextOfKin=$nextOfKin;
	}
	
	public function getLastUpdated(){
	  return $this->lastUpdated;
	}
	public function setLastUpdated($lastUpdated){
		$this->lastUpdated=$lastUpdated;
	}
	
	public function getUserId(){
	  return $this->userId;
	}
	public function setUserId($userId){
		$this->userId=$userId;
	}

}
?>