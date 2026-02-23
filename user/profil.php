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

// Check if user has registered
$pendaftaran_query = mysqli_query($conn, "SELECT p.*, j.nama_jurusan FROM pendaftaran p 
                                        LEFT JOIN jurusan j ON p.id_jurusan = j.id_jurusan 
                                        WHERE p.id_calon = $user_id");
$has_registered = mysqli_num_rows($pendaftaran_query) > 0;
if ($has_registered) {
    $data_pendaftaran = mysqli_fetch_assoc($pendaftaran_query);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
        $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
        $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
        $asal_sekolah = mysqli_real_escape_string($conn, $_POST['asal_sekolah']);
        $jurusan_sekolah = mysqli_real_escape_string($conn, $_POST['jurusan_sekolah']);
        $tahun_lulus = mysqli_real_escape_string($conn, $_POST['tahun_lulus']);
        $jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
        $tempat_lahir = mysqli_real_escape_string($conn, $_POST['tempat_lahir']);
        $tanggal_lahir = mysqli_real_escape_string($conn, $_POST['tanggal_lahir']);

        $query = "UPDATE calon_mahasiswa SET 
                  nama_lengkap = '$nama_lengkap',
                  no_hp = '$no_hp',
                  alamat = '$alamat',
                  asal_sekolah = '$asal_sekolah',
                  jurusan_sekolah = '$jurusan_sekolah',
                  tahun_lulus = '$tahun_lulus',
                  jenis_kelamin = '$jenis_kelamin',
                  tempat_lahir = '$tempat_lahir',
                  tanggal_lahir = '$tanggal_lahir'
                  WHERE id_calon = $user_id";

        if (mysqli_query($conn, $query)) {
            $_SESSION['user_nama'] = $nama_lengkap;
            $success = "Profil berhasil diperbarui!";
            // Refresh user data
            $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM calon_mahasiswa WHERE id_calon = $user_id"));
        } else {
            $error = "Gagal memperbarui profil: " . mysqli_error($conn);
        }
    }

    if (isset($_POST['change_password'])) {
        $current_password = md5($_POST['current_password']);
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Check current password
        if ($current_password != $user['password']) {
            $error_password = "Password saat ini salah!";
        } elseif ($new_password != $confirm_password) {
            $error_password = "Password baru tidak cocok!";
        } elseif (strlen($new_password) < 6) {
            $error_password = "Password minimal 6 karakter!";
        } else {
            $new_password_hash = md5($new_password);
            $query = "UPDATE calon_mahasiswa SET password = '$new_password_hash' WHERE id_calon = $user_id";

            if (mysqli_query($conn, $query)) {
                $success_password = "Password berhasil diubah!";
            } else {
                $error_password = "Gagal mengubah password: " . mysqli_error($conn);
            }
        }
    }
}
?>

