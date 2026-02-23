<?php
require_once 'config/database.php';
?>
<?php include 'includes/header.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PMB - Portal Penerimaan Mahasiswa Baru | Arten Campus</title>
    <style>
        /* Hero Section */
        .hero-section {
            padding-top: 120px;
            padding-bottom: 80px;
            background: var(--arten-gradient);
            color: white;
            position: relative;
            overflow: hidden;
            min-height: 90vh;
            display: flex;
            align-items: center;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,170.7C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: bottom;
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 900;
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(to right, #ffffff, #f8f9fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .hero-subtitle {
            font-size: 1.3rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            max-width: 700px;
        }

        .hero-stats {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin-top: 3rem;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .stat-item {
            text-align: center;
            color: white;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            display: block;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Cards Section */
        .cards-section {
            padding: 100px 0;
            position: relative;
            background: white;
        }

        .cards-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: linear-gradient(to bottom right, var(--arten-primary), transparent);
            clip-path: polygon(0 0, 100% 0, 0 100%);
            opacity: 0.1;
        }

        .section-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            text-align: center;
            position: relative;
            display: inline-block;
            left: 50%;
            transform: translateX(-50%);
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--arten-gradient-secondary);
            border-radius: 2px;
        }

        .feature-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            margin-bottom: 30px;
            border: none;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-20px) scale(1.02);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: var(--arten-gradient);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .card-header::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
        }

        .card-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            display: block;
        }

        .card-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .card-body {
            padding: 2.5rem;
        }

        .card-features {
            list-style: none;
            padding: 0;
            margin: 2rem 0;
        }

        .card-features li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }

        .card-features li:last-child {
            border-bottom: none;
        }

        .card-features li i {
            color: var(--arten-secondary);
            margin-right: 10px;
            font-size: 1.2rem;
        }

        /* Features Grid */
        .features-grid {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .features-grid::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.05)" d="M0,160L48,176C96,192,192,224,288,224C384,224,480,192,576,170.7C672,149,768,139,864,154.7C960,171,1056,213,1152,229.3C1248,245,1344,235,1392,229.3L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
        }

        .feature-box {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 2.5rem;
            margin: 1rem;
            text-align: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .feature-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            background: white;
        }

        .feature-icon {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Steps Section */
        .steps-section {
            padding: 100px 0;
            background: white;
        }

        .step-item {
            text-align: center;
            padding: 2rem;
            position: relative;
        }

        .step-number {
            width: 70px;
            height: 70px;
            background: var(--arten-gradient);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 30px rgba(44, 62, 80, 0.2);
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
        }

        .step-item:hover .step-number {
            transform: scale(1.1);
            background: var(--arten-gradient-secondary);
        }

        .step-connector {
            position: absolute;
            top: 35px;
            right: -15%;
            width: 30%;
            height: 3px;
            background: linear-gradient(to right, var(--arten-primary), var(--arten-secondary));
            opacity: 0.3;
        }

        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,128L48,138.7C96,149,192,171,288,181.3C384,192,480,192,576,165.3C672,139,768,85,864,69.3C960,53,1056,75,1152,101.3C1248,128,1344,160,1392,176L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
        }

        .cta-title {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
        }

        /* Footer */
        .footer {
            background: var(--arten-dark);
            color: white;
            padding: 80px 0 30px;
        }

        .footer-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: white;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: #bdc3c7;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .footer-links a:hover {
            color: white;
            transform: translateX(5px);
        }

        .footer-links a i {
            margin-right: 10px;
            width: 20px;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 1.5rem;
        }

        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: var(--arten-secondary);
            transform: translateY(-3px);
        }

        .copyright {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 30px;
            margin-top: 50px;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .step-connector {
                display: none;
            }
            
            .cta-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body id="home">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content animate__animated animate__fadeInLeft">
                    <h1 class="hero-title">
                        Selamat Datang di<br>
                        <span style="color: #E74C3C">Arten Campus</span>
                    </h1>
                    <p class="hero-subtitle">
                        Portal Penerimaan Mahasiswa Baru. Bergabunglah dengan komunitas akademik yang inovatif dan 
                        transformatif untuk meraih masa depan gemilang bersama kami.
                    </p>
                    <div class="mt-4">
                        <a href="#pendaftaran" class="btn btn-arten-secondary btn-lg me-3">
                            <i class="fas fa-rocket me-2"></i>Mulai Pendaftaran
                        </a>
                        <a href="#informasi" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-play-circle me-2"></i>Tonton Video
                        </a>
                    </div>
                    
                    <div class="hero-stats animate__animated animate__fadeInUp animate__delay-1s">
                        <div class="stat-item">
                            <span class="stat-number">5,000+</span>
                            <span class="stat-label">Mahasiswa Aktif</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">50+</span>
                            <span class="stat-label">Program Studi</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">98%</span>
                            <span class="stat-label">Kepuasan Mahasiswa</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">150+</span>
                            <span class="stat-label">Dosen Berkualitas</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 animate__animated animate__fadeInRight animate__delay-1s">
                    <div class="text-center floating">
                        <i class="fas fa-graduation-cap" style="font-size: 20rem; opacity: 0.1;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cards Section -->
    <section class="cards-section" id="pendaftaran">
        <div class="container">
            <h2 class="section-title text-dark">Pilihan Akses</h2>
            
            <div class="row justify-content-center">
                <!-- Calon Mahasiswa Card -->
                <div class="col-lg-5 col-md-6 mb-4">
                    <div class="feature-card animate__animated animate__fadeInUp">
                        <div class="card-header">
                            <i class="fas fa-user-graduate card-icon"></i>
                            <h3 class="card-title">Calon Mahasiswa</h3>
                            <p class="mb-0">Bergabunglah dengan kami</p>
                        </div>
                        <div class="card-body">
                            <p>Daftarkan diri Anda sebagai calon mahasiswa baru Arten Campus dan mulailah perjalanan akademik Anda menuju kesuksesan.</p>
                            
                            <ul class="card-features">
                                <li><i class="fas fa-check-circle"></i> Formulir pendaftaran online</li>
                                <li><i class="fas fa-check-circle"></i> Upload dokumen digital</li>
                                <li><i class="fas fa-check-circle"></i> Tes seleksi online</li>
                                <li><i class="fas fa-check-circle"></i> Notifikasi real-time</li>
                            </ul>
                            
                            <div class="d-grid gap-2">
                                <a href="user/registrasi.php" class="btn btn-arten">
                                    <i class="fas fa-user-plus me-2"></i>Daftar Akun Baru
                                </a>
                                <a href="user/index.php" class="btn btn-outline-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login Mahasiswa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Administrator Card -->
                <div class="col-lg-5 col-md-6 mb-4">
                    <div class="feature-card animate__animated animate__fadeInUp animate__delay-1s">
                        <div class="card-header">
                            <i class="fas fa-user-shield card-icon"></i>
                            <h3 class="card-title">Administrator</h3>
                            <p class="mb-0">Kelola sistem pendaftaran</p>
                        </div>
                        <div class="card-body">
                            <p>Akses panel administrator untuk mengelola seluruh proses penerimaan mahasiswa baru dengan sistem yang terintegrasi.</p>
                            
                            <ul class="card-features">
                                <li><i class="fas fa-tasks"></i> Kelola data pendaftar</li>
                                <li><i class="fas fa-chart-bar"></i> Monitoring real-time</li>
                                <li><i class="fas fa-file-export"></i> Laporan lengkap</li>
                                <li><i class="fas fa-cogs"></i> Konfigurasi sistem</li>
                            </ul>
                            
                            <div class="d-grid">
                                <a href="admin/index.php" class="btn btn-arten-secondary">
                                    <i class="fas fa-lock me-2"></i>Login Administrator
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section class="features-grid" id="informasi">
        <div class="container">
            <h2 class="section-title text-white">Keunggulan Arten Campus</h2>
            
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-box animate__animated animate__fadeIn">
                        <i class="fas fa-award feature-icon"></i>
                        <h4 class="mb-3">Akreditasi Premium</h4>
                        <p class="text-muted">Semua program studi terakreditasi A dengan standar internasional.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-box animate__animated animate__fadeIn animate__delay-1s">
                        <i class="fas fa-network-wired feature-icon"></i>
                        <h4 class="mb-3">Jaringan Global</h4>
                        <p class="text-muted">Kerjasama dengan 100+ universitas dan industri internasional.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-box animate__animated animate__fadeIn animate__delay-2s">
                        <i class="fas fa-graduation-cap feature-icon"></i>
                        <h4 class="mb-3">Dosen Berpengalaman</h4>
                        <p class="text-muted">Dosen dengan pengalaman industri dan akademik bertaraf internasional.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Steps Section -->
    <section class="steps-section">
        <div class="container">
            <h2 class="section-title text-dark">Alur Pendaftaran</h2>
            
            <div class="row">
                <div class="col-lg-3 col-md-6 step-item">
                    <div class="step-number">01</div>
                    <h5>Registrasi Akun</h5>
                    <p class="text-muted">Buat akun dengan email aktif dan verifikasi data diri</p>
                    <div class="step-connector"></div>
                </div>
                
                <div class="col-lg-3 col-md-6 step-item">
                    <div class="step-number">02</div>
                    <h5>Isi Formulir</h5>
                    <p class="text-muted">Lengkapi data pribadi, akademik, dan pilihan program studi</p>
                    <div class="step-connector"></div>
                </div>
                
                <div class="col-lg-3 col-md-6 step-item">
                    <div class="step-number">03</div>
                    <h5>Tes Online</h5>
                    <p class="text-muted">Ikuti tes potensi akademik dan wawancara virtual</p>
                    <div class="step-connector"></div>
                </div>
                
                <div class="col-lg-3 col-md-6 step-item">
                    <div class="step-number">04</div>
                    <h5>Daftar Ulang</h5>
                    <p class="text-muted">Konfirmasi penerimaan dan pembayaran administrasi</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h2 class="cta-title animate__animated animate__pulse animate__infinite">
                        Siap Memulai Perjalanan Akademik Anda?
                    </h2>
                    <p class="lead mb-5">
                        Bergabunglah dengan 5.000+ mahasiswa yang telah memilih Arten Campus sebagai tempat 
                        mengembangkan potensi dan meraih kesuksesan.
                    </p>
                    <a href="user/registrasi.php" class="btn btn-light btn-lg px-5 py-3">
                        <i class="fas fa-paper-plane me-2"></i>Daftar Sekarang - Gratis!
                    </a>
                    <p class="mt-3">
                        <small>Pendaftaran ditutup: <strong>30 Desember 2024</strong></small>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="kontak">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <h3 class="footer-title">Arten Campus</h3>
                    <p class="mb-4" style="color: #bdc3c7;">
                        Portal Penerimaan Mahasiswa Baru yang terintegrasi dan modern untuk memudahkan proses 
                        pendaftaran calon mahasiswa baru.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <h3 class="footer-title">Kontak Kami</h3>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-map-marker-alt"></i> Jl. Pendidikan No. 123, Bandung</a></li>
                        <li><a href="tel:+622112345678"><i class="fas fa-phone"></i> (022) 1234-5678</a></li>
                        <li><a href="mailto:pmb@artencampus.ac.id"><i class="fas fa-envelope"></i> pmb@artencampus.ac.id</a></li>
                        <li><a href="#"><i class="fas fa-clock"></i> Senin - Jumat: 08.00 - 16.00</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4">
                    <h3 class="footer-title">Link Cepat</h3>
                    <ul class="footer-links">
                        <li><a href="user/registrasi.php"><i class="fas fa-chevron-right"></i> Pendaftaran Online</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Program Studi</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Beasiswa</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> FAQ</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Blog Kampus</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; 2024 PMB Arten Campus. Semua hak dilindungi. | <a href="#" style="color: #3498DB;">Kebijakan Privasi</a> | <a href="#" style="color: #3498DB;">Syarat & Ketentuan</a></p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if(targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if(targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                }
            });
        }, observerOptions);

        // Observe elements
        document.querySelectorAll('.feature-card, .feature-box, .step-item').forEach(el => {
            observer.observe(el);
        });

        // Count-up animation for stats
        function animateCounter(element, target) {
            let current = 0;
            const increment = target / 100;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    element.textContent = target.toLocaleString();
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(current).toLocaleString();
                }
            }, 20);
        }

        // Start counter animation when stats are in view
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.querySelectorAll('.stat-number').forEach(stat => {
                        const target = parseInt(stat.textContent.replace(/[^0-9]/g, ''));
                        animateCounter(stat, target);
                    });
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        const statsSection = document.querySelector('.hero-stats');
        if(statsSection) {
            statsObserver.observe(statsSection);
        }
    </script>
</body>
</html>

<?php include 'includes/footer.php'; ?>