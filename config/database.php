<?php
/**
 * Konfigurasi Database PMB UTN
 * File ini HARUS diamankan
 */

// ===================== KONFIGURASI KEAMANAN =====================
// Nonaktifkan error reporting di production
error_reporting(E_ALL & ~E_NOTICE); // Nonaktifkan notice saja
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

// Session security - HANYA jika session belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    
    // Regenerate session ID untuk mencegah fixation
    if (!isset($_SESSION['created'])) {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    } elseif (time() - $_SESSION['created'] > 1800) {
        // Regenerate setiap 30 menit
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

// ===================== KONFIGURASI DATABASE =====================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pmb_utn');

// ===================== FUNGSI KONEKSI =====================
function getConnection() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if (!$conn) {
        error_log("Database connection failed: " . mysqli_connect_error());
        die("System maintenance. Please try again later.");
    }
    
    mysqli_set_charset($conn, "utf8mb4");
    return $conn;
}

$conn = getConnection();
?>