<?php $hideNavbar = true; ?>
<?php include '../includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar d-md-block">
            <div class="position-sticky pt-4">
                <!-- User Profile -->
                <div class="text-center mb-4 px-3">
                    <div class="mb-3 position-relative">
                        <div class="avatar-container">
                            <i class="fas fa-user-circle fa-5x text-white"></i>
                            <button class="btn btn-sm btn-primary btn-avatar" data-bs-toggle="modal" data-bs-target="#avatarModal" type="button">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                    </div>
                    <h6 class="text-white mb-1"><?php echo htmlspecialchars($user['nama_lengkap']); ?></h6>
                    <small class="text-white-50"><?php echo htmlspecialchars($user['email']); ?></small>
                    <div class="mt-2">
                        <span class="badge bg-info">Calon Mahasiswa</span>
                    </div>
                </div>

                <!-- Navigation Menu -->
                <h6 class="text-white-50 mb-3 px-3">MENU UTAMA</h6>
                <ul class="nav flex-column mb-4 px-2">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="profil.php" class="nav-link active">
                            <i class="fas fa-user-edit me-2"></i>Profil Saya
                        </a>
                    </li>

                    <?php if($has_registered): ?>
                    <li class="nav-item">
                        <a href="pendaftaran.php" class="nav-link">
                            <i class="fas fa-file-contract me-2"></i>Detail Pendaftaran
                        </a>
                    </li>
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
                    <?php if($has_registered && $data_pendaftaran['status'] == 'lulus'): ?>
                    <li class="nav-item">
                        <a href="daftar_ulang.php" class="nav-link">
                            <i class="fas fa-check-double me-2"></i>Daftar Ulang
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>
                </ul>

                <!-- Profile Completion -->
                <div class="card bg-dark border-0 mb-4 mx-3">
                    <div class="card-body p-3">
                        <h6 class="text-white mb-2">Kelengkapan Profil</h6>
                        <div class="progress mb-2" style="height: 8px;">
                            <?php
                            $completion = 20; // Base for having account
                            $completion += !empty($user['no_hp']) ? 10 : 0;
                            $completion += !empty($user['jenis_kelamin']) ? 10 : 0;
                            $completion += !empty($user['tempat_lahir']) ? 10 : 0;
                            $completion += !empty($user['tanggal_lahir']) ? 10 : 0;
                            $completion += !empty($user['alamat']) ? 10 : 0;
                            $completion += !empty($user['asal_sekolah']) ? 10 : 0;
                            $completion += !empty($user['jurusan_sekolah']) ? 10 : 0;
                            $completion += !empty($user['tahun_lulus']) ? 10 : 0;
                            ?>
                            <div class="progress-bar bg-success" style="width: <?php echo $completion; ?>%"></div>
                        </div>
                        <small class="text-white-50"><?php echo $completion; ?>% lengkap</small>
                    </div>
                </div>

                <!-- Logout -->
                <div class="px-3 pb-4">
                    <a href="../logout.php" class="btn btn-sm btn-outline-light w-100">
                        <i class="fas fa-sign-out-alt me-1"></i>Keluar
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-3">

            <!-- Compact Topbar Header (FIX HEADER) -->
            <div class="page-topbar mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="page-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div>
                        <div class="page-title">Profil Saya</div>
                        <div class="page-subtitle">Kelola informasi akun dan data diri Anda</div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border px-3 py-2">
                        <i class="fas fa-user me-1 text-primary"></i>
                        <?php echo htmlspecialchars($user['nama_lengkap']); ?>
                    </span>

                    <a href="dashboard.php" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Dashboard
                    </a>
                </div>
            </div>

            <?php if(isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="alert-heading">Berhasil!</h5>
                            <p class="mb-0"><?php echo $success; ?></p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="fas fa-exclamation-circle fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="alert-heading">Gagal!</h5>
                            <p class="mb-0"><?php echo $error; ?></p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Personal Information Form -->
                <div class="col-lg-8 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-gradient-primary text-white py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-user-circle me-2"></i>Informasi Pribadi
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="" id="profileForm">
                                <div class="row g-3">
                                    <!-- Nama Lengkap -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-user me-1 text-primary"></i>Nama Lengkap
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-user text-muted"></i>
                                            </span>
                                            <input type="text" class="form-control"
                                                   name="nama_lengkap"
                                                   value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>"
                                                   required>
                                        </div>
                                    </div>

                                    <!-- Email (Disabled) -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-envelope me-1 text-primary"></i>Email
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-at text-muted"></i>
                                            </span>
                                            <input type="email" class="form-control"
                                                   value="<?php echo htmlspecialchars($user['email']); ?>"
                                                   disabled>
                                        </div>
                                        <small class="text-muted">Email tidak dapat diubah</small>
                                    </div>

                                    <!-- No. HP -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-phone me-1 text-primary"></i>No. Handphone
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-mobile-alt text-muted"></i>
                                            </span>
                                            <input type="text" class="form-control"
                                                   name="no_hp"
                                                   value="<?php echo htmlspecialchars($user['no_hp']); ?>"
                                                   required>
                                        </div>
                                    </div>

                                    <!-- Jenis Kelamin -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-venus-mars me-1 text-primary"></i>Jenis Kelamin
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-user text-muted"></i>
                                            </span>
                                            <select class="form-select" name="jenis_kelamin">
                                                <option value="">Pilih Jenis Kelamin</option>
                                                <option value="L" <?php echo ($user['jenis_kelamin'] == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                                                <option value="P" <?php echo ($user['jenis_kelamin'] == 'P') ? 'selected' : ''; ?>>Perempuan</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Tempat Lahir -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-map-marker-alt me-1 text-primary"></i>Tempat Lahir
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-city text-muted"></i>
                                            </span>
                                            <input type="text" class="form-control"
                                                   name="tempat_lahir"
                                                   value="<?php echo htmlspecialchars($user['tempat_lahir']); ?>">
                                        </div>
                                    </div>

                                    <!-- Tanggal Lahir -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-calendar-alt me-1 text-primary"></i>Tanggal Lahir
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-birthday-cake text-muted"></i>
                                            </span>
                                            <input type="date" class="form-control"
                                                   name="tanggal_lahir"
                                                   value="<?php echo htmlspecialchars($user['tanggal_lahir']); ?>">
                                        </div>
                                    </div>

                                    <!-- Alamat -->
                                    <div class="col-12">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-home me-1 text-primary"></i>Alamat Lengkap
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light align-items-start pt-3">
                                                <i class="fas fa-map-marked-alt text-muted"></i>
                                            </span>
                                            <textarea class="form-control"
                                                      name="alamat"
                                                      rows="3"><?php echo htmlspecialchars($user['alamat']); ?></textarea>
                                        </div>
                                    </div>

                                    <!-- Asal Sekolah -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-school me-1 text-primary"></i>Asal Sekolah
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-graduation-cap text-muted"></i>
                                            </span>
                                            <input type="text" class="form-control"
                                                   name="asal_sekolah"
                                                   value="<?php echo htmlspecialchars($user['asal_sekolah']); ?>">
                                        </div>
                                    </div>

                                    <!-- Jurusan Sekolah -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-book me-1 text-primary"></i>Jurusan Sekolah
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-book-open text-muted"></i>
                                            </span>
                                            <input type="text" class="form-control"
                                                   name="jurusan_sekolah"
                                                   value="<?php echo htmlspecialchars($user['jurusan_sekolah']); ?>">
                                        </div>
                                    </div>

                                    <!-- Tahun Lulus -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-calendar-check me-1 text-primary"></i>Tahun Lulus
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-graduation-cap text-muted"></i>
                                            </span>
                                            <select class="form-select" name="tahun_lulus">
                                                <option value="">Pilih Tahun</option>
                                                <?php for($year = date('Y'); $year >= 2000; $year--): ?>
                                                <option value="<?php echo $year; ?>"
                                                    <?php echo ($user['tahun_lulus'] == $year) ? 'selected' : ''; ?>>
                                                    <?php echo $year; ?>
                                                </option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Account Created -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-calendar-plus me-1 text-primary"></i>Tanggal Daftar
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-clock text-muted"></i>
                                            </span>
                                            <input type="text" class="form-control"
                                                   value="<?php echo !empty($user['created_at']) ? date('d F Y', strtotime($user['created_at'])) : '-'; ?>"
                                                   disabled>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="col-12 mt-4">
                                        <button type="submit" name="update_profile" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                                        </button>
                                        <button type="reset" class="btn btn-outline-secondary btn-lg ms-2">
                                            <i class="fas fa-redo me-2"></i>Reset Form
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Change Password & Info -->
                <div class="col-lg-4">
                    <!-- Change Password Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-gradient-warning text-white py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-key me-2"></i>Ubah Password
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <?php if(isset($success_password)): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <?php echo $success_password; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <?php if(isset($error_password)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <?php echo $error_password; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="" id="passwordForm">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Password Saat Ini</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control"
                                               name="current_password"
                                               required>
                                        <button class="btn btn-outline-secondary" type="button"
                                                onclick="togglePassword(this)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Password Baru</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control"
                                               name="new_password"
                                               required>
                                        <button class="btn btn-outline-secondary" type="button"
                                                onclick="togglePassword(this)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Minimal 6 karakter</small>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Konfirmasi Password Baru</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control"
                                               name="confirm_password"
                                               required>
                                        <button class="btn btn-outline-secondary" type="button"
                                                onclick="togglePassword(this)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <button type="submit" name="change_password" class="btn btn-warning w-100">
                                    <i class="fas fa-key me-2"></i>Ubah Password
                                </button>
                            </form>

                            <div class="mt-4">
                                <h6 class="fw-bold">Tips Password Aman:</h6>
                                <ul class="list-unstyled small text-muted">
                                    <li><i class="fas fa-check text-success me-1"></i> Gunakan kombinasi huruf besar & kecil</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Tambahkan angka dan simbol</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Minimal 8 karakter</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Jangan gunakan informasi pribadi</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Account Info Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-gradient-info text-white py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Informasi Akun
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">Status Akun:</span>
                                        <span class="badge bg-success">Aktif</span>
                                    </div>
                                </div>

                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">Terdaftar Sejak:</span>
                                        <span><?php echo date('d M Y', strtotime($user['created_at'])); ?></span>
                                    </div>
                                </div>

                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">Terakhir Diperbarui:</span>
                                        <span><?php echo date('d M Y', strtotime($user['updated_at'] ?? $user['created_at'])); ?></span>
                                    </div>
                                </div>

                                <?php if($has_registered): ?>
                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">Status Pendaftaran:</span>
                                        <span class="badge bg-<?php echo $data_pendaftaran['status'] == 'lulus' ? 'success' :
                                                              ($data_pendaftaran['status'] == 'tidak_lulus' ? 'danger' : 'warning'); ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $data_pendaftaran['status'])); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">Program Studi:</span>
                                        <span><?php echo htmlspecialchars($data_pendaftaran['nama_jurusan']); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div><!-- /col -->
            </div><!-- /row -->

        </div><!-- /main -->
    </div><!-- /row -->
</div><!-- /container -->

<!-- Avatar Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-camera me-2"></i>Ubah Foto Profil
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Fitur upload foto profil sedang dalam pengembangan.</p>
                <div class="text-center">
                    <i class="fas fa-user-circle fa-5x text-muted"></i>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(button) {
    const input = button.parentElement.querySelector('input');
    const icon = button.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Form validation
document.getElementById('profileForm').addEventListener('submit', function(e) {
    const nama = this.querySelector('input[name="nama_lengkap"]').value.trim();
    const noHp = this.querySelector('input[name="no_hp"]').value.trim().replace(/\D/g, '');

    if (nama.length < 3) {
        e.preventDefault();
        alert('Nama lengkap minimal 3 karakter');
        return false;
    }

    if (!noHp.match(/^[0-9]{10,13}$/)) {
        e.preventDefault();
        alert('Nomor HP harus 10-13 digit angka');
        return false;
    }

    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    submitBtn.disabled = true;

    return true;
});

document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const newPassword = this.querySelector('input[name="new_password"]').value;
    const confirmPassword = this.querySelector('input[name="confirm_password"]').value;

    if (newPassword.length < 6) {
        e.preventDefault();
        alert('Password baru minimal 6 karakter');
        return false;
    }

    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('Password baru dan konfirmasi tidak cocok');
        return false;
    }

    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengubah...';
    submitBtn.disabled = true;

    return true;
});

