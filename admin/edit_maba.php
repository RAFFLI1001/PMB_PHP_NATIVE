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
    
    // Check if email already exists (other than current user)
    $check_email = mysqli_query($conn, "SELECT id_calon FROM calon_mahasiswa WHERE email = '$email' AND id_calon != $id");
    
    if (mysqli_num_rows($check_email) > 0) {
        $error = "Email sudah digunakan oleh user lain!";
    } else {
        // Update data
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
            // Refresh user data
            $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM calon_mahasiswa WHERE id_calon = $id"));
        } else {
            $error = "Gagal memperbarui data: " . mysqli_error($conn);
        }
    }
}
?>
<?php include '../includes/header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Data Calon Mahasiswa</h2>
        <a href="data_maba.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
    
    <?php if(isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_lengkap" 
                               value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" 
                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">No. HP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="no_hp" 
                               value="<?php echo htmlspecialchars($user['no_hp']); ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select class="form-control" name="jenis_kelamin">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="L" <?php echo $user['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                            <option value="P" <?php echo $user['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text" class="form-control" name="tempat_lahir" 
                               value="<?php echo htmlspecialchars($user['tempat_lahir']); ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" class="form-control" name="tanggal_lahir" 
                               value="<?php echo $user['tanggal_lahir']; ?>">
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="3"><?php echo htmlspecialchars($user['alamat']); ?></textarea>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Asal Sekolah</label>
                        <input type="text" class="form-control" name="asal_sekolah" 
                               value="<?php echo htmlspecialchars($user['asal_sekolah']); ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jurusan Sekolah</label>
                        <input type="text" class="form-control" name="jurusan_sekolah" 
                               value="<?php echo htmlspecialchars($user['jurusan_sekolah']); ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun Lulus</label>
                        <select class="form-control" name="tahun_lulus">
                            <option value="">Pilih Tahun</option>
                            <?php for($year = date('Y'); $year >= 2010; $year--): ?>
                            <option value="<?php echo $year; ?>" 
                                    <?php echo $user['tahun_lulus'] == $year ? 'selected' : ''; ?>>
                                <?php echo $year; ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Simpan Perubahan
                        </button>
                        <a href="data_maba.php" class="btn btn-secondary">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- User Info Card -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Informasi Pendaftaran</h5>
        </div>
        <div class="card-body">
            <?php
            // Check if user has registered
            $pendaftaran_query = mysqli_query($conn, "SELECT p.*, j.nama_jurusan 
                                                    FROM pendaftaran p
                                                    JOIN jurusan j ON p.id_jurusan = j.id_jurusan
                                                    WHERE p.id_calon = $id");
            
            if (mysqli_num_rows($pendaftaran_query) > 0):
                $pendaftaran = mysqli_fetch_assoc($pendaftaran_query);
            ?>
            <div class="row">
                <div class="col-md-4">
                    <strong>No. Test:</strong><br>
                    <span class="badge bg-primary fs-6"><?php echo $pendaftaran['no_test']; ?></span>
                </div>
                <div class="col-md-4">
                    <strong>Jurusan:</strong><br>
                    <?php echo $pendaftaran['nama_jurusan']; ?>
                </div>
                <div class="col-md-4">
                    <strong>Status:</strong><br>
                    <?php 
                    $status = $pendaftaran['status'];
                    $badge_color = $status == 'lulus' ? 'success' : 
                                  ($status == 'tidak_lulus' ? 'danger' : 'warning');
                    ?>
                    <span class="badge bg-<?php echo $badge_color; ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $status)); ?>
                    </span>
                </div>
            </div>
            
            <?php if($pendaftaran['nilai_test']): ?>
            <div class="row mt-3">
                <div class="col-md-6">
                    <strong>Nilai Test:</strong><br>
                    <span class="fs-4"><?php echo number_format($pendaftaran['nilai_test'], 2); ?></span>
                </div>
                <div class="col-md-6">
                    <strong>Tanggal Daftar:</strong><br>
                    <?php echo date('d F Y', strtotime($pendaftaran['tanggal_daftar'])); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                User ini belum melakukan pendaftaran PMB.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>