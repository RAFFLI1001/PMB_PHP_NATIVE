<?php
require_once '../config/database.php';

// Inisialisasi variabel error
$error = '';
$success = '';

// Cek jika form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    
    // Query untuk cek admin
    $query = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        
        // Set session
        $_SESSION['admin_id'] = $admin['id_admin'];
        $_SESSION['admin_nama'] = $admin['nama_lengkap'];
        $_SESSION['role'] = 'admin';
        $_SESSION['last_login'] = time();
        
        // Redirect ke dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<?php include '../includes/header.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - PMB UTN</title>
    <style>
        :root {
            --primary-color: #003366;
            --secondary-color: #FF6B00;
            --danger-color: #DC3545;
            --success-color: #28A745;
            --dark-color: #1A2B4D;
            --light-color: #F8F9FA;
            --gradient-primary: linear-gradient(135deg, #003366 0%, #1A2B4D 100%);
            --gradient-danger: linear-gradient(135deg, #DC3545 0%, #C82333 100%);
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Login Container */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23003366" fill-opacity="0.05" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,192C672,181,768,139,864,138.7C960,139,1056,181,1152,197.3C1248,213,1344,203,1392,197.3L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom center;
            background-size: contain;
        }
        
        .login-wrapper {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
        }
        
        /* Login Card */
        .login-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            animation: fadeInUp 0.8s ease-out;
            border: none;
        }
        
        .login-header {
            background: var(--gradient-danger);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            animation: float 20s linear infinite;
        }
        
        .admin-icon {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
        }
        
        .login-title {
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .login-subtitle {
            opacity: 0.9;
            font-size: 0.95rem;
        }
        
        .login-body {
            padding: 2.5rem 2rem;
        }
        
        /* Form Styling */
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark-color);
            font-size: 0.95rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #E2E8F0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #F8FAFC;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
            outline: none;
            background-color: white;
        }
        
        .input-icon {
            position: absolute;
            right: 1rem;
            top: 2.5rem;
            color: #94A3B8;
        }
        
        /* Button Styling */
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: var(--gradient-danger);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(220, 53, 69, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(-1px);
        }
        
        .btn-back {
            width: 100%;
            padding: 1rem;
            background: white;
            color: var(--dark-color);
            border: 2px solid #E2E8F0;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
            text-decoration: none;
        }
        
        .btn-back:hover {
            background-color: #F1F5F9;
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        /* Alert Messages */
        .alert-danger {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%);
            border: 1px solid rgba(220, 53, 69, 0.2);
            color: #721C24;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            animation: shake 0.5s ease-in-out;
        }
        
        .alert-info {
            background: linear-gradient(135deg, rgba(0, 51, 102, 0.1) 0%, rgba(0, 51, 102, 0.05) 100%);
            border: 1px solid rgba(0, 51, 102, 0.2);
            color: var(--primary-color);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-top: 1.5rem;
        }
        
        /* Security Features */
        .security-features {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #E2E8F0;
        }
        
        .security-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            color: #64748B;
            font-size: 0.875rem;
        }
        
        .security-icon {
            color: var(--success-color);
            margin-right: 0.75rem;
            font-size: 0.9rem;
        }
        
        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 2.5rem;
            background: none;
            border: none;
            color: #94A3B8;
            cursor: pointer;
            padding: 0.25rem;
            font-size: 1rem;
        }
        
        .password-toggle:hover {
            color: var(--primary-color);
        }
        
        /* Footer Login */
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            color: #64748B;
            font-size: 0.875rem;
        }
        
        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        @keyframes float {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .login-container {
                padding: 1rem;
            }
            
            .login-header {
                padding: 2rem 1.5rem;
            }
            
            .login-body {
                padding: 2rem 1.5rem;
            }
            
            .admin-icon {
                font-size: 3rem;
            }
            
            .login-title {
                font-size: 1.5rem;
            }
        }
        
        /* Loading Animation */
        .btn-loading {
            position: relative;
            color: transparent !important;
        }
        
        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-wrapper">
            <div class="login-card">
                <!-- Header -->
                <div class="login-header">
                    <i class="fas fa-user-shield admin-icon"></i>
                    <h1 class="login-title">Login Administrator</h1>
                    <p class="login-subtitle">Portal Penerimaan Mahasiswa Baru UTN</p>
                </div>
                
                <!-- Body -->
                <div class="login-body">
                    <!-- Error Message -->
                    <?php if(isset($error)): ?>
                        <div class="alert-danger">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <span><?php echo $error; ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Login Form -->
                    <form method="POST" action="" id="loginForm">
                        <div class="form-group">
                            <label for="username" class="form-label">
                                <i class="fas fa-user me-1"></i> Username
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="username" 
                                   name="username" 
                                   required 
                                   placeholder="Masukkan username admin"
                                   autocomplete="username"
                                   autofocus>
                            <i class="fas fa-user input-icon"></i>
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i> Password
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   placeholder="Masukkan password admin"
                                   autocomplete="current-password">
                            <button type="button" class="password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        
                        <button type="submit" class="btn-login" id="loginButton">
                            <i class="fas fa-sign-in-alt"></i> Masuk ke Dashboard
                        </button>
                        
                        <a href="../index.php" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali ke Halaman Utama
                        </a>
                    </form>
                    
                    <!-- Security Info -->
                    <div class="security-features">
                        <div class="security-item">
                            <i class="fas fa-shield-alt security-icon"></i>
                            <span>Sesi akan berakhir setelah 30 menit tidak aktif</span>
                        </div>
                        <div class="security-item">
                            <i class="fas fa-history security-icon"></i>
                            <span>Log aktivitas akan dicatat untuk keamanan</span>
                        </div>
                        <div class="security-item">
                            <i class="fas fa-lock security-icon"></i>
                            <span>Hanya personel berwenang yang dapat mengakses</span>
                        </div>
                    </div>
                    
                    <!-- Default Credentials -->
                    <div class="alert-info">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-info-circle me-2 mt-1"></i>
                            <div>
                                <small><strong>Kredensial default:</strong></small><br>
                                <small>Username: <code>admin</code> | Password: <code>admin123</code></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="login-footer">
                <p>&copy; <?php echo date('Y'); ?> PMB Universitas Teknologi Nusantara</p>
                <p>Versi 1.0.0 | <a href="#" id="forgotPassword">Lupa Password?</a></p>
            </div>
        </div>
    </div>
    
    <!-- JavaScript for Enhanced Features -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password toggle visibility
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            });
            
            // Form submission with loading animation
            const loginForm = document.getElementById('loginForm');
            const loginButton = document.getElementById('loginButton');
            
            loginForm.addEventListener('submit', function(e) {
                const username = document.getElementById('username').value.trim();
                const password = document.getElementById('password').value.trim();
                
                if (!username || !password) {
                    e.preventDefault();
                    return;
                }
                
                // Show loading animation
                loginButton.classList.add('btn-loading');
                loginButton.disabled = true;
                
                // Simulate API call delay
                setTimeout(() => {
                    loginButton.classList.remove('btn-loading');
                    loginButton.disabled = false;
                }, 2000);
            });
            
            // Forgot password modal
            document.getElementById('forgotPassword').addEventListener('click', function(e) {
                e.preventDefault();
                alert('Silakan hubungi super administrator untuk reset password.');
            });
            
            // Input validation
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            
            usernameInput.addEventListener('input', function() {
                this.style.borderColor = this.value.length >= 3 ? '#28A745' : '#E2E8F0';
            });
            
            // Auto focus on username if empty
            if (!usernameInput.value) {
                usernameInput.focus();
            }
            
            // Enter key to submit
            loginForm.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && e.target.type !== 'submit') {
                    e.preventDefault();
                    if (usernameInput.value && passwordInput.value) {
                        loginButton.click();
                    }
                }
            });
            
            // Add security warning for password field
            passwordInput.addEventListener('focus', function() {
                if (this.value === 'admin123') {
                    console.warn('⚠️ Warning: Using default password is not secure!');
                }
            });
        });
    </script>
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>

<?php include '../includes/footer.php'; ?>