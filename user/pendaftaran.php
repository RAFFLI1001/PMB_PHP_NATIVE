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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Formulir Pendaftaran PMB Aretn Campus </title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-file-signature me-2"></i>Formulir Pendaftaran PMB Arten Campus</h4>
                </div>

                <div class="card-body">
                    <?php if(isset($success)): ?>
                        <div class="alert alert-success">
                            <h5 class="mb-2"><i class="fas fa-check-circle me-2"></i>Pendaftaran Berhasil!</h5>
                            <p class="mb-1"><?php echo $success; ?></p>
                            <p class="mb-0">Anda akan dialihkan ke dashboard dalam 3 detik...</p>
                        </div>
                    <?php else: ?>

                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle me-2"></i>Informasi Penting</h5>
                            <ul class="mb-0">
                                <li>Pastikan data profil Anda sudah lengkap sebelum mendaftar</li>
                                <li>Pilih jurusan dengan teliti, tidak dapat diubah setelah pendaftaran</li>
                                <li>Setelah mendaftar, Anda akan mendapatkan nomor test untuk mengikuti ujian</li>
                                <li>Ujian dapat diikuti kapan saja setelah pendaftaran</li>
                            </ul>
                        </div>

                        <form method="POST" action="" onsubmit="return confirmSubmit()">
                            <div class="mb-4">
                                <h5>Pilih Program Studi</h5>
                                <div class="row">
                                    <?php while($jurusan = mysqli_fetch_assoc($jurusan_result)): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check border rounded p-3">
                                                <input class="form-check-input" type="radio"
                                                       name="id_jurusan"
                                                       id="jurusan<?php echo $jurusan['id_jurusan']; ?>"
                                                       value="<?php echo $jurusan['id_jurusan']; ?>"
                                                       required>
                                                <label class="form-check-label" for="jurusan<?php echo $jurusan['id_jurusan']; ?>">
                                                    <strong><?php echo $jurusan['nama_jurusan']; ?></strong>
                                                    <small class="d-block text-muted">Kode: <?php echo $jurusan['kode_jurusan']; ?></small>
                                                    <small class="d-block">Kuota: <?php echo $jurusan['kuota']; ?> mahasiswa</small>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5>Persyaratan</h5>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="syarat1" required>
                                    <label class="form-check-label" for="syarat1">
                                        Saya telah mengisi data diri dengan lengkap dan benar
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="syarat2" required>
                                    <label class="form-check-label" for="syarat2">
                                        Saya bersedia mengikuti seluruh proses seleksi
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="syarat3" required>
                                    <label class="form-check-label" for="syarat3">
                                        Data yang saya berikan dapat dipertanggungjawabkan kebenarannya
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
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

            <div class="card mt-4 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Jadwal Pendaftaran</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Gelombang 1</h6>
                            <p class="mb-0">Pendaftaran: 1 Jan - 30 April 2024<br>Test Online: 1 - 31 Mei 2024</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Gelombang 2</h6>
                            <p class="mb-0">Pendaftaran: 1 Mei - 31 Juli 2024<br>Test Online: 1 - 31 Agustus 2024</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap JS (opsional tapi aman) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function confirmSubmit() {
    return confirm("Apakah Anda yakin dengan pilihan jurusan ini? Setelah mendaftar, tidak dapat diubah.");
}
</script>

</body>
</html>