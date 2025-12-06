<?php

class PersonController extends BaseController
{

    public $modelPerson;
    public function __construct()
    {
        $this->modelPerson = new PersonModel();
    }

    public function index()
    {
        $person = $this->modelPerson->getAllPerson();
        $data = [
            'person' => $person,
            'pageTitle' => 'Danh sách nhân sự'
        ];

        $this->renderView('pages/admin/person/nhanSu.php', $data);
    }

    public function delete()
    {
        if (isset($_GET['id'])) {
            $MaHDV = $_GET['id'];
            $this->modelPerson->deleteItem($MaHDV);
        }
        header("Location: index.php?controller=person&action=person");
        exit();
    }

    public function formAddPerson()
    {
        $this->renderView('pages/admin/person/addPerson.php');
    }

    public function addPerson()
    {
        $hoTen = $_POST['HoTen'];
        $ngaySinh = $_POST['NgaySinh'];
        $gioiTinh = $_POST['GioiTinh'];
        $sdt = $_POST['SDT'];
        $email = $_POST['Email'];
        $diaChi = $_POST['DiaChi'];
        $cccd = $_POST['CCCD'];
        $maThe = $_POST['MaThe'];
        $loaiHDV = $_POST['LoaiHDV'];
        $trangThai = $_POST['TrangThai'];
        $ngonNgu = $_POST['NgonNgu'];
        $kinhNghiem = $_POST['KinhNghiem'];
        $sucKhoe = $_POST['TinhTrangSucKhoe'];

        $soTour = 0;
        $danhGia = 0;

        $anhDaiDien = '';
        if (isset($_FILES['AnhDaiDien']) && $_FILES['AnhDaiDien']['error'] == 0) {
            $anhDaiDien = time() . '_' . $_FILES['AnhDaiDien']['name'];
            move_uploaded_file($_FILES['AnhDaiDien']['tmp_name'], "uploads/" . $anhDaiDien);
        }

        $this->modelPerson->addPerson(
            $hoTen,
            $gioiTinh,
            $ngaySinh,
            $anhDaiDien,
            $sdt,
            $email,
            $diaChi,
            $cccd,
            $maThe,
            $loaiHDV,
            $trangThai,
            $ngonNgu,
            $kinhNghiem,
            $sucKhoe,
            $soTour,
            $danhGia
        );

        header("Location: index.php?controller=person&action=person");
        exit();
    }


    public function editPerson()
    {
        if (isset($_GET['id'])) {
            $MaHDV = $_GET['id'];
            $person = $this->modelPerson->getPersonById($MaHDV);

            if (!$person) {
                header("Location: index.php?controller=person&action=person");
                exit();
            }

            $this->renderView('pages/admin/person/editPerson.php', [
                'person' => $person
            ]);
        }
    }

    public function updatePerson()
    {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];

            $hoTen      = $_POST['HoTen'];
            $ngaySinh   = $_POST['NgaySinh'];
            $gioiTinh   = $_POST['GioiTinh'];
            $cccd       = $_POST['CCCD'];
            $sdt        = $_POST['SDT'];
            $email      = $_POST['Email'];
$diaChi     = $_POST['DiaChi'];
            $maThe      = $_POST['MaThe'];
            $loaiHDV    = $_POST['LoaiHDV'];
            $trangThai  = $_POST['TrangThai'];
            $ngonNgu    = $_POST['NgonNgu'];
            $kinhNghiem = $_POST['KinhNghiem'];
            $sucKhoe    = $_POST['TinhTrangSucKhoe'];

            $anhDaiDien = '';

            if (isset($_FILES['AnhDaiDien']) && $_FILES['AnhDaiDien']['error'] == 0 && !empty($_FILES['AnhDaiDien']['name'])) {
                $anhDaiDien = time() . '_' . $_FILES['AnhDaiDien']['name'];
                move_uploaded_file($_FILES['AnhDaiDien']['tmp_name'], "uploads/" . $anhDaiDien);
            } else {

                $anhDaiDien = $_POST['AnhCu'];
            }

            $this->modelPerson->updatePerson(
                $id,
                $hoTen,
                $gioiTinh,
                $ngaySinh,
                $anhDaiDien,
                $sdt,
                $email,
                $diaChi,
                $cccd,
                $maThe,
                $loaiHDV,
                $trangThai,
                $ngonNgu,
                $kinhNghiem,
                $sucKhoe
            );

            header("Location: index.php?controller=person&action=person");
            exit();
        }
    }
}