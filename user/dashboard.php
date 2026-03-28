<?php
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: index.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

// Get user data
$query = "SELECT * FROM calon_mahasiswa WHERE id_calon = $user_id";
$user = mysqli_fetch_assoc(mysqli_query($conn, $query));

// Check if user has registered for PMB
$pendaftaran = mysqli_query($conn, "SELECT p.*, j.nama_jurusan FROM pendaftaran p
                                   LEFT JOIN jurusan j ON p.id_jurusan = j.id_jurusan
                                   WHERE p.id_calon = $user_id");
$has_registered = mysqli_num_rows($pendaftaran) > 0;
if ($has_registered) {
    $data_pendaftaran = mysqli_fetch_assoc($pendaftaran);
}

// Check if already did daftar ulang
if ($has_registered) {
    $daftar_ulang_query = mysqli_query($conn, "SELECT * FROM daftar_ulang WHERE id_pendaftaran = {$data_pendaftaran['id_pendaftaran']}");
    $has_daftar_ulang = mysqli_num_rows($daftar_ulang_query) > 0;
} else {
    $has_daftar_ulang = false;
}
?>

<?php $hideNavbar = true; // supaya navbar atas dari header.php tidak muncul ?>
<?php include '../includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar d-md-block">
            <div class="position-sticky pt-4">
                <!-- User Profile -->
                <div class="text-center mb-4 px-3">
                    <div class="mb-3 avatar-container">

                        <?php if (!empty($user['foto'])): ?>

                            <img src="../uploads/profile/<?php echo $user['foto']; ?>">

                        <?php else: ?>

                            <i class="fas fa-user-circle"></i>

                        <?php endif; ?>

                    </div>
                    <h6 class="text-white mb-1"><?php echo htmlspecialchars($user['nama_lengkap']); ?></h6>
                    <small class="text-white-50"><?php echo htmlspecialchars($user['email']); ?></small>
                    <div class="mt-2">
                        <span class="badge bg-info">Calon Mahasiswa</span>
                    </div>
                </div>

                <!-- Navigation Menu -->
                <h6 class="text-white-50 mb-2 px-3">MENU UTAMA</h6>
                <ul class="nav flex-column mb-4 px-2">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link active">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="profil.php" class="nav-link">
                            <i class="fas fa-user-edit me-2"></i>Profil Saya
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="pendaftaran.php" class="nav-link">
                            <i class="fas fa-user-graduate me-2"></i>Pendaftaran
                        </a>
                    </li>

                    <?php if ($has_registered): ?>
        
                        <li class="nav-item">
                            <a href="test.php" class="nav-link">
                                <i class="fas fa-clipboard-list me-2"></i>Test Online
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="hasil.php" class="nav-link">
                                <i class="fas fa-chart-line me-2"></i>Hasil Test
                            </a>
                        </li>
                        <?php if ($data_pendaftaran['status'] == 'lulus' && !$has_daftar_ulang): ?>
                            <li class="nav-item">
                                <a href="daftar_ulang.php" class="nav-link">
                                    <i class="fas fa-check-double me-2"></i>Daftar Ulang
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>

                <!-- Quick Actions -->
              

                <!-- Logout -->
                <div class="px-3 mt-4">
                    <a href="../logout.php" class="btn btn-sm btn-outline-light w-100">
                        <i class="fas fa-sign-out-alt me-1"></i>Keluar
                    </a>
                </div>

                <!-- Footer -->
                
            </div>
        </div>

        <!-- Main Content -->
        <!-- padding dibuat lebih kecil biar header tidak tinggi -->
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-3">

            <!-- Compact Header Topbar -->
            <div class="page-topbar mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="page-icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div>
                        <div class="page-title">Dashboard</div>
                        <div class="page-subtitle">
                            Selamat datang, <?php echo htmlspecialchars($user['nama_lengkap']); ?>!
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border px-3 py-2">
                        <i class="fas fa-clock me-1 text-primary"></i>
                        <span id="currentTime"><?php echo date('d F Y H:i'); ?></span>
                    </span>

                </div>
            </div>

            <!-- Status Banner -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center g-3">
                                <div class="col-md-8">
                                    <h5 class="card-title mb-2">Status Pendaftaran PMB</h5>
                                    <p class="card-text text-muted mb-0">
                                        <?php if ($has_registered): ?>
                                            Anda telah mendaftar program studi
                                            <strong><?php echo htmlspecialchars($data_pendaftaran['nama_jurusan']); ?></strong>.
                                        <?php else: ?>
                                            Anda belum mendaftar PMB. Silakan daftar terlebih dahulu.
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <?php if ($has_registered): ?>
                                        <span class="badge bg-success py-2 px-3 fs-6">
                                            <i class="fas fa-check-circle me-1"></i>Telah Mendaftar
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning py-2 px-3 fs-6">
                                            <i class="fas fa-clock me-1"></i>Belum Mendaftar
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-3"><i class="fas fa-id-card fa-3x text-primary"></i></div>
                            <h5 class="card-title">No. Test</h5>
                            <p class="card-text display-6">
                                <?php echo $has_registered ? htmlspecialchars($data_pendaftaran['no_test']) : '---'; ?>
                            </p>
                            <small class="text-muted">Nomor identifikasi test</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-3"><i class="fas fa-graduation-cap fa-3x text-info"></i></div>
                            <h5 class="card-title">Jurusan</h5>
                            <p class="card-text fs-4">
                                <?php echo $has_registered ? htmlspecialchars($data_pendaftaran['nama_jurusan']) : '---'; ?>
                            </p>
                            <small class="text-muted">Program studi pilihan</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i
                                    class="fas fa-chart-line fa-3x 
                                <?php echo $has_registered && $data_pendaftaran['status'] == 'lulus' ? 'text-success' :
                                    ($has_registered && $data_pendaftaran['status'] == 'tidak_lulus' ? 'text-danger' : 'text-warning'); ?>">
                                </i>
                            </div>
                            <h5 class="card-title">Status Test</h5>
                            <p class="card-text fs-4">
                                <?php if ($has_registered):
                                    $status = $data_pendaftaran['status'];
                                    $badge_color = $status == 'lulus' ? 'success' :
                                        ($status == 'tidak_lulus' ? 'danger' : 'warning');
                                    ?>
                                    <span class="badge bg-<?php echo $badge_color; ?> py-2 px-3">
                                        <?php echo ucfirst(str_replace('_', ' ', $status)); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary py-2 px-3">Belum</span>
                                <?php endif; ?>
                            </p>
                            <small class="text-muted">Status kelulusan test</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i
                                    class="fas fa-star fa-3x 
                                <?php echo $has_registered && $data_pendaftaran['nilai_test'] ?
                                    ($data_pendaftaran['nilai_test'] >= 70 ? 'text-success' : 'text-danger') : 'text-secondary'; ?>">
                                </i>
                            </div>
                            <h5 class="card-title">Nilai Test</h5>
                            <p class="card-text display-6">
                                <?php echo $has_registered && $data_pendaftaran['nilai_test'] ?
                                    number_format((float) $data_pendaftaran['nilai_test'], 2) : '---'; ?>
                            </p>
                            <small class="text-muted">
                                <?php if ($has_registered && $data_pendaftaran['nilai_test']): ?>
                                    <?php echo ((float) $data_pendaftaran['nilai_test'] >= 70) ? 'Lulus' : 'Tidak Lulus'; ?>
                                <?php else: ?>
                                    Belum ada nilai
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Timeline -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-project-diagram me-2"></i>Progress Pendaftaran</h5>
                        </div>
                        <div class="card-body">
                            <div class="steps">
                                <div class="step <?php echo $has_registered ? 'completed' : 'active'; ?>">
                                    <div class="step-icon">
                                        <?php if ($has_registered): ?><i
                                                class="fas fa-check"></i><?php else: ?><span>1</span><?php endif; ?>
                                    </div>
                                    <div class="step-content">
                                        <h6>Registrasi Akun</h6>
                                        <p class="text-muted mb-0">Membuat akun calon mahasiswa</p>
                                        <small class="text-success">
                                            <?php echo !empty($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : '-'; ?>
                                        </small>
                                    </div>
                                </div>

                                <div
                                    class="step <?php echo $has_registered ? ($data_pendaftaran['status'] != 'pending' ? 'completed' : 'active') : ''; ?>">
                                    <div class="step-icon">
                                        <?php if ($has_registered && $data_pendaftaran['status'] != 'pending'): ?><i
                                                class="fas fa-check"></i><?php else: ?><span>2</span><?php endif; ?>
                                    </div>
                                    <div class="step-content">
                                        <h6>Pendaftaran PMB</h6>
                                        <p class="text-muted mb-0">Mengisi formulir pendaftaran</p>
                                        <?php if ($has_registered): ?>
                                            <small
                                                class="text-success"><?php echo date('d M Y', strtotime($data_pendaftaran['tanggal_daftar'])); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div
                                    class="step <?php echo $has_registered && $data_pendaftaran['status'] != 'pending' ? 'completed' : ''; ?>">
                                    <div class="step-icon">
                                        <?php if ($has_registered && $data_pendaftaran['status'] != 'pending'): ?><i
                                                class="fas fa-check"></i><?php else: ?><span>3</span><?php endif; ?>
                                    </div>
                                    <div class="step-content">
                                        <h6>Test Online</h6>
                                        <p class="text-muted mb-0">Mengikuti ujian seleksi</p>
                                        <?php if ($has_registered && $data_pendaftaran['status'] != 'pending'): ?>
                                            <small class="text-success">Selesai</small>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div
                                    class="step <?php echo $has_registered && $data_pendaftaran['status'] == 'lulus' ? 'completed' : ''; ?>">
                                    <div class="step-icon">
                                        <?php if ($has_registered && $data_pendaftaran['status'] == 'lulus'): ?><i
                                                class="fas fa-check"></i><?php else: ?><span>4</span><?php endif; ?>
                                    </div>
                                    <div class="step-content">
                                        <h6>Hasil Test</h6>
                                        <p class="text-muted mb-0">Melihat nilai dan status</p>
                                        <?php if ($has_registered && $data_pendaftaran['status'] == 'lulus'): ?>
                                            <small class="text-success">Lulus</small>
                                        <?php elseif ($has_registered && $data_pendaftaran['status'] == 'tidak_lulus'): ?>
                                            <small class="text-danger">Tidak Lulus</small>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="step <?php echo $has_daftar_ulang ? 'completed' : ''; ?>">
                                    <div class="step-icon">
                                        <?php if ($has_daftar_ulang): ?><i
                                                class="fas fa-check"></i><?php else: ?><span>5</span><?php endif; ?>
                                    </div>
                                    <div class="step-content">
                                        <h6>Daftar Ulang</h6>
                                        <p class="text-muted mb-0">Menyelesaikan administrasi</p>
                                        <?php if ($has_daftar_ulang): ?>
                                            <small class="text-success">Selesai</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div><!-- /steps -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- (Bagian bawah: Quick Actions, Notifications, Recent Activity)
                 Kalau kamu mau, bisa kamu paste dari file lama karena tidak berpengaruh ke header.
                 Tapi sebenarnya sudah aman. -->
        </div>
    </div>
</div>

<style>
    /* Sidebar Styles */
    /* Sidebar */
    .sidebar {
        background: linear-gradient(180deg, #003366 0%, #002244 100%);
        height: 100vh;
        position: sticky;
        top: 0;
    }

    /* Scroll jika menu panjang */
  

    /* Menu */
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const timeEl = document.getElementById('currentTime');
        const printBtn = document.getElementById('printDashboard');

        function updateTime() {
            if (!timeEl) return;
            const now = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            timeEl.textContent = now.toLocaleDateString('id-ID', options);
        }

        updateTime();
        setInterval(updateTime, 60000);

        if (printBtn) {
            printBtn.addEventListener('click', function () {
                window.print();
            });
        }
    });
</script>

<?php include '../includes/footer.php'; ?>