<?php
/**
 * Fungsi-fungsi untuk autentikasi dan keamanan
 */

// Cek jika user sudah login
function checkUserLogin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
        header("Location: ../user/index.php");
        exit();
    }
}

// Cek jika admin sudah login
function checkAdminLogin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
        header("Location: ../admin/index.php");
        exit();
    }
}


// Generate random string untuk token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Sanitize input data
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

// Redirect dengan pesan
function redirectWithMessage($url, $type, $message) {
    $_SESSION['flash_message'] = array(
        'type' => $type,
        'message' => $message
    );
    header("Location: $url");
    exit();
}

// Tampilkan flash message
function showFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_message']['type'];
        $message = $_SESSION['flash_message']['message'];
        
        echo "<div class='alert alert-$type alert-dismissible fade show' role='alert'>";
        echo $message;
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo "</div>";
        
        unset($_SESSION['flash_message']);
    }
}

// Validasi email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validasi nomor telepon
function validatePhone($phone) {
    return preg_match('/^[0-9]{10,13}$/', $phone);
}

// Hash password
function hashPassword($password) {
    return md5($password); // Gunakan password_hash() untuk keamanan lebih
}

// Verify password
function verifyPassword($password, $hash) {
    return md5($password) === $hash; // Gunakan password_verify() untuk keamanan lebih
}
?>