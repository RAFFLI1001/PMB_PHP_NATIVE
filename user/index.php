<?php
require_once '../config/database.php';

// Start session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect jika sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']);
    
    $query = "SELECT * FROM calon_mahasiswa WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id_calon'];
        $_SESSION['user_nama'] = $user['nama_lengkap'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role'] = 'user';
        
        // Set cookie jika remember me dicentang
        if (isset($_POST['rememberMe'])) {
            setcookie('user_email', $email, time() + (86400 * 30), "/"); // 30 hari
        }
        
        // Redirect ke dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Email atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Calon Mahasiswa | Arten Campus</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --arten-primary: #2C3E50;
            --arten-secondary: #E74C3C;
            --arten-accent: #3498DB;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 30px;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #2C3E50, #E74C3C);
        }

        .logo-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo-icon {
            font-size: 2.5rem;
            color: #2C3E50;
            margin-bottom: 10px;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2C3E50;
            margin: 0;
        }

        .logo-subtext {
            font-size: 0.9rem;
            color: #666;
            margin: 0;
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2C3E50;
            margin-bottom: 5px;
            text-align: center;
        }

        .login-subtitle {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 25px;
            text-align: center;
        }

        .alert-custom {
            border-radius: 10px;
            border: none;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
        }

        .alert-success {
            background: linear-gradient(135deg, #1dd1a1 0%, #10ac84 100%);
            color: white;
        }

        .alert-info {
            background: linear-gradient(135deg, #54a0ff 0%, #2e86de 100%);
            color: white;
        }

        .form-label {
            font-weight: 600;
            color: #2C3E50;
            font-size: 0.9rem;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .form-label i {
            color: #3498DB;
            margin-right: 8px;
            font-size: 1rem;
        }

        .input-group {
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid #e0e0e0;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            border-color: #3498DB;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.1);
        }

        .input-group-text {
            background: white;
            border: none;
            padding: 0 15px;
            color: #2C3E50;
        }

        .form-control {
            border: none;
            padding: 12px 15px;
            font-size: 0.95rem;
            height: auto;
        }

        .form-control:focus {
            box-shadow: none;
        }

        .password-toggle {
            background: white;
            border: none;
            padding: 0 15px;
            color: #2C3E50;
            cursor: pointer;
        }

        .form-check {
            margin-bottom: 20px;
        }

        .form-check-input:checked {
            background-color: #3498DB;
            border-color: #3498DB;
        }

        .form-check-label {
            font-size: 0.9rem;
            color: #555;
        }

        .forgot-link {
            font-size: 0.85rem;
            color: #3498DB;
            text-decoration: none;
            float: right;
        }

        .forgot-link:hover {
            color: #E74C3C;
            text-decoration: underline;
        }

        .btn-login {
            background: linear-gradient(135deg, #2C3E50 0%, #4A6491 100%);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(44, 62, 80, 0.2);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .register-section {
            text-align: center;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .register-link {
            color: #2C3E50;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .register-link:hover {
            color: #E74C3C;
        }

        .demo-section {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #eee;
        }

        .demo-title {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 10px;
        }

        .demo-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .demo-btn {
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .demo-btn-primary {
            background: rgba(52, 152, 219, 0.1);
            color: #3498DB;
            border-color: #3498DB;
        }

        .demo-btn-warning {
            background: rgba(243, 156, 18, 0.1);
            color: #f39c12;
            border-color: #f39c12;
        }

        .demo-btn:hover {
            transform: translateY(-2px);
        }

        .back-home {
            text-align: center;
            margin-top: 20px;
        }

        .back-home a {
            color: #666;
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .back-home a:hover {
            color: #2C3E50;
        }

        /* Modal styles */
        .modal-content {
            border-radius: 15px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, #2C3E50 0%, #4A6491 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }

        .modal-title i {
            margin-right: 8px;
        }

        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        @media (max-width: 576px) {
            .login-card {
                padding: 25px 20px;
            }
            
            .logo-text {
                font-size: 1.3rem;
            }
            
            .login-title {
                font-size: 1.3rem;
            }
            
            .form-control {
                padding: 10px 12px;
            }
            
            .btn-login {
                padding: 10px 15px;
                font-size: 0.95rem;
            }
            
            .demo-buttons {
                flex-direction: column;
                gap: 8px;
            }
        }

        @media (max-width: 360px) {
            .login-card {
                padding: 20px 15px;
            }
            
            .logo-icon {
                font-size: 2rem;
            }
            
            .logo-text {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <!-- Logo Header -->
        <div class="logo-header">
            <i class="fas fa-graduation-cap logo-icon"></i>
            <h1 class="logo-text">Arten Campus</h1>
            <p class="logo-subtext">Portal PMB</p>
        </div>

        <!-- Title -->
        <h2 class="login-title">Login Calon Mahasiswa</h2>
        <p class="login-subtitle">Masukkan email dan password Anda</p>

        <!-- Alert Messages -->
        <?php if(isset($error)): ?>
        <div class="alert alert-danger alert-custom" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>
                    <strong>Login Gagal</strong>
                    <p class="mb-0"><?php echo $error; ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['registered'])): ?>
        <div class="alert alert-success alert-custom" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <div>
                    <strong>Berhasil!</strong>
                    <p class="mb-0">Akun telah dibuat. Silakan login.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['logout'])): ?>
        <div class="alert alert-info alert-custom" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle me-2"></i>
                <div>
                    <strong>Info</strong>
                    <p class="mb-0">Silakan login kembali.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="" id="loginForm">
            <!-- Email -->
            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i>Email
                </label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-at"></i>
                    </span>
                    <input type="email" 
                           class="form-control" 
                           id="email" 
                           name="email" 
                           placeholder="contoh@email.com"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : (isset($_COOKIE['user_email']) ? htmlspecialchars($_COOKIE['user_email']) : ''); ?>"
                           required>
                </div>
            </div>

            <!-- Password -->
            <div class="form-group">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i>Password
                    </label>
                    <a href="#" class="forgot-link" data-bs-toggle="modal" data-bs-target="#forgotModal">
                        Lupa password?
                    </a>
                </div>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-key"></i>
                    </span>
                    <input type="password" 
                           class="form-control" 
                           id="password" 
                           name="password" 
                           placeholder="Masukkan password"
                           required>
                    <button type="button" 
                            class="password-toggle" 
                            onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="form-check">
                <input class="form-check-input" 
                       type="checkbox" 
                       id="rememberMe" 
                       name="rememberMe"
                       <?php echo isset($_COOKIE['user_email']) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="rememberMe">
                    Ingat saya di perangkat ini
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-login" id="loginBtn">
                <i class="fas fa-sign-in-alt me-2"></i>Masuk
            </button>

            <!-- Register Link -->
            <div class="register-section">
                <p class="mb-2">Belum punya akun?</p>
                <a href="registrasi.php" class="register-link">
                    <i class="fas fa-user-plus me-2"></i>Daftar Akun Baru
                </a>
            </div>
        </form>

        <!-- Demo Login Section -->
        

        <!-- Back to Home -->
        <div class="back-home">
            <a href="../index.php">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Beranda
            </a>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-key me-2"></i>Reset Password
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Untuk reset password, hubungi admin:</p>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            pmb@artencampus.ac.id
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-phone text-success me-2"></i>
                            (022) 1234-5678
                        </li>
                        <li>
                            <i class="fab fa-whatsapp text-success me-2"></i>
                            0812-3456-7890
                        </li>
                    </ul>
                    <div class="alert alert-info small mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Reset password memerlukan verifikasi identitas.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                    <a href="mailto:pmb@artencampus.ac.id" class="btn btn-primary btn-sm">
                        <i class="fas fa-paper-plane me-1"></i>Email
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle Password Visibility
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Demo Login
        document.querySelectorAll('.demo-login').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('email').value = this.dataset.email;
                document.getElementById('password').value = this.dataset.password;
                document.getElementById('rememberMe').checked = true;
                
                // Show brief message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-info alert-custom';
                alertDiv.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>
                            <strong>Akun demo diisi</strong>
                            <p class="mb-0">Klik tombol Masuk untuk login</p>
                        </div>
                    </div>
                `;
                
                // Insert after form
                const form = document.getElementById('loginForm');
                form.parentNode.insertBefore(alertDiv, form);
                
                // Remove alert after 3 seconds
                setTimeout(() => {
                    alertDiv.style.opacity = '0';
                    alertDiv.style.transition = 'opacity 0.3s';
                    setTimeout(() => alertDiv.remove(), 300);
                }, 3000);
            });
        });
        
        // Form Validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            
            // Validate email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                showMessage('Format email tidak valid', 'error');
                return;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                showMessage('Password minimal 6 karakter', 'error');
                return;
            }
            
            // Show loading state
            const submitBtn = document.getElementById('loginBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
            submitBtn.disabled = true;
            
            // Auto lowercase email
            document.getElementById('email').value = email.toLowerCase();
        });
        
        // Auto lowercase email on blur
        document.getElementById('email').addEventListener('blur', function() {
            this.value = this.value.toLowerCase();
        });
        
        // Show message function
        function showMessage(message, type) {
            // Remove existing messages
            document.querySelectorAll('.custom-alert').forEach(alert => alert.remove());
            
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-custom custom-alert`;
            alertDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'check-circle'} me-2"></i>
                    <div>
                        <strong>${type === 'error' ? 'Error' : 'Berhasil'}</strong>
                        <p class="mb-0">${message}</p>
                    </div>
                </div>
            `;
            
            // Insert at the top
            const card = document.querySelector('.login-card');
            const firstChild = card.firstChild;
            card.insertBefore(alertDiv, firstChild);
            
            // Remove after 3 seconds
            setTimeout(() => {
                alertDiv.style.opacity = '0';
                alertDiv.style.transition = 'opacity 0.3s';
                setTimeout(() => alertDiv.remove(), 300);
            }, 3000);
        }
        
        // Auto focus email field
        document.addEventListener('DOMContentLoaded', function() {
            const emailField = document.getElementById('email');
            if (!emailField.value) {
                setTimeout(() => emailField.focus(), 100);
            }
            
            // Check URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('registered')) {
                showMessage('Registrasi berhasil! Silakan login.', 'success');
            }
        });
        
        // Enter key to submit
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                if (e.target.id === 'email' || e.target.id === 'password') {
                    if (document.getElementById('email').value && document.getElementById('password').value) {
                        document.getElementById('loginBtn').click();
                    }
                }
            }
        });
    </script>
</body>
</html>