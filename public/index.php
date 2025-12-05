<?php

// BƯỚC 1: ĐỊNH NGHĨA CÁC HẰNG SỐ CƠ SỞ VÀ ĐƯỜNG DẪN GỐC
// -------------------------------------------------------------
// Định nghĩa thư mục gốc của dự án (BASEXAM/ hay DA01-PRO1014.01/)
define('ROOT_PATH', dirname(__DIR__)); 

// Định nghĩa đường dẫn tới các thư mục quan trọng
define('CONFIG_PATH', ROOT_PATH . '/configs');
define('CONTROLLER_PATH', ROOT_PATH . '/controllers');
define('MODEL_PATH', ROOT_PATH . '/models');
define('ROUTE_PATH', ROOT_PATH . '/routes');

// BẮT ĐẦU PHIÊN (Session)
session_start();


// BƯỚC 2: TẢI CÁC FILE CẦN THIẾT (ĐÃ SỬA LẠI THỨ TỰ)
// -------------------------------------------------------------

// 🎯 BẮT BUỘC: Nạp 'env.php' TRƯỚC TIÊN
// Việc này để định nghĩa các hằng số (CONTROLLER_PATH, MODEL_PATH, DB_HOST,...)
require CONFIG_PATH . '/env.php';

// Tải các hàm helper (như asset_url(), base_url())
require CONFIG_PATH . '/helper.php'; 

// Tải autoloader (Nó sẽ SỬ DỤNG các hằng số đã được nạp từ env.php)
require CONFIG_PATH . '/autoloader.php'; 


// BƯỚC 3: CHẠY BỘ ĐỊNH TUYẾN
// -------------------------------------------------------------
// Nạp file routes/index.php.
// File này sẽ tự động đọc $_GET['action'] và gọi Controller tương ứng.
require ROUTE_PATH . '/index.php'; 
?>