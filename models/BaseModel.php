<?php

class BaseModel {
   
    protected $conn;

    
    public function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . DB_HOST . 
                ";port=" . DB_PORT . 
                ";dbname=" . DB_NAME, 
                DB_USERNAME, 
                DB_PASSWORD, 
                DB_OPTIONS
            );
        } catch (PDOException $e) {
            die("Lỗi kết nối Database: " . $e->getMessage());
        }
    }

    
    protected function _getAll($table) {
        $sql = "SELECT * FROM $table";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }


}