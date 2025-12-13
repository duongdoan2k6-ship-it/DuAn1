<?php
class NhatKyTourModel extends BaseModel
{
    protected $tableName = 'nhat_ky_tour';

    public function getLogsByLichId($lich_khoi_hanh_id)
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE lich_khoi_hanh_id = :id ORDER BY thoi_gian_tao DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $lich_khoi_hanh_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // [MỚI] Lấy chi tiết 1 nhật ký theo ID
    public function getLogById($id)
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addNhatKy($data)
    {
        $sql = "INSERT INTO {$this->tableName} (lich_khoi_hanh_id, tieu_de, noi_dung, su_co, phan_hoi_khach, hinh_anh) 
                VALUES (:lich_khoi_hanh_id, :tieu_de, :noi_dung, :su_co, :phan_hoi_khach, :hinh_anh)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function updateNhatKy($id, $data)
    {
        // [ĐÃ SỬA] Thêm cập nhật thoi_gian_tao = NOW()
        // Kiểm tra xem có cập nhật ảnh mới không
        if (!empty($data['hinh_anh'])) {
            $sql = "UPDATE {$this->tableName} 
                    SET tieu_de = :tieu_de, 
                        noi_dung = :noi_dung, 
                        su_co = :su_co, 
                        phan_hoi_khach = :phan_hoi_khach, 
                        hinh_anh = :hinh_anh, 
                        thoi_gian_tao = NOW() 
                    WHERE id = :id";
        } else {
            // Nếu không có ảnh mới, không update cột hinh_anh
            $sql = "UPDATE {$this->tableName} 
                    SET tieu_de = :tieu_de, 
                        noi_dung = :noi_dung, 
                        su_co = :su_co, 
                        phan_hoi_khach = :phan_hoi_khach, 
                        thoi_gian_tao = NOW() 
                    WHERE id = :id";
            unset($data['hinh_anh']); // Bỏ key hinh_anh khỏi mảng data
        }

        $data['id'] = $id; // Thêm id vào mảng tham số
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    // [MỚI] Xóa nhật ký
    public function deleteNhatKy($id)
    {
        $sql = "DELETE FROM {$this->tableName} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
?>