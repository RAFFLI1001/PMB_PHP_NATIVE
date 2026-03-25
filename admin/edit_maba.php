<?php
require_once '../config/database.php';

// Check if admin is logged in
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Get ID from URL
if (!isset($_GET['id'])) {
    header("Location: data_maba.php");
    exit();
}

$id = intval($_GET['id']);

// Get user data
$query = "SELECT * FROM calon_mahasiswa WHERE id_calon = $id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: data_maba.php?error=Data tidak ditemukan");
    exit();
}

$user = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $asal_sekolah = mysqli_real_escape_string($conn, $_POST['asal_sekolah']);
    $jurusan_sekolah = mysqli_real_escape_string($conn, $_POST['jurusan_sekolah']);
    $tahun_lulus = $_POST['tahun_lulus'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tempat_lahir = mysqli_real_escape_string($conn, $_POST['tempat_lahir']);
    $tanggal_lahir = $_POST['tanggal_lahir'];

    // Check email
    $check_email = mysqli_query($conn, "SELECT id_calon FROM calon_mahasiswa 
                                        WHERE email = '$email' AND id_calon != $id");

    if (mysqli_num_rows($check_email) > 0) {
        $error = "Email sudah digunakan oleh user lain!";
    } else {

        $query = "UPDATE calon_mahasiswa SET 
                  nama_lengkap = '$nama_lengkap',
                  email = '$email',
                  no_hp = '$no_hp',
                  alamat = '$alamat',
                  asal_sekolah = '$asal_sekolah',
                  jurusan_sekolah = '$jurusan_sekolah',
                  tahun_lulus = '$tahun_lulus',
                  jenis_kelamin = '$jenis_kelamin',
                  tempat_lahir = '$tempat_lahir',
                  tanggal_lahir = '$tanggal_lahir'
                  WHERE id_calon = $id";

        if (mysqli_query($conn, $query)) {
            $success = "Data berhasil diperbarui!";
            $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM calon_mahasiswa WHERE id_calon = $id"));
        } else {
            $error = "Gagal memperbarui data!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Maba - Admin PMB</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            background: #F1F5F9;
            font-family: 'Inter', sans-serif;
        }

        .main-content {
            margin-left: 280px;
            min-height: 100vh;
        }

        .top-nav {
            background: white;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .dashboard-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .card-title {
            font-weight: 600;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <main class="main-content">

        <!-- Top Navbar -->
        <header class="top-nav">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0">Edit Data Calon Mahasiswa</h3>
                    <small class="text-muted">Perbarui data mahasiswa</small>
                </div>
               
            </div>
        </header>

        <div class="container-fluid py-4">

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- FORM -->
            <div class="dashboard-card">
                <h4 class="card-title">Form Edit Mahasiswa</h4>

                <form method="POST">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control form-control-lg" name="nama_lengkap"
                                value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control form-control-lg" name="email"
                                value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">No HP</label>
                            <input type="text" class="form-control form-control-lg" name="no_hp"
                                value="<?php echo htmlspecialchars($user['no_hp']); ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select class="form-select form-select-lg" name="jenis_kelamin">
                                <option value="">Pilih</option>
                                <option value="L" <?php echo $user['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>Laki-laki
                                </option>
                                <option value="P" <?php echo $user['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>Perempuan
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" class="form-control form-control-lg" name="tempat_lahir"
                                value="<?php echo htmlspecialchars($user['tempat_lahir']); ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control form-control-lg" name="tanggal_lahir"
                                value="<?php echo $user['tanggal_lahir']; ?>">
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control form-control-lg" name="alamat"
                                rows="3"><?php echo htmlspecialchars($user['alamat']); ?></textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Asal Sekolah</label>
                            <input type="text" class="form-control form-control-lg" name="asal_sekolah"
                                value="<?php echo htmlspecialchars($user['asal_sekolah']); ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jurusan Sekolah</label>
                            <input type="text" class="form-control form-control-lg" name="jurusan_sekolah"
                                value="<?php echo htmlspecialchars($user['jurusan_sekolah']); ?>">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Tahun Lulus</label>
                            <select class="form-select form-select-lg" name="tahun_lulus">
                                <option value="">Pilih Tahun</option>
                                <?php for ($year = date('Y'); $year >= 2010; $year--): ?>
                                    <option value="<?php echo $year; ?>" <?php echo $user['tahun_lulus'] == $year ? 'selected' : ''; ?>>
                                        <?php echo $year; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>

                            <a href="data_maba.php" class="btn btn-outline-secondary btn-lg">
                                Batal
                            </a>
                        </div>

                    </div>
                </form>
            </div>

        </div>
    </main>

</body>

</html>