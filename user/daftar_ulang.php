<?php
require_once '../config/database.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if user has passed the test
$query = "SELECT p.*, j.nama_jurusan, 
          (SELECT COUNT(*) FROM daftar_ulang du WHERE du.id_pendaftaran = p.id_pendaftaran) as sudah_daftar_ulang
          FROM pendaftaran p
          JOIN jurusan j ON p.id_jurusan = j.id_jurusan
          WHERE p.id_calon = $user_id AND p.status = 'lulus'";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: hasil.php");
    exit();
}

$data = mysqli_fetch_assoc($result);

// Check if already registered for daftar ulang
if ($data['sudah_daftar_ulang'] > 0) {
    header("Location: hasil.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle file upload
    $upload_ok = true;
    $bukti_pembayaran = '';
    
    if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
        $target_dir = "../assets/uploads/bukti/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_name = time() . '_' . basename($_FILES['bukti_pembayaran']['name']);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES['bukti_pembayaran']['tmp_name']);
        if ($check === false) {
            $error = "File bukan gambar.";
            $upload_ok = false;
        }
        
        // Check file size (max 2MB)
        if ($_FILES['bukti_pembayaran']['size'] > 2000000) {
            $error = "Ukuran file maksimal 2MB.";
            $upload_ok = false;
        }
        
        // Allow certain file formats
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        if (!in_array($imageFileType, $allowed_types)) {
            $error = "Hanya format JPG, JPEG, PNG & GIF yang diizinkan.";
            $upload_ok = false;
        }
        
        if ($upload_ok) {
            if (move_uploaded_file($_FILES['bukti_pembayaran']['tmp_name'], $target_file)) {
                $bukti_pembayaran = $file_name;
            } else {
                $error = "Terjadi kesalahan saat upload file.";
                $upload_ok = false;
            }
        }
    } else {
        $error = "Silakan upload bukti pembayaran.";
        $upload_ok = false;
    }
    
    if ($upload_ok) {
        // Generate NIM (will be done by admin, but we insert with empty NIM first)
        $query = "INSERT INTO daftar_ulang (id_pendaftaran, tanggal_daftar_ulang, bukti_pembayaran, status_pembayaran) 
                  VALUES ({$data['id_pendaftaran']}, CURDATE(), '$bukti_pembayaran', 'belum')";
        
        if (mysqli_query($conn, $query)) {
            $success = "Daftar ulang berhasil! Tunggu verifikasi dari admin untuk mendapatkan NIM.";
            header("refresh:3;url=hasil.php");
        } else {
            $error = "Gagal menyimpan data: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Ulang - Arten Campus</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .badge {
            padding: 8px 12px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-check-double me-2"></i>Form Daftar Ulang</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($success)): ?>
                            <div class="alert alert-success">
                                <h5><i class="fas fa-check-circle me-2"></i>Berhasil!</h5>
                                <p><?php echo $success; ?></p>
                                <p>Anda akan dialihkan ke halaman hasil dalam 3 detik...</p>
                            </div>
                        <?php else: ?>
                        
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <!-- Information Card -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Peserta</h5>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <strong>Nama:</strong><br>
                                        <?php echo $_SESSION['user_nama']; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>No. Test:</strong><br>
                                        <?php echo $data['no_test']; ?>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <strong>Jurusan:</strong><br>
                                        <?php echo $data['nama_jurusan']; ?>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <strong>Nilai Test:</strong><br>
                                        <span class="badge bg-success fs-6"><?php echo number_format($data['nilai_test'], 2); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Information -->
                        <div class="alert alert-info mb-4">
                            <h5><i class="fas fa-credit-card me-2"></i>Informasi Pembayaran</h5>
                            <p>Biaya daftar ulang: <strong>Rp 1.500.000</strong></p>
                            <p>Transfer ke:<br>
                               <strong>Bank BCA</strong><br>
                               No. Rekening: <strong>123-456-7890</strong><br>
                               Atas Nama: <strong>UNIVERSITAS TEKNOLOGI NUSANTARA</strong>
                            </p>
                            <p class="mb-0"><strong>Catatan:</strong> Upload bukti transfer yang jelas</p>
                        </div>
                        
                        <!-- Registration Form -->
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-4">
                                <h5>Form Daftar Ulang</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label">Upload Bukti Pembayaran <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" name="bukti_pembayaran" accept="image/*" required>
                                    <small class="text-muted">Format: JPG, PNG, GIF (Maks. 2MB)</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Keterangan (Opsional)</label>
                                    <textarea class="form-control" name="keterangan" rows="3" 
                                              placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                                </div>
                            </div>
                            
                            <!-- Agreement -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="agree1" required>
                                    <label class="form-check-label" for="agree1">
                                        Saya telah mentransfer biaya daftar ulang sesuai dengan nominal yang ditentukan
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="agree2" required>
                                    <label class="form-check-label" for="agree2">
                                        Saya bersedia mengikuti seluruh kegiatan akademik di Universitas Teknologi Nusantara
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="agree3" required>
                                    <label class="form-check-label" for="agree3">
                                        Saya menyetujui semua peraturan dan tata tertib yang berlaku
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Daftar Ulang
                                </button>
                                <a href="hasil.php" class="btn btn-outline-secondary">Kembali ke Hasil</a>
                            </div>
                        </form>
                        
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Important Notes -->
                <div class="card mt-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Catatan Penting</h5>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>Daftar ulang hanya dapat dilakukan oleh peserta yang LULUS seleksi</li>
                            <li>NIM akan diberikan setelah admin memverifikasi bukti pembayaran</li>
                            <li>Proses verifikasi membutuhkan waktu 1-3 hari kerja</li>
                            <li>Setelah daftar ulang, Anda resmi menjadi mahasiswa UTN</li>
                            <li>Informasi perkuliahan akan dikirimkan via email setelah proses selesai</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include '../includes/footer.php'; ?>