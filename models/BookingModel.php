<?php
class BookingModel extends BaseModel
{
    protected $table = 'dattour';

    // 1. Lấy danh sách tất cả đơn hàng (để hiện trang index)
    public function getAll()
    {
        $sql = "
            SELECT 
                dt.*,
                tt.TenTrangThai,
                lk.NgayKhoiHanh,
                lk.NgayKetThuc,
                t.TenTour -- Lấy thêm tên tour cho dễ nhìn
            FROM dattour dt
            LEFT JOIN trangthaidattour tt ON dt.MaTrangThai = tt.MaTrangThai
            LEFT JOIN lichkhoihanh lk ON dt.MaLichKhoiHanh = lk.MaLichKhoiHanh
            LEFT JOIN tourdulich t ON lk.MaTour = t.MaTour
            ORDER BY dt.MaDatTour DESC
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. [MỚI] Lấy danh sách lịch trình sắp tới (để đổ vào Dropdown trang Thêm mới)
    public function getAvailableSchedules()
    {
        $sql = "SELECT 
                    lk.MaLichKhoiHanh, 
                    lk.NgayKhoiHanh, 
                    lk.SoChoToiDa, 
                    lk.SoChoDaDat, 
                    lk.GiaNguoiLon, 
                    lk.GiaTreEm,
                    t.TenTour 
                FROM lichkhoihanh lk
                JOIN tourdulich t ON lk.MaTour = t.MaTour
                WHERE lk.NgayKhoiHanh >= CURDATE() -- Chỉ lấy lịch tương lai
                AND (lk.SoChoToiDa - lk.SoChoDaDat) > 0 -- Chỉ lấy lịch còn chỗ
                ORDER BY lk.NgayKhoiHanh ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. [MỚI] Hàm Thêm Booking vào Database
    public function createBooking($data)
    {
        try {
            // BƯỚC 1: Lấy giá tiền từ bảng Lịch để tính toán chính xác
            $sqlPrice = "SELECT GiaNguoiLon, GiaTreEm, SoChoDaDat, SoChoToiDa 
                         FROM lichkhoihanh WHERE MaLichKhoiHanh = :id";
            $stmtPrice = $this->conn->prepare($sqlPrice);
            $stmtPrice->execute(['id' => $data['MaLichKhoiHanh']]);
            $lich = $stmtPrice->fetch(PDO::FETCH_ASSOC);

            if (!$lich) return false;

            // BƯỚC 2: Xử lý logic dữ liệu cho khớp với Database

            // a. Tính tổng khách (Vì DB chỉ lưu tổng)
            $tongKhach = $data['SoLuongNguoiLon'] + $data['SoLuongTreEm'];

            // b. Tính tổng tiền (Dựa vào giá trong DB * số lượng nhập từ form)
            $tongTien = ($data['SoLuongNguoiLon'] * $lich['GiaNguoiLon']) + ($data['SoLuongTreEm'] * $lich['GiaTreEm']);

            // c. Ghép thông tin liên hệ (Vì DB chỉ có 1 cột LienHeKhachHang)
            // Cấu trúc ghép: "SĐT - Email - Địa chỉ"
            $arrLienHe = [];
            if (!empty($data['SoDienThoai'])) $arrLienHe[] = $data['SoDienThoai'];
            if (!empty($data['Email']))       $arrLienHe[] = $data['Email'];
            if (!empty($data['DiaChi']))      $arrLienHe[] = $data['DiaChi'];
            $lienHeGhep = implode(" - ", $arrLienHe);

            // d. Kiểm tra xem còn đủ chỗ không
            if (($lich['SoChoDaDat'] + $tongKhach) > $lich['SoChoToiDa']) {
                return "full_slots"; // Hết chỗ
            }

            // BƯỚC 3: CÂU LỆNH INSERT CHUẨN (Khớp 100% với ảnh bạn gửi)
            $sql = "INSERT INTO dattour 
                    (MaLichKhoiHanh, TenKhachHang, LienHeKhachHang, SoLuongKhach, TongTien, MaTrangThai, GhiChu, NgayDatTour) 
                    VALUES 
                    (:maLich, :tenKhach, :lienHe, :slKhach, :tongTien, 1, :ghiChu, NOW())";

            $stmt = $this->conn->prepare($sql);

            $result = $stmt->execute([
                'maLich'   => $data['MaLichKhoiHanh'],
                'tenKhach' => $data['HoTen'],        // Map từ Form 'HoTen' sang DB 'TenKhachHang'
                'lienHe'   => $lienHeGhep,           // Map chuỗi đã ghép sang DB 'LienHeKhachHang'
                'slKhach'  => $tongKhach,            // Map tổng khách
                'tongTien' => $tongTien,
                'ghiChu'   => $data['GhiChu'] ?? ''
            ]);

            // BƯỚC 4: Cập nhật số chỗ đã đặt trong bảng lichkhoihanh
            if ($result) {
                $sqlUpdate = "UPDATE lichkhoihanh SET SoChoDaDat = SoChoDaDat + :sl WHERE MaLichKhoiHanh = :id";
                $this->conn->prepare($sqlUpdate)->execute(['sl' => $tongKhach, 'id' => $data['MaLichKhoiHanh']]);
                return true;
            }
            return false;
        } catch (Exception $e) {
            echo "<div style='background:red; color:white; padding:10px;'>LỖI SQL: " . $e->getMessage() . "</div>";
            die();
        }
    }
    // 4. Hủy Booking (Cập nhật lại status và trả chỗ)
    public function cancelBooking($MaDatTour)
    {
        // Lấy thông tin đơn hàng
        $sqlInfo = "SELECT MaLichKhoiHanh, SoLuongKhach, MaTrangThai FROM dattour WHERE MaDatTour = :id";
        $stmtInfo = $this->conn->prepare($sqlInfo);
        $stmtInfo->execute(['id' => $MaDatTour]);
        $booking = $stmtInfo->fetch(PDO::FETCH_ASSOC);

        // Chỉ cho phép hủy khi đang ở trạng thái 1 (Chờ xác nhận)
        // Nếu muốn cho phép hủy cả khi đã xác nhận (2) thì sửa điều kiện ở đây
        if ($booking && $booking['MaTrangThai'] == 1) {

            $statusHuy = 3; // 3 là Đã hủy

            // Cập nhật trạng thái
            $sqlUpdate = "UPDATE dattour SET MaTrangThai = :status WHERE MaDatTour = :id";
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            $stmtUpdate->execute(['status' => $statusHuy, 'id' => $MaDatTour]);

            // Trả lại số chỗ ngồi cho lịch trình
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

    // 5. Cập nhật trạng thái (Dùng cho Admin Edit)
    public function updateStatus($id, $newStatus)
    {
        $allowed = [1, 2, 3];
        if (!in_array($newStatus, $allowed)) return false;

        // Nếu chuyển sang trạng thái 3 (Hủy) -> Gọi hàm hủy để xử lý trả chỗ
        if ($newStatus == 3) {
            return $this->cancelBooking($id);
        }

        // Các trạng thái khác (1, 2)
        $sql = "UPDATE dattour SET MaTrangThai = :status WHERE MaDatTour = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['status' => $newStatus, 'id' => $id]);
    }

    // 6. Tìm chi tiết theo ID
    // 6. Tìm chi tiết theo ID (Đã sửa để lấy thêm giá tiền)
    public function findById($id)
    {
        $sql = "SELECT 
                    dt.*, 
                    tt.TenTrangThai, 
                    lk.NgayKhoiHanh, 
                    lk.NgayKetThuc, 
                    lk.SoChoToiDa, 
                    lk.SoChoDaDat, 
                    lk.GiaNguoiLon,
                    lk.GiaTreEm,     
                    t.TenTour
                FROM dattour dt
                LEFT JOIN trangthaidattour tt ON dt.MaTrangThai = tt.MaTrangThai
                LEFT JOIN lichkhoihanh lk ON dt.MaLichKhoiHanh = lk.MaLichKhoiHanh
                LEFT JOIN tourdulich t ON lk.MaTour = t.MaTour
                WHERE dt.MaDatTour = :id LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- Thay thế hàm updateBooking cũ trong models/BookingModel.php ---

    // --- Thay thế hàm updateBooking cũ trong models/BookingModel.php ---

    public function updateBooking($id, $data)
    {
        try {
            // 1. Lấy thông tin đơn hàng CŨ
            $oldBooking = $this->findById($id);
            if (!$oldBooking) return false;

            // Tính toán chênh lệch khách
            $oldTotalGuests = $oldBooking['SoLuongKhach'];
            $newTotalGuests = $data['SoLuongNguoiLon'] + $data['SoLuongTreEm'];
            $diff = $newTotalGuests - $oldTotalGuests;

            // 2. Kiểm tra chỗ trống (Nếu khách TĂNG)
            if ($diff > 0) {
                $sqlCheck = "SELECT SoChoToiDa, SoChoDaDat FROM lichkhoihanh WHERE MaLichKhoiHanh = :maLich";
                $stmtCheck = $this->conn->prepare($sqlCheck);
                $stmtCheck->execute(['maLich' => $oldBooking['MaLichKhoiHanh']]);
                $schedule = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if ($schedule) {
                    $seatsAvailable = $schedule['SoChoToiDa'] - $schedule['SoChoDaDat'];
                    if ($diff > $seatsAvailable) {
                        return 'not_enough_seats';
                    }
                }
            }

            // 3. Xử lý Hủy (nếu có)
            if ($data['MaTrangThai'] == 3) {
                if ($oldBooking['MaTrangThai'] != 3) {
                    $this->cancelBooking($id);
                    return true;
                }
            }

            // 4. CẬP NHẬT DATABASE (Đã xóa dòng NgayCapNhat gây lỗi)
            $sql = "UPDATE dattour 
                    SET TenKhachHang = :ten, 
                        LienHeKhachHang = :lienhe, 
                        GhiChu = :ghichu, 
                        MaTrangThai = :status,
                        SoLuongKhach = :slTong,
                        TongTien = :tongTien
                    WHERE MaDatTour = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                'ten'      => $data['TenKhachHang'],
                'lienhe'   => $data['LienHeKhachHang'],
                'ghichu'   => $data['GhiChu'],
                'status'   => $data['MaTrangThai'],
                'slTong'   => $newTotalGuests,
                'tongTien' => $data['TongTien'],
                'id'       => $id
            ]);

            // 5. CẬP NHẬT LỊCH TRÌNH
            if ($diff != 0 && $data['MaTrangThai'] != 3) {
                $sqlUpdateLich = "UPDATE lichkhoihanh 
                                  SET SoChoDaDat = SoChoDaDat + :diff 
                                  WHERE MaLichKhoiHanh = :maLich";
                $this->conn->prepare($sqlUpdateLich)->execute([
                    'diff' => $diff,
                    'maLich' => $oldBooking['MaLichKhoiHanh']
                ]);
            }

            return true;
        } catch (Exception $e) {
            echo "Lỗi SQL: " . $e->getMessage();
            die();
            return false;
        }
    }
    public function getBookingById($id)
    {
        try {
            $sql = "SELECT * FROM dattour WHERE MaDatTour = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }

    public function deleteBooking($id)
    {
        try {
            $this->conn->beginTransaction();
            $sqlDelGuest = "DELETE FROM khachthamgiatour WHERE MaDatTour = :id";
            $stmtGuest = $this->conn->prepare($sqlDelGuest);
            $stmtGuest->execute([':id' => $id]);

            $sqlDelBooking = "DELETE FROM dattour WHERE MaDatTour = :id";
            $stmtBooking = $this->conn->prepare($sqlDelBooking);
            $stmtBooking->execute([':id' => $id]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
