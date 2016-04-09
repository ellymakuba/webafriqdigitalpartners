<?php
class DB_Connect {
    private $conn; 
    // Connecting to database
    public function connect() {
        require_once 'config/config.php';         
        // Connecting to mysql database
		try{
        $this->conn = new PDO("mysql:host=localhost;dbname=mango", 'root', ''); 
        return $this->conn;
		}
		catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
}
 
?>