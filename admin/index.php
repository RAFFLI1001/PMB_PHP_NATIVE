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
        session_start();
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
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Admin - PMB UTN</title>

<style>

/* ================= GLOBAL ================= */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background:#f4f6f9;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}

/* ================= CONTAINER ================= */
.login-wrapper{
    width:100%;
    max-width:420px;
    padding:20px;
}

/* ================= CARD ================= */
.login-card{
    width:100%;
    background:#ffffff;
    border-radius:22px;
    overflow:hidden;
    box-shadow:0 20px 50px rgba(0,0,0,0.15);
    animation:fadeUp 0.7s ease;
}

/* ================= HEADER ================= */
.login-header{
    background:linear-gradient(135deg,#dc3545,#c82333);
    color:white;
    text-align:center;
    padding:35px 20px;
}

.login-title{
    font-size:28px;
    font-weight:700;
    margin-bottom:6px;
}

.login-subtitle{
    font-size:14px;
    opacity:0.9;
}

/* ================= BODY ================= */
.login-body{
    padding:30px 25px 25px;
}

/* ================= FORM ================= */
.form-group{
    margin-bottom:18px;
}

.form-label{
    font-size:14px;
    font-weight:600;
    color:#1a2b4d;
    margin-bottom:6px;
    display:block;
}

.form-control{
    width:100%;
    padding:14px 15px;
    border-radius:14px;
    border:2px solid #e2e8f0;
    font-size:15px;
    transition:0.3s;
}

.form-control:focus{
    outline:none;
    border-color:#003366;
}

/* ================= BUTTON ================= */
.btn-login{
    width:100%;
    padding:14px;
    background:linear-gradient(135deg,#dc3545,#c82333);
    border:none;
    border-radius:14px;
    color:white;
    font-weight:600;
    font-size:15px;
    cursor:pointer;
    transition:0.3s;
}

.btn-login:hover{
    transform:translateY(-2px);
}

.btn-back{
    width:100%;
    padding:14px;
    border-radius:14px;
    border:2px solid #d1d5db;
    text-decoration:none;
    color:#1a2b4d;
    display:flex;
    align-items:center;
    justify-content:center;
    margin-top:12px;
    font-weight:600;
    transition:0.3s;
}

.btn-back:hover{
    background:#f3f4f6;
    border-color:#003366;
    color:#003366;
}

/* ================= ERROR ================= */
.error-box{
    background:#ffe5e5;
    padding:10px;
    border-radius:10px;
    margin-bottom:15px;
    font-size:14px;
}

/* ================= FOOTER ================= */
.login-footer{
    text-align:center;
    margin-top:18px;
    font-size:13px;
    color:#6b7280;
}

/* ================= ANIMATION ================= */
@keyframes fadeUp{
    from{
        opacity:0;
        transform:translateY(30px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

/* ================= RESPONSIVE ================= */
@media(max-width:480px){
    .login-wrapper{
        max-width:95%;
    }
}

</style>
</head>

<body>

<div class="login-wrapper">

    <div class="login-card">

        <div class="login-header">
            <h1 class="login-title">Login Administrator</h1>
            <p class="login-subtitle">Portal Penerimaan Mahasiswa Baru </p>
        </div>

        <div class="login-body">

            <?php if($error): ?>
                <div class="error-box"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">

                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username atau email" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>

                <button class="btn-login">Masuk ke Dashboard</button>

                <a href="../index.php" class="btn-back">Kembali ke Halaman Utama</a>

            </form>

        </div>

    </div>

    
</div>

</body>
</html>