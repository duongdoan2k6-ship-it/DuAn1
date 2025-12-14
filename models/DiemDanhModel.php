<?php
class DiemDanhModel extends BaseModel
{
    // 1. Lấy danh sách các phiên của 1 lịch trình
    public function getPhienByLich($lichId)
    {
        // Sửa câu SQL để đếm số người
        $sql = "SELECT p.*, 
                       COUNT(ct.khach_id) as tong_so,
                       SUM(CASE WHEN ct.trang_thai = 1 THEN 1 ELSE 0 END) as co_mat
                FROM phien_diem_danh p
                LEFT JOIN chi_tiet_diem_danh ct ON p.id = ct.phien_id
                WHERE p.lich_khoi_hanh_id = :id 
                GROUP BY p.id
                ORDER BY p.thoi_gian_tao DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $lichId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Lấy chi tiết khách và trạng thái điểm danh trong 1 phiên
    public function getChiTietPhien($phienId, $lichId)
    {
        $sql = "
            SELECT 
                k.id AS khach_id, 
                k.ho_ten_khach, 
                b.sdt_lien_he,                                
                COALESCE(ddct.trang_thai, 0) AS trang_thai, -- Mặc định là 0 (Chưa điểm danh) nếu null
                ddct.ghi_chu
            FROM khach_tour k
            JOIN bookings b ON k.booking_id = b.id
            -- LEFT JOIN để vẫn hiện khách kể cả khi lỗi chưa có trong bảng chi tiết
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

    // 3. [QUAN TRỌNG] Tạo phiên mới & TỰ ĐỘNG THÊM KHÁCH
    public function createPhien($lichId, $tieuDe)
    {
        try {
            // Sử dụng Transaction để đảm bảo toàn vẹn dữ liệu
            $this->conn->beginTransaction();

            // Bước 1: Tạo phiên điểm danh (Header)
            $sql = "INSERT INTO phien_diem_danh (lich_khoi_hanh_id, tieu_de) VALUES (:id, :tieu_de)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['id' => $lichId, 'tieu_de' => $tieuDe]);
            $phienId = $this->conn->lastInsertId();

            // Bước 2: Lấy danh sách khách hợp lệ (Đã xác nhận/Đã thanh toán) từ tour này
            // Logic này sửa lỗi danh sách trống
            $sqlGetKhach = "SELECT kt.id 
                            FROM khach_tour kt
                            JOIN bookings b ON kt.booking_id = b.id
                            WHERE b.lich_khoi_hanh_id = :lid 
                            AND b.trang_thai IN ('DaXacNhan', 'DaThanhToan')";
            $stmtGet = $this->conn->prepare($sqlGetKhach);
            $stmtGet->execute(['lid' => $lichId]);
            $khachs = $stmtGet->fetchAll(PDO::FETCH_ASSOC);

            // Bước 3: Insert từng khách vào bảng chi tiết điểm danh
            if (!empty($khachs)) {
                $sqlInsertDetail = "INSERT INTO chi_tiet_diem_danh (phien_id, khach_id, trang_thai) VALUES (:pid, :kid, 0)";
                $stmtInsert = $this->conn->prepare($sqlInsertDetail);

                foreach ($khachs as $k) {
                    $stmtInsert->execute(['pid' => $phienId, 'kid' => $k['id']]);
                }
            }

            $this->conn->commit(); // Lưu tất cả
            return $phienId;
        } catch (Exception $e) {
            $this->conn->rollBack(); // Hoàn tác nếu lỗi
            return false;
        }
    }

    // 4. Lưu/Cập nhật trạng thái điểm danh
    public function saveChiTiet($phienId, $khachId, $trangThai, $ghiChu = null)
    {
        // Dùng ON DUPLICATE KEY UPDATE để vừa có thể Thêm mới vừa có thể Sửa
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

    public function lockPhien($phienId)
    {
        $sql = "UPDATE phien_diem_danh SET trang_thai_khoa = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $phienId]);
    }
}
