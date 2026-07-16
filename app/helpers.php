<?php

if (!function_exists('format_bytes')) {
    function format_bytes($bytes, $precision = 2) {
        $bytes = (int) $bytes;
        if ($bytes == 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = (int) floor(log($bytes, 1024));
        $i = min($i, count($units) - 1);
        return round($bytes / pow(1024, $i), $precision) . ' ' . $units[$i];
    }
}
