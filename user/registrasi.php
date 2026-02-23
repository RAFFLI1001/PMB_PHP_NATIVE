<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    
    // Check if email exists
    $check = mysqli_query($conn, "SELECT id_calon FROM calon_mahasiswa WHERE email='$email'");
    
    if (mysqli_num_rows($check) > 0) {
        $error = "Email sudah terdaftar!";
    } else {
        $query = "INSERT INTO calon_mahasiswa (nama_lengkap, email, password, no_hp) 
                  VALUES ('$nama', '$email', '$password', '$no_hp')";
        
        if (mysqli_query($conn, $query)) {
            $success = "Registrasi berhasil! Silakan login.";
            // Auto-redirect after 3 seconds
            header("refresh:3;url=index.php");
        } else {
            $error = "Registrasi gagal: " . mysqli_error($conn);
        }
    }
}
?>
<?php include '../includes/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center align-items-center min-vh-80">
        <div class="col-lg-8">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold text-primary mb-3">
                    <i class="fas fa-user-graduate me-2"></i>PMB UTN
                </h1>
                <p class="lead text-muted">Bergabunglah dengan Universitas Teknologi Nusantara</p>
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
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Pendaftaran online 24 jam</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Test online fleksibel</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Proses cepat dan transparan</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Biaya terjangkau</li>
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
                                <p class="mb-0 small">Gunakan email aktif yang mudah diakses. Pastikan data yang diisi valid dan dapat dipertanggungjawabkan.</p>
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
                            <?php if(isset($success)): ?>
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
                            
                            <?php if(isset($error)): ?>
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
                                <!-- Nama Lengkap -->
                                <div class="mb-4">
                                    <label for="nama" class="form-label fw-bold">
                                        <i class="fas fa-user me-2 text-primary"></i>Nama Lengkap
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-user text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control form-control-lg" 
                                               id="nama" name="nama" 
                                               placeholder="Masukkan nama lengkap" 
                                               required
                                               oninput="validateName(this)">
                                        <div class="invalid-feedback">
                                            Harap isi nama lengkap yang valid (minimal 3 karakter).
                                        </div>
                                    </div>
                                    <div class="form-text">Gunakan nama lengkap sesuai ijazah</div>
                                </div>
                                
                                <!-- Email -->
                                <div class="mb-4">
                                    <label for="email" class="form-label fw-bold">
                                        <i class="fas fa-envelope me-2 text-primary"></i>Email
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-at text-muted"></i>
                                        </span>
                                        <input type="email" class="form-control form-control-lg" 
                                               id="email" name="email" 
                                               placeholder="contoh@email.com" 
                                               required
                                               oninput="validateEmail(this)">
                                        <div class="invalid-feedback">
                                            Harap isi email yang valid.
                                        </div>
                                    </div>
                                    <div class="form-text">Email akan digunakan untuk login dan notifikasi</div>
                                </div>
                                
                                <!-- Password -->
                                <div class="mb-4">
                                    <label for="password" class="form-label fw-bold">
                                        <i class="fas fa-lock me-2 text-primary"></i>Password
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-key text-muted"></i>
                                        </span>
                                        <input type="password" class="form-control form-control-lg" 
                                               id="password" name="password" 
                                               placeholder="Minimal 6 karakter" 
                                               required
                                               oninput="validatePassword(this)">
                                        <button class="btn btn-outline-secondary" type="button" 
                                                onclick="togglePassword('password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <div class="invalid-feedback">
                                            Password minimal 6 karakter.
                                        </div>
                                    </div>
                                    <div class="progress mt-2" style="height: 5px;">
                                        <div class="progress-bar" id="passwordStrength" role="progressbar"></div>
                                    </div>
                                    <div class="form-text" id="passwordHelp">Kekuatan password: lemah</div>
                                </div>
                                
                                <!-- No. HP -->
                                <div class="mb-4">
                                    <label for="no_hp" class="form-label fw-bold">
                                        <i class="fas fa-phone me-2 text-primary"></i>Nomor Handphone
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-mobile-alt text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control form-control-lg" 
                                               id="no_hp" name="no_hp" 
                                               placeholder="08xxxxxxxxxx" 
                                               required
                                               oninput="formatPhoneNumber(this)">
                                        <div class="invalid-feedback">
                                            Harap isi nomor handphone yang valid (10-13 digit).
                                        </div>
                                    </div>
                                    <div class="form-text">Format: 08xxxxxxxxxx (tanpa spasi)</div>
                                </div>
                                
                                <!-- Terms and Conditions -->
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="agreeTerms" name="agreeTerms" required>
                                        <label class="form-check-label" for="agreeTerms">
                                            Saya setuju dengan 
                                            <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#termsModal">
                                                Syarat dan Ketentuan
                                            </a>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="invalid-feedback">
                                            Anda harus menyetujui syarat dan ketentuan.
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="d-grid gap-3 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold">
                                        <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                                    </button>
                                    
                                    <div class="text-center">
                                        <a href="../index.php" class="btn btn-outline-secondary">
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
                                <a href="#" class="text-decoration-none">Kebijakan Privasi</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-contract me-2"></i>Syarat dan Ketentuan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>1. Persyaratan Umum</h6>
                <p>Calon mahasiswa harus memenuhi persyaratan umum sebagai berikut:</p>
                <ul>
                    <li>Lulusan SMA/SMK/MA atau sederajat</li>
                    <li>Memiliki ijazah dan transkrip nilai yang sah</li>
                    <li>Sehat jasmani dan rohani</li>
                    <li>Bersedia mengikuti seluruh proses seleksi</li>
                </ul>
                
                <h6>2. Data dan Informasi</h6>
                <p>Calon mahasiswa wajib mengisi data dengan:</p>
                <ul>
                    <li>Jujur, lengkap, dan dapat dipertanggungjawabkan</li>
                    <li>Menggunakan data asli sesuai dokumen resmi</li>
                    <li>Memiliki email aktif untuk komunikasi</li>
                </ul>
                
                <h6>3. Proses Seleksi</h6>
                <p>Proses seleksi meliputi:</p>
                <ul>
                    <li>Pendaftaran online melalui sistem PMB UTN</li>
                    <li>Test online sesuai jadwal yang ditentukan</li>
                    <li>Verifikasi dokumen dan wawancara (jika diperlukan)</li>
                </ul>
                
                <h6>4. Hak dan Kewajiban</h6>
                <p>Dengan mendaftar, calon mahasiswa menyetujui:</p>
                <ul>
                    <li>Mematuhi peraturan dan tata tertib UTN</li>
                    <li>Membayar biaya pendidikan sesuai ketentuan</li>
                    <li>Mengikuti program akademik dengan baik</li>
                </ul>
                
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Dengan mencentang persetujuan, Anda telah membaca dan menyetujui seluruh syarat dan ketentuan di atas.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="fas fa-check me-1"></i>Saya Mengerti
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
(function () {
    'use strict'
    
    // Fetch all forms we want to apply custom validation styles to
    var forms = document.querySelectorAll('.needs-validation')
    
    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                
                form.classList.add('was-validated')
            }, false)
        })
})()

// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.nextElementSibling.querySelector('i');
    
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

// Password strength checker
function validatePassword(field) {
    const password = field.value;
    const strengthBar = document.getElementById('passwordStrength');
    const helpText = document.getElementById('passwordHelp');
    
    let strength = 0;
    let color = '#dc3545';
    let text = 'Lemah';
    
    // Length check
    if (password.length >= 6) strength++;
    if (password.length >= 8) strength++;
    
    // Character type checks
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    // Determine strength level
    if (strength <= 2) {
        color = '#dc3545';
        text = 'Lemah';
    } else if (strength <= 4) {
        color = '#ffc107';
        text = 'Sedang';
    } else {
        color = '#198754';
        text = 'Kuat';
    }
    
    // Update UI
    const percentage = Math.min(strength * 20, 100);
    strengthBar.style.width = percentage + '%';
    strengthBar.style.backgroundColor = color;
    strengthBar.className = 'progress-bar';
    
    if (strength <= 2) {
        strengthBar.classList.add('bg-danger');
    } else if (strength <= 4) {
        strengthBar.classList.add('bg-warning');
    } else {
        strengthBar.classList.add('bg-success');
    }
    
    helpText.textContent = 'Kekuatan password: ' + text;
    helpText.className = 'form-text';
    
    if (strength <= 2) {
        helpText.classList.add('text-danger');
    } else if (strength <= 4) {
        helpText.classList.add('text-warning');
    } else {
        helpText.classList.add('text-success');
    }
}

