<?php
// configs/autoloader.php

spl_autoload_register(function ($class) {

    // 🎯 ĐÃ XÓA DẤU "/" (Vì CONTROLLER_PATH đã có / ở cuối)
    $file_controller = CONTROLLER_PATH . "$class.php";
    if (is_readable($file_controller)) {
        require_once $file_controller;
        return; 
    }

    // 🎯 ĐÃ XÓA DẤU "/" (Vì MODEL_PATH đã có / ở cuối)
    $file_model = MODEL_PATH . "$class.php";
    if (is_readable($file_model)) {
        require_once $file_model;
        return;
    }
});
?>