<?php
require_once '../config/database.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$check = mysqli_query($conn, "SELECT * FROM pendaftaran WHERE id_calon = $user_id");
if (mysqli_num_rows($check) > 0) {
    header("Location: dashboard.php");
    exit();
}

$jurusan_result = mysqli_query($conn, "SELECT * FROM jurusan ORDER BY nama_jurusan");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_jurusan = intval($_POST['id_jurusan']);

    $date = date('Ymd');
    $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    $no_test = $date . $random;

    while (mysqli_num_rows(mysqli_query($conn, "SELECT id_pendaftaran FROM pendaftaran WHERE no_test = '$no_test'")) > 0) {
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $no_test = $date . $random;
    }

    $query = "INSERT INTO pendaftaran (id_calon, id_jurusan, no_test, tanggal_daftar, status) 
              VALUES ($user_id, $id_jurusan, '$no_test', CURDATE(), 'pending')";

    if (mysqli_query($conn, $query)) {
        $success = "Pendaftaran berhasil! No. Test Anda: <strong>$no_test</strong>";
        header("refresh:3;url=dashboard.php");
    } else {
        $error = "Gagal mendaftar: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pendaftaran PMB | Arten Campus</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>
body{
    background: linear-gradient(135deg,#f8f9fa,#eef2f7);
}

.page-header{
    background: linear-gradient(135deg,#198754,#157347);
    color:white;
    border-radius:16px;
    padding:30px;
    margin-bottom:25px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

.jurusan-card{
    border-radius:14px;
    border:1px solid #eaeaea;
    padding:18px;
    transition:0.3s;
    cursor:pointer;
}

.jurusan-card:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 20px rgba(0,0,0,0.08);
    border-color:#198754;
}

.form-check-input:checked{
    background-color:#198754;
    border-color:#198754;
}

.btn-daftar{
    background:#198754;
    border:none;
    padding:12px;
    font-size:17px;
    font-weight:600;
    border-radius:10px;
}

.btn-daftar:hover{
    background:#157347;
}

.info-box{
    background:#f1f8ff;
    border-radius:12px;
    padding:18px;
    border-left:5px solid #0d6efd;
}
</style>
</head>

<body>

<div class="container py-5">

<div class="page-header">
    <h3 class="mb-1"><i class="fas fa-user-graduate me-2"></i>Pendaftaran Mahasiswa Baru</h3>
    <p class="mb-0">Silakan pilih program studi yang ingin Anda daftarkan</p>
</div>

<div class="card shadow-sm border-0">
<div class="card-body p-4">

<?php if(isset($success)): ?>
<div class="alert alert-success">
    <h5><i class="fas fa-check-circle me-2"></i>Pendaftaran Berhasil</h5>
    <?php echo $success; ?>
</div>
<?php else: ?>

<?php if(isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="info-box mb-4">
    <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Informasi Penting</h6>
    <ul class="mb-0">
        <li>Pastikan data profil Anda sudah lengkap</li>
        <li>Pilih jurusan dengan teliti</li>
        <li>Nomor test akan dibuat otomatis</li>
        <li>Ujian bisa langsung dilakukan setelah daftar</li>
    </ul>
</div>

<form method="POST" action="" onsubmit="return confirmSubmit()">

<h5 class="mb-3">Pilih Program Studi</h5>

<div class="row">

<?php while($jurusan = mysqli_fetch_assoc($jurusan_result)): ?>
<div class="col-md-6 mb-3">

<label class="jurusan-card w-100">
    <div class="form-check">
        <input class="form-check-input"
               type="radio"
               name="id_jurusan"
               value="<?php echo $jurusan['id_jurusan']; ?>"
               required>

        <div class="ms-2">
            <h6 class="mb-1"><?php echo $jurusan['nama_jurusan']; ?></h6>
            <small class="text-muted">Kode: <?php echo $jurusan['kode_jurusan']; ?></small><br>
            <small>Kuota: <?php echo $jurusan['kuota']; ?> Mahasiswa</small>
        </div>
    </div>
</label>

</div>
<?php endwhile; ?>

</div>

<hr class="my-4">

<h5 class="mb-3">Konfirmasi Pendaftaran</h5>

<div class="form-check mb-2">
<input class="form-check-input" type="checkbox" required>
<label class="form-check-label">Data yang saya isi sudah benar</label>
</div>

<div class="form-check mb-3">
<input class="form-check-input" type="checkbox" required>
<label class="form-check-label">Saya bersedia mengikuti proses seleksi</label>
</div>

<div class="d-grid gap-2">
<button type="submit" class="btn btn-daftar">
<i class="fas fa-paper-plane me-2"></i>Daftar Sekarang
</button>

<a href="dashboard.php" class="btn btn-outline-secondary">
Kembali ke Dashboard
</a>
</div>

</form>

<?php endif; ?>

</div>
</div>

</div>

<script>
function confirmSubmit() {
return confirm("Yakin ingin mendaftar jurusan ini?");
}
</script>

</body>
</html>