<?php
session_start();
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama_lengkap   = mysqli_real_escape_string($conn, $_POST['nama_lengkap'] ?? '');
    $email          = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $password_raw   = $_POST['password'] ?? '';
    $no_hp          = mysqli_real_escape_string($conn, $_POST['no_hp'] ?? '');
    $jenis_kelamin  = mysqli_real_escape_string($conn, $_POST['jenis_kelamin'] ?? '');
    $tempat_lahir   = mysqli_real_escape_string($conn, $_POST['tempat_lahir'] ?? '');
    $tanggal_lahir  = mysqli_real_escape_string($conn, $_POST['tanggal_lahir'] ?? '');
    $alamat         = mysqli_real_escape_string($conn, $_POST['alamat'] ?? '');
    $asal_sekolah   = mysqli_real_escape_string($conn, $_POST['asal_sekolah'] ?? '');
    $jurusan_sekolah= mysqli_real_escape_string($conn, $_POST['jurusan_sekolah'] ?? '');
    $tahun_lulus    = mysqli_real_escape_string($conn, $_POST['tahun_lulus'] ?? '');

    $foto = '';
    if(isset($_FILES['foto']) && $_FILES['foto']['name'] != ''){
        $namaFile = $_FILES['foto']['name'];
        $tmpFile  = $_FILES['foto']['tmp_name'];
        $ext      = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
        $namaBaru = "user_" . time() . "." . $ext;
        move_uploaded_file($tmpFile, "../uploads/profile/" . $namaBaru);
        $foto = $namaBaru;
    }

    if (strlen(trim($nama_lengkap)) < 3) {
        $error = "Nama minimal 3 karakter.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (strlen($password_raw) < 6) {
        $error = "Password minimal 6 karakter.";
    } elseif (empty($no_hp)) {
        $error = "Nomor handphone wajib diisi.";
    } elseif (empty($asal_sekolah)) {
        $error = "Asal sekolah wajib diisi.";
    } else {
        $password = md5($password_raw);
        $check = mysqli_query($conn, "SELECT id_calon FROM calon_mahasiswa WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            $query = "INSERT INTO calon_mahasiswa (
                nama_lengkap, email, password, no_hp, jenis_kelamin,
                tempat_lahir, tanggal_lahir, alamat, asal_sekolah,
                jurusan_sekolah, tahun_lulus, foto, created_at
            ) VALUES (
                '$nama_lengkap','$email','$password','$no_hp','$jenis_kelamin',
                '$tempat_lahir','$tanggal_lahir','$alamat','$asal_sekolah',
                '$jurusan_sekolah','$tahun_lulus','$foto',NOW()
            )";
            if (mysqli_query($conn, $query)) {
                $id = mysqli_insert_id($conn);
                $no_test = "UR-" . date("Y") . "-" . str_pad($id, 4, "0", STR_PAD_LEFT);
                mysqli_query($conn, "UPDATE calon_mahasiswa SET no_test='$no_test' WHERE id_calon='$id'");
                $success = $no_test;
            } else {
                $error = "Registrasi gagal: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registrasi PMB — Universitas Arten</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        --success:#059669;
    }

    html, body {
        height: 100%;
        font-family: 'Inter', sans-serif;
        background: #EEF2F7;
        overflow: hidden;
    }

    .page { height: 100vh; display: flex; overflow: hidden; }

    /* ── Left panel ── */
    .panel-left {
        width: 42%;
        background: linear-gradient(160deg, var(--navy) 0%, var(--navy2) 60%, #0D1B33 100%);
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 2.5rem;
        overflow: hidden;
        flex-shrink: 0;
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
        max-width: 360px;
    }

    .brand-emblem {
        width: 80px; height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,.08);
        border: 2px solid var(--gold);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1.25rem;
        font-size: 2rem;
        color: var(--gold);
    }

    .brand-name {
        font-family: 'Playfair Display', serif;
        font-size: 1.8rem;
        color: #ffffff;
        line-height: 1.2;
        margin-bottom: .4rem;
    }

    .brand-tagline {
        font-size: .8rem;
        color: rgba(255,255,255,.55);
        letter-spacing: .06em;
        text-transform: uppercase;
        margin-bottom: 2rem;
    }

    .brand-divider {
        width: 50px; height: 2px;
        background: var(--gold);
        margin: 0 auto 1.75rem;
        border-radius: 2px;
    }

    .info-box {
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        text-align: left;
        margin-bottom: 1rem;
    }

    .info-box-title {
        color: var(--gold);
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        margin-bottom: .85rem;
    }

    .steps-list {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: .7rem;
    }

    .steps-list li {
        display: flex;
        align-items: flex-start;
        gap: .65rem;
        color: rgba(255,255,255,.75);
        font-size: .82rem;
        line-height: 1.4;
    }

    .step-num {
        width: 22px; height: 22px;
        border-radius: 50%;
        background: rgba(201,168,76,.2);
        border: 1px solid var(--gold);
        display: flex; align-items: center; justify-content: center;
        color: var(--gold);
        font-size: .68rem;
        font-weight: 700;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .tip-box {
        background: rgba(201,168,76,.1);
        border: 1px solid rgba(201,168,76,.25);
        border-radius: 10px;
        padding: .85rem 1rem;
        font-size: .78rem;
        color: rgba(255,255,255,.65);
        line-height: 1.5;
    }

    .tip-box i { color: var(--gold); margin-right: .4rem; }

    .panel-footer-text {
        position: absolute;
        bottom: 1.25rem;
        left: 0; right: 0;
        text-align: center;
        font-size: .72rem;
        color: rgba(255,255,255,.3);
        z-index: 2;
    }

    /* ── Right panel ── */
    .panel-right {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: var(--light);
        padding: 1.5rem 2rem;
        overflow-y: auto;
    }

    .form-box {
        width: 100%;
        max-width: 640px;
        animation: fadeUp .5s ease;
    }

    .form-head { margin-bottom: 1.25rem; }

    .badge-reg {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        background: rgba(26,74,138,.08);
        color: var(--accent);
        border: 1px solid rgba(26,74,138,.2);
        border-radius: 20px;
        padding: .3rem .9rem;
        font-size: .72rem;
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
        margin-bottom: .75rem;
    }

    .form-head h2 { font-size: 1.45rem; font-weight: 700; color: var(--navy); margin-bottom: .3rem; }
    .form-head p  { font-size: .82rem; color: var(--muted); }

    /* ── Section label ── */
    .section-label {
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--accent);
        margin-bottom: .6rem;
        margin-top: .2rem;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border);
    }

    /* ── Grid layout ── */
    .fields-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .6rem 1rem;
    }

    .col-full { grid-column: 1 / -1; }

    /* inputs */
    .field-group { display: flex; flex-direction: column; }

    .field-label {
        font-size: .76rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: .3rem;
        display: block;
    }

    .input-wrap { position: relative; }

    .input-wrap > i.input-icon {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: #94A3B8;
        font-size: .82rem;
        pointer-events: none;
    }

    .field-input {
        width: 100%;
        padding: .6rem .9rem .6rem 2.2rem;
        border: 1.5px solid var(--border);
        border-radius: 9px;
        font-size: .84rem;
        font-family: 'Inter', sans-serif;
        color: #1E293B;
        background: #fff;
        transition: border-color .25s, box-shadow .25s;
        height: 38px;
    }

    .field-input:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(26,74,138,.1);
    }

    select.field-input { cursor: pointer; }

    textarea.field-input {
        height: 60px;
        resize: none;
        padding-top: .5rem;
    }

    /* no icon inputs */
    .field-input.no-icon { padding-left: .9rem; }

    .toggle-pw {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #94A3B8;
        cursor: pointer;
        font-size: .82rem;
        padding: 4px;
    }
    .toggle-pw:hover { color: var(--accent); }

    /* file input */
    .file-label {
        width: 100%;
        height: 38px;
        border: 1.5px dashed var(--border);
        border-radius: 9px;
        display: flex;
        align-items: center;
        gap: .5rem;
        padding: 0 .9rem;
        font-size: .82rem;
        color: var(--muted);
        cursor: pointer;
        background: #fff;
        transition: border-color .25s;
    }
    .file-label:hover { border-color: var(--accent); color: var(--accent); }
    .file-label i { font-size: .85rem; }
    #foto { display: none; }

    /* error */
    .error-alert {
        background: #FEF2F2;
        border: 1px solid #FECACA;
        border-radius: 8px;
        padding: .6rem .9rem;
        margin-bottom: .9rem;
        display: flex;
        align-items: center;
        gap: .6rem;
        font-size: .82rem;
        color: var(--danger);
    }

    /* buttons */
    .btn-daftar {
        width: 100%;
        padding: .75rem;
        background: linear-gradient(135deg, var(--navy) 0%, var(--accent) 100%);
        border: none;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: .9rem;
        font-family: 'Inter', sans-serif;
        cursor: pointer;
        transition: all .3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        margin-top: .75rem;
    }

    .btn-daftar:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,51,102,.25);
    }

    .btn-back {
        width: 100%;
        padding: .6rem;
        background: transparent;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        color: #475569;
        font-weight: 500;
        font-size: .84rem;
        font-family: 'Inter', sans-serif;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        margin-top: .6rem;
        transition: all .25s ease;
    }

    .btn-back:hover {
        background: #F1F5F9;
        border-color: var(--accent);
        color: var(--accent);
    }

    .form-note {
        text-align: center;
        margin-top: 1rem;
        font-size: .75rem;
        color: #94A3B8;
    }

    /* ── Responsive ── */
    @media (max-width: 900px) {
        .panel-left  { display: none; }
        html, body   { overflow: auto; }
        .page        { height: auto; min-height: 100vh; }
        .panel-right { padding: 1.5rem 1rem; align-items: stretch; }
        .form-box    { max-width: 100%; }
    }

    @media (max-width: 540px) {
        .fields-grid { grid-template-columns: 1fr; }
        .col-full    { grid-column: 1; }
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>
</head>
<body>

<!-- MODAL NO TEST -->
<div class="modal fade" id="noTestModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4" style="border-radius:16px;">
      <div style="font-size:3rem;margin-bottom:.75rem;">🎉</div>
      <h4 class="mb-2" style="font-weight:700;color:var(--navy);">Registrasi Berhasil!</h4>
      <p style="font-size:.875rem;color:var(--muted);">Simpan nomor test berikut untuk login:</p>
      <div id="noTestText" style="font-size:1.6rem;font-weight:800;color:var(--accent);margin:1rem 0;letter-spacing:.05em;">
        <?php echo $success; ?>
      </div>
      <button class="btn btn-primary mb-2" style="border-radius:10px;" onclick="copyNoTest()">
        <i class="fas fa-copy me-2"></i>Salin No Test
      </button>
      <a href="index.php" class="btn btn-outline-secondary" style="border-radius:10px;">
        <i class="fas fa-sign-in-alt me-2"></i>Login Sekarang
      </a>
    </div>
  </div>
</div>

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

            <div class="info-box">
                <div class="info-box-title"><i class="fas fa-list-ol me-1"></i> Alur Pendaftaran</div>
                <ul class="steps-list">
                    <li>
                        <span class="step-num">1</span>
                        <span>Isi formulir registrasi dengan data yang benar</span>
                    </li>
                    <li>
                        <span class="step-num">2</span>
                        <span>Simpan No Test yang diberikan sistem</span>
                    </li>
                    <li>
                        <span class="step-num">3</span>
                        <span>Login & pilih program studi yang diinginkan</span>
                    </li>
                    <li>
                        <span class="step-num">4</span>
                        <span>Ikuti tes seleksi dan pantau hasil kelulusan</span>
                    </li>
                </ul>
            </div>

            <div class="tip-box">
                <i class="fas fa-lightbulb"></i>
                Pastikan email dan nomor HP aktif — informasi penting akan dikirimkan melalui kontak tersebut.
            </div>
        </div>

        <p class="panel-footer-text">&copy; <?php echo date('Y'); ?> Universitas Arten — Sistem Informasi PMB</p>
    </div>

    <!-- ===== RIGHT PANEL ===== -->
    <div class="panel-right">
        <div class="form-box">

            <div class="form-head">
                <div class="badge-reg">
                    <i class="fas fa-user-plus"></i>
                    Pendaftaran Baru
                </div>
                <h2>Buat Akun Mahasiswa</h2>
                <p>Lengkapi semua data di bawah ini untuk mendaftar sebagai calon mahasiswa.</p>
            </div>

            <?php if (!empty($error)): ?>
            <div class="error-alert">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" autocomplete="off">

                <!-- SEKSI: Data Akun -->
                <div class="section-label">Data Akun</div>
                <div class="fields-grid" style="margin-bottom:.8rem;">

                    <div class="field-group col-full">
                        <label class="field-label">Nama Lengkap <span style="color:red">*</span></label>
                        <div class="input-wrap">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" name="nama_lengkap" class="field-input" placeholder="Nama sesuai KTP" required>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">Email <span style="color:red">*</span></label>
                        <div class="input-wrap">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" name="email" class="field-input" placeholder="email@domain.com" required>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">Password <span style="color:red">*</span></label>
                        <div class="input-wrap">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="password" name="password" class="field-input" placeholder="Min. 6 karakter" required>
                            <button type="button" class="toggle-pw" onclick="togglePassword()">
                                <i class="fas fa-eye" id="pwIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">Nomor HP <span style="color:red">*</span></label>
                        <div class="input-wrap">
                            <i class="fas fa-phone input-icon"></i>
                            <input type="text" name="no_hp" class="field-input" placeholder="08xxxxxxxxxx" required>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">Jenis Kelamin</label>
                        <div class="input-wrap">
                            <i class="fas fa-venus-mars input-icon"></i>
                            <select name="jenis_kelamin" class="field-input">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>

                </div>

                <!-- SEKSI: Data Diri -->
                <div class="section-label">Data Diri</div>
                <div class="fields-grid" style="margin-bottom:.8rem;">

                    <div class="field-group">
                        <label class="field-label">Tempat Lahir</label>
                        <div class="input-wrap">
                            <i class="fas fa-map-marker-alt input-icon"></i>
                            <input type="text" name="tempat_lahir" class="field-input" placeholder="Kota kelahiran">
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">Tanggal Lahir</label>
                        <div class="input-wrap">
                            <i class="fas fa-calendar input-icon"></i>
                            <input type="date" name="tanggal_lahir" class="field-input">
                        </div>
                    </div>

                    <div class="field-group col-full">
                        <label class="field-label">Alamat Lengkap</label>
                        <div class="input-wrap">
                            <i class="fas fa-home input-icon" style="top:16px;transform:none;"></i>
                            <textarea name="alamat" class="field-input" placeholder="Jl. ..."></textarea>
                        </div>
                    </div>

                </div>

                <!-- SEKSI: Riwayat Sekolah -->
                <div class="section-label">Riwayat Sekolah</div>
                <div class="fields-grid" style="margin-bottom:.8rem;">

                    <div class="field-group">
                        <label class="field-label">Asal Sekolah <span style="color:red">*</span></label>
                        <div class="input-wrap">
                            <i class="fas fa-school input-icon"></i>
                            <input type="text" name="asal_sekolah" class="field-input" placeholder="Nama SMA/SMK/MA" required>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">Jurusan Sekolah</label>
                        <div class="input-wrap">
                            <i class="fas fa-book input-icon"></i>
                            <input type="text" name="jurusan_sekolah" class="field-input" placeholder="IPA / IPS / dll">
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">Tahun Lulus</label>
                        <div class="input-wrap">
                            <i class="fas fa-calendar-check input-icon"></i>
                            <select name="tahun_lulus" class="field-input">
                                <option value="">Pilih Tahun</option>
                                <?php for($i=date('Y'); $i>=2000; $i--): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">Foto Profil</label>
                        <label class="file-label" for="foto" id="fileLabel">
                            <i class="fas fa-camera"></i>
                            <span id="fileName">Pilih foto...</span>
                        </label>
                        <input type="file" id="foto" name="foto" accept="image/*" onchange="updateFileName(this)">
                    </div>

                </div>

                <button type="submit" class="btn-daftar">
                    <i class="fas fa-user-plus"></i>
                    Daftar Sekarang
                </button>

            </form>

            <a href="index.php" class="btn-back">
                <i class="fas fa-sign-in-alt"></i>
                Sudah punya akun? Login di sini
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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

function updateFileName(input) {
    var label = document.getElementById('fileName');
    if (input.files && input.files[0]) {
        label.textContent = input.files[0].name;
    } else {
        label.textContent = 'Pilih foto...';
    }
}

function copyNoTest() {
    var text = document.getElementById("noTestText").innerText.trim();
    navigator.clipboard.writeText(text);
    alert("No Test berhasil disalin: " + text);
}
</script>

<?php if(!empty($success)): ?>
<script>
var myModal = new bootstrap.Modal(document.getElementById('noTestModal'));
myModal.show();
</script>
<?php endif; ?>

</body>
</html>