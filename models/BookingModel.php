<?php
class BookingModel extends BaseModel 
{
    // Lấy tất cả booking + join trạng thái + join tour
    public function getAllBooking()
    {
        $sql = "SELECT 
                    b.MaDatTour,
                    b.MaLichKhoiHanh,
                    b.TenKhachHang,
                    b.LienHeKhachHang,
                    b.SoLuongKhach,
                    b.NgayDatTour,
                    b.TongTien,
                    b.MaTrangThai,
                    tt.TenTrangThai,
                    t.TenTour
                FROM dattour b
                LEFT JOIN trangthaidattour tt 
                    ON b.MaTrangThai = tt.MaTrangThai
                LEFT JOIN tourdulich t 
                    ON t.MaTour = b.MaLichKhoiHanh 
                ORDER BY b.MaDatTour DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy 1 booking theo id
    public function getBookingById($id)
{
    $sql = "SELECT 
                b.*,
                tt.TenTrangThai
            FROM dattour b
            LEFT JOIN trangthaidattour tt
                ON b.MaTrangThai = tt.MaTrangThai
            WHERE b.MaDatTour = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    return $stmt->fetch();
}


    // Lấy danh sách trạng thái
    public function getAllStatus()
    {
        $sql = "SELECT * FROM trangthaidattour ORDER BY MaTrangThai ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Thêm booking
    public function insert($maLich, $tenKH, $lienHe, $soKhach, $tongTien, $ghiChu)
    {
        $sql = "INSERT INTO dattour 
                (MaLichKhoiHanh, TenKhachHang, LienHeKhachHang, SoLuongKhach, NgayDatTour, TongTien, MaTrangThai, GhiChu)
                VALUES 
                (:maLich, :ten, :lienHe, :so, NOW(), :tongTien, 1, :ghiChu)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':maLich', $maLich);
        $stmt->bindValue(':ten', $tenKH);
        $stmt->bindValue(':lienHe', $lienHe);
        $stmt->bindValue(':so', $soKhach);
        $stmt->bindValue(':tongTien', $tongTien);
        $stmt->bindValue(':ghiChu', $ghiChu);

        return $stmt->execute();
    }

    // Cập nhật trạng thái
    public function updateStatus($id, $newStatus)
    {
        $sql = "UPDATE dattour 
                SET MaTrangThai = :tt  
                WHERE MaDatTour = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':tt', $newStatus);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }

    // Log lịch sử thay đổi trạng thái
    public function addStatusLog($id, $oldStatus, $newStatus, $nhanVien, $ghiChu)
    {
        $sql = "INSERT INTO booking_trangthai_log 
                (MaDatTour, TrangThaiCu, TrangThaiMoi, ThoiGian, NhanVienThayDoi, GhiChu)
                VALUES (:id, :cu, :moi, NOW(), :nv, :ghichu)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':cu', $oldStatus);
        $stmt->bindValue(':moi', $newStatus);
        $stmt->bindValue(':nv', $nhanVien);
        $stmt->bindValue(':ghichu', $ghiChu);

        return $stmt->execute();
    }

    // Lấy chi tiết booking + join trạng thái + join tour
    public function getBookingDetail($id)
    {
        $sql = "SELECT 
                    b.*, 
                    tt.TenTrangThai,
                    tt.MoTa,
                    t.TenTour
                FROM dattour b
                LEFT JOIN trangthaidattour tt ON b.MaTrangThai = tt.MaTrangThai
                LEFT JOIN tourdulich t ON t.MaTour = b.MaLichKhoiHanh
                WHERE b.MaDatTour = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Lấy lịch sử thay đổi trạng thái
    public function getStatusLog($id)
    {
        $sql = "SELECT 
                    lg.*, 
                    t1.TenTrangThai AS TrangThaiCuTen,
                    t2.TenTrangThai AS TrangThaiMoiTen
                FROM booking_trangthai_log lg
                LEFT JOIN trangthaidattour t1 
                    ON lg.TrangThaiCu = t1.MaTrangThai
                LEFT JOIN trangthaidattour t2 
                    ON lg.TrangThaiMoi = t2.MaTrangThai
                WHERE lg.MaDatTour = :id
                ORDER BY lg.ThoiGian DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Xóa booking
   public function delete($id)
{
    // Kiểm tra trạng thái trước khi xóa
    $sqlCheck = "SELECT MaTrangThai FROM dattour WHERE MaDatTour = :id";
    $stmtCheck = $this->conn->prepare($sqlCheck);
    $stmtCheck->bindValue(':id', $id);
    $stmtCheck->execute();
    $row = $stmtCheck->fetch();

    if (!$row) {
        return false; // không tồn tại booking
    }

    $status = $row['MaTrangThai'];

    // Chỉ cho xóa khi trạng thái = 1 (chờ xác nhận) hoặc 3 (đã hủy)
    if ($status != 1 && $status != 3) {
        return false;
    }

    // Xóa log trước (nếu có)
    $sqlLog = "DELETE FROM booking_trangthai_log WHERE MaDatTour = :id";
    $stmtLog = $this->conn->prepare($sqlLog);
    $stmtLog->bindValue(':id', $id);
    $stmtLog->execute();

    // Xóa booking
    $sql = "DELETE FROM dattour WHERE MaDatTour = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':id', $id);
    return $stmt->execute();
}


    // Kiểm tra số chỗ trống
    public function checkSlot($maLich, $soKhach)
    {
        $sql = "SELECT SoLuongToiDa, SoLuongDaDat 
                FROM lichkhoihanh 
                WHERE MaLichKhoiHanh = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $maLich);
        $stmt->execute();

        $row = $stmt->fetch();

        if (!$row) return false;

        $conLai = $row['SoLuongToiDa'] - $row['SoLuongDaDat'];

        return $conLai >= $soKhach;
    }
}
