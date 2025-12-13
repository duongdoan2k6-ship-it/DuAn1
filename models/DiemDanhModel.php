<?php
class DiemDanhModel extends BaseModel
{
    // 1. Lấy danh sách các phiên của 1 lịch trình
    public function getPhienByLich($lichId)
    {
        $sql = "SELECT * FROM phien_diem_danh WHERE lich_khoi_hanh_id = :id ORDER BY thoi_gian_tao DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $lichId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Lấy chi tiết khách và trạng thái điểm danh trong 1 phiên
    public function getChiTietPhien($phienId, $lichId) {
        $sql = "
            SELECT 
                k.id AS khach_id, 
                k.ho_ten_khach, 
                b.sdt_lien_he,                                
                COALESCE(ddct.trang_thai, 0) AS trang_thai, -- Nếu chưa có record thì mặc định là 0 (Vắng/Chưa điểm)
                ddct.ghi_chu
            FROM khach_tour k
            JOIN bookings b ON k.booking_id = b.id
            LEFT JOIN chi_tiet_diem_danh ddct 
                ON k.id = ddct.khach_id AND ddct.phien_id = :phien_id 
            WHERE b.lich_khoi_hanh_id = :lich_id
            -- Chỉ lấy khách của booking Đã xác nhận hoặc Đã thanh toán
            AND b.trang_thai IN ('DaXacNhan', 'DaThanhToan')
            ORDER BY k.id
        ";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'phien_id' => $phienId,
            'lich_id' => $lichId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. [NÂNG CẤP] Tạo phiên điểm danh mới & Tự động thêm khách vào danh sách
    public function createPhien($lichId, $tieuDe)
    {
        try {
            // Bắt đầu transaction để đảm bảo dữ liệu nhất quán
            $this->conn->beginTransaction();

            // Bước 1: Tạo phiên điểm danh (Header)
            $sql = "INSERT INTO phien_diem_danh (lich_khoi_hanh_id, tieu_de) VALUES (:id, :tieu_de)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['id' => $lichId, 'tieu_de' => $tieuDe]);
            $phienId = $this->conn->lastInsertId();

            // Bước 2: Lấy danh sách khách hợp lệ (Đã xác nhận/Đã thanh toán) của tour này
            $sqlGetKhach = "SELECT kt.id 
                            FROM khach_tour kt
                            JOIN bookings b ON kt.booking_id = b.id
                            WHERE b.lich_khoi_hanh_id = :lid 
                            AND b.trang_thai IN ('DaXacNhan', 'DaThanhToan')";
            $stmtGet = $this->conn->prepare($sqlGetKhach);
            $stmtGet->execute(['lid' => $lichId]);
            $khachs = $stmtGet->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($khachs)) {
                $sqlInsertDetail = "INSERT INTO chi_tiet_diem_danh (phien_id, khach_id, trang_thai) VALUES (:pid, :kid, 0)";
                $stmtInsert = $this->conn->prepare($sqlInsertDetail);

                foreach ($khachs as $k) {
                    $stmtInsert->execute(['pid' => $phienId, 'kid' => $k['id']]);
                }
            }
            $this->conn->commit();
            return $phienId;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function saveChiTiet($phienId, $khachId, $trangThai, $ghiChu = null)
    {
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
?>