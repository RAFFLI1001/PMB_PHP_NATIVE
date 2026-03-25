<?php
session_start();
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp'] ?? '');
    $password_raw = $_POST['password'] ?? '';
    $asal_sekolah = mysqli_real_escape_string($conn, $_POST['asal_sekolah'] ?? '');

    // Validasi sederhana
    if (strlen(trim($nama)) < 3) {
        $error = "Nama minimal 3 karakter.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (strlen($password_raw) < 6) {
        $error = "Password minimal 6 karakter.";
    } elseif (empty($no_hp)) {
        $error = "Nomor handphone wajib diisi.";
    } else {
        $password = md5($password_raw);

        // Check if email exists
        $check = mysqli_query($conn, "SELECT id_calon FROM calon_mahasiswa WHERE email='$email'");
        if ($check && mysqli_num_rows($check) > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            $query = "INSERT INTO calon_mahasiswa (nama_lengkap, email, password, no_hp)
                      VALUES ('$nama', '$email', '$password', '$no_hp')";

            if (mysqli_query($conn, $query)) {
                $success = "Registrasi berhasil! Silakan login.";
                header("refresh:3;url=index.php");
            } else {
                $error = "Registrasi gagal: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registrasi - PMB UTN</title>

    <!-- Bootstrap CSS (wajib kalau kamu pakai class bootstrap) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .min-vh-80 {
            min-height: 80vh;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #003366 0%, #00509e 100%);
        }

        .card {
            border-radius: 15px;
            overflow: hidden;
        }

        .card-header {
            border-bottom: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #003366 0%, #00509e 100%);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #002244 0%, #003366 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 51, 102, 0.3);
        }

        .progress {
            border-radius: 10px;
        }

        .progress-bar {
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert {
            animation: fadeInUp 0.5s ease;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center align-items-center min-vh-80">
            <div class="col-lg-8">

                <!-- Header Section -->
                <div class="text-center mb-5">
                    <h1 class="display-5 fw-bold text-primary mb-3">
                        <i class="fas fa-user-graduate me-2"></i>PMB Arten Campus
                    </h1>
                    <p class="lead text-muted">Bergabunglah dengan Arten Campus</p>
                </div>

                <div class="row g-4">
                    <!-- Left Column - Information -->
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-lg">
                            <div class="card-body p-5">
                                <h3 class="card-title mb-4 text-primary">
                                    <i class="fas fa-info-circle me-2"></i>Informasi Pendaftaran
                                </h3>

                                <div class="mb-4">
                                    <h5 class="text-success mb-3">
                                        <i class="fas fa-check-circle me-2"></i>Keuntungan Mendaftar
                                    </h5>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Pendaftaran
                                            online 24 jam</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Test online
                                            fleksibel</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Proses cepat dan
                                            transparan</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Biaya terjangkau
                                        </li>
                                        <li><i class="fas fa-check text-success me-2"></i>Fasilitas lengkap</li>
                                    </ul>
                                </div>

                                <div class="mb-4">
                                    <h5 class="text-info mb-3">
                                        <i class="fas fa-calendar-alt me-2"></i>Jadwal Pendaftaran
                                    </h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td><strong>Gelombang 1:</strong></td>
                                                <td>1 Jan - 30 April 2024</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Gelombang 2:</strong></td>
                                                <td>1 Mei - 31 Juli 2024</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Gelombang 3:</strong></td>
                                                <td>1 Agustus - 30 Oktober 2024</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-lightbulb me-2"></i>Tips Pendaftaran
                                    </h6>
                                    <p class="mb-0 small">Gunakan email aktif yang mudah diakses. Pastikan data yang
                                        diisi valid dan dapat dipertanggung jawabkan.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Registration Form -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-lg">
                            <div class="card-header bg-gradient-primary text-white py-4">
                                <h3 class="mb-0 text-center">
                                    <i class="fas fa-user-plus me-2"></i>Formulir Registrasi
                                </h3>
                                <p class="mb-0 text-center opacity-75">Isi data diri dengan lengkap dan benar</p>
                            </div>

                            <div class="card-body p-5">
                                <?php if (!empty($success)): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <div class="d-flex">
                                            <div class="me-3">
                                                <i class="fas fa-check-circle fa-2x"></i>
                                            </div>
                                            <div>
                                                <h5 class="alert-heading">Registrasi Berhasil!</h5>
                                                <p class="mb-0"><?php echo $success; ?></p>
                                                <small class="text-muted">Anda akan dialihkan ke halaman login...</small>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($error)): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <div class="d-flex">
                                            <div class="me-3">
                                                <i class="fas fa-exclamation-circle fa-2x"></i>
                                            </div>
                                            <div>
                                                <h5 class="alert-heading">Registrasi Gagal</h5>
                                                <p class="mb-0"><?php echo $error; ?></p>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>

                                <form method="POST" action="" id="registrationForm" class="needs-validation" novalidate>
                                    <!-- Nama -->
                                    <div class="mb-4">
                                        <label for="nama" class="form-label fw-bold">
                                            <i class="fas fa-user me-2 text-primary"></i>Nama Lengkap <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control form-control-lg" id="nama" name="nama"
                                            placeholder="Masukkan nama lengkap" required>
                                        <div class="invalid-feedback">Harap isi nama lengkap (minimal 3 karakter).</div>
                                    </div>

                                    <!-- Email -->
                                    <div class="mb-4">
                                        <label for="email" class="form-label fw-bold">
                                            <i class="fas fa-envelope me-2 text-primary"></i>Email <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="email" class="form-control form-control-lg" id="email" name="email"
                                            placeholder="contoh@email.com" required>
                                        <div class="invalid-feedback">Harap isi email yang valid.</div>
                                    </div>

                                    <!-- Password -->
                                    <div class="mb-4">
                                        <label for="password" class="form-label fw-bold">
                                            <i class="fas fa-lock me-2 text-primary"></i>Password <span
                                                class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" class="form-control form-control-lg" id="password"
                                                name="password" placeholder="Minimal 6 karakter" required>
                                            <button class="btn btn-outline-secondary" type="button"
                                                onclick="togglePassword('password')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <div class="invalid-feedback">Password minimal 6 karakter.</div>
                                        </div>
                                    </div>

                                    <!-- No HP -->
                                    <div class="mb-4">
                                        <label for="no_hp" class="form-label fw-bold">
                                            <i class="fas fa-phone me-2 text-primary"></i>Nomor Handphone <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control form-control-lg" id="no_hp" name="no_hp"
                                            placeholder="08xxxxxxxxxx" required>
                                        <div class="invalid-feedback">Harap isi nomor HP yang valid.</div>
                                    </div>


                                    <div class="mb-4">
                                        <label for="asal_sekolah" class="form-label fw-bold">
                                            <i class="fas fa-school me-2 text-primary"></i>Asal Sekolah <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control form-control-lg" id="asal_sekolah"
                                            name="asal_sekolah" placeholder="Contoh: SMK Negeri 1 Jakarta" required>
                                        <div class="invalid-feedback">Harap isi asal sekolah.</div>
                                    </div>

                                    <!-- Terms -->
                                    <div class="mb-4 form-check">
                                        <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                        <label class="form-check-label" for="agreeTerms">
                                            Saya setuju dengan Syarat dan Ketentuan <span class="text-danger">*</span>
                                        </label>
                                        <div class="invalid-feedback">Anda harus menyetujui syarat dan ketentuan.</div>
                                    </div>

                                    <div class="d-grid gap-3 mt-4">
                                        <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold">
                                            <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                                        </button>

                                        <div class="text-center">
                                            <a href="../index.php" class="btn btn-outline-secondary mb-3">
                                                <i class="fas fa-home me-1"></i>Kembali ke Beranda
                                            </a>
                                            <a href="index.php" class="btn btn-outline-primary ms-2">
                                                <i class="fas fa-sign-in-alt me-1"></i>Sudah Punya Akun?
                                            </a>
                                        </div>
                                    </div>
                                </form>

                                <hr class="my-4">

                                <div class="text-center">
                                    <p class="text-muted mb-2">Butuh bantuan?</p>
                                    <a href="mailto:pmb@utn.ac.id" class="btn btn-sm btn-outline-info me-2">
                                        <i class="fas fa-envelope me-1"></i>Hubungi Kami
                                    </a>
                                    <a href="tel:+6281234567890" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-phone me-1"></i>Telepon
                                    </a>
                                </div>
                            </div>

                            <div class="card-footer bg-light text-center py-3">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Data Anda aman bersama kami.
                                </small>
                            </div>
                        </div>
                    </div>

                </div><!-- /row -->
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (wajib untuk alert dismiss, modal, dll) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })();

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const btn = field.nextElementSibling;
            const icon = btn.querySelector('i');


            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>