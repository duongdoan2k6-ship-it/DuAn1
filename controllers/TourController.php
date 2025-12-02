<?php

class TourController extends BaseController {
    
    
    public function index() {
        
        $tourModel = new TourModel();
        $allTours = $tourModel->getAllTours();

        
        $data = [
            'tours' => $allTours,
            'pageTitle' => 'Quản lý Danh sách Tour'
        ];

        
        $this->renderView('pages/tours/list_tour.php', $data);

    }

    public function add() {
    $tourModel = new TourModel();
    $dsLoaiTour = $tourModel->getAllLoaiTour();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $tenTour     = $_POST['ten_tour'];
        $maLoaiTour  = $_POST['ma_loai_tour'];
        $thoiLuong   = $_POST['thoi_luong'];
        $giaTour     = $_POST['gia_tour'];
        $diaDiem     = $_POST['dia_diem'];
        $moTa        = $_POST['mo_ta'];

        // Gọi đúng thứ tự tham số theo Model: 
        // insert($ten, $maLoai, $thoiLuong, $giaTour, $diaDiem, $moTa, $trangThai = 1)
        $isInserted = $tourModel->insert(
            $tenTour,
            $maLoaiTour,
            $thoiLuong,
            $giaTour,
            $diaDiem,
            $moTa,
            1 // trạng thái mặc định: đang hoạt động
        );

        if ($isInserted) {
            header('Location: index.php?action=list-tours');
            exit();
        } else {
            echo "Lỗi khi thêm tour mới.";
        }
    }

    $data = [
        'dsLoaiTour' => $dsLoaiTour,
        'pageTitle'  => 'Thêm Tour Mới'
    ];

    $this->renderView('pages/tours/add_tour.php', $data);
}



    
    public function edit() {
    if (!isset($_GET['id'])) {
        die("Thiếu ID tour cần sửa");
    }

    $maTour = $_GET['id'];
    $tourModel = new TourModel();
    $dsLoaiTour = $tourModel->getAllLoaiTour();

    // Lấy thông tin tour hiện tại để điền vào form
    $allTours = $tourModel->getAllTours();
    $currentTour = null;
    foreach ($allTours as $tour) {
        if ($tour['MaTour'] == $maTour) {
            $currentTour = $tour;
            break;
        }
    }

    if (!$currentTour) {
        die("Không tìm thấy tour này");
    }

    // Xử lý khi submit form
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tenTour = $_POST['ten_tour'];
        $maLoaiTour = $_POST['ma_loai_tour'];
        $thoiLuong = $_POST['thoi_luong'];
        $giaTour = $_POST['gia_tour'];
        $diaDiem = $_POST['dia_diem'];
        $moTa = $_POST['mo_ta'];

        $isUpdated = $tourModel->updateTour($maTour, $tenTour, $maLoaiTour, $thoiLuong, $giaTour, $diaDiem, $moTa);

        if ($isUpdated) {
            header('Location: index.php?action=list-tours');
            exit();
        } else {
            echo "Lỗi khi cập nhật tour.";
        }
    }

    
    $data = [
        'dsLoaiTour' => $dsLoaiTour,
        'tour' => $currentTour,
        'pageTitle' => 'Chỉnh sửa Tour'
    ];

    $this->renderView('pages/tours/edit_tour.php', $data);
}



    public function delete() {
    if (!isset($_GET['id'])) {
        die("Thiếu ID tour cần xoá");
    }

    $maTour = $_GET['id'];
    $tourModel = new TourModel();
$isDeleted = $tourModel->delete($maTour);

    if ($isDeleted) {
        header("Location: index.php?action=list-tours");
        exit();
    } else {
        echo "Lỗi: Không thể xoá tour.";
    }
}

}