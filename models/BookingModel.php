<?php
class BookingModel extends BaseModel
{
    protected $table = "dattour"; // Tên bảng viết thường như trong ảnh

    public function getAll()
    {
        try {
            $sql = "SELECT 
                        d.MaDatTour,
                        d.TenKhachHang,      -- Lấy trực tiếp (trong ảnh có cột này)
                        d.LienHeKhachHang,   -- Lấy sđt/email trực tiếp
                        d.SoLuongKhach,
                        d.NgayDatTour,
                        d.TongTien,
                        d.MaTrangThai AS TrangThai, -- Đổi tên thành TrangThai cho khớp View
                        
                        -- Thử lấy tên Tour qua Lịch Khởi Hành
                        -- Nếu chạy mà lỗi bảng 'LichKhoiHanh' thì xóa 2 dòng LEFT JOIN bên dưới đi
                        t.TenTour
                        
                    FROM dattour d
                    
                    -- Nối: DatTour -> LichKhoiHanh -> TourDuLich
                    LEFT JOIN LichKhoiHanh l ON d.MaLichKhoiHanh = l.MaLichKhoiHanh
                    LEFT JOIN TourDuLich t ON l.MaTour = t.MaTour
                    
                    ORDER BY d.NgayDatTour DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();

        } catch (Exception $e) {
            echo "Lỗi Model: " . $e->getMessage();
            // Nếu lỗi JOIN, hãy thử bỏ đoạn LEFT JOIN đi để code chạy được đã
            die();
        }
    }
}