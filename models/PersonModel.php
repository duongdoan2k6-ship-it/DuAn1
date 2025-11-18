<?php
class PersonModel extends BaseModel
{

    public function getAllPerson()
    {
        $sql = "SELECT * FROM `huongdanvien`";
        $stmt = $this -> conn -> prepare($sql);
        $stmt -> execute();
        return $stmt -> fetchAll();
    }

    public function deleteItem($MaHDV)
    {
        $sql = "DELETE FROM huongdanvien WHERE MaHDV = :MaHDV";
        $stmt = $this -> conn -> prepare($sql);
        $stmt->execute(['MaHDV' => $MaHDV]);
    }

    public function addPerson($name, $date, $img, $sdt, $languages, $exp, $health)
    {
        $sql = "INSERT INTO `huongdanvien` 
            (`HoTen`, `NgaySinh`, `AnhDaiDien`, `ThongTinLienHe`, `NgonNgu`, `KinhNghiem`, `TinhTrangSucKhoe`) 
            VALUES (:name, :date, :img, :sdt, :languages, :exp, :health)";

        $stmt = $this -> conn -> prepare($sql);

        $stmt -> execute([
            ':name'      => $name,
            ':date'      => $date,
            ':img'       => $img,
            ':sdt'       => $sdt,
            ':languages' => $languages,
            ':exp'       => $exp,
            ':health'    => $health
        ]);
    }
}
