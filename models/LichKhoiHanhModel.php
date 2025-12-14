<?php
class LichKhoiHanhModel extends BaseModel
{
    public function getToursByHdv($hdvId)
    {
        $sql = "SELECT lkh.*, t.ten_tour, t.anh_tour, t.so_ngay, lnv.vai_tro
                FROM lich_khoi_hanh lkh
                JOIN tours t ON lkh.tour_id = t.id
                JOIN lich_nhan_vien lnv ON lkh.id = lnv.lich_khoi_hanh_id
                WHERE lnv.nhan_vien_id = :hdv_id 
                AND lnv.vai_tro IN ('HDV_chinh', 'HDV_phu') 
                ORDER BY lkh.ngay_khoi_hanh ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['hdv_id' => $hdvId]);
        return $stmt->fetchAll();
    }
    public function countAll()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM lich_khoi_hanh");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
    public function getAllToursAdmin()
    {
        $sql = "SELECT 
                    lkh.*, 
                    t.ten_tour, 
                    t.so_ngay, 
                    t.so_dem, 
                    (SELECT nv.ho_ten FROM lich_nhan_vien lnv 
                     JOIN huong_dan_vien nv ON lnv.nhan_vien_id = nv.id
                     WHERE lnv.lich_khoi_hanh_id = lkh.id AND lnv.vai_tro = 'HDV_chinh' LIMIT 1) as ten_hdv
                FROM lich_khoi_hanh lkh
                JOIN tours t ON lkh.tour_id = t.id
                ORDER BY lkh.ngay_khoi_hanh DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function getAllToursList()
    {
        $stmt = $this->conn->prepare("SELECT id, ten_tour, so_ngay FROM tours");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function getAllHDVList()
    {
        $stmt = $this->conn->prepare("SELECT id, ho_ten, phan_loai FROM huong_dan_vien WHERE (trang_thai = 'SanSang' OR trang_thai = 'DangBan')");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function getAllNhanVienList()
    {
        $sql = "SELECT id, ho_ten, phan_loai_nhan_su, sdt 
                FROM huong_dan_vien 
                WHERE trang_thai IN ('SanSang', 'DangBan') 
                ORDER BY phan_loai_nhan_su ASC, ho_ten ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function insert($data)
    {
        $sql = "INSERT INTO lich_khoi_hanh 
                (tour_id, ngay_khoi_hanh, ngay_ket_thuc, so_cho_toi_da, diem_tap_trung, trang_thai) 
                VALUES 
                (:tour_id, :ngay_khoi_hanh, :ngay_ket_thuc, :so_cho_toi_da, :diem_tap_trung, 'NhanKhach')";

        unset($data['hdv_id'], $data['ghi_chu_nhan_su']);

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($data);
        return $this->conn->lastInsertId();
    }
    public function getDetail($id)
    {
        $sql = "SELECT l.*, t.ten_tour, t.so_ngay 
                FROM lich_khoi_hanh l
                JOIN tours t ON l.tour_id = t.id
                WHERE l.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    public function getDetailForUpdate($id)
    {
        $sql = "SELECT * FROM lich_khoi_hanh WHERE id = :id FOR UPDATE";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    public function update($id, $data)
    {
        $sql = "UPDATE lich_khoi_hanh 
                SET tour_id = :tour_id, 
                    ngay_khoi_hanh = :ngay_khoi_hanh, 
                    ngay_ket_thuc = :ngay_ket_thuc, 
                    so_cho_toi_da = :so_cho_toi_da,
                    diem_tap_trung = :diem_tap_trung,
                    trang_thai = :trang_thai 
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $data['id'] = $id;
        unset($data['hdv_id'], $data['ghi_chu_nhan_su']);
        return $stmt->execute($data);
    }
    public function delete($id)
    {
        $sql = "DELETE FROM lich_khoi_hanh WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    public function getOpenSchedules()
    {
        $sql = "SELECT lkh.*, t.ten_tour, t.gia_nguoi_lon, t.gia_tre_em 
                FROM lich_khoi_hanh lkh
                JOIN tours t ON lkh.tour_id = t.id
                WHERE lkh.trang_thai = 'NhanKhach' 
                AND lkh.ngay_khoi_hanh > NOW()
                ORDER BY lkh.ngay_khoi_hanh ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function updateSoCho($id, $soLuongKhach)
    {
        $sql = "UPDATE lich_khoi_hanh 
                SET so_cho_da_dat = so_cho_da_dat + :so_luong 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['so_luong' => $soLuongKhach, 'id' => $id]);
    }
    public function checkSeatAvailability($lich_id, $so_luong_khach)
    {
        $sql = "SELECT (so_cho_toi_da - so_cho_da_dat) as cho_trong 
                FROM lich_khoi_hanh 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $lich_id]);
        $result = $stmt->fetch();

        if ($result && $result['cho_trong'] >= $so_luong_khach) {
            return true;
        }
        return false;
    }
    public function getAssignedStaff($lich_id)
    {
        $sql = "SELECT lnv.*, nv.ho_ten, nv.sdt, nv.phan_loai_nhan_su
                FROM lich_nhan_vien lnv
                JOIN huong_dan_vien nv ON lnv.nhan_vien_id = nv.id
                WHERE lnv.lich_khoi_hanh_id = :id
                ORDER BY lnv.vai_tro ASC, nv.ho_ten ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $lich_id]);
        return $stmt->fetchAll();
    }
    public function assignStaff($lich_id, $nhan_vien_id, $vai_tro)
    {
        $sql = "INSERT INTO lich_nhan_vien (lich_khoi_hanh_id, nhan_vien_id, vai_tro)
                VALUES (:lich_id, :nv_id, :vai_tro)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':lich_id' => $lich_id,
            ':nv_id' => $nhan_vien_id,
            ':vai_tro' => $vai_tro
        ]);
    }
    public function unassignStaff($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM lich_nhan_vien WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    public function checkStaffAvailability($nhan_vien_id, $startDate, $endDate)
    {
        $sql = "SELECT t.ten_tour, lkh.ngay_khoi_hanh, lkh.ngay_ket_thuc
                FROM lich_nhan_vien lnv
                JOIN lich_khoi_hanh lkh ON lnv.lich_khoi_hanh_id = lkh.id
                JOIN tours t ON lkh.tour_id = t.id
                WHERE lnv.nhan_vien_id = :nv_id 
                AND lkh.trang_thai != 'Huy' 
                AND (
                    lkh.ngay_khoi_hanh < :end_date AND lkh.ngay_ket_thuc > :start_date
                )
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);

        $params = [
            ':nv_id' => $nhan_vien_id,
            ':start_date' => $startDate,
            ':end_date' => $endDate
        ];

        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getServices($lich_id)
    {
        $sql = "SELECT dv.*, ncc.ten_ncc, ncc.sdt, ncc.dich_vu as linh_vuc_ncc
                FROM lich_dich_vu dv
                JOIN nha_cung_cap ncc ON dv.ncc_id = ncc.id
                WHERE dv.lich_khoi_hanh_id = :id
                ORDER BY dv.ngay_su_dung ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $lich_id]);
        return $stmt->fetchAll();
    }
    public function addService($data)
    {
        $sql = "INSERT INTO lich_dich_vu (lich_khoi_hanh_id, ncc_id, loai_dich_vu, ngay_su_dung, so_luong, ghi_chu)
                VALUES (:lich_id, :ncc_id, :loai_dv, :ngay_sd, :sl, :ghi_chu)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }
    public function deleteService($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM lich_dich_vu WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    public function getServiceDetail($id)
    {
        $sql = "SELECT * FROM lich_dich_vu WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    public function updateService($id, $data)
    {
        $sql = "UPDATE lich_dich_vu 
                SET ncc_id = :ncc_id, 
                    loai_dich_vu = :loai_dv, 
                    ngay_su_dung = :ngay_sd, 
                    so_luong = :sl, 
                    ghi_chu = :ghi_chu
                WHERE id = :id";

        $data['id'] = $id;
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }
    public function getDetailForHdv($lichId)
    {
        $sql = "SELECT lkh.*, t.ten_tour, t.lich_trinh, t.luu_y, t.id as tour_id 
                FROM lich_khoi_hanh lkh
                JOIN tours t ON lkh.tour_id = t.id
                WHERE lkh.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $lichId]);
        return $stmt->fetch();
    }
    public function getTourItinerary($tourId)
    {
        $sql = "SELECT * FROM tour_itineraries 
                WHERE tour_id = :tour_id 
                ORDER BY ngay_thu ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['tour_id' => $tourId]);
        return $stmt->fetchAll();
    }
    public function checkAvailability($lichId, $soLuongKhach)
    {
        $sql = "SELECT (so_cho_toi_da - so_cho_da_dat) as cho_trong 
                FROM lich_khoi_hanh 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $lichId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && $result['cho_trong'] >= $soLuongKhach) {
            return true;
        }
        return false;
    }
    public function updateBookedSeats($lichId, $soLuongThem)
    {
        $sql = "UPDATE lich_khoi_hanh 
                SET so_cho_da_dat = so_cho_da_dat + :so_luong 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['so_luong' => $soLuongThem, 'id' => $lichId]);
    }
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE lich_khoi_hanh 
                SET trang_thai = :trang_thai 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'trang_thai' => $status,
            'id' => $id
        ]);
    }
    public function updateScheduleInfo($id, $data)
    {
        $sql = "UPDATE lich_khoi_hanh 
                SET ngay_khoi_hanh = :ngay_khoi_hanh, 
                    ngay_ket_thuc = :ngay_ket_thuc, 
                    so_cho_toi_da = :so_cho_toi_da,
                    diem_tap_trung = :diem_tap_trung,
                    trang_thai = :trang_thai 
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }
    public function getAvailableStaff($startDate, $endDate, $role = null)
    {
        // Giữ nguyên logic SQL kiểm tra ngày tháng
        $sql = "SELECT id, ho_ten, phan_loai_nhan_su, sdt 
            FROM huong_dan_vien 
            WHERE (trang_thai = 'SanSang' OR trang_thai = 'DangBan')
            AND id NOT IN (
                SELECT DISTINCT lnv.nhan_vien_id 
                FROM lich_nhan_vien lnv
                JOIN lich_khoi_hanh lkh ON lnv.lich_khoi_hanh_id = lkh.id
                WHERE lkh.trang_thai != 'Huy'
                AND (
                    lkh.ngay_khoi_hanh < :end_date AND lkh.ngay_ket_thuc > :start_date
                )
            )";

        // [THÊM] Nếu có truyền vai trò vào thì lọc thêm vai trò đó
        if ($role) {
            $sql .= " AND phan_loai_nhan_su = :role";
        }

        $sql .= " ORDER BY phan_loai_nhan_su ASC, ho_ten ASC";

        $stmt = $this->conn->prepare($sql);

        // Tạo mảng tham số
        $params = [
            ':start_date' => $startDate,
            ':end_date' => $endDate
        ];

        // [THÊM] Bind tham số role nếu có
        if ($role) {
            $params[':role'] = $role;
        }

        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getUpcomingSchedulesByStaff($staffId)
    {
        $sql = "SELECT t.ten_tour, lkh.ngay_khoi_hanh, lkh.id
                FROM lich_nhan_vien lnv
                JOIN lich_khoi_hanh lkh ON lnv.lich_khoi_hanh_id = lkh.id
                JOIN tours t ON lkh.tour_id = t.id
                WHERE lnv.nhan_vien_id = :id
                AND lkh.trang_thai != 'Huy'
                AND lkh.ngay_khoi_hanh >= CURDATE() 
                ORDER BY lkh.ngay_khoi_hanh ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $staffId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
