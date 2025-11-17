<?php

// Lấy đường dẫn thư mục gốc của dự án
$projectRoot = dirname(__DIR__);

// --- NẠP CONTROLLERS ---
require_once $projectRoot . '/controllers/BaseController.php';
require_once $projectRoot . '/controllers/HomeController.php';
require_once $projectRoot . '/controllers/TourController.php';
require_once $projectRoot . '/controllers/PersonController.php';



// --- NẠP MODELS ---
// (Nếu bạn có file BaseModel.php, hãy nạp nó trước)
// require_once $projectRoot . '/models/BaseModel.php'; 

require_once $projectRoot . '/models/BaseModel.php';
require_once $projectRoot . '/models/TourModel.php'; 
require_once $projectRoot . '/models/PersonModel.php'; 

// ----------------------------------------------------

// Lấy action từ URL, mặc định là '/' (trang chủ)
$action = $_GET['action'] ?? '/';

// Sử dụng match để gọi Controller tương ứng
match ($action) {
    '/'     => (new HomeController) -> index(),
    'list-tours' => (new TourController) -> index(),
    'add-tour' => (new TourController) -> add(),
    'person' => (new PersonController) -> index(),
    'delete' => (new PersonController) -> delete(),
    'formAddPerson' => (new PersonController) -> formAddPerson(),
};