// Format phone number (optional formatting)
document.querySelector('input[name="no_hp"]').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length > 13) value = value.substring(0, 13);

    if (value.length > 4) value = value.substring(0, 4) + '-' + value.substring(4);
    if (value.length > 9) value = value.substring(0, 9) + '-' + value.substring(9);

    this.value = value;
});

// Auto-capitalize name
document.querySelector('input[name="nama_lengkap"]').addEventListener('blur', function() {
    let name = this.value.trim();
    name = name.toLowerCase().split(' ').map(word =>
        word.charAt(0).toUpperCase() + word.slice(1)
    ).join(' ');
    this.value = name;
});
</script>

<style>
/* Sidebar Styles */
.sidebar {
    background: linear-gradient(180deg, #003366 0%, #002244 100%);
    min-height: 100vh;
}

.sidebar .position-sticky{
    top: 0;
    height: 100vh;
    overflow-y: auto;
    padding-bottom: 24px;
}

.sidebar .nav-link {
    color: rgba(255,255,255,0.85);
    padding: 12px 16px;
    border-left: 3px solid transparent;
    border-radius: 10px;
    margin: 4px 10px;
    transition: all 0.2s ease;
}

.sidebar .nav-link:hover {
    color: white;
    background-color: rgba(255,255,255,0.1);
    border-left-color: #28a745;
}

.sidebar .nav-link.active {
    color: white;
    background-color: rgba(255,255,255,0.15);
    border-left-color: #28a745;
}

/* Avatar Styles */
.avatar-container {
    position: relative;
    display: inline-block;
}

.btn-avatar {
    position: absolute;
    bottom: 0;
    right: 0;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Compact Topbar Header */
.page-topbar{
    background: #fff;
    border: 1px solid rgba(0,0,0,.06);
    border-radius: 14px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    box-shadow: 0 10px 26px rgba(16,24,40,.06);
}
.page-icon{
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: rgba(13,110,253,.10);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0d6efd;
    font-size: 18px;
}
.page-title{
    font-size: 18px;
    font-weight: 800;
    line-height: 1.1;
}
.page-subtitle{
    font-size: 13px;
    color: #6c757d;
    margin-top: 2px;
}
@media (max-width: 768px){
    .page-topbar{
        flex-direction: column;
        align-items: flex-start;
    }
}

/* Card header gradients */
.bg-gradient-primary { background: linear-gradient(135deg, #003366 0%, #00509e 100%); }
.bg-gradient-warning { background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); }
.bg-gradient-info { background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%); }

/* Form focus */
.form-control:focus, .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar { min-height: auto; margin-bottom: 20px; }
    .btn-lg { padding: 0.5rem 1rem; font-size: 1rem; }
}
</style>

<?php include '../includes/footer.php'; ?>
