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

    /* ===============================
       UPLOAD FOTO
    =============================== */
    $foto = '';

    if(isset($_FILES['foto']) && $_FILES['foto']['name'] != ''){

        $namaFile = $_FILES['foto']['name'];
        $tmpFile  = $_FILES['foto']['tmp_name'];

        $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

        $namaBaru = "user_" . time() . "." . $ext;

        move_uploaded_file($tmpFile, "../uploads/profile/" . $namaBaru);

        $foto = $namaBaru;
    }

    // VALIDASI
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

        // cek email
        $check = mysqli_query($conn, "SELECT id_calon FROM calon_mahasiswa WHERE email='$email'");

        if (mysqli_num_rows($check) > 0) {

            $error = "Email sudah terdaftar!";

        } else {

            // INSERT DATA + FOTO
            $query = "INSERT INTO calon_mahasiswa (
                nama_lengkap,
                email,
                password,
                no_hp,
                jenis_kelamin,
                tempat_lahir,
                tanggal_lahir,
                alamat,
                asal_sekolah,
                jurusan_sekolah,
                tahun_lulus,
                foto,
                created_at
            ) VALUES (
                '$nama_lengkap',
                '$email',
                '$password',
                '$no_hp',
                '$jenis_kelamin',
                '$tempat_lahir',
                '$tanggal_lahir',
                '$alamat',
                '$asal_sekolah',
                '$jurusan_sekolah',
                '$tahun_lulus',
                '$foto',
                NOW()
            )";

            if (mysqli_query($conn, $query)) {

                $success = "Registrasi berhasil! Silakan login.";

                header("refresh:3;url=index.php");

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

<title>Registrasi PMB | Arten Campus</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:linear-gradient(135deg,#4f46e5,#9333ea);
    min-height:100vh;
    padding:40px 20px;
}

/* CONTAINER */
.register-container{
    width:100%;
    max-width:900px;
    background:white;
    border-radius:20px;
    overflow:hidden;
    display:flex;
    margin:auto;
    box-shadow:0 25px 60px rgba(0,0,0,0.2);
}

/* LEFT */
.register-left{
    flex:1;
    background:linear-gradient(135deg,#4f46e5,#7c3aed);
    color:white;
    padding:50px;
    display:flex;
    flex-direction:column;
    justify-content:center;
}

.register-left h1{
    font-size:32px;
    font-weight:700;
}

/* RIGHT */
.register-right{
    flex:1;
    padding:40px;
}

.register-title{
    font-size:24px;
    font-weight:700;
    margin-bottom:5px;
}

.register-sub{
    font-size:14px;
    color:#666;
    margin-bottom:25px;
}

.form-control{
    border-radius:12px;
    padding:12px;
    border:1px solid #ddd;
    margin-bottom:18px;
}

.form-control:focus{
    border-color:#4f46e5;
    box-shadow:none;
}

.btn-register{
    width:100%;
    border:none;
    border-radius:12px;
    padding:12px;
    background:linear-gradient(135deg,#4f46e5,#9333ea);
    color:white;
    font-weight:600;
}

.alert{
    border-radius:12px;
}

/* RESPONSIVE */
@media(max-width:768px){
    .register-left{display:none;}
    .register-container{max-width:400px;}
    .register-right{padding:30px;}
}

</style>
</head>

<body>

<div class="register-container">

    <div class="register-left">
        <h1>Arten Campus</h1>
        <p>Portal Pendaftaran Mahasiswa Baru</p>
        <p>Daftarkan diri Anda sekarang dan mulai perjalanan kuliah Anda bersama kami.</p>
    </div>

    <div class="register-right">

        <div class="register-title">Form Registrasi</div>
        <div class="register-sub">Isi data dengan benar</div>

        <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <i class="fa fa-check-circle"></i> <?php echo $success; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <i class="fa fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
        <?php endif; ?>

<form method="POST" enctype="multipart/form-data">

<label>Nama Lengkap</label>
<input type="text" name="nama_lengkap" class="form-control" required>

<label>Email</label>
<input type="email" name="email" class="form-control" required>

<label>Password</label>
<input type="password" name="password" class="form-control" required>

<label>Nomor HP</label>
<input type="text" name="no_hp" class="form-control" required>

<label>Jenis Kelamin</label>
<select name="jenis_kelamin" class="form-control">
    <option value="">Pilih Jenis Kelamin</option>
    <option value="L">Laki-laki</option>
    <option value="P">Perempuan</option>
</select>

<label>Tempat Lahir</label>
<input type="text" name="tempat_lahir" class="form-control">

<label>Tanggal Lahir</label>
<input type="date" name="tanggal_lahir" class="form-control">

<label>Alamat</label>
<textarea name="alamat" class="form-control"></textarea>

<label>Asal Sekolah</label>
<input type="text" name="asal_sekolah" class="form-control" required>

<label>Jurusan Sekolah</label>
<input type="text" name="jurusan_sekolah" class="form-control">

<label>Tahun Lulus</label>
<select name="tahun_lulus" class="form-control">
<option value="">Pilih Tahun</option>
<?php for($i=date('Y'); $i>=2000; $i--): ?>
<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
<?php endfor; ?>
</select>

<label>Upload Foto</label>
<input type="file" name="foto" class="form-control" accept="image/*">

<button type="submit" class="btn btn-register mt-3">
    <i class="fa fa-user-plus"></i> Daftar Sekarang
</button>

</form>

        <div class="text-center mt-4">
            Sudah punya akun? <a href="index.php">Login di sini</a>
        </div>

        <div class="text-center mt-3">
            <a href="../index.php">Kembali ke Beranda</a>
        </div>

    </div>

</div>

</body>
</html>