<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Universitas Arten</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Poppins & Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Animate.css (untuk animasi tambahan) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #fefefe;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        /* ================= NAVBAR MODERN & GLASS ================= */
        .navbar {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
            padding: 12px 0;
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 1.8rem;
            background: linear-gradient(135deg, #5F4B8B, #7B4A9E);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.5px;
        }

        .navbar-brand i {
            background: none;
            -webkit-background-clip: unset;
            background-clip: unset;
            color: #6C4A9E;
            font-size: 1.8rem;
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            margin: 0 8px;
            color: #2c3e50;
            transition: 0.2s;
            position: relative;
        }

        .navbar-nav .nav-link:hover {
            color: #6C4A9E;
        }

        .btn-arten {
            background: linear-gradient(105deg, #6C4A9E, #8B6BB0);
            color: white;
            border-radius: 40px;
            padding: 8px 28px;
            font-weight: 600;
            border: none;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(108, 74, 158, 0.25);
        }

        .btn-arten:hover {
            transform: translateY(-2px);
            background: linear-gradient(105deg, #5B3C86, #7A58A0);
            color: white;
            box-shadow: 0 10px 20px rgba(108, 74, 158, 0.3);
        }

        /* ================= HERO SECTION PREMIUM ================= */
        .hero-section {
            padding-top: 140px;
            padding-bottom: 100px;
            background: linear-gradient(125deg, #F9F5FF 0%, #F0EBFA 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(108,74,158,0.08) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            z-index: 0;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-weight: 800;
            font-size: 3.5rem;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            color: #1E1A2F;
        }

        .hero-title span {
            background: linear-gradient(120deg, #6C4A9E, #AA8BCF);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .hero-subtitle {
            font-size: 1.15rem;
            color: #4a5568;
            margin-bottom: 2rem;
            max-width: 550px;
        }

        .hero-buttons .btn {
            border-radius: 60px;
            padding: 12px 32px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary-soft {
            background: #6C4A9E;
            color: white;
            border: none;
            box-shadow: 0 12px 22px -8px rgba(108, 74, 158, 0.4);
        }

        .btn-primary-soft:hover {
            background: #553c7a;
            transform: translateY(-3px);
        }

        .btn-outline-soft {
            border: 2px solid #6C4A9E;
            color: #6C4A9E;
            background: transparent;
        }

        .btn-outline-soft:hover {
            background: #6C4A9E;
            color: white;
            transform: translateY(-3px);
        }

        .hero-stats {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(8px);
            border-radius: 32px;
            padding: 20px 25px;
            margin-top: 30px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            border: 1px solid rgba(108,74,158,0.2);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: #4A2A7A;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #4a5568;
            font-weight: 500;
        }

        .hero-image {
            text-align: center;
            animation: floatHero 5s ease-in-out infinite;
        }

        .hero-image i {
            font-size: 14rem;
            color: #b49ad6;
            filter: drop-shadow(0 20px 20px rgba(0,0,0,0.1));
        }

        @keyframes floatHero {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        /* ================= SECTION TITLE ================= */
        .section-title {
            font-weight: 700;
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 55px;
            color: #1E1A2F;
            position: relative;
        }

        .section-title:after {
            content: '';
            display: block;
            width: 70px;
            height: 4px;
            background: linear-gradient(90deg, #6C4A9E, #B594E0);
            margin: 12px auto 0;
            border-radius: 4px;
        }

        /* ================= CARDS ELEGANT ================= */
        .feature-card {
            background: white;
            border-radius: 40px;
            overflow: hidden;
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.2, 0.8, 0.3, 1);
            height: 100%;
            border: 1px solid rgba(108,74,158,0.1);
        }

        .feature-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 35px 50px -18px rgba(108, 74, 158, 0.25);
        }

        .card-header-gradient {
            background: linear-gradient(125deg, #6C4A9E, #9A77C2);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }

        .card-icon {
            font-size: 3.6rem;
            margin-bottom: 12px;
        }

        .card-title {
            font-weight: 700;
            font-size: 1.8rem;
        }

        .card-body-custom {
            padding: 30px;
        }

        /* ================= FEATURE GRID MINIMALIS ================= */
        .features-grid {
            background: #ffffff;
        }

        .feature-box {
            background: #FCFAFE;
            border-radius: 32px;
            padding: 38px 20px;
            text-align: center;
            transition: 0.3s;
            border: 1px solid #eee9f5;
        }

        .feature-box:hover {
            background: white;
            transform: translateY(-8px);
            border-color: #cbbff0;
            box-shadow: 0 25px 40px -15px rgba(108, 74, 158, 0.15);
        }

        .feature-icon {
            font-size: 3rem;
            background: linear-gradient(135deg, #6C4A9E, #b28ce0);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 20px;
        }

        /* ================= STEPS MODERN ================= */
        .steps-section {
            background: #F9F7FE;
        }

        .step-item {
            background: white;
            border-radius: 32px;
            padding: 30px 20px;
            transition: all 0.3s;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.02);
        }

        .step-item:hover {
            transform: translateY(-5px);
        }

        .step-number {
            width: 70px;
            height: 70px;
            background: linear-gradient(125deg, #6C4A9E, #8F6BB8);
            color: white;
            border-radius: 60px;
            font-size: 1.8rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 12px 15px -8px rgba(108,74,158,0.4);
        }

        /* ================= CTA PREMIUM ================= */
        .cta-section {
            padding: 100px 0;
            background: linear-gradient(115deg, #4A2A7A, #764ba2);
            color: white;
            text-align: center;
            border-radius: 0;
        }

        .cta-title {
            font-size: 2.8rem;
            font-weight: 800;
        }

        /* ================= FOOTER MINIMAL ================= */
        .footer {
            background: #11101c;
            color: #ccc;
            padding: 45px 0 30px;
            text-align: center;
        }

        /* ================= SCROLL ANIMATION ================= */
        .fade-up-scroll {
            opacity: 0;
            transform: translateY(45px);
            transition: opacity 0.8s ease, transform 0.7s ease;
        }

        .fade-up-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Additional hero animation delay classes */
        .animate__delay-0-2s {
            animation-delay: 0.2s;
        }
        .animate__delay-0-4s {
            animation-delay: 0.4s;
        }
        .animate__delay-0-6s {
            animation-delay: 0.6s;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title { font-size: 2.4rem; }
            .hero-image i { font-size: 8rem; }
            .section-title { font-size: 2rem; }
            .cta-title { font-size: 2rem; }
            .navbar-brand { font-size: 1.5rem; }
            .hero-section { padding-top: 110px; }
        }
    </style>
</head>
<body>

<!-- NAVBAR MODERN -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#home">
            <i class="fas fa-graduation-cap me-2"></i>Universitas Arten
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="#home">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="#pendaftaran">Pendaftaran</a></li>
                <li class="nav-item"><a class="nav-link" href="#informasi">Keunggulan</a></li>
                <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                    <a href="#register-cta" class="btn btn-arten btn-sm">Daftar Sekarang</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- MAIN CONTENT -->
<main>
    <!-- HERO SECTION -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 hero-content">
                    <h1 class="hero-title animate__animated animate__fadeInUp">
                        Selamat Datang di <br>
                        <span>Universitas Arten</span>
                    </h1>
                    <p class="hero-subtitle animate__animated animate__fadeInUp animate__delay-0-2s">
                        Portal Penerimaan Mahasiswa Baru dengan pengalaman digital modern. Mudah, cepat, dan terintegrasi untuk masa depan gemilang.
                    </p>
                    <div class="hero-buttons animate__animated animate__fadeInUp animate__delay-0-4s">
                        <a href="#pendaftaran" class="btn btn-primary-soft me-3">Mulai Pendaftaran</a>
                        <a href="#informasi" class="btn btn-outline-soft">Eksplor Kampus</a>
                    </div>
                    <div class="hero-stats row animate__animated animate__fadeInUp animate__delay-0-6s">
                        <div class="col-3 stat-item text-center">
                            <span class="stat-number">5k+</span>
                            <span class="stat-label">Mahasiswa</span>
                        </div>
                        <div class="col-3 stat-item text-center">
                            <span class="stat-number">50+</span>
                            <span class="stat-label">Program Studi</span>
                        </div>
                        <div class="col-3 stat-item text-center">
                            <span class="stat-number">98%</span>
                            <span class="stat-label">Kepuasan</span>
                        </div>
                        <div class="col-3 stat-item text-center">
                            <span class="stat-number">150+</span>
                            <span class="stat-label">Dosen Ahli</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center hero-image">
                    <i class="fas fa-landmark"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- CARDS PENDAFTARAN -->
    <section id="pendaftaran" style="padding: 80px 0;">
        <div class="container">
            <h2 class="section-title fade-up-scroll">Akses Pendaftaran</h2>
            <div class="row justify-content-center g-4">
                <div class="col-lg-5 col-md-6 fade-up-scroll">
                    <div class="feature-card">
                        <div class="card-header-gradient">
                            <i class="fas fa-user-graduate card-icon"></i>
                            <h3 class="card-title">Calon Mahasiswa</h3>
                        </div>
                        <div class="card-body-custom">
                            <p class="mb-4">Mulai perjalanan akademikmu dengan mendaftar sebagai mahasiswa baru. Akses formulir online dengan mudah.</p>
                            <a href="user/registrasi.php" class="btn btn-primary-soft w-100 mb-2">Daftar Akun</a>
                            <a href="user/index.php" class="btn btn-outline-soft w-100">Login Mahasiswa</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-md-6 fade-up-scroll">
                    <div class="feature-card">
                        <div class="card-header-gradient">
                            <i class="fas fa-user-shield card-icon"></i>
                            <h3 class="card-title">Administrator</h3>
                        </div>
                        <div class="card-body-custom">
                            <p class="mb-4">Panel khusus admin untuk mengelola data pendaftaran, verifikasi, dan laporan penerimaan.</p>
                            <a href="admin/index.php" class="btn btn-primary-soft w-100">Login Admin</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- KEUUNGGULAN (FEATURE GRID) -->
    <section class="features-grid" id="informasi" style="padding: 80px 0;">
        <div class="container">
            <h2 class="section-title fade-up-scroll">Keunggulan Universitas Arten</h2>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4 fade-up-scroll">
                    <div class="feature-box">
                        <i class="fas fa-award feature-icon"></i>
                        <h5 class="fw-bold">Akreditasi Unggul</h5>
                        <p class="text-muted">Semua prodi terakreditasi A/Unggul, standar pendidikan tinggi terbaik.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4 fade-up-scroll">
                    <div class="feature-box">
                        <i class="fas fa-globe-asia feature-icon"></i>
                        <h5 class="fw-bold">Jaringan Global</h5>
                        <p class="text-muted">Kerjasama internasional dengan kampus top dan perusahaan multinasional.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4 fade-up-scroll">
                    <div class="feature-box">
                        <i class="fas fa-laptop-code feature-icon"></i>
                        <h5 class="fw-bold">Kurikulum Modern</h5>
                        <p class="text-muted">Merdeka belajar, magang bersertifikat, dan pembelajaran berbasis proyek.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- LANGKAH PENDAFTARAN (STEPS) -->
    <section class="steps-section" style="padding: 80px 0;">
        <div class="container">
            <h2 class="section-title fade-up-scroll">Cara Mudah Mendaftar</h2>
            <div class="row g-4">
                <div class="col-md-4 fade-up-scroll">
                    <div class="step-item">
                        <div class="step-number">1</div>
                        <h5 class="fw-bold">Buat Akun</h5>
                        <p class="text-muted">Registrasi online dengan email aktif dan data diri.</p>
                    </div>
                </div>
                <div class="col-md-4 fade-up-scroll">
                    <div class="step-item">
                        <div class="step-number">2</div>
                        <h5 class="fw-bold">Isi Formulir</h5>
                        <p class="text-muted">Lengkapi biodata, pilih prodi, dan upload berkas.</p>
                    </div>
                </div>
                <div class="col-md-4 fade-up-scroll">
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <h5 class="fw-bold">Verifikasi & Final</h5>
                        <p class="text-muted">Konfirmasi pembayaran (gratis) dan dapatkan nomor pendaftaran.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CALL TO ACTION -->
    <section class="cta-section" id="register-cta">
        <div class="container">
            <h2 class="cta-title fade-up-scroll">Mulai Masa Depanmu Sekarang!</h2>
            <p class="lead mb-4">Pendaftaran gelombang awal gratis. Kuota terbatas.</p>
            <a href="user/registrasi.php" class="btn btn-light btn-lg px-5 py-3 rounded-pill fw-bold shadow">Daftar Sekarang →</a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p class="mb-2">Universitas Arten — Inspiring Future Leaders</p>
                    <p class="small text-white-50">Jl. Pendidikan No. 45, Jakarta | @Universitas Arten.ac.id</p>
                </div>
            </div>
        </div>
    </footer>
</main>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    (function() {
        // ANIMASI SCROLL: fade-up-scroll
        const fadeElements = document.querySelectorAll('.fade-up-scroll');
        
        function checkScroll() {
            const triggerBottom = window.innerHeight * 0.85;
            fadeElements.forEach(el => {
                const boxTop = el.getBoundingClientRect().top;
                if(boxTop < triggerBottom) {
                    el.classList.add('visible');
                }
            });
        }
        
        window.addEventListener('scroll', checkScroll);
        window.addEventListener('resize', checkScroll);
        checkScroll(); // langsung cek saat load
        
        // Smooth scroll untuk anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if(targetId === "#" || targetId === "") return;
                const targetElement = document.querySelector(targetId);
                if(targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Efek navbar transparan dinamis jika di-scroll
        const navbar = document.querySelector('.navbar');
        window.addEventListener('scroll', () => {
            if(window.scrollY > 50) {
                navbar.style.background = 'rgba(255,255,255,0.98)';
                navbar.style.boxShadow = '0 10px 25px rgba(0,0,0,0.05)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.92)';
                navbar.style.boxShadow = '0 8px 25px rgba(0,0,0,0.05)';
            }
        });
        
        // Tambahan efek hover untuk tombol dan elemen interaktif
        const allButtons = document.querySelectorAll('.btn');
        allButtons.forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transition = 'all 0.2s ease';
            });
        });
    })();
</script>
</body>
</html>