<?php
// models/TourModel.php

class TourModel extends BaseModel {
    /**
     * Lấy tất cả tour (cùng với Tên Loại Tour)
     * Hàm này sử dụng kết nối $this->conn được kế thừa từ cha (BaseModel).
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
        // Gọi hàm _getAll() từ lớp cha (BaseModel)
        return $this->_getAll('loaitour');
    }
}