// Name validation
function validateName(field) {
    const name = field.value.trim();
    const isValid = name.length >= 3 && /^[a-zA-Z\s]+$/.test(name);
    
    if (isValid) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
    } else if (name.length > 0) {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
    } else {
        field.classList.remove('is-valid', 'is-invalid');
    }
}

// Email validation
function validateEmail(field) {
    const email = field.value.trim();
    const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    
    if (isValid) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
    } else if (email.length > 0) {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
    } else {
        field.classList.remove('is-valid', 'is-invalid');
    }
}

// Phone number formatting
function formatPhoneNumber(field) {
    let value = field.value.replace(/\D/g, '');
    
    // Limit to 13 digits
    if (value.length > 13) {
        value = value.substring(0, 13);
    }
    
    // Format: 08xx-xxxx-xxxx
    if (value.length > 4) {
        value = value.substring(0, 4) + '-' + value.substring(4);
    }
    if (value.length > 9) {
        value = value.substring(0, 9) + '-' + value.substring(9);
    }
    
    field.value = value;
    
    // Validation
    const isValid = /^08[0-9]{2}-[0-9]{4}-[0-9]{4,5}$/.test(value);
    
    if (isValid) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
    } else if (value.length > 0) {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
    } else {
        field.classList.remove('is-valid', 'is-invalid');
    }
}

// Real-time form validation
document.addEventListener('DOMContentLoaded', function() {
    // Initialize validation for all fields
    document.getElementById('nama').addEventListener('blur', function() {
        validateName(this);
    });
    
    document.getElementById('email').addEventListener('blur', function() {
        validateEmail(this);
    });
    
    document.getElementById('password').addEventListener('blur', function() {
        validatePassword(this);
    });
    
    document.getElementById('no_hp').addEventListener('blur', function() {
        const value = this.value.replace(/\D/g, '');
        const isValid = value.length >= 10 && value.length <= 13 && value.startsWith('08');
        
        if (isValid) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else if (this.value.length > 0) {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        }
    });
    
    // Auto-capitalize name on blur
    document.getElementById('nama').addEventListener('blur', function() {
        let name = this.value.trim();
        name = name.toLowerCase().split(' ').map(word => 
            word.charAt(0).toUpperCase() + word.slice(1)
        ).join(' ');
        this.value = name;
        validateName(this);
    });
    
    // Auto-lowercase email
    document.getElementById('email').addEventListener('blur', function() {
        this.value = this.value.toLowerCase();
        validateEmail(this);
    });
    
    // Show password requirements on focus
    document.getElementById('password').addEventListener('focus', function() {
        const helpText = document.getElementById('passwordHelp');
        helpText.innerHTML = `
            <strong>Password harus memenuhi:</strong><br>
            • Minimal 6 karakter<br>
            • Disarankan kombinasi huruf besar, kecil, angka, dan simbol
        `;
    });
    
    document.getElementById('password').addEventListener('blur', function() {
        validatePassword(this);
    });
});

// Submit confirmation
document.getElementById('registrationForm').addEventListener('submit', function(e) {
    const terms = document.getElementById('agreeTerms').checked;
    const password = document.getElementById('password').value;
    
    if (!terms) {
        e.preventDefault();
        alert('Anda harus menyetujui Syarat dan Ketentuan sebelum mendaftar.');
        return false;
    }
    
    if (password.length < 6) {
        e.preventDefault();
        alert('Password minimal 6 karakter.');
        return false;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
    submitBtn.disabled = true;
    
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 3000);
    
    return true;
});
</script>

<style>
/* Custom Styles for Registration Page */
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

.input-group-text {
    border-right: none;
}

.form-control-lg {
    border-left: none;
    padding-left: 0;
}

.form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
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

.list-unstyled li {
    padding: 5px 0;
    border-bottom: 1px dashed #eee;
}

.list-unstyled li:last-child {
    border-bottom: none;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-body {
        padding: 2rem !important;
    }
    
    .display-5 {
        font-size: 2.5rem;
    }
}

/* Animation for success message */
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

/* Form validation styles */
.is-valid {
    border-color: #198754 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e") !important;
}

.is-invalid {
    border-color: #dc3545 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e") !important;
}
</style>

<?php include '../includes/footer.php'; ?>