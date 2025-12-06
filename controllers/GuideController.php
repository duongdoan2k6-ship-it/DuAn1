<?php

class GuideController extends BaseController
{
    private $tourModel;

    public function __construct()
    {
        $this->tourModel = new TourModel();
    }

    public function danhSachKhach()
    {
        $id_hdv = 1;
        $list_khach = $this->tourModel->getKhachTheoDoan($id_hdv);

        $data = [
            'list_khach' => $list_khach,
            'pageTitle' => 'Danh Sách'
        ];
        $this->renderView('pages/hdv/List_guide/list_guide.php', $data);
    }
}
