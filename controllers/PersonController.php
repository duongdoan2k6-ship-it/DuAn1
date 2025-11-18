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

        $this->renderView('pages/tours/nhanSu.php', $data);
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
        $this->renderView('pages/tours/addPerson.php');
    }

    public function addPerson()
    {
        $name = $_POST['HoTen'];
        $date = $_POST['NgaySinh'];
        $img = $_FILES['AnhDaiDien'];
        $sdt = $_POST['ThongTinLienHe'];
        $languages = $_POST['NgonNgu'];
        $exp = $_POST['KinhNghiem'];
        $health = $_POST['TinhTrangSucKhoe'];

        $this->modelPerson->addPerson($name, $date, $img, $sdt, $languages, $exp, $health);
        header("Location: index.php?controller=person&action=person");
        exit();
    }
}
