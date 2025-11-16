<?php

class TourController extends BaseController {
    
    
    public function index() {
        
        $tourModel = new TourModel();
        $allTours = $tourModel->getAllTours();

        
        $data = [
            'tours' => $allTours,
            'pageTitle' => 'Quản lý Danh sách Tour'
        ];

        
        $this->renderView('pages/tours/list.php', $data);
    }
}