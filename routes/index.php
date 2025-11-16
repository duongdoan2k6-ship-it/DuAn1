<?php

// Lấy đường dẫn thư mục gốc của dự án
$projectRoot = dirname(__DIR__);

// --- NẠP CONTROLLERS ---
require_once $projectRoot . '/controllers/BaseController.php';
require_once $projectRoot . '/controllers/HomeController.php';
require_once $projectRoot . '/controllers/TourController.php';

// --- NẠP MODELS ---
// (Nếu bạn có file BaseModel.php, hãy nạp nó trước)
// require_once $projectRoot . '/models/BaseModel.php'; 

require_once $projectRoot . '/models/BaseModel.php';

require_once $projectRoot . '/models/TourModel.php'; 

// ----------------------------------------------------

// Lấy action từ URL, mặc định là '/' (trang chủ)
$action = $_GET['action'] ?? '/';

// Sử dụng match để gọi Controller tương ứng
match ($action) {
    '/'     => (new HomeController)->index(),
    
    // Dòng này (dòng 27) giờ sẽ chạy được
    'list-tours' => (new TourController)->index(), 
    
    // ...
};