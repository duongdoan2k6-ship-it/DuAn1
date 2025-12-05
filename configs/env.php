<?php
define('BASE_URL',          'http://localhost/DA01-PRO1014.01/');

// Đường dẫn tới các thư mục quan trọng
define('PATH_VIEW',         ROOT_PATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR);
define('PATH_CONTROLLER',   ROOT_PATH . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR);
define('PATH_MODEL',        ROOT_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR);

// Sửa tương tự cho đường dẫn Uploads
define('BASE_ASSETS_UPLOADS',   BASE_URL . 'public/uploads/'); 
define('PATH_ASSETS_UPLOADS',   ROOT_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR); 

// Cấu hình Database (cả db này nữa)
define('DB_HOST',     'localhost');
define('DB_PORT',     '3306');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME',     'da1'); 
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
?>