<?php

if (!function_exists('debug')) {
    function debug($data)
    {
        echo '<pre>';
        print_r($data);
        die;
    }
}

// Hàm lấy URL gốc
if (!function_exists('base_url')) {
    function base_url($uri = '')
    {
        return BASE_URL . $uri;
    }
}

// 🎯 HÀM NÀY ĐÃ ĐÚNG:
// Trỏ tới public/assets/ (chứa CSS, JS, Fonts của template)
if (!function_exists('asset_url')) {
    function asset_url($uri = '')
    {
        return BASE_URL . 'public/assets/' . $uri;
    }
}

// Hàm này của bạn đã ổn
if (!function_exists('upload_file')) {
    function upload_file($folder, $file)
    {
        // $folder là thư mục con bên trong /public/uploads/
        $targetFile = $folder . '/' . time() . '-' . $file["name"];

        // ⚠️ PATH_ASSETS_UPLOADS giờ đã trỏ đúng vào /public/uploads/
        if (move_uploaded_file($file["tmp_name"], PATH_ASSETS_UPLOADS . $targetFile)) {
            return $targetFile;
        }

        throw new Exception('Upload file không thành công!');
    }
}
?>