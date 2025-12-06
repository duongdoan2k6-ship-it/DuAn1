<?php
// models/TourModel.php

class TourModel extends BaseModel
{
    public function getConnection()
    {
        return $this->conn;
    }

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

    public function getAllNhaCungCap()
    {
        $sql = "SELECT * FROM nhacungcap ORDER BY TenNhaCungCap ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // === CÁC PHƯƠNG THỨC MỚI THÊM ===

    public function getSelectedNhaCungCap($maTour)
    {
        $sql = "SELECT MaNhaCungCap FROM tournhacungcap WHERE MaTour = :maTour";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':maTour', $maTour);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function deleteLichTrinh($maTour)
    {
        $sql = "DELETE FROM lichtrinhtour WHERE MaTour = :maTour";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':maTour', $maTour);
        return $stmt->execute();
    }

    public function deleteChinhSach($maTour)
    {
        $sql = "DELETE FROM chinhsachtour WHERE MaTour = :maTour";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':maTour', $maTour);
        return $stmt->execute();
    }

    public function deleteNhaCungCap($maTour)
    {
        $sql = "DELETE FROM tournhacungcap WHERE MaTour = :maTour";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':maTour', $maTour);
        return $stmt->execute();
    }

    public function insertLichTrinh($maTour, $soNgay, $tieuDe, $hoatDong)
    {
        $sql = "INSERT INTO lichtrinhtour (MaTour, SoNgay, TieuDe, HoatDong)
                VALUES (:maTour, :soNgay, :tieuDe, :hoatDong)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':maTour', $maTour);
        $stmt->bindParam(':soNgay', $soNgay);
        $stmt->bindParam(':tieuDe', $tieuDe);
        $stmt->bindParam(':hoatDong', $hoatDong);

        return $stmt->execute();
    }

    public function insertChinhSach($maTour, $tenChinhSach, $noiDung)
    {
        $sql = "INSERT INTO chinhsachtour (MaTour, TenChinhSach, NoiDungChinhSach)
                VALUES (:maTour, :ten, :noiDung)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':maTour', $maTour);
        $stmt->bindParam(':ten', $tenChinhSach);
        $stmt->bindParam(':noiDung', $noiDung);

        return $stmt->execute();
    }

    public function insertNhaCungCap($maTour, $maNhaCungCap)
    {
        $sql = "INSERT INTO tournhacungcap (MaTour, MaNhaCungCap)
                VALUES (:maTour, :maNCC)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':maTour', $maTour);
        $stmt->bindParam(':maNCC', $maNhaCungCap);

        return $stmt->execute();
    }

    public function insertHinhAnh($maTour, $urlHinhAnh, $chuThich = '')
    {
        $sql = "INSERT INTO hinhanhtour (MaTour, URLHinhAnh, ChuThich)
                VALUES (:maTour, :url, :chuThich)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':maTour', $maTour);
        $stmt->bindParam(':url', $urlHinhAnh);
        $stmt->bindParam(':chuThich', $chuThich);

        return $stmt->execute();
    }

    public function getHinhAnhByTour($maTour)
    {
        $sql = "SELECT * FROM hinhanhtour WHERE MaTour = :maTour ORDER BY MaHinhAnh ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':maTour', $maTour);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getKhachTheoDoan($id_hdv)
    {
        $id_hdv = intval($id_hdv);

        $sql = "SELECT 
                dt.MaDatTour,
                dt.TenKhachHang AS TruongDoan,      
                dt.LienHeKhachHang AS SDT_LienHe,    
                kt.MaKhach,                      
                kt.TenKhachHang AS TenKhach,        
                kt.GioiTinh,
                kt.NamSinh,
                kt.SoCMND_HoChieu
                
            FROM phancongtour pct
            JOIN lichkhoihanh lkh ON pct.MaLichKhoiHanh = lkh.MaLichKhoiHanh
            JOIN dattour dt ON lkh.MaLichKhoiHanh = dt.MaLichKhoiHanh
            JOIN khachthamgiatour kt ON dt.MaDatTour = kt.MaDatTour
            WHERE 
                pct.MaHDV = $id_hdv 
                AND dt.MaTrangThai IN (1, 2) 
            ORDER BY dt.MaDatTour ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
