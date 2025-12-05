<?php
// controllers/BaseController.php

class BaseController
{
    protected function renderView($viewPage, $data = [])
    {
        extract($data);

        // Thêm dấu / vào
        $viewPage = PATH_VIEW . '/' . $viewPage;

        // Sửa lỗi: Bỏ chữ "s" ở "layouts" để khớp với tên thư mục
        require_once PATH_VIEW . '/layout/master.php'; 
    }
}