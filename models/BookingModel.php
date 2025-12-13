<?php
class BookingModel extends BaseModel {
    
    // 1. Lấy danh sách booking (Có hỗ trợ bộ lọc)
   public function getAllBookings($filters = []) {
        $sql = "SELECT b.*, t.ten_tour, lkh.ngay_khoi_hanh 
                FROM bookings b
                JOIN lich_khoi_hanh lkh ON b.lich_khoi_hanh_id = lkh.id
                JOIN tours t ON lkh.tour_id = t.id
                WHERE 1=1"; 
        
        $params = [];

        if (!empty($filters['keyword'])) {
            $sql .= " AND (b.ten_nguoi_dat LIKE :kw OR b.sdt_lien_he LIKE :kw OR b.id LIKE :kw)";
            $params['kw'] = '%' . $filters['keyword'] . '%';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND b.trang_thai = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['tour_id'])) {
            $sql .= " AND t.id = :tour_id";
            $params['tour_id'] = $filters['tour_id'];
        }

        // [MỚI] Lọc theo ID lịch khởi hành cụ thể
        if (!empty($filters['lich_id'])) {
            $sql .= " AND b.lich_khoi_hanh_id = :lich_id";
            $params['lich_id'] = $filters['lich_id'];
        }

        $sql .= " ORDER BY b.ngay_dat DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // [MỚI] Lấy danh sách tất cả hành khách của 1 lịch khởi hành (Để in danh sách đoàn)
    public function getPassengersByLich($lichId) {
        $sql = "SELECT kt.*, b.sdt_lien_he, b.ten_nguoi_dat 
                FROM khach_tour kt
                JOIN bookings b ON kt.booking_id = b.id
                WHERE b.lich_khoi_hanh_id = :lich_id 
                AND b.trang_thai IN ('DaXacNhan', 'DaThanhToan')
                ORDER BY kt.ho_ten_khach ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['lich_id' => $lichId]);
        return $stmt->fetchAll();
    }

    // 2. Cập nhật trạng thái
    public function updateStatus($id, $status) {
        $sql = "UPDATE bookings SET trang_thai = :status WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    // 3. Lấy chi tiết đơn hàng
    public function getDetail($id) {
        $sql = "SELECT b.*, t.ten_tour, lkh.ngay_khoi_hanh, lkh.ngay_ket_thuc 
                FROM bookings b
                JOIN lich_khoi_hanh lkh ON b.lich_khoi_hanh_id = lkh.id
                JOIN tours t ON lkh.tour_id = t.id
                WHERE b.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO bookings (lich_khoi_hanh_id, ten_nguoi_dat, sdt_lien_he, email_lien_he, so_nguoi_lon, so_tre_em, tong_tien, ghi_chu, trang_thai) 
                VALUES (:lich_id, :ten, :sdt, :email, :sl_lon, :sl_tre, :tong_tien, :ghi_chu, 'ChoXacNhan')";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($data);
        return $this->conn->lastInsertId();
    }

    // [MỚI] Hàm tạo booking và trả về ID (Dùng cho BookingController mới)
    public function createAndGetId($data) {
        $sql = "INSERT INTO bookings (lich_khoi_hanh_id, ten_nguoi_dat, sdt_lien_he, email_lien_he, so_nguoi_lon, so_tre_em, tong_tien, ghi_chu, trang_thai) 
                VALUES (:lich_khoi_hanh_id, :ten_nguoi_dat, :sdt_lien_he, :email_lien_he, :so_nguoi_lon, :so_tre_em, :tong_tien, :ghi_chu, :trang_thai)";
        
        $stmt = $this->conn->prepare($sql);
        if ($stmt->execute($data)) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getStatus($id) {
        $stmt = $this->conn->prepare("SELECT trang_thai FROM bookings WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchColumn(); 
    }

    // Cập nhật trạng thái và ghi lịch sử (Transaction an toàn)
    public function updateStatusAndLog($id, $newStatus, $adminName, $note = '') {
        try {
            $this->conn->beginTransaction();

            $booking = $this->getDetail($id); 
            
            if (!$booking) {
                $this->conn->rollBack();
                return false;
            }

            $oldStatus = $booking['trang_thai'];
            $lichId = $booking['lich_khoi_hanh_id'];
            $soKhach = (int)$booking['so_nguoi_lon'] + (int)$booking['so_tre_em'];

            if ($oldStatus === $newStatus) {
                $this->conn->commit();
                return true; 
            }

            $lkhModel = new LichKhoiHanhModel();

            // Nếu HỦY đơn -> Trả lại chỗ (Sử dụng hàm updateSoCho có sẵn)
            if ($newStatus === 'Huy' && $oldStatus !== 'Huy') {
                $lkhModel->updateSoCho($lichId, -($soKhach)); 
            }

            // Nếu KHÔI PHỤC đơn -> Kiểm tra và trừ lại chỗ
            if ($oldStatus === 'Huy' && $newStatus !== 'Huy') {
                // Kiểm tra chỗ trống (Dùng hàm checkSeatAvailability có sẵn)
                if (!$lkhModel->checkSeatAvailability($lichId, $soKhach)) {
                    throw new Exception("Lịch khởi hành này đã hết chỗ, không thể khôi phục booking!");
                }
                $lkhModel->updateSoCho($lichId, $soKhach);
            }

            $sql = "UPDATE bookings SET trang_thai = :status WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['status' => $newStatus, 'id' => $id]);

            $sqlHistory = "INSERT INTO booking_history (booking_id, nguoi_thay_doi, trang_thai_cu, trang_thai_moi, ghi_chu_thay_doi) 
                            VALUES (:id, :admin, :old, :new, :note)";
            $stmtHistory = $this->conn->prepare($sqlHistory);
            $stmtHistory->execute([
                'id' => $id,
                'admin' => $adminName,
                'old' => $oldStatus,
                'new' => $newStatus,
                'note' => $note
            ]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    public function getHistory($bookingId) {
        $sql = "SELECT * FROM booking_history WHERE booking_id = :id ORDER BY thoi_gian DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $bookingId]);
        return $stmt->fetchAll();
    }

    public function getGuests($bookingId) {
        $sql = "SELECT * FROM khach_tour WHERE booking_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $bookingId]);
        return $stmt->fetchAll();
    }
}
?>