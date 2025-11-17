<?php
// models/TourModel.php

class TourModel extends BaseModel {
    /**
     * Lấy tất cả tour (cùng với Tên Loại Tour)
     */
    public function getAllTours() {
        $sql = "SELECT 
                    t.MaTour, 
                    t.TenTour, 
                    t.ThoiLuong, 
                    t.DiaDiemKhoiHanh, 
                    lt.TenLoai      
                FROM 
                    tourdulich t
                LEFT JOIN 
                    loaitour lt ON t.MaLoaiTour = lt.MaLoaiTour
                ORDER BY 
                    t.MaTour DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    // Ví dụ về việc gọi hàm được kế thừa:
    public function getAllLoaiTour() {
        
        $sql = "SELECT * FROM loaitour";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // --- HÀM INSERT PHẢI NẰM TRONG CLASS (TRƯỚC DẤU ĐÓNG NGOẶC) ---
    public function insert($ten, $maLoai, $thoiLuong, $diaDiem, $moTa) {
        try {
            $sql = "INSERT INTO tourdulich (TenTour, MaLoaiTour, ThoiLuong, DiaDiemKhoiHanh, MoTa) 
                    VALUES (:ten, :maLoai, :thoiLuong, :diaDiem, :moTa)";
            
            $stmt = $this->conn->prepare($sql);
            
            // Gán giá trị vào các tham số
            $stmt->bindParam(':ten', $ten);
            $stmt->bindParam(':maLoai', $maLoai);
            $stmt->bindParam(':thoiLuong', $thoiLuong);
            $stmt->bindParam(':diaDiem', $diaDiem);
            $stmt->bindParam(':moTa', $moTa);
            
            return $stmt->execute(); // Trả về true nếu thành công
        } catch (PDOException $e) {
            echo "Lỗi SQL: " . $e->getMessage();
            return false;
        }
    }

} 
?>