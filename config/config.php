<?php
/**
 * Global Configuration for Durian Si Buyunk Application
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Application Settings
define('APP_NAME', 'Durian Si Buyunk');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/DurianSiBuyunk');

// Security Settings
define('RESET_CODE', 'BUYUNK2025');
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// Upload Settings
define('UPLOAD_PATH', '../public/assets/images/products/');
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
define('MAX_FILE_SIZE', 2097152); // 2MB in bytes

// Cabang (Branch) Settings
define('CABANG_TASIK', 1);
define('CABANG_GARUT', 2);

// Helper function to get base URL
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $path = dirname($script);
    return $protocol . '://' . $host . $path;
}

// Helper function to redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Helper function to sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Helper function to format currency
function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

// Helper function to get branch name
function getBranchName($cabang_id) {
    switch ($cabang_id) {
        case CABANG_TASIK:
            return 'Tasikmalaya';
        case CABANG_GARUT:
            return 'Garut';
        default:
            return 'Unknown';
    }
}

// Include database configuration
require_once __DIR__ . '/database.php';
?>