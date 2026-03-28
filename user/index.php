<?php
require_once '../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

        if (isset($_POST['rememberMe'])) {
            setcookie('user_email', $email, time() + (86400 * 30), "/");
        }

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
<title>Login PMB | Arten Campus</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

body{
    margin:0;
    height:100vh;
    font-family:'Segoe UI',sans-serif;
    background:linear-gradient(135deg,#4f46e5,#9333ea);
    display:flex;
    align-items:center;
    justify-content:center;
}

/* Container */
.login-container{
    width:100%;
    max-width:1000px;
    background:white;
    border-radius:20px;
    overflow:hidden;
    display:flex;
    box-shadow:0 25px 60px rgba(0,0,0,0.2);
    animation:fadeIn 0.8s ease;
}

/* LEFT SIDE */
.login-left{
    flex:1;
    background:linear-gradient(135deg,#2563eb,#3b82f6);
    color:white;
    padding:50px;
    display:flex;
    flex-direction:column;
    justify-content:center;
}

.login-left h1{
    font-size:38px;
    font-weight:700;
}

.login-left p{
    opacity:0.9;
}

/* RIGHT SIDE */
.login-right{
    flex:1;
    padding:50px;
}

.login-title{
    font-size:26px;
    font-weight:700;
    margin-bottom:5px;
}

.login-sub{
    font-size:14px;
    color:#666;
    margin-bottom:30px;
}

/* INPUT */
.form-control{
    border-radius:12px;
    padding:12px;
    border:1px solid #ddd;
    margin-bottom:20px;
}

.form-control:focus{
    border-color:#4f46e5;
    box-shadow:none;
}

/* BUTTON */
.btn-login{
    width:100%;
    border:none;
    border-radius:12px;
    padding:12px;
    background:linear-gradient(135deg,#4f46e5,#9333ea);
    color:white;
    font-weight:600;
    transition:0.3s;
}

.btn-login:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(79,70,229,0.4);
}

/* ALERT */
.alert{
    border-radius:12px;
}

/* ANIMATION */
@keyframes fadeIn{
    from{opacity:0; transform:translateY(20px);}
    to{opacity:1; transform:translateY(0);}
}

/* RESPONSIVE */
@media(max-width:768px){
    .login-left{display:none;}
    .login-container{max-width:400px;}
}

</style>
</head>

<body>

<div class="login-container">

    <!-- LEFT -->
    <div class="login-left">
        <h1>Arten Campus</h1>
        <p>Portal Pendaftaran Mahasiswa Baru</p>
        <p>Masuk untuk melanjutkan pendaftaran Anda.</p>
    </div>

    <!-- RIGHT -->
    <div class="login-right">

        <div class="login-title">Login Calon Mahasiswa</div>
        <div class="login-sub">Masukkan email dan password Anda</div>

        <?php if(isset($error)): ?>
        <div class="alert alert-danger">
            <i class="fa fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
        <?php endif; ?>

        <form method="POST">

            <label>Email</label>
            <input type="email" name="email" class="form-control"
            value="<?php echo isset($_COOKIE['user_email']) ? $_COOKIE['user_email'] : ''; ?>"
            required>

            <label>Password</label>
            <input type="password" name="password" class="form-control" required>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="rememberMe" id="rememberMe"
                <?php echo isset($_COOKIE['user_email']) ? 'checked' : ''; ?>>
                <label class="form-check-label">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="fa fa-sign-in-alt"></i> Masuk Sekarang
            </button>

        </form>

        <div class="text-center mt-4">
            Belum punya akun? <a href="registrasi.php">Daftar di sini</a>
        </div>

        <div class="text-center mt-3">
            <a href="../index.php">Kembali ke Beranda</a>
        </div>

    </div>

</div>

</body>
</html>