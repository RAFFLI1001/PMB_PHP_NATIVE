<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* =================
AMBIL WAKTU UJIAN
================= */
$timer = mysqli_query($conn, "SELECT * FROM pengaturan_ujian LIMIT 1");
$data_timer = mysqli_fetch_assoc($timer);

$durasi_menit = $data_timer['durasi_menit'] ?? 30;

/* =================
AMBIL DATA USER
================= */
$query = "SELECT * FROM calon_mahasiswa WHERE id_calon = '$user_id'";
$user = mysqli_fetch_assoc(mysqli_query($conn, $query));

/* =================
AMBIL DATA PENDAFTARAN
================= */
$pendaftaran = mysqli_query($conn, "
    SELECT p.*, j.nama_jurusan 
    FROM pendaftaran p
    LEFT JOIN jurusan j ON p.id_jurusan=j.id_jurusan
    WHERE p.id_calon='$user_id'
");

$data_pendaftaran = mysqli_fetch_assoc($pendaftaran);

if (!$data_pendaftaran) {
    header("Location: pendaftaran.php");
    exit();
}

/* =================
CEK SUDAH TEST
================= */
if (!empty($data_pendaftaran['nilai_test'])) {
    header("Location: hasil.php");
    exit();
}

/* =================
CEK DAFTAR ULANG
================= */
$has_registered   = true;
$has_daftar_ulang = false;
$cek_du = mysqli_query($conn, "SELECT id FROM daftar_ulang WHERE id_calon = '$user_id' LIMIT 1");
if ($cek_du && mysqli_num_rows($cek_du) > 0) {
    $has_daftar_ulang = true;
}

/* =================
VERIFIKASI NO TEST
================= */
if (!isset($_SESSION['test_verified'])) {

    if (isset($_POST['cek_notest'])) {
        if ($_POST['no_test'] == $user['no_test']) {
            $_SESSION['test_verified'] = true;
            header("Location: test.php");
            exit();
        } else {
            $error = "No Test tidak sesuai!";
        }
    }

}

/* =================
VARIABEL SIDEBAR
================= */
$current_page = 'test';
$hide_sidebar = isset($_SESSION['test_verified']); // true = fullscreen saat ujian

$hideNavbar = true;
include '../includes/header.php';
?>

<style>
    /* Sidebar Styles */
    .sidebar {
        background: linear-gradient(180deg, #003366 0%, #002244 100%);
        height: 100vh;
        position: sticky;
        top: 0;
    }

    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.85);
        padding: 12px 16px;
        border-left: 3px solid transparent;
        border-radius: 10px;
        margin: 4px 10px;
        transition: all .2s ease;
    }

    .sidebar .nav-link:hover {
        color: white;
        background: rgba(255, 255, 255, 0.1);
        border-left-color: #28a745;
    }

    .sidebar .nav-link.active {
        color: white;
        background: rgba(255, 255, 255, 0.15);
        border-left-color: #28a745;
    }

    .avatar-container {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        overflow: hidden;
        margin: auto;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-container i {
        font-size: 90px;
        color: white;
    }

    /* Compact Topbar Header */
    .page-topbar {
        background: #fff;
        border: 1px solid rgba(0, 0, 0, .06);
        border-radius: 14px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        box-shadow: 0 10px 26px rgba(16, 24, 40, .06);
    }

    .page-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: rgba(13, 110, 253, .10);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0d6efd;
        font-size: 18px;
    }

    .page-title {
        font-size: 18px;
        font-weight: 800;
        line-height: 1.1;
    }

    .page-subtitle {
        font-size: 13px;
        color: #6c757d;
        margin-top: 2px;
    }

    @media (max-width: 768px) {
        .page-topbar {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    /* Progress Timeline */
    .steps {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 40px 0;
    }

    .steps::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background-color: #e9ecef;
        z-index: 1;
    }

    .step {
        position: relative;
        z-index: 2;
        text-align: center;
        flex: 1;
    }

    .step-icon {
        width: 40px;
        height: 40px;
        background-color: #e9ecef;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-weight: bold;
        color: #6c757d;
    }

    .step.completed .step-icon {
        background-color: #28a745;
        color: white;
    }

    .step.active .step-icon {
        background-color: #007bff;
        color: white;
    }

    .step-content {
        padding: 0 10px;
    }

    @media (max-width: 768px) {
        .sidebar {
            min-height: auto;
        }

        .steps {
            flex-direction: column;
            align-items: flex-start;
        }

        .steps::before {
            display: none;
        }

        .step {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            text-align: left;
            width: 100%;
        }

        .step-icon {
            margin: 0 15px 0 0;
            flex-shrink: 0;
        }

        .step-content {
            padding: 0;
        }
    }
</style>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include '../includes/sidebar_user.php'; ?>

        <!-- MAIN CONTENT -->
        <div class="<?php echo $hide_sidebar ? 'col-12' : 'col-md-9 col-lg-10 ms-sm-auto'; ?> px-md-4 py-3">

            <?php if (!$hide_sidebar): ?>

                <!-- INPUT NO TEST -->
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-lock me-2"></i>Masukkan No Test</h4>
                    </div>

                    <div class="card-body">

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <!-- STATUS VERIFIKASI ADMIN -->
                        <?php if (isset($data_pendaftaran['status_verifikasi'])): ?>

                            <?php if ($data_pendaftaran['status_verifikasi'] == 'menunggu'): ?>
                                <div class="alert alert-warning">
                                    Menunggu verifikasi admin
                                </div>

                            <?php elseif ($data_pendaftaran['status_verifikasi'] == 'disetujui'): ?>
                                <div class="alert alert-success">
                                    Sudah diverifikasi admin
                                </div>

                            <?php elseif ($data_pendaftaran['status_verifikasi'] == 'ditolak'): ?>
                                <div class="alert alert-danger">
                                    Bukti pembayaran ditolak, silakan upload ulang
                                </div>

                            <?php endif; ?>

                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label>No Test</label>
                                <input type="text" name="no_test" class="form-control" required>
                            </div>
                            <button type="submit" name="cek_notest" class="btn btn-primary">Mulai Test</button>
                        </form>

                    </div>
                </div>

            <?php else: ?>

                <!-- SOAL -->
                <div class="card shadow">

                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-question-circle me-2"></i>Soal Test</h4>

                        <div class="bg-dark px-3 py-1 rounded">
                            <i class="fas fa-clock me-1"></i>
                            <span id="timer"><?php echo $durasi_menit; ?>:00</span>
                        </div>
                    </div>

                    <div class="card-body">

                        <form method="POST" action="submit_test.php" id="formTest">

                            <?php
                            $id_jurusan = $data_pendaftaran['id_jurusan'];

                            $soal = mysqli_query($conn, "
                                SELECT * FROM soal_test 
                                WHERE id_jurusan = '$id_jurusan'
                                ORDER BY RAND() 
                                LIMIT 10
                            ");

                            if (mysqli_num_rows($soal) == 0) {
                            ?>

                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    Soal belum tersedia untuk jurusan ini.
                                </div>

                                <div class="text-center mt-3">
                                    <a href="dashboard.php" class="btn btn-primary px-4 py-2">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                                    </a>
                                </div>

                            <?php
                            } else {

                                $no = 1;
                                while ($s = mysqli_fetch_assoc($soal)) {
                            ?>

                                <div class="mb-4">

                                    <p><b><?php echo $no++; ?>. <?php echo $s['pertanyaan']; ?></b></p>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio"
                                            name="jawaban[<?php echo $s['id_soal']; ?>]" value="a">
                                        <label class="form-check-label"><?php echo $s['pilihan_a']; ?></label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio"
                                            name="jawaban[<?php echo $s['id_soal']; ?>]" value="b">
                                        <label class="form-check-label"><?php echo $s['pilihan_b']; ?></label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio"
                                            name="jawaban[<?php echo $s['id_soal']; ?>]" value="c">
                                        <label class="form-check-label"><?php echo $s['pilihan_c']; ?></label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio"
                                            name="jawaban[<?php echo $s['id_soal']; ?>]" value="d">
                                        <label class="form-check-label"><?php echo $s['pilihan_d']; ?></label>
                                    </div>

                                </div>
                                <hr>

                            <?php
                                }
                            } ?>

                            <button class="btn btn-success">Kirim Jawaban</button>

                        </form>

                    </div>
                </div>

            <?php endif; ?>

        </div><!-- /main -->
    </div><!-- /row -->
</div><!-- /container-fluid -->

<!-- TIMER -->
<?php if ($hide_sidebar): ?>
<script>
    let waktu = <?php echo $durasi_menit; ?> * 60;

    let timer = setInterval(function () {
        let menit = Math.floor(waktu / 60);
        let detik = waktu % 60;

        document.getElementById("timer").innerHTML =
            menit + ":" + (detik < 10 ? "0" + detik : detik);

        waktu--;

        if (waktu < 0) {
            clearInterval(timer);
            alert("Waktu habis! Jawaban akan dikirim otomatis.");
            document.getElementById("formTest").submit();
        }
    }, 1000);
</script>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>