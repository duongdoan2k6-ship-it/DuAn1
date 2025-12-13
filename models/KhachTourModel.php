<?php
class KhachTourModel extends BaseModel {
    
    // Lấy danh sách khách của một chuyến đi cụ thể
    public function getPassengersByTour($lichId) {
        $sql = "SELECT 
                    kt.id as id_khach,
                    kt.ho_ten_khach,
                    kt.gioi_tinh,
                    kt.loai_khach,
                    kt.ghi_chu_dac_biet, 
                    kt.trang_thai_diem_danh,
                    b.ten_nguoi_dat,
                    b.sdt_lien_he
                FROM khach_tour kt
                JOIN bookings b ON kt.booking_id = b.id
                WHERE b.lich_khoi_hanh_id = :lich_id
                ORDER BY b.id ASC"; // Sắp xếp để khách cùng đoàn đứng gần nhau
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['lich_id' => $lichId]);
        return $stmt->fetchAll();
    }

    // Cập nhật trạng thái điểm danh
    public function updateStatus($idKhach, $status) {
        $sql = "UPDATE khach_tour SET trang_thai_diem_danh = :status WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['status' => $status, 'id' => $idKhach]);
    }

    // Reset điểm danh của cả đoàn về 0 (để tránh lỗi lưu đè)
    public function resetStatus($lichId) {
        // Lấy tất cả khách của lịch này và set về 0
        $sql = "UPDATE khach_tour kt
                INNER JOIN bookings b ON kt.booking_id = b.id
                SET kt.trang_thai_diem_danh = 0
                WHERE b.lich_khoi_hanh_id = :lich_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['lich_id' => $lichId]);
    }

    /**
     * Cập nhật ghi chú đặc biệt cho một khách cụ thể
     * @param int $khachId ID của khách trong bảng khach_tour
     * @param string $ghiChu Nội dung ghi chú mới
     * @return bool
     */
    public function updateGhiChuDacBiet($khachId, $ghiChu)
    {
        $sql = "UPDATE khach_tour SET ghi_chu_dac_biet = :ghi_chu WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'ghi_chu' => $ghiChu,
            'id' => $khachId
        ]);
    }

    // [MỚI] Hàm thêm khách tour (Dùng khi tạo Booking)
    public function insert($data) {
        $sql = "INSERT INTO khach_tour (booking_id, ho_ten_khach, loai_khach, gioi_tinh, ngay_sinh, ghi_chu_dac_biet, trang_thai_diem_danh) 
                VALUES (:booking_id, :ho_ten_khach, :loai_khach, :gioi_tinh, :ngay_sinh, :ghi_chu_dac_biet, :trang_thai_diem_danh)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }
}
?>