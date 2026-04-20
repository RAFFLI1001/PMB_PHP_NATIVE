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
    
    $query = "INSERT INTO pendaftaran (id_calon, id_jurusan, tanggal_daftar, status) 
              VALUES ($user_id, $id_jurusan, CURDATE(), 'pending')";
    
    if (mysqli_query($conn, $query)) {
        $success = "Pendaftaran berhasil! Silakan lanjut ke halaman test online.";
        header("refresh:3;url=dashboard.php");
    } else {
        $error = "Gagal mendaftar: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran PMB | Arten Campus</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #198754;
            --primary-dark: #0f5c3e;
            --primary-light: #d1e7dd;
            --secondary: #6c757d;
            --accent: #0d6efd;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        /* Header Styles */
        .page-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-radius: 20px;
            padding: 40px 35px;
            margin-bottom: 30px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            pointer-events: none;
        }
        
        .page-header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            pointer-events: none;
        }
        
        .page-header h3 {
            font-weight: 700;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 1;
        }
        
        .page-header p {
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }
        
        /* Card Styles */
        .main-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .card-body-custom {
            padding: 40px;
        }
        
        /* Info Box */
        .info-box {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            border-radius: 16px;
            padding: 25px;
            border-left: 5px solid var(--accent);
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .info-box h6 {
            color: var(--accent);
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .info-box ul {
            margin-bottom: 0;
            padding-left: 20px;
        }
        
        .info-box li {
            margin-bottom: 8px;
            color: #495057;
        }
        
        /* Jurusan Card Styles */
        .jurusan-card {
            border-radius: 16px;
            border: 2px solid #e9ecef;
            padding: 20px;
            transition: all 0.3s ease;
            cursor: pointer;
            background: white;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .jurusan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.12);
            border-color: var(--primary);
        }
        
        .jurusan-card.selected {
            border-color: var(--primary);
            background: linear-gradient(135deg, #ffffff, #f8fff9);
            box-shadow: 0 8px 20px rgba(25,135,84,0.15);
        }
        
        .form-check-input {
            width: 1.3em;
            height: 1.3em;
            margin-top: 0.15em;
            cursor: pointer;
        }
        
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(25,135,84,0.2);
        }
        
        .jurusan-card h6 {
            font-weight: 700;
            color: #212529;
            margin-bottom: 8px;
        }
        
        .badge-jurusan {
            background: var(--primary-light);
            color: var(--primary);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            margin-top: 8px;
        }
        
        /* Button Styles */
        .btn-daftar {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            padding: 14px;
            font-size: 16px;
            font-weight: 700;
            border-radius: 12px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-daftar:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(25,135,84,0.3);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
        }
        
        .btn-outline-secondary {
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-outline-secondary:hover {
            transform: translateY(-2px);
        }
        
        /* Checkbox Styles */
        .form-check-input[type="checkbox"] {
            width: 1.2em;
            height: 1.2em;
            cursor: pointer;
        }
        
        .form-check-label {
            cursor: pointer;
            color: #495057;
            font-weight: 500;
        }
        
        /* Alert Styles */
        .alert-success {
            border-radius: 16px;
            border-left: 5px solid #198754;
            background: linear-gradient(135deg, #d1e7dd, #ffffff);
        }
        
        .alert-danger {
            border-radius: 16px;
            border-left: 5px solid #dc3545;
        }
        
        /* Divider */
        .custom-divider {
            margin: 30px 0;
            border: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #dee2e6, transparent);
        }
        
        @media (max-width: 768px) {
            .page-header {
                padding: 25px 20px;
            }
            
            .card-body-custom {
                padding: 25px;
            }
            
            .jurusan-card {
                padding: 15px;
            }
            
            .info-box {
                padding: 20px;
            }
        }
        
        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .main-card {
            animation: fadeInUp 0.6s ease;
        }
        
        /* Loading effect */
        .btn-loading {
            pointer-events: none;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="main-card">
            <div class="card-body-custom">
                
                <div class="page-header">
                    <h3 class="mb-2">
                        <i class="fas fa-graduation-cap me-2"></i>Pendaftaran Mahasiswa Baru
                    </h3>
                    <p class="mb-0">Bergabunglah dengan keluarga besar Arten Campus</p>
                </div>
                
                <?php if(isset($success)): ?>
                    <div class="alert alert-success">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle fa-2x me-3"></i>
                            <div>
                                <h5 class="mb-1">Pendaftaran Berhasil! 🎉</h5>
                                <p class="mb-0"><?php echo $success; ?></p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="info-box">
                        <h6>
                            <i class="fas fa-info-circle me-2"></i>
                            Informasi Penting
                        </h6>
                        <ul>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Pastikan data profil Anda sudah lengkap</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Pilih jurusan dengan teliti</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Nomor test sudah Anda dapatkan saat registrasi</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Ujian bisa langsung dilakukan setelah daftar</li>
                        </ul>
                    </div>
                    
                    <form method="POST" action="" id="registrationForm" onsubmit="return confirmSubmit(event)">
                        
                        <h5 class="mb-3 fw-bold">
                            <i class="fas fa-book-open me-2 text-primary"></i>
                            Pilih Program Studi
                        </h5>
                        
                        <div class="row g-3 mb-4">
                            <?php while($jurusan = mysqli_fetch_assoc($jurusan_result)): ?>
                                <div class="col-md-6">
                                    <label class="jurusan-card w-100" onclick="selectJurusan(this, <?php echo $jurusan['id_jurusan']; ?>)">
                                        <div class="form-check">
                                            <input class="form-check-input jurusan-radio"
                                                   type="radio"
                                                   name="id_jurusan"
                                                   value="<?php echo $jurusan['id_jurusan']; ?>"
                                                   id="jurusan_<?php echo $jurusan['id_jurusan']; ?>"
                                                   required>
                                            <div class="ms-2">
                                                <h6 class="mb-1">
                                                    <?php echo htmlspecialchars($jurusan['nama_jurusan']); ?>
                                                </h6>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <small class="text-muted">
                                                            <i class="fas fa-tag me-1"></i>
                                                            Kode: <?php echo $jurusan['kode_jurusan']; ?>
                                                        </small>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fas fa-users me-1"></i>
                                                            Kuota: <?php echo $jurusan['kuota']; ?> Mahasiswa
                                                        </small>
                                                    </div>
                                                    <span class="badge-jurusan">
                                                        <i class="fas fa-star me-1"></i>Favorit
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        
                        <hr class="custom-divider">
                        
                        <h5 class="mb-3 fw-bold">
                            <i class="fas fa-clipboard-list me-2 text-primary"></i>
                            Konfirmasi Pendaftaran
                        </h5>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="confirmData" required>
                            <label class="form-check-label" for="confirmData">
                                Data yang saya isi sudah benar dan lengkap
                            </label>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="confirmSeleksi" required>
                            <label class="form-check-label" for="confirmSeleksi">
                                Saya bersedia mengikuti proses seleksi dengan jujur
                            </label>
                        </div>
                        
                        <div class="d-grid gap-3">
                            <button type="submit" class="btn btn-daftar" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>
                                Daftar Sekarang
                            </button>
                            
                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Kembali ke Dashboard
                            </a>
                        </div>
                        
                    </form>
                    
                <?php endif; ?>
                
            </div>
        </div>
    </div>
    
    <script>
        // Fungsi untuk menandai card yang dipilih
        function selectJurusan(cardElement, jurusanId) {
            // Remove selected class from all cards
            document.querySelectorAll('.jurusan-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            cardElement.classList.add('selected');
            
            // Check the radio button
            const radio = cardElement.querySelector('.jurusan-radio');
            if (radio) {
                radio.checked = true;
            }
        }
        
        // Fungsi konfirmasi submit dengan validasi
        function confirmSubmit(event) {
            event.preventDefault();
            
            // Validasi pilihan jurusan
            const selectedJurusan = document.querySelector('input[name="id_jurusan"]:checked');
            if (!selectedJurusan) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Jurusan',
                    text: 'Silakan pilih program studi terlebih dahulu!',
                    confirmButtonColor: '#198754'
                });
                return false;
            }
            
            // Validasi checkbox konfirmasi
            const confirmData = document.getElementById('confirmData');
            const confirmSeleksi = document.getElementById('confirmSeleksi');
            
            if (!confirmData.checked || !confirmSeleksi.checked) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Konfirmasi Diperlukan',
                    text: 'Silakan centang kedua kotak konfirmasi untuk melanjutkan!',
                    confirmButtonColor: '#198754'
                });
                return false;
            }
            
            // Tampilkan konfirmasi dengan SweetAlert
            Swal.fire({
                title: 'Konfirmasi Pendaftaran',
                text: 'Apakah Anda yakin ingin mendaftar di jurusan ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Ya, Daftar!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form
                    const submitBtn = document.getElementById('submitBtn');
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
                    submitBtn.classList.add('btn-loading');
                    document.getElementById('registrationForm').submit();
                }
            });
            
            return false;
        }
        
        // Auto-highlight card when radio is clicked directly
        document.querySelectorAll('.jurusan-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                const card = this.closest('.jurusan-card');
                if (card && this.checked) {
                    document.querySelectorAll('.jurusan-card').forEach(c => c.classList.remove('selected'));
                    card.classList.add('selected');
                }
            });
        });
        
        const style = document.createElement('style');
        style.textContent = `
            .jurusan-card {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .btn-daftar:active {
                transform: translateY(0);
            }
            
            .form-check-input:focus {
                box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
            }
        `;
        document.head.appendChild(style);
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
</body>
</html>