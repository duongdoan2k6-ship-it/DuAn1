<?php
if (!function_exists('debug')) {
    function debug($var) {
        echo "<div style='background: #f8f9fa; border: 1px solid #ddd; padding: 15px; margin: 10px; border-radius: 5px; z-index: 99999; position: relative;'>";
        echo "<pre style='color: #d63384; font-weight: bold;'>";
        print_r($var);
        echo "</pre>";
        echo "</div>";
        die(); 
    }
}
if (!function_exists('currency_format')) {
    function currency_format($number, $suffix = ' VNĐ') {
        if ($number === null) return '0' . $suffix;
        return number_format($number, 0, ',', '.') . $suffix;
    }
}
?>