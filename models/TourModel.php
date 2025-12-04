<?php
// models/TourModel.php

class TourModel extends BaseModel
{

    public function getAllTours()
{
    $sql = "SELECT 
                t.MaTour, 
                t.TenTour, 
                t.ThoiLuong, 
                t.DiaDiemKhoiHanh, 
                t.GiaTour,
                t.TrangThai,
                t.MoTa,
                lt.TenLoai,
                t.MaLoaiTour
            FROM tourdulich t
            LEFT JOIN loaitour lt 
                ON t.MaLoaiTour = lt.MaLoaiTour
            ORDER BY t.MaTour DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}




    // Ví dụ về việc gọi hàm được kế thừa:
    public function getAllLoaiTour()
    {

        $sql = "SELECT * FROM loaitour";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    public function insert($ten, $maLoai, $thoiLuong, $giaTour, $diaDiem, $moTa, $trangThai = 1)
{
    try {
        $sql = "INSERT INTO tourdulich 
                (TenTour, MaLoaiTour, ThoiLuong, GiaTour, TrangThai, DiaDiemKhoiHanh, MoTa) 
                VALUES (:ten, :maLoai, :thoiLuong, :giaTour, :trangThai, :diaDiem, :moTa)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':ten', $ten);
        $stmt->bindParam(':maLoai', $maLoai);
        $stmt->bindParam(':thoiLuong', $thoiLuong);
        $stmt->bindParam(':giaTour', $giaTour);
        $stmt->bindParam(':trangThai', $trangThai);
        $stmt->bindParam(':diaDiem', $diaDiem);
        $stmt->bindParam(':moTa', $moTa);

        return $stmt->execute();
    } catch (PDOException $e) {
        echo "Lỗi SQL: " . $e->getMessage();
        return false;
    }
}


    public function updateTour($maTour, $ten, $maLoai, $thoiLuong, $giaTour, $diaDiem, $moTa, $trangThai)
{
    try {
        $sql = "UPDATE tourdulich 
            SET TenTour = :ten, 
                MaLoaiTour = :maLoai, 
                ThoiLuong = :thoiLuong, 
                GiaTour = :giaTour, 
                DiaDiemKhoiHanh = :diaDiem, 
                MoTa = :moTa,
                TrangThai = :trangThai
            WHERE MaTour = :maTour";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':ten', $ten);
        $stmt->bindParam(':maLoai', $maLoai);
        $stmt->bindParam(':thoiLuong', $thoiLuong);
        $stmt->bindParam(':giaTour', $giaTour);
        $stmt->bindParam(':diaDiem', $diaDiem);
        $stmt->bindParam(':moTa', $moTa);
        $stmt->bindParam(':trangThai', $trangThai);
        $stmt->bindParam(':maTour', $maTour);

        return $stmt->execute();
    } catch (PDOException $e) {
        echo "Lỗi SQL: " . $e->getMessage();
        return false;
    }
}






    public function delete($maTour)
    {
        try {
            $sql = "DELETE FROM tourdulich WHERE MaTour = :maTour";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':maTour', $maTour);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Lỗi SQL: " . $e->getMessage();
            return false;
        }
    }


    public function getTourDetail($maTour)
{
    // Lấy thông tin tour
    $sql = "SELECT t.*, lt.TenLoai 
            FROM tourdulich t 
            LEFT JOIN loaitour lt ON t.MaLoaiTour = lt.MaLoaiTour
            WHERE t.MaTour = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $maTour);
    $stmt->execute();
    $tour = $stmt->fetch();

    if (!$tour) return null;

    // Lấy lịch trình
    $sqlLT = "SELECT * FROM lichtrinhtour WHERE MaTour = :id ORDER BY SoNgay ASC";
    $stmt = $this->conn->prepare($sqlLT);
    $stmt->bindParam(':id', $maTour);
    $stmt->execute();
    $tour['LichTrinh'] = $stmt->fetchAll();

    // Lấy hình ảnh
    $sqlHA = "SELECT * FROM hinhanhtour WHERE MaTour = :id";
    $stmt = $this->conn->prepare($sqlHA);
    $stmt->bindParam(':id', $maTour);
    $stmt->execute();
    $tour['HinhAnh'] = $stmt->fetchAll();

    // Lấy chính sách
    $sqlCS = "SELECT * FROM chinhsachtour WHERE MaTour = :id";
    $stmt = $this->conn->prepare($sqlCS);
    $stmt->bindParam(':id', $maTour);
    $stmt->execute();
    $tour['ChinhSach'] = $stmt->fetchAll();

    // Lấy nhà cung cấp của tour
    $sqlNCC = "SELECT ncc.* 
               FROM tournhacungcap tnc 
               JOIN nhacungcap ncc ON tnc.MaNhaCungCap = ncc.MaNhaCungCap
               WHERE tnc.MaTour = :id";
    $stmt = $this->conn->prepare($sqlNCC);
    $stmt->bindParam(':id', $maTour);
    $stmt->execute();
    $tour['NhaCungCap'] = $stmt->fetchAll();

    return $tour;
}

}
