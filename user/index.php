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
    $no_test  = mysqli_real_escape_string($conn, $_POST['no_test']);
    $password = md5($_POST['password']);

    $query  = "SELECT * FROM calon_mahasiswa WHERE no_test='$no_test' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        $_SESSION['user_id']       = $user['id_calon'];
        $_SESSION['user_nama']     = $user['nama_lengkap'];
        $_SESSION['user_no_test']  = $user['no_test'];
        $_SESSION['role']          = 'user';

        if (isset($_POST['rememberMe'])) {
            setcookie('user_no_test', $no_test, time() + (86400 * 30), "/");
        }

        header("Location: dashboard.php");
        exit();
    } else {
        $error = "No Test atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Mahasiswa — PMB Universitas Arten</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy:   #003366;
            --navy2:  #1A2B4D;
            --accent: #1A4A8A;
            --gold:   #C9A84C;
            --light:  #F8FAFC;
            --muted:  #64748B;
            --border: #E2E8F0;
            --danger: #DC2626;
        }

        html, body { height: 100%; font-family: 'Inter', sans-serif; background: #EEF2F7; }

        .page { min-height: 100vh; display: flex; }

        /* ── Left panel ── */
        .panel-left {
            width: 55%;
            background: linear-gradient(160deg, var(--navy) 0%, var(--navy2) 60%, #0D1B33 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            overflow: hidden;
        }

        .panel-left::before {
            content: '';
            position: absolute;
            width: 420px; height: 420px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,.07);
            top: -100px; left: -100px;
        }
        .panel-left::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,.07);
            bottom: -80px; right: -80px;
        }

        .circle-mid {
            position: absolute;
            width: 600px; height: 600px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,.04);
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }

        .panel-brand {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 400px;
        }

        .brand-emblem {
            width: 90px; height: 90px;
            border-radius: 50%;
            background: rgba(255,255,255,.08);
            border: 2px solid var(--gold);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.2rem;
            color: var(--gold);
        }

        .brand-name {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: #ffffff;
            line-height: 1.2;
            margin-bottom: .5rem;
        }

        .brand-tagline {
            font-size: .85rem;
            color: rgba(255,255,255,.55);
            letter-spacing: .06em;
            text-transform: uppercase;
            margin-bottom: 2.5rem;
        }

        .brand-divider {
            width: 50px; height: 2px;
            background: var(--gold);
            margin: 0 auto 2rem;
            border-radius: 2px;
        }

        .steps-list {
            list-style: none;
            text-align: left;
            display: inline-flex;
            flex-direction: column;
            gap: .85rem;
        }

        .steps-list li {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            color: rgba(255,255,255,.7);
            font-size: .875rem;
            line-height: 1.4;
        }

        .step-num {
            width: 26px; height: 26px;
            border-radius: 50%;
            background: rgba(201,168,76,.2);
            border: 1px solid var(--gold);
            display: flex; align-items: center; justify-content: center;
            color: var(--gold);
            font-size: .72rem;
            font-weight: 700;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .panel-footer-text {
            position: absolute;
            bottom: 1.5rem;
            left: 0; right: 0;
            text-align: center;
            font-size: .75rem;
            color: rgba(255,255,255,.3);
            z-index: 2;
        }

        /* ── Right panel ── */
        .panel-right {
            width: 45%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: var(--light);
            padding: 3rem 2.5rem;
        }

        .form-box {
            width: 100%;
            max-width: 380px;
            animation: fadeUp .5s ease;
        }

        .form-head { margin-bottom: 2rem; }

        .badge-user {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: rgba(16,185,129,.08);
            color: #059669;
            border: 1px solid rgba(16,185,129,.2);
            border-radius: 20px;
            padding: .3rem .9rem;
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .form-head h2 { font-size: 1.6rem; font-weight: 700; color: var(--navy); margin-bottom: .4rem; }
        .form-head p  { font-size: .875rem; color: var(--muted); }

        /* inputs */
        .field-group { margin-bottom: 1.25rem; }

        .field-label {
            font-size: .82rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: .4rem;
            display: block;
        }

        .input-wrap { position: relative; }

        .input-wrap > i.input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            font-size: .9rem;
            pointer-events: none;
        }

        .field-input {
            width: 100%;
            padding: .75rem 1rem .75rem 2.6rem;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: .9rem;
            font-family: 'Inter', sans-serif;
            color: #1E293B;
            background: #fff;
            transition: border-color .25s, box-shadow .25s;
        }

        .field-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(26,74,138,.1);
        }

        .toggle-pw {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94A3B8;
            cursor: pointer;
            font-size: .9rem;
            padding: 4px;
        }

        .toggle-pw:hover { color: var(--accent); }

        /* remember me */
        .remember-row {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: 1.25rem;
        }

        .remember-row input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: var(--accent);
            cursor: pointer;
        }

        .remember-row label {
            font-size: .85rem;
            color: var(--muted);
            cursor: pointer;
            margin: 0;
        }

        /* error */
        .error-alert {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 8px;
            padding: .75rem 1rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: .6rem;
            font-size: .85rem;
            color: var(--danger);
        }

        /* buttons */
        .btn-masuk {
            width: 100%;
            padding: .85rem;
            background: linear-gradient(135deg, var(--navy) 0%, var(--accent) 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            font-size: .95rem;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all .3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
        }

        .btn-masuk:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,51,102,.25);
        }

        .btn-masuk:active { transform: translateY(0); }

        .btn-back {
            width: 100%;
            padding: .75rem;
            background: transparent;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            color: #475569;
            font-weight: 500;
            font-size: .875rem;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            margin-top: .75rem;
            transition: all .25s ease;
        }

        .btn-back:hover {
            background: #F1F5F9;
            border-color: var(--accent);
            color: var(--accent);
        }

        .or-divider {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin: 1.25rem 0;
            color: #CBD5E1;
            font-size: .8rem;
        }

        .or-divider::before,
        .or-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .register-row {
            text-align: center;
            font-size: .85rem;
            color: var(--muted);
            margin-top: 1.25rem;
        }

        .register-row a {
            color: var(--accent);
            font-weight: 600;
            text-decoration: none;
        }

        .register-row a:hover { text-decoration: underline; }

        .form-note {
            text-align: center;
            margin-top: 1.5rem;
            font-size: .78rem;
            color: #94A3B8;
        }

        /* ── Responsive ── */
        @media (max-width: 900px) {
            .panel-left  { display: none; }
            .panel-right { width: 100%; background: #EEF2F7; }
            .form-box    { max-width: 420px; background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 8px 30px rgba(0,0,0,.08); }
        }

        @media (max-width: 480px) {
            .panel-right { padding: 1.5rem 1rem; }
            .form-box    { padding: 1.5rem; }
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="page">

    <!-- ===== LEFT PANEL ===== -->
    <div class="panel-left">
        <div class="circle-mid"></div>

        <div class="panel-brand">
            <div class="brand-emblem">
                <i class="fas fa-graduation-cap"></i>
            </div>

            <h1 class="brand-name">Universitas Arten</h1>
            <p class="brand-tagline">Portal Penerimaan Mahasiswa Baru</p>
            <div class="brand-divider"></div>

            <ul class="steps-list">
                <li>
                    <span class="step-num">1</span>
                    <span>Daftar akun dan lengkapi data diri Anda</span>
                </li>
                <li>
                    <span class="step-num">2</span>
                    <span>Pilih program studi yang diinginkan</span>
                </li>
                <li>
                    <span class="step-num">3</span>
                    <span>Ikuti tes seleksi masuk universitas</span>
                </li>
                <li>
                    <span class="step-num">4</span>
                    <span>Lakukan daftar ulang jika dinyatakan lulus</span>
                </li>
            </ul>
        </div>

        <p class="panel-footer-text">&copy; <?php echo date('Y'); ?> Universitas Arten — Sistem Informasi PMB</p>
    </div>

    <!-- ===== RIGHT PANEL ===== -->
    <div class="panel-right">
        <div class="form-box">

            <div class="form-head">
                <div class="badge-user">
                    <i class="fas fa-user-graduate"></i>
                    Calon Mahasiswa
                </div>
                <h2>Selamat Datang</h2>
                <p>Masuk menggunakan No Test dan password yang Anda daftarkan.</p>
            </div>

            <?php if(isset($error)): ?>
            <div class="error-alert">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">

                <div class="field-group">
                    <label class="field-label" for="no_test">Nomor Test</label>
                    <div class="input-wrap">
                        <i class="fas fa-id-badge input-icon"></i>
                        <input type="text" id="no_test" name="no_test" class="field-input"
                               placeholder="Contoh: UR-2024-0001" required
                               value="<?php echo isset($_COOKIE['user_no_test']) ? htmlspecialchars($_COOKIE['user_no_test']) : ''; ?>">
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label" for="password">Password</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" class="field-input"
                               placeholder="Masukkan password" required>
                        <button type="button" class="toggle-pw" onclick="togglePassword()" title="Tampilkan/sembunyikan password">
                            <i class="fas fa-eye" id="pwIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="remember-row">
                    <input type="checkbox" name="rememberMe" id="rememberMe"
                        <?php echo isset($_COOKIE['user_no_test']) ? 'checked' : ''; ?>>
                    <label for="rememberMe">Ingat No Test saya</label>
                </div>

                <button type="submit" class="btn-masuk">
                    <i class="fas fa-sign-in-alt"></i>
                    Masuk Sekarang
                </button>

            </form>

            <div class="or-divider">atau</div>

            <a href="registrasi.php" class="btn-back" style="border-color: rgba(16,185,129,.3); color:#059669;">
                <i class="fas fa-user-plus"></i>
                Belum punya akun? Daftar di sini
            </a>

            <a href="../index.php" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Beranda
            </a>

            <p class="form-note">
                Sistem Informasi PMB &mdash; Universitas Arten &copy; <?php echo date('Y'); ?>
            </p>
        </div>
    </div>

</div>

<script>
function togglePassword() {
    var input = document.getElementById('password');
    var icon  = document.getElementById('pwIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

</body>
</html>