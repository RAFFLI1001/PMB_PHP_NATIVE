<?php
require_once '../config/database.php';

// Check admin login
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: data_maba.php");
    exit();
}

$id = intval($_GET['id']);

// Ambil data mahasiswa
$query = "SELECT * FROM calon_mahasiswa WHERE id_calon = $id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: data_maba.php?error=Data tidak ditemukan");
    exit();
}

$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detail Calon Mahasiswa</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
body{
    background:#F1F5F9;
    font-family:'Inter',sans-serif;
}

.main-content{
    margin-left:280px;
    min-height:100vh;
}

.top-nav{
    background:white;
    padding:20px;
    box-shadow:0 1px 3px rgba(0,0,0,0.1);
}

.dashboard-card{
    background:white;
    border-radius:12px;
    padding:25px;
    box-shadow:0 4px 20px rgba(0,0,0,0.05);
}

.label{
    font-weight:600;
    color:#475569;
}

.value{
    font-size:16px;
    color:#0F172A;
    margin-bottom:15px;
}
</style>
</head>

<body>

<?php include 'sidebar.php'; ?>

<main class="main-content">

<!-- TOP NAV -->
<header class="top-nav">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-0">Detail Calon Mahasiswa</h3>
            <small class="text-muted">Informasi lengkap mahasiswa</small>
        </div>

        <div>
            <a href="edit_maba.php?id=<?php echo $data['id_calon']; ?>" class="btn btn-warning me-2">
                <i class="fas fa-edit me-1"></i>Edit
            </a>

            <a href="data_maba.php" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
</header>

<div class="container-fluid py-4">

<div class="dashboard-card">

<h4 class="mb-4">Data Pribadi</h4>

<div class="row">

<div class="col-md-6">
<div class="label">Nama Lengkap</div>
<div class="value"><?php echo $data['nama_lengkap']; ?></div>
</div>

<div class="col-md-6">
<div class="label">Email</div>
<div class="value"><?php echo $data['email']; ?></div>
</div>

<div class="col-md-6">
<div class="label">No HP</div>
<div class="value"><?php echo $data['no_hp']; ?></div>
</div>

<div class="col-md-6">
<div class="label">Jenis Kelamin</div>
<div class="value">
<?php echo $data['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?>
</div>
</div>

<div class="col-md-6">
<div class="label">Tempat Lahir</div>
<div class="value"><?php echo $data['tempat_lahir']; ?></div>
</div>

<div class="col-md-6">
<div class="label">Tanggal Lahir</div>
<div class="value"><?php echo $data['tanggal_lahir']; ?></div>
</div>

<div class="col-12">
<div class="label">Alamat</div>
<div class="value"><?php echo $data['alamat']; ?></div>
</div>

</div>

<hr class="my-4">

<h4 class="mb-4">Data Sekolah</h4>

<div class="row">

<div class="col-md-6">
<div class="label">Asal Sekolah</div>
<div class="value"><?php echo $data['asal_sekolah']; ?></div>
</div>

<div class="col-md-6">
<div class="label">Jurusan Sekolah</div>
<div class="value"><?php echo $data['jurusan_sekolah']; ?></div>
</div>

<div class="col-md-6">
<div class="label">Tahun Lulus</div>
<div class="value"><?php echo $data['tahun_lulus']; ?></div>
</div>

</div>

</div>
</div>

</main>
</body>
</html>