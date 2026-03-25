<?php
require_once 'config/database.php';

// Set flag untuk menyembunyikan navbar ganda
$hideNavbar = true;
?>
<?php include 'includes/header.php'; ?>

<style>
    /* Hero Section Enhanced */
    .hero-section {
        padding-top: 140px;
        padding-bottom: 100px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        position: relative;
        overflow: hidden;
        min-height: 100vh;
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
        animation: wave 10s linear infinite;
    }

    @keyframes wave {
        0% { transform: translateX(0) translateY(0); }
        50% { transform: translateX(-10px) translateY(5px); }
        100% { transform: translateX(0) translateY(0); }
    }

    .hero-content {
        position: relative;
        z-index: 1;
    }

    .hero-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 900;
        font-size: 4rem;
        margin-bottom: 1.5rem;
        background: linear-gradient(to right, #ffffff, #f8f9fa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0 2px 10px rgba(0,0,0,0.1);
        animation: fadeInUp 1s ease;
    }

    .hero-subtitle {
        font-size: 1.3rem;
        opacity: 0.95;
        margin-bottom: 2rem;
        max-width: 700px;
        animation: fadeInUp 1s ease 0.2s both;
    }

    .hero-buttons {
        animation: fadeInUp 1s ease 0.4s both;
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
        border: 1px solid rgba(255, 255, 255, 0.2);
        animation: fadeInUp 1s ease 0.6s both;
    }

    .stat-item {
        text-align: center;
        color: white;
        transition: transform 0.3s ease;
    }

    .stat-item:hover {
        transform: translateY(-5px);
    }

    .stat-number {
        font-size: 2.8rem;
        font-weight: 700;
        display: block;
        margin-bottom: 5px;
        background: linear-gradient(135deg, #fff, #f0f0f0);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .stat-label {
        font-size: 0.95rem;
        opacity: 0.9;
    }

    .hero-image {
        animation: float 6s ease-in-out infinite;
        text-align: center;
    }

    .hero-image i {
        font-size: 25rem;
        opacity: 0.8;
        filter: drop-shadow(0 20px 30px rgba(0,0,0,0.3));
    }

    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(2deg); }
    }

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

    /* Cards Section Enhanced */
    .cards-section {
        padding: 100px 0;
        position: relative;
        background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
    }

    .section-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 800;
        font-size: 3rem;
        margin-bottom: 3rem;
        text-align: center;
        position: relative;
        background: linear-gradient(135deg, #2C3E50 0%, #4A6491 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        border-radius: 2px;
    }

    .feature-card {
        background: white;
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        margin-bottom: 30px;
        border: none;
        height: 100%;
        position: relative;
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
        opacity: 0;
        transition: opacity 0.5s ease;
    }

    .feature-card:hover {
        transform: translateY(-20px) scale(1.02);
        box-shadow: 0 40px 60px rgba(0, 0, 0, 0.15);
    }

    .feature-card:hover::before {
        opacity: 1;
    }

    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .card-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.2), transparent);
        transform: rotate(45deg);
        animation: shine 3s infinite;
    }

    @keyframes shine {
        0% { transform: translateX(-100%) rotate(45deg); }
        20% { transform: translateX(100%) rotate(45deg); }
        100% { transform: translateX(100%) rotate(45deg); }
    }

    .card-icon {
        font-size: 5rem;
        margin-bottom: 1.5rem;
        display: block;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    .card-title {
        font-size: 2.2rem;
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
        padding: 12px 0;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
    }

    .card-features li:hover {
        transform: translateX(10px);
        color: #667eea;
    }

    .card-features li i {
        color: #667eea;
        margin-right: 15px;
        font-size: 1.3rem;
        width: 25px;
    }

    /* Features Grid Enhanced */
    .features-grid {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
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
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(102,126,234,0.1)" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,224C672,245,768,267,864,261.3C960,256,1056,224,1152,213.3C1248,203,1344,213,1392,218.7L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
        background-size: cover;
        opacity: 0.3;
    }

    .feature-box {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 30px;
        padding: 3rem 2rem;
        margin: 1rem;
        text-align: center;
        transition: all 0.4s ease;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        position: relative;
        z-index: 1;
        height: 100%;
    }

    .feature-box:hover {
        transform: translateY(-15px) scale(1.03);
        box-shadow: 0 30px 50px rgba(0, 0, 0, 0.2);
        background: white;
    }

    .feature-icon {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Steps Section Enhanced */
    .steps-section {
        padding: 100px 0;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .step-item {
        text-align: center;
        padding: 2.5rem 2rem;
        position: relative;
        background: white;
        border-radius: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        height: 100%;
    }

    .step-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .step-number {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0 auto 1.8rem;
        box-shadow: 0 15px 30px rgba(102, 126, 234, 0.3);
        position: relative;
        z-index: 1;
        transition: all 0.3s ease;
    }

    .step-item:hover .step-number {
        transform: scale(1.1) rotate(360deg);
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }

    .step-connector {
        position: absolute;
        top: 45px;
        right: -25%;
        width: 50%;
        height: 3px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        opacity: 0.3;
    }

    /* CTA Section Enhanced */
    .cta-section {
        padding: 120px 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.15)" d="M0,192L48,197.3C96,203,192,213,288,218.7C384,224,480,224,576,208C672,192,768,160,864,154.7C960,149,1056,171,1152,176C1248,181,1344,171,1392,165.3L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
        background-size: cover;
        opacity: 0.3;
    }

    .cta-title {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .cta-section .btn-light {
        background: white;
        color: #667eea;
        border: none;
        padding: 15px 40px;
        font-size: 1.2rem;
        font-weight: 600;
        border-radius: 50px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }

    .cta-section .btn-light:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    }

    /* Footer Enhanced */
    .footer {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        color: white;
        padding: 80px 0 30px;
        position: relative;
    }

    .footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(102,126,234,0.1)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,170.7C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
        background-size: cover;
        opacity: 0.1;
    }

    .footer-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: white;
        position: relative;
        display: inline-block;
    }

    .footer-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 50px;
        height: 3px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        border-radius: 2px;
    }

    .footer-links {
        list-style: none;
        padding: 0;
    }

    .footer-links li {
        margin-bottom: 12px;
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
        transform: translateX(8px);
    }

    .footer-links a i {
        margin-right: 12px;
        width: 20px;
        color: #667eea;
    }

    .social-links {
        display: flex;
        gap: 12px;
        margin-top: 2rem;
    }

    .social-link {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 1.2rem;
    }

    .social-link:hover {
        background: linear-gradient(135deg, #667eea, #764ba2);
        transform: translateY(-5px) scale(1.1);
        color: white;
    }

    .copyright {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 30px;
        margin-top: 50px;
        text-align: center;
        color: rgba(255, 255, 255, 0.7);
    }

    .copyright a {
        color: #667eea;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .copyright a:hover {
        color: white;
        text-decoration: underline;
    }

    /* Responsive Enhancements */
    @media (max-width: 991px) {
        .hero-title {
            font-size: 3rem;
        }
        
        .hero-image i {
            font-size: 18rem;
        }
        
        .step-connector {
            display: none;
        }
        
        .cta-title {
            font-size: 2.5rem;
        }
    }

    @media (max-width: 768px) {
        .hero-section {
            padding-top: 100px;
            padding-bottom: 60px;
            min-height: auto;
        }
        
        .hero-title {
            font-size: 2.5rem;
        }
        
        .hero-image i {
            font-size: 15rem;
            margin-top: 2rem;
        }
        
        .section-title {
            font-size: 2.2rem;
        }
        
        .card-header {
            padding: 2rem;
        }
        
        .card-icon {
            font-size: 4rem;
        }
        
        .card-title {
            font-size: 1.8rem;
        }
        
        .feature-box {
            padding: 2rem;
            margin: 0.5rem;
        }
        
        .step-item {
            margin-bottom: 2rem;
        }
        
        .cta-section {
            padding: 80px 0;
        }
        
        .cta-title {
            font-size: 2rem;
        }
        
        .footer {
            padding: 50px 0 20px;
        }
        
        .footer [class^="col-"] {
            margin-bottom: 30px;
        }
    }

    @media (max-width: 576px) {
        .hero-title {
            font-size: 2rem;
        }
        
        .hero-subtitle {
            font-size: 1.1rem;
        }
        
        .hero-stats {
            padding: 1.5rem;
        }
        
        .stat-number {
            font-size: 2rem;
        }
        
        .hero-image i {
            font-size: 12rem;
        }
        
        .btn-lg {
            padding: 10px 20px;
            font-size: 1rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .card-features li {
            font-size: 0.95rem;
        }
    }

    /* Loading Animation */
    .loading-spinner {
        display: inline-block;
        width: 50px;
        height: 50px;
        border: 3px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 10px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 5px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #764ba2, #667eea);
    }
</style>

<body id="home">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="hero-title">
                        Selamat Datang di<br>
                        <span style="background: linear-gradient(135deg, #FFD700, #FFA500); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Arten Campus</span>
                    </h1>
                    <p class="hero-subtitle">
                        Portal Penerimaan Mahasiswa Baru. Bergabunglah dengan komunitas akademik yang inovatif dan 
                        transformatif untuk meraih masa depan gemilang bersama kami.
                    </p>
                    <div class="hero-buttons">
                        <a href="#pendaftaran" class="btn btn-arten-secondary btn-lg me-3">
                            <i class="fas fa-rocket me-2"></i>Mulai Pendaftaran
                        </a>
                        <a href="#informasi" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-play-circle me-2"></i>Tonton Video
                        </a>
                    </div>
                    
                    <div class="hero-stats">
                        <div class="stat-item">
                            <span class="stat-number" data-target="5000">5000</span>
                            <span class="stat-label">Mahasiswa Aktif</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number" data-target="50">50+</span>
                            <span class="stat-label">Program Studi</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number" data-target="98">98%</span>
                            <span class="stat-label">Kepuasan Mahasiswa</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number" data-target="150">150+</span>
                            <span class="stat-label">Dosen Berkualitas</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cards Section -->
    <section class="cards-section" id="pendaftaran">
        <div class="container">
            <h2 class="section-title">Pilihan Akses</h2>
            
            <div class="row justify-content-center">
                <!-- Calon Mahasiswa Card -->
                <div class="col-lg-5 col-md-6 mb-4">
                    <div class="feature-card animate-on-scroll">
                        <div class="card-header">
                            <i class="fas fa-user-graduate card-icon"></i>
                            <h3 class="card-title">Calon Mahasiswa</h3>
                            <p class="mb-0">Bergabunglah dengan kami</p>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Daftarkan diri Anda sebagai calon mahasiswa baru Arten Campus dan mulailah perjalanan akademik Anda menuju kesuksesan.</p>
                            
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
                    <div class="feature-card animate-on-scroll" data-delay="200">
                        <div class="card-header">
                            <i class="fas fa-user-shield card-icon"></i>
                            <h3 class="card-title">Administrator</h3>
                            <p class="mb-0">Kelola sistem pendaftaran</p>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Akses panel administrator untuk mengelola seluruh proses penerimaan mahasiswa baru dengan sistem yang terintegrasi.</p>
                            
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
                    <div class="feature-box animate-on-scroll">
                        <i class="fas fa-award feature-icon"></i>
                        <h4 class="mb-3">Akreditasi Premium</h4>
                        <p class="text-muted">Semua program studi terakreditasi A dengan standar internasional.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-box animate-on-scroll" data-delay="200">
                        <i class="fas fa-network-wired feature-icon"></i>
                        <h4 class="mb-3">Jaringan Global</h4>
                        <p class="text-muted">Kerjasama dengan 100+ universitas dan industri internasional.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-box animate-on-scroll" data-delay="400">
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
            <h2 class="section-title">Alur Pendaftaran</h2>
            
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="step-item animate-on-scroll">
                        <div class="step-number">01</div>
                        <h5>Registrasi Akun</h5>
                        <p class="text-muted">Buat akun dengan email aktif dan verifikasi data diri</p>
                        <div class="step-connector"></div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="step-item animate-on-scroll" data-delay="200">
                        <div class="step-number">02</div>
                        <h5>Isi Formulir</h5>
                        <p class="text-muted">Lengkapi data pribadi, akademik, dan pilihan program studi</p>
                        <div class="step-connector"></div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="step-item animate-on-scroll" data-delay="400">
                        <div class="step-number">03</div>
                        <h5>Tes Online</h5>
                        <p class="text-muted">Ikuti tes potensi akademik dan wawancara virtual</p>
                        <div class="step-connector"></div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="step-item animate-on-scroll" data-delay="600">
                        <div class="step-number">04</div>
                        <h5>Daftar Ulang</h5>
                        <p class="text-muted">Konfirmasi penerimaan dan pembayaran administrasi</p>
                    </div>
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
                <p>&copy; 2024 PMB Arten Campus. Semua hak dilindungi. | <a href="#">Kebijakan Privasi</a> | <a href="#">Syarat & Ketentuan</a></p>
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
        const animateOnScroll = () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                        
                        // Add animation class
                        entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                        
                        // Unobserve after animation
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'all 0.6s ease';
                observer.observe(el);
            });
        };

        // Count-up animation for stats
        const animateStats = () => {
            const statsObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const stats = entry.target.querySelectorAll('.stat-number');
                        
                        stats.forEach(stat => {
                            const target = parseInt(stat.getAttribute('data-target'));
                            let current = 0;
                            const increment = target / 100;
                            
                            const timer = setInterval(() => {
                                current += increment;
                                if (current >= target) {
                                    stat.textContent = target.toLocaleString();
                                    clearInterval(timer);
                                } else {
                                    stat.textContent = Math.floor(current).toLocaleString();
                                }
                            }, 20);
                        });
                        
                        statsObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });

            const statsSection = document.querySelector('.hero-stats');
            if(statsSection) {
                statsObserver.observe(statsSection);
            }
        };

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            animateOnScroll();
            animateStats();
            
            // Add loading animation
            const heroImage = document.querySelector('.hero-image i');
            if(heroImage) {
                heroImage.style.animation = 'float 6s ease-in-out infinite';
            }
        });

        // Parallax effect
        window.addEventListener('scroll', function() {
            const scrolled = window.scrollY;
            const heroSection = document.querySelector('.hero-section');
            if(heroSection) {
                heroSection.style.backgroundPositionY = scrolled * 0.5 + 'px';
            }
        });

        // Add ripple effect to buttons
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                ripple.classList.add('ripple');
                this.appendChild(ripple);
                
                const x = e.clientX - e.target.offsetLeft;
                const y = e.clientY - e.target.offsetTop;
                
                ripple.style.left = `${x}px`;
                ripple.style.top = `${y}px`;
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
    </script>
</body>

<?php include 'includes/footer.php'; ?>