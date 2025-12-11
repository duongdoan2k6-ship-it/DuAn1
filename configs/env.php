<?php
define('BASE_URL', 'http://localhost/DuAn1/'); // Sửa lại nếu bạn dùng port khác

// Cấu hình Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'Xuannam2006'); // XAMPP mặc định không có pass
define('DB_NAME', 'DuAn1_QL_Tours'); // Tên DB bạn vừa tạo
define('DB_CHARSET', 'utf8mb4');

// Đường dẫn hệ thống
define('PATH_ROOT', dirname(__DIR__) . '/');
define('PATH_VIEW', PATH_ROOT . 'views/');
define('PATH_CONTROLLER', PATH_ROOT . 'controllers/');
define('PATH_MODEL', PATH_ROOT . 'models/');
?>