<?php
require_once '../config/database.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT p.*, j.nama_jurusan, du.status_verifikasi,
          (SELECT COUNT(*) FROM daftar_ulang du2 WHERE du2.id_pendaftaran = p.id_pendaftaran) as sudah_daftar_ulang
          FROM pendaftaran p
          JOIN jurusan j ON p.id_jurusan = j.id_jurusan
          LEFT JOIN daftar_ulang du ON du.id_pendaftaran = p.id_pendaftaran
          WHERE p.id_calon = $user_id AND p.status = 'lulus'";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: hasil.php");
    exit();
}

$data = mysqli_fetch_assoc($result);

if ($data['sudah_daftar_ulang'] > 0) {
    header("Location: hasil.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

$upload_ok = true;
$target_dir = "../assets/uploads/daftar_ulang/";

if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

function uploadFile($name, $dir){
    if($_FILES[$name]['error'] != 0) return false;

    $ext = strtolower(pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','pdf'];

    if(!in_array($ext,$allowed)) return false;
    if($_FILES[$name]['size'] > 5000000) return false;

    $filename = time().'_'.$_FILES[$name]['name'];

    if(move_uploaded_file($_FILES[$name]['tmp_name'],$dir.$filename)){
        return $filename;
    }

    return false;
}

$bukti = uploadFile('bukti_pembayaran',$target_dir);
$ktp   = uploadFile('upload_ktp',$target_dir);
$kk    = uploadFile('upload_kk',$target_dir);

if(!$bukti || !$ktp || !$kk){
    $error = "Semua file wajib diupload (JPG / PNG / PDF max 5MB)";
}else{

    // ===== GENERATE NIM =====
    $tahun = date('y');
    $kode_jurusan = str_pad($data['id_jurusan'],2,'0',STR_PAD_LEFT);

    $q = mysqli_query($conn,"
        SELECT COUNT(*) as total 
        FROM daftar_ulang du
        JOIN pendaftaran p ON du.id_pendaftaran = p.id_pendaftaran
        WHERE p.id_jurusan = {$data['id_jurusan']}
    ");

    $row = mysqli_fetch_assoc($q);
    $nomor = str_pad($row['total']+1,4,'0',STR_PAD_LEFT);

    $nim = $tahun.$kode_jurusan.$nomor;

    // ===== INSERT DATA =====
    $insert = mysqli_query($conn,"
        INSERT INTO daftar_ulang(
            id_pendaftaran,
            tanggal_daftar_ulang,
            bukti_pembayaran,
            upload_ktp,
            upload_kk,
            status_pembayaran,
            status_verifikasi
        ) VALUES(
            {$data['id_pendaftaran']},
            CURDATE(),
            '$bukti',
            '$ktp',
            '$kk',
            'lunas',
            'menunggu'
        )
    ");

    if($insert){

        mysqli_query($conn,"
            UPDATE calon_mahasiswa 
            SET nim='$nim'
            WHERE id_calon=$user_id
        ");

        $success = "Daftar ulang berhasil! NIM Anda: <b>$nim</b>";
        header("refresh:3;url=hasil.php");

    }else{
        $error = "Gagal menyimpan data!";
    }

}

}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Daftar Ulang - Arten Campus | Verifikasi Kelulusan</title>
    <!-- Bootstrap 5 + Icons + Modern Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts: Poppins & Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(145deg, #f0f5fe 0%, #e9f0fa 100%);
            font-family: 'Inter', sans-serif;
            padding: 40px 0 60px 0;
            min-height: 100vh;
        }

        .modern-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 32px;
            border: none;
            box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
        }

        .modern-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 28px 40px -16px rgba(0, 0, 0, 0.12);
        }

        .card-header-gradient {
            background: linear-gradient(135deg, #0B2B40 0%, #1A4A5F 100%);
            padding: 1.4rem 2rem;
            border-bottom: none;
        }

        .badge-modern {
            background: rgba(255,255,240,0.15);
            backdrop-filter: blur(4px);
            padding: 8px 16px;
            border-radius: 60px;
            font-weight: 500;
            font-size: 0.85rem;
            letter-spacing: 0.3px;
        }

        .info-grid {
            background: #F9FBFE;
            border-radius: 28px;
            padding: 1.5rem;
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 600;
            color: #5b6e8c;
            margin-bottom: 6px;
        }

        .info-value {
            font-weight: 700;
            font-size: 1.2rem;
            color: #0B2B40;
            word-break: break-word;
        }

        .badge-score {
            background: #0B2B40;
            color: white;
            padding: 8px 16px;
            border-radius: 60px;
            font-weight: 600;
            font-size: 1rem;
            display: inline-block;
        }

        .payment-alert {
            background: linear-gradient(120deg, #EFF9FF 0%, #E6F3FC 100%);
            border-left: 5px solid #1E88E5;
            border-radius: 24px;
            padding: 1.4rem 1.8rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }

        .bank-details {
            background: white;
            border-radius: 20px;
            padding: 0.8rem 1.2rem;
            font-family: monospace;
            font-weight: 600;
            border: 1px solid #e2edf2;
            display: inline-block;
        }

        .upload-area {
            border: 2px dashed #cbdbe0;
            border-radius: 24px;
            padding: 0.8rem 1rem;
            transition: all 0.2s;
            background: #ffffff;
        }

        .upload-area:hover {
            border-color: #1A4A5F;
            background: #FCFDFF;
        }

        .form-check-modern .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            margin-top: 0.1rem;
            border: 2px solid #bdc5d5;
            cursor: pointer;
        }

        .form-check-modern .form-check-input:checked {
            background-color: #1A4A5F;
            border-color: #1A4A5F;
        }

        .btn-modern-primary {
            background: linear-gradient(105deg, #0F3B4C 0%, #1C5D74 100%);
            border: none;
            padding: 14px 20px;
            border-radius: 60px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.2s;
            box-shadow: 0 6px 14px rgba(27, 85, 106, 0.2);
        }

        .btn-modern-primary:hover {
            background: linear-gradient(105deg, #0B2B40 0%, #144e62 100%);
            transform: scale(1.01);
            box-shadow: 0 10px 20px rgba(27, 85, 106, 0.25);
        }

        .btn-outline-modern {
            border-radius: 60px;
            padding: 12px 20px;
            font-weight: 500;
            border: 1.5px solid #cbdbe0;
            color: #2c3e4e;
        }

        .btn-outline-modern:hover {
            background: #F4F9FE;
            border-color: #1A4A5F;
            color: #1A4A5F;
        }

        .note-card {
            background: #FFFBF0;
            border-radius: 28px;
            border: 1px solid #FFE6B3;
        }

        .note-icon {
            background: #FFE3A4;
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 30px;
            color: #9b6f0b;
        }

        hr.modern-hr {
            background: linear-gradient(90deg, transparent, #cbdbe0, transparent);
            height: 1px;
            margin: 1rem 0;
        }

        @media (max-width: 768px) {
            body {
                padding: 20px 12px;
            }
            .info-value {
                font-size: 1rem;
            }
            .card-header-gradient h4 {
                font-size: 1.3rem;
            }
        }

        .file-hint {
            font-size: 0.7rem;
            color: #6f7c91;
            margin-top: 6px;
        }
        
        .required-star {
            color: #dc3545;
            margin-left: 3px;
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<div class="container py-2">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-11 col-12">
            <div class="modern-card fade-in">
                <div class="card-header-gradient text-white">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h4 class="mb-0 fw-bold" style="font-family: 'Poppins', sans-serif;">
                                <i class="fas fa-graduation-cap me-2"></i> Pendaftaran Ulang
                            </h4>
                            <p class="mb-0 mt-1 opacity-75 small">Konfirmasi kelulusan & verifikasi berkas</p>
                        </div>
                        <div class="badge-modern">
                            <i class="fas fa-check-circle me-1"></i> Lulus Seleksi
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-xl-5">
                    <?php if(isset($data['status_verifikasi'])): ?>

    <?php if($data['status_verifikasi'] == 'menunggu'): ?>
        <div class="alert alert-warning border-0 rounded-4 shadow-sm mb-4">
            <i class="fas fa-clock me-2"></i>
            Bukti pembayaran sudah diupload. <b>Menunggu verifikasi admin.</b>
        </div>

    <?php elseif($data['status_verifikasi'] == 'disetujui'): ?>
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i>
            Pembayaran sudah diverifikasi admin.
        </div>

    <?php elseif($data['status_verifikasi'] == 'ditolak'): ?>
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
            <i class="fas fa-times-circle me-2"></i>
            Bukti pembayaran ditolak, silakan upload ulang.
        </div>
    <?php endif; ?>

<?php endif; ?>
                    <?php if(isset($success)): ?>
                        <div class="alert alert-success border-0 rounded-4 shadow-sm d-flex align-items-center" role="alert">
                            <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
                            <div>
                                <h5 class="alert-heading fw-bold mb-1">Pendaftaran Berhasil!</h5>
                                <p class="mb-0"><?php echo htmlspecialchars($success); ?></p>
                                <hr class="my-2">
                                <p class="mb-0 small">⏳ Mengalihkan ke halaman hasil dalam 3 detik...</p>
                            </div>
                        </div>
                    <?php else: ?>
                    
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger border-0 rounded-4 shadow-sm d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle fa-lg me-3"></i>
                                <div><?php echo htmlspecialchars($error); ?></div>
                            </div>
                        <?php endif; ?>

                        <!-- Info peserta dengan desain modern -->
                        <div class="info-grid mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="info-label"><i class="far fa-user-circle me-1"></i> Nama Lengkap</div>
                                    <div class="info-value"><?php echo htmlspecialchars($_SESSION['user_nama'] ?? 'Tidak tersedia'); ?></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label"><i class="fas fa-qrcode me-1"></i> No. Test</div>
                                    <div class="info-value"><?php echo htmlspecialchars($data['no_test'] ?? '-'); ?></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label"><i class="fas fa-book-open me-1"></i> Jurusan</div>
                                    <div class="info-value"><?php echo htmlspecialchars($data['nama_jurusan'] ?? '-'); ?></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label"><i class="fas fa-chart-line me-1"></i> Nilai Test</div>
                                    <div class="info-value">
                                        <span class="badge-score">
                                            <i class="fas fa-star me-1"></i> <?php echo isset($data['nilai_test']) ? number_format($data['nilai_test'], 2) : '-'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment info elegant -->
                        <div class="payment-alert mb-4 d-flex flex-wrap align-items-start gap-3">
                            <div class="bg-white rounded-circle p-3 shadow-sm" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-money-bill-wave fa-2x text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-1" style="color:#0B2B40;"><i class="fas fa-credit-card me-1"></i> Informasi Pembayaran</h5>
                                <p class="mb-2 fw-semibold">Biaya daftar ulang: <span class="fs-5 fw-bold text-dark">Rp 1.500.000</span></p>
                                <div class="bank-details mt-2">
                                    <i class="fas fa-university me-2 text-secondary"></i> Bank BCA &nbsp;|&nbsp; 
                                    <span class="fw-bold">123-456-7890</span><br>
                                    <small>a.n. <strong>Universitas Arten</strong></small>
                                </div>
                                <p class="mt-2 small text-muted mb-0"><i class="fas fa-info-circle"></i> Upload bukti transfer dengan data yang jelas (nama / no.rek)</p>
                            </div>
                        </div>

                        <!-- Form Daftar Ulang -->
                        <form method="POST" action="" enctype="multipart/form-data">
                            <h5 class="fw-bold mb-3" style="color:#0F3B4C;"><i class="fas fa-cloud-upload-alt me-2"></i>Upload Dokumen Persyaratan</h5>
                            
                            <div class="row g-4">
                                <!-- Bukti Pembayaran -->
                                <div class="col-md-12">
                                    <div class="upload-area">
                                        <label class="form-label fw-semibold">Bukti Pembayaran <span class="required-star">*</span></label>
                                        <input type="file" class="form-control border-0 bg-transparent ps-0" name="bukti_pembayaran" accept="image/*,.pdf" required style="box-shadow: none;">
                                        <div class="file-hint"><i class="fas fa-image me-1"></i> Format JPG, PNG, PDF (Maks. 5MB)</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="upload-area">
                                        <label class="form-label fw-semibold">KTP / Kartu Pelajar <span class="required-star">*</span></label>
                                        <input type="file" class="form-control border-0 bg-transparent ps-0" name="upload_ktp" accept="image/*,.pdf" required style="box-shadow: none;">
                                        <div class="file-hint"><i class="fas fa-id-card"></i> JPG, PNG, PDF (Maks. 5MB)</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="upload-area">
                                        <label class="form-label fw-semibold">Kartu Keluarga (KK) <span class="required-star">*</span></label>
                                        <input type="file" class="form-control border-0 bg-transparent ps-0" name="upload_kk" accept="image/*,.pdf" required style="box-shadow: none;">
                                        <div class="file-hint"><i class="fas fa-users"></i> JPG, PNG, PDF (Maks. 5MB)</div>
                                    </div>
                                </div>
                            </div>

                            <hr class="modern-hr my-4">

                            <!-- Agreement checklist modern -->
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3"><i class="fas fa-hand-peace me-2"></i>Persetujuan Pendaftaran Ulang</h6>
                                <div class="form-check-modern d-flex gap-2 mb-2">
                                    <input class="form-check-input mt-1" type="checkbox" id="agree1" required>
                                    <label class="form-check-label" for="agree1">
                                        Saya telah mentransfer biaya daftar ulang sesuai dengan nominal yang ditentukan
                                    </label>
                                </div>
                                <div class="form-check-modern d-flex gap-2 mb-2">
                                    <input class="form-check-input mt-1" type="checkbox" id="agree2" required>
                                    <label class="form-check-label" for="agree2">
                                        Saya bersedia mengikuti seluruh kegiatan akademik di Universitas Arten
                                    </label>
                                </div>
                                <div class="form-check-modern d-flex gap-2 mb-2">
                                    <input class="form-check-input mt-1" type="checkbox" id="agree3" required>
                                    <label class="form-check-label" for="agree3">
                                        Saya menyetujui semua peraturan dan tata tertib yang berlaku
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-sm-row gap-3 mt-4">
                                <button type="submit" class="btn btn-modern-primary text-white px-4 py-3 flex-grow-1">
                                    <i class="fas fa-paper-plane me-2"></i> Submit Daftar Ulang
                                </button>
                                <a href="hasil.php" class="btn btn-outline-modern text-center px-4 py-3">
                                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Hasil
                                </a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Notes card modern -->
            <div class="modern-card note-card mt-4">
                <div class="card-body p-4">
                    <div class="d-flex gap-3 align-items-start">
                        <div class="note-icon">
                            <i class="fas fa-clipboard-list fa-fw"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-2" style="color:#7a6300;"><i class="fas fa-exclamation-triangle me-2"></i>Catatan Penting</h5>
                            <ul class="mb-0 ps-3 small" style="color: #4e3e0c;">
                                <li class="mb-1">Daftar ulang hanya dapat dilakukan oleh peserta yang <strong class="text-dark">LULUS seleksi</strong></li>
                                <li class="mb-1">NIM akan diberikan setelah admin memverifikasi bukti pembayaran (1-3 hari kerja)</li>
                                <li class="mb-1">Setelah verifikasi, Anda resmi menjadi mahasiswa UTN dan akan mendapatkan informasi perkuliahan via email</li>
                                <li class="mb-0">Pastikan file yang diupload jelas dan tidak melebihi batas ukuran 5MB</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>