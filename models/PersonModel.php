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
        $stmt -> execute(['MaHDV' => $MaHDV]);
    }
}
