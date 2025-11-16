<?php

class HomeController extends BaseController
{
    public function index()
    {
        // Tạm thời chỉ hiển thị một cái gì đó
        echo "Đây là trang chủ (HomeController)";

        // Hoặc bạn có thể render một view nếu muốn
        // $this->renderView('home.php', []);
    }
}