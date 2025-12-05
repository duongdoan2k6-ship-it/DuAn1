<?php
class BookingModel extends BaseModel
{
    protected $table = 'dattour';

    public function getAll()
    {
        $sql = "
            SELECT 
                dt.*,
                tt.TenTrangThai,
                lk.NgayKhoiHanh,
                lk.NgayKetThuc,
                lk.SoChoToiDa,
                lk.SoChoDaDat,
                lk.GiaNguoiLon,
                lk.GiaTreEm
            FROM dattour dt
            LEFT JOIN trangthaidattour tt ON dt.MaTrangThai = tt.MaTrangThai
            LEFT JOIN lichkhoihanh lk ON dt.MaLichKhoiHanh = lk.MaLichKhoiHanh
            ORDER BY dt.MaDatTour DESC
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cancelBooking($MaDatTour)
    {
        // Lấy thông tin
        $sqlInfo = "SELECT MaLichKhoiHanh, SoLuongKhach, MaTrangThai FROM dattour WHERE MaDatTour = :id";
        $stmtInfo = $this->conn->prepare($sqlInfo);
        $stmtInfo->execute(['id' => $MaDatTour]);
        $booking = $stmtInfo->fetch(PDO::FETCH_ASSOC);
        
        $statusChoXacNhan = 1; 
        
        // Chỉ cho phép hủy khi đang chờ (1)
        if ($booking && $booking['MaTrangThai'] == $statusChoXacNhan) {

            // [QUAN TRỌNG] Sửa số 4 thành số 3 (Đã hủy)
            $statusHuy = 3; 

            // Cập nhật trạng thái
            $sqlUpdate = "UPDATE dattour SET MaTrangThai = :status WHERE MaDatTour = :id";
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            $stmtUpdate->execute([
                'status' => $statusHuy, 
                'id' => $MaDatTour
            ]);

            // Trả lại chỗ ngồi (Giữ nguyên logic của bạn)
            $sqlUpdateSlots = "UPDATE lichkhoihanh 
                               SET SoChoDaDat = SoChoDaDat - :soLuong 
                               WHERE MaLichKhoiHanh = :maLich AND SoChoDaDat >= :soLuong";
            
            $stmtSlots = $this->conn->prepare($sqlUpdateSlots);
            $stmtSlots->execute([
                'soLuong' => $booking['SoLuongKhach'],
                'maLich'  => $booking['MaLichKhoiHanh']
            ]);
            return true; 
        }
        return false; 
    }

    // --- THÊM HÀM MỚI ĐỂ ADMIN ĐỔI TRẠNG THÁI ---
    // Hàm này dùng khi Admin chọn dropdown (1 -> 2 hoặc 2 -> 3)
    public function updateStatus($id, $newStatus) {
        $allowed = [1, 2, 3]; // Chỉ cho phép 3 số này
        if (!in_array($newStatus, $allowed)) return false;

        // Nếu chuyển sang trạng thái 3 (Hủy) -> Cần gọi logic trừ chỗ
        if ($newStatus == 3) {
            return $this->cancelBooking($id);
        }

        // Nếu chuyển sang trạng thái 2 (Đã xác nhận) hoặc quay lại 1
        $sql = "UPDATE dattour SET MaTrangThai = :status WHERE MaDatTour = :id";
        $stmt = $this->conn->prepare($sql);
return $stmt->execute(['status' => $newStatus, 'id' => $id]);
    }

    public function findById($id)
    {
       // ... (Giữ nguyên code cũ của bạn) ...
       $sql = "SELECT dt.*, tt.TenTrangThai, lk.NgayKhoiHanh, lk.NgayKetThuc,
                      lk.SoChoToiDa, lk.SoChoDaDat, lk.GiaNguoiLon, lk.GiaTreEm
               FROM dattour dt
               LEFT JOIN trangthaidattour tt ON dt.MaTrangThai = tt.MaTrangThai
               LEFT JOIN lichkhoihanh lk ON dt.MaLichKhoiHanh = lk.MaLichKhoiHanh
               WHERE dt.MaDatTour = :id LIMIT 1";
       
       $stmt = $this->conn->prepare($sql);
       $stmt->execute(['id' => $id]);
       return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>