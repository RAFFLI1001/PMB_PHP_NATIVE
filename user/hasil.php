<?php
require_once '../config/database.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT p.*, j.nama_jurusan,
         (SELECT COUNT(*) FROM daftar_ulang du WHERE du.id_pendaftaran=p.id_pendaftaran) as sudah_daftar_ulang
         FROM pendaftaran p
         JOIN jurusan j ON p.id_jurusan=j.id_jurusan
         WHERE p.id_calon=$user_id";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: pendaftaran.php");
    exit();
}

$data = mysqli_fetch_assoc($result);

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM calon_mahasiswa WHERE id_calon=$user_id"));
?>

<?php $hideNavbar = true; ?>
<?php include '../includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR (SAMA PERSIS SEPERTI DASHBOARD) -->
        <div class="col-md-3 col-lg-2 sidebar d-md-block">

            <div class="position-sticky pt-4">

                <div class="text-center mb-4 px-3">
                    <div class="mb-3 avatar-container">

                        <?php if (!empty($user['foto'])) { ?>
                            <img src="../uploads/profile/<?php echo $user['foto']; ?>">
                        <?php } else { ?>
                            <i class="fas fa-user-circle"></i>
                        <?php } ?>

                    </div>

                    <h6 class="text-white mb-1"><?php echo $user['nama_lengkap']; ?></h6>
                    <small class="text-white-50"><?php echo $user['email']; ?></small>

                    <div class="mt-2">
                        <span class="badge bg-info">Calon Mahasiswa</span>
                    </div>
                </div>

                <h6 class="text-white-50 mb-2 px-3">MENU UTAMA</h6>

                <ul class="nav flex-column mb-4 px-2">

                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="profil.php" class="nav-link">
                            <i class="fas fa-user-edit me-2"></i>Profil Saya
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="test.php" class="nav-link">
                            <i class="fas fa-clipboard-list me-2"></i>Test Online
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="hasil.php" class="nav-link active">
                            <i class="fas fa-chart-line me-2"></i>Hasil Test
                        </a>
                    </li>

                        
                </ul>

                <div class="px-3 mt-4">
                    <a href="../logout.php" class="btn btn-sm btn-outline-light w-100">
                        <i class="fas fa-sign-out-alt me-1"></i>Keluar
                    </a>
                </div>

            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-3">

            <div class="page-topbar mb-3">

                <div class="d-flex align-items-center gap-2">
                    <div class="page-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>

                    <div>
                        <div class="page-title">Hasil Test</div>
                        <div class="page-subtitle">
                            Status seleksi PMB
                        </div>
                    </div>
                </div>

            </div>

            <div class="row justify-content-center">

                <div class="col-md-8">

                    <div class="card shadow-sm">

                        <div class="card-header text-white
<?php echo $data['status'] == 'lulus' ? 'bg-success' : ($data['status'] == 'tidak_lulus' ? 'bg-danger' : 'bg-warning'); ?>">

                            <h5 class="mb-0">

                                <?php if ($data['status'] == 'lulus') { ?>
                                    SELAMAT! Anda Lulus
                                <?php } elseif ($data['status'] == 'tidak_lulus') { ?>
                                    Hasil Seleksi
                                <?php } else { ?>
                                    Status Pendaftaran
                                <?php } ?>

                            </h5>

                        </div>

                        <div class="card-body">

                            <div class="text-center mb-4">

                                <?php if ($data['status'] == 'lulus') { ?>

                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                                        <h4>Selamat! Anda Lulus Seleksi</h4>
                                        <p>Silakan melakukan daftar ulang untuk melanjutkan proses penerimaan.</p>
                                    </div>

                                <?php } elseif ($data['status'] == 'tidak_lulus') { ?>

                                    <div class="alert alert-danger">
                                        <i class="fas fa-times-circle fa-3x mb-3"></i>
                                        <h4>Maaf Anda Tidak Lulus</h4>
                                    </div>

                                <?php } else { ?>

                                    <div class="alert alert-warning">
                                        <i class="fas fa-clock fa-3x mb-3"></i>
                                        <h4>Menunggu Hasil Test</h4>
                                    </div>

                                <?php } ?>

                            </div>

                            <table class="table">

                                <tr>
                                    <th>No Test</th>
                                    <td><?php echo $data['no_test']; ?></td>
                                </tr>

                                <tr>
                                    <th>Nama</th>
                                    <td><?php echo $_SESSION['user_nama']; ?></td>
                                </tr>

                                <tr>
                                    <th>Jurusan</th>
                                    <td><?php echo $data['nama_jurusan']; ?></td>
                                </tr>

                                <tr>
                                    <th>Nilai Test</th>
                                    <td>
                                        <?php
                                        if ($data['nilai_test']) {
                                            echo number_format($data['nilai_test'], 2);
                                        } else {
                                            echo "Belum Ada";
                                        }
                                        ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th>Status</th>
                                    <td>

                                        <?php
                                        $badge = $data['status'] == 'lulus' ? 'success' : ($data['status'] == 'tidak_lulus' ? 'danger' : 'warning');
                                        ?>

                                        <span class="badge bg-<?php echo $badge; ?>">
                                            <?php echo ucfirst($data['status']); ?>
                                        </span>

                                    </td>
                                </tr>

                            </table>

                            <div class="text-center mt-4">

                                <a href="dashboard.php" class="btn btn-secondary me-2">
                                    <i class="fas fa-home"></i> Dashboard
                                </a>

                                <?php if ($data['status'] == 'lulus') { ?>

                                    <?php if ($data['sudah_daftar_ulang'] == 0) { ?>

                                        <a href="daftar_ulang.php?id=<?php echo $data['id_pendaftaran']; ?>"
                                            class="btn btn-success">
                                            <i class="fas fa-file-signature"></i> Daftar Ulang
                                        </a>

                                    <?php } else { ?>

                                        <div class="alert alert-info mt-3">
                                            <i class="fas fa-check-circle"></i> Anda sudah melakukan daftar ulang
                                        </div>

                                    <?php } ?>

                                <?php } ?>

                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<style>
    /* SIDEBAR SAMA PERSIS DENGAN DASHBOARD */
    .sidebar {
        background: linear-gradient(180deg, #003366 0%, #002244 100%);
        height: 100vh;
        position: sticky;
        top: 0;
    }

    /* MENU */
    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.85);
        padding: 12px 16px;
        border-left: 3px solid transparent;
        border-radius: 10px;
        margin: 4px 10px;
    }

    .sidebar .nav-link:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .sidebar .nav-link.active {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        border-left-color: #28a745;
    }

    /* AVATAR */
    .avatar-container {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        overflow: hidden;
        margin: auto;
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

    /* TOPBAR */
    .page-topbar {
        background: #fff;
        border-radius: 14px;
        padding: 14px 16px;
        display: flex;
        justify-content: space-between;
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
    }

    .page-title {
        font-size: 18px;
        font-weight: 700;
    }

    .page-subtitle {
        font-size: 13px;
        color: #6c757d;
    }
</style>

<?php include '../includes/footer.php'; ?>