<?php
class SupplierModel extends BaseModel {
    
    // Lấy danh sách tất cả
    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM nha_cung_cap ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy chi tiết
    public function getDetail($id) {
        $stmt = $this->conn->prepare("SELECT * FROM nha_cung_cap WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // Thêm mới
    public function insert($data) {
        $sql = "INSERT INTO nha_cung_cap (ten_ncc, dich_vu, sdt, email, dia_chi) 
                VALUES (:ten, :dv, :sdt, :email, :dc)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    // Cập nhật
    public function update($id, $data) {
        $sql = "UPDATE nha_cung_cap 
                SET ten_ncc=:ten, dich_vu=:dv, sdt=:sdt, email=:email, dia_chi=:dc 
                WHERE id=:id";
        $data['id'] = $id;
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    // Xóa (Chỉ dùng khi an toàn)
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM nha_cung_cap WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // [QUAN TRỌNG] Kiểm tra xem NCC này có đang được sử dụng trong các Tour không?
    public function checkUsage($id) {
        // Kiểm tra trong bảng lịch dịch vụ (liên quan đến tiền nong)
        $sql = "SELECT COUNT(*) as total FROM lich_dich_vu WHERE ncc_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        
        return $result['total'] > 0; // Trả về True nếu đã từng sử dụng
    }
}
?>