<?php
class PersonModel extends BaseModel
{

    public function getAllPerson()
    {
        $sql = "SELECT * FROM `huongdanvien`";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function deleteItem($MaHDV)
    {
        $sql = "DELETE FROM huongdanvien WHERE MaHDV = :MaHDV";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['MaHDV' => $MaHDV]);
    }

    public function addPerson($hoTen, $gioiTinh, $ngaySinh, $anhDaiDien, $sdt, $email, $diaChi, $cccd, $maThe, $loaiHDV, $trangThai, $ngonNgu, $kinhNghiem, $sucKhoe, $soTour, $danhGia)
    {

        $sql = "INSERT INTO `huongdanvien` 
        (
            `HoTen`, `GioiTinh`, `NgaySinh`, `AnhDaiDien`, `SDT`, `Email`, `DiaChi`, `CCCD`, 
            `MaThe`, `LoaiHDV`, `TrangThai`, `NgonNgu`, `KinhNghiem`, `TinhTrangSucKhoe`, `Sotour`, `DanhGia`
        ) 
        VALUES 
        (
            :HoTen, :GioiTinh, :NgaySinh, :AnhDaiDien, :SDT, :Email, :DiaChi, :CCCD, 
            :MaThe, :LoaiHDV, :TrangThai, :NgonNgu, :KinhNghiem, :TinhTrangSucKhoe, :Sotour, :DanhGia
        )";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':HoTen'            => $hoTen,
            ':GioiTinh'         => $gioiTinh,
            ':NgaySinh'         => $ngaySinh,
            ':AnhDaiDien'       => $anhDaiDien,
            ':SDT'              => $sdt,
            ':Email'            => $email,
            ':DiaChi'           => $diaChi,
            ':CCCD'             => $cccd,
            ':MaThe'            => $maThe,
            ':LoaiHDV'          => $loaiHDV,
            ':TrangThai'        => $trangThai,
            ':NgonNgu'          => $ngonNgu,
            ':KinhNghiem'       => $kinhNghiem,
            ':TinhTrangSucKhoe' => $sucKhoe,
            ':Sotour'           => $soTour,
            ':DanhGia'          => $danhGia
        ]);
    }

    public function getPersonById($MaHDV)
    {
        $sql = "SELECT * FROM huongdanvien WHERE MaHDV = :MaHDV";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['MaHDV' => $MaHDV]);
        return $stmt->fetch();
    }


    public function updatePerson($id, $hoTen, $gioiTinh, $ngaySinh, $anhDaiDien, $sdt, $email, $diaChi, $cccd, $maThe, $loaiHDV, $trangThai, $ngonNgu, $kinhNghiem, $sucKhoe)
    {
        $sql = "UPDATE huongdanvien 
                SET HoTen = :HoTen,
                    GioiTinh = :GioiTinh,
                    NgaySinh = :NgaySinh,
                    AnhDaiDien = :AnhDaiDien,
                    SDT = :SDT,
                    Email = :Email,
                    DiaChi = :DiaChi,
                    CCCD = :CCCD,
                    MaThe = :MaThe,
                    LoaiHDV = :LoaiHDV,
                    TrangThai = :TrangThai,
                    NgonNgu = :NgonNgu,
                    KinhNghiem = :KinhNghiem,
                    TinhTrangSucKhoe = :TinhTrangSucKhoe
WHERE MaHDV = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':HoTen'            => $hoTen,
            ':GioiTinh'         => $gioiTinh,
            ':NgaySinh'         => $ngaySinh,
            ':AnhDaiDien'       => $anhDaiDien,
            ':SDT'              => $sdt,
            ':Email'            => $email,
            ':DiaChi'           => $diaChi,
            ':CCCD'             => $cccd,
            ':MaThe'            => $maThe,
            ':LoaiHDV'          => $loaiHDV,
            ':TrangThai'        => $trangThai,
            ':NgonNgu'          => $ngonNgu,
            ':KinhNghiem'       => $kinhNghiem,
            ':TinhTrangSucKhoe' => $sucKhoe,
            ':id'               => $id
        ]);
    }
}