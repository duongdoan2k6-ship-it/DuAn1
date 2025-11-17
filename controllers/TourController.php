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
            $tenTour = $_POST['ten_tour'];
            $maLoaiTour = $_POST['ma_loai_tour'];
            $thoiLuong = $_POST['thoi_luong'];
            $diaDiem = $_POST['dia_diem'];
            $moTa = $_POST['mo_ta'];

            
            $isInserted = $tourModel->insert($tenTour, $maLoaiTour, $thoiLuong, $diaDiem, $moTa);

            if ($isInserted) {
                
                header('Location: index.php?action=list-tours');
                exit();
            } else {
                
                echo "Lỗi khi thêm tour mới.";
            }
        }

        
        $data = [
            'dsLoaiTour' => $dsLoaiTour,
            'pageTitle' => 'Thêm Tour Mới'
        ];

        
        $this->renderView('pages/tours/add_tour.php', $data);
    }   
}