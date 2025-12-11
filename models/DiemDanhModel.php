<?php
class DiemDanhModel extends BaseModel
{
    // 1. Lấy danh sách các phiên điểm danh của 1 tour
    public function getPhienByLich($lichId)
    {
        $sql = "SELECT * FROM phien_diem_danh WHERE lich_khoi_hanh_id = :id ORDER BY thoi_gian_tao DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $lichId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Lấy thông tin chi tiết 1 phiên (kèm danh sách khách và trạng thái)
    public function getChiTietPhien($phienId, $lichId) {
    $sql = "
        SELECT 
            k.id AS khach_id, 
            k.ho_ten_khach, 
            b.sdt_lien_he,                                        
            COALESCE(ddct.trang_thai, 0) AS trang_thai,
            ddct.ghi_chu
        FROM khach_tour k
        JOIN bookings b ON k.booking_id = b.id
        LEFT JOIN chi_tiet_diem_danh ddct 
            -- Đã sửa: dùng tên cột chính xác là 'khach_id' và 'phien_id'
            ON k.id = ddct.khach_id AND ddct.phien_id = :phien_id 
        WHERE b.lich_khoi_hanh_id = :lich_id
        ORDER BY k.id
    ";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
        'phien_id' => $phienId,
        'lich_id' => $lichId
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    // 3. Tạo phiên điểm danh mới
    public function createPhien($lichId, $tieuDe)
    {
        $sql = "INSERT INTO phien_diem_danh (lich_khoi_hanh_id, tieu_de) VALUES (:id, :tieu_de)";
        $stmt = $this->conn->prepare($sql);
        if ($stmt->execute(['id' => $lichId, 'tieu_de' => $tieuDe])) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // 4. Lưu/Cập nhật trạng thái điểm danh cho 1 khách
    public function saveChiTiet($phienId, $khachId, $trangThai, $ghiChu = null)
    {
        // Dùng INSERT ... ON DUPLICATE KEY UPDATE để vừa thêm mới vừa cập nhật
        $sql = "INSERT INTO chi_tiet_diem_danh (phien_id, khach_id, trang_thai, ghi_chu) 
                VALUES (:phien_id, :khach_id, :trang_thai, :ghi_chu)
                ON DUPLICATE KEY UPDATE trang_thai = :trang_thai_update, ghi_chu = :ghi_chu_update";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'phien_id' => $phienId,
            'khach_id' => $khachId,
            'trang_thai' => $trangThai,
            'ghi_chu' => $ghiChu,
            'trang_thai_update' => $trangThai,
            'ghi_chu_update' => $ghiChu
        ]);
    }

    // 5. Xóa phiên điểm danh
    public function deletePhien($phienId)
    {
        $sql = "DELETE FROM phien_diem_danh WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $phienId]);
    }

    public function getPhienById($id)
    {
        $sql = "SELECT * FROM phien_diem_danh WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
