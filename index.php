<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Universitas Arten — Portal PMB</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,800;1,700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy:    #003366;
            --navy2:   #1A2B4D;
            --navy3:   #0D1B33;
            --accent:  #1A4A8A;
            --gold:    #C9A84C;
            --gold2:   #E8C97A;
            --light:   #F8FAFC;
            --muted:   #64748B;
            --border:  #E2E8F0;
            --white:   #ffffff;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: #fff;
            overflow-x: hidden;
            color: #1E293B;
        }

        /* ========================================================
           NAVBAR
        ======================================================== */
   

        /* ========================================================
           HERO
        ======================================================== */
        .hero {
            min-height: 100vh;
            background: linear-gradient(160deg, var(--navy) 0%, var(--navy2) 55%, var(--navy3) 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            padding-top: 80px;
        }

        /* Decorative circles */
        .hero-circle {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,.06);
            pointer-events: none;
        }
        .hero-circle-1 { width: 600px; height: 600px; top: -150px; right: -150px; }
        .hero-circle-2 { width: 400px; height: 400px; bottom: -100px; left: -100px; }
        .hero-circle-3 { width: 900px; height: 900px; top: 50%; left: 50%; transform: translate(-50%,-50%); border-color: rgba(255,255,255,.03); }

        /* Gold accent glow */
        .hero-glow {
            position: absolute;
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(201,168,76,.18) 0%, transparent 70%);
            top: 10%; right: 8%;
            pointer-events: none;
        }

        .hero-content { position: relative; z-index: 2; }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            background: rgba(201,168,76,.15);
            border: 1px solid rgba(201,168,76,.3);
            border-radius: 20px;
            padding: .35rem 1rem;
            font-size: .78rem;
            font-weight: 600;
            color: var(--gold2);
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.4rem, 5vw, 3.8rem);
            font-weight: 800;
            color: white;
            line-height: 1.18;
            margin-bottom: 1.25rem;
        }

        .hero-title .gold-text {
            color: var(--gold2);
        }

        .hero-desc {
            font-size: 1rem;
            color: rgba(255,255,255,.7);
            max-width: 500px;
            line-height: 1.75;
            margin-bottom: 2rem;
        }

        .hero-actions { display: flex; gap: .85rem; flex-wrap: wrap; margin-bottom: 3rem; }

        .btn-hero-primary {
            background: var(--gold);
            color: var(--navy);
            border: none;
            border-radius: 10px;
            padding: .85rem 2rem;
            font-weight: 700;
            font-size: .95rem;
            transition: all .3s;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            text-decoration: none;
        }

        .btn-hero-primary:hover {
            background: var(--gold2);
            color: var(--navy);
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(201,168,76,.35);
        }

        .btn-hero-outline {
            background: transparent;
            color: rgba(255,255,255,.85);
            border: 1.5px solid rgba(255,255,255,.3);
            border-radius: 10px;
            padding: .85rem 2rem;
            font-weight: 600;
            font-size: .95rem;
            transition: all .3s;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            text-decoration: none;
        }

        .btn-hero-outline:hover {
            background: rgba(255,255,255,.08);
            border-color: rgba(255,255,255,.5);
            color: white;
            transform: translateY(-2px);
        }

        /* Stats bar */
        .hero-stats-bar {
            display: flex;
            gap: 0;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 14px;
            overflow: hidden;
            max-width: 460px;
        }

        .stat-cell {
            flex: 1;
            padding: 1rem .75rem;
            text-align: center;
            border-right: 1px solid rgba(255,255,255,.08);
        }

        .stat-cell:last-child { border-right: none; }

        .stat-num {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--gold2);
            display: block;
            line-height: 1;
            margin-bottom: .25rem;
        }

        .stat-lbl {
            font-size: .72rem;
            color: rgba(255,255,255,.55);
            font-weight: 500;
        }

        /* Hero right — emblem display */
        .hero-emblem-wrap {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .hero-emblem-bg {
            width: 340px; height: 340px;
            border-radius: 50%;
            background: rgba(255,255,255,.05);
            border: 2px solid rgba(201,168,76,.25);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .hero-emblem-bg::before {
            content: '';
            position: absolute;
            width: 280px; height: 280px;
            border-radius: 50%;
            border: 1px solid rgba(201,168,76,.15);
        }

        .hero-emblem-icon {
            font-size: 8rem;
            color: rgba(201,168,76,.6);
            animation: float 6s ease-in-out infinite;
        }

        .emblem-ring {
            position: absolute;
            border-radius: 50%;
            border: 1px dashed rgba(201,168,76,.2);
            animation: spin-slow 30s linear infinite;
        }
        .emblem-ring-1 { width: 420px; height: 420px; }
        .emblem-ring-2 { width: 500px; height: 500px; animation-direction: reverse; }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-14px); }
        }

        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        /* ========================================================
           SECTION COMMON
        ======================================================== */
        .section { padding: 90px 0; }
        .section-light { background: var(--light); }
        .section-white { background: white; }

        .section-label {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: .5rem;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.8rem, 3vw, 2.6rem);
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 1rem;
        }

        .section-desc {
            font-size: .95rem;
            color: var(--muted);
            max-width: 540px;
            line-height: 1.75;
        }

        .gold-bar {
            width: 48px; height: 3px;
            background: var(--gold);
            border-radius: 2px;
            margin-bottom: 1rem;
        }

        /* ========================================================
           AKSES PENDAFTARAN CARDS
        ======================================================== */
        .access-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            transition: all .35s cubic-bezier(.2,.8,.3,1);
            height: 100%;
            box-shadow: 0 4px 20px rgba(0,51,102,.05);
        }

        .access-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 24px 48px rgba(0,51,102,.14);
            border-color: rgba(0,51,102,.15);
        }

        .access-card-header {
            background: linear-gradient(160deg, var(--navy) 0%, var(--navy2) 100%);
            padding: 2rem 2rem 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .access-card-header::after {
            content: '';
            position: absolute;
            bottom: -20px; right: -20px;
            width: 120px; height: 120px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,.07);
        }

        .access-card-icon {
            width: 64px; height: 64px;
            border-radius: 16px;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(201,168,76,.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: var(--gold2);
            margin-bottom: 1rem;
        }

        .access-card-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            color: white;
            margin-bottom: .35rem;
        }

        .access-card-header p {
            font-size: .83rem;
            color: rgba(255,255,255,.6);
            margin: 0;
        }

        .access-card-body {
            padding: 1.75rem 2rem;
        }

        .access-card-body p {
            font-size: .875rem;
            color: var(--muted);
            line-height: 1.7;
            margin-bottom: 1.25rem;
        }

        .btn-card-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            width: 100%;
            padding: .75rem;
            background: linear-gradient(135deg, var(--navy), var(--accent));
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: .875rem;
            text-decoration: none;
            transition: all .3s;
            margin-bottom: .6rem;
        }

        .btn-card-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(0,51,102,.2);
            color: white;
        }

        .btn-card-outline {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            width: 100%;
            padding: .7rem;
            background: transparent;
            color: var(--navy);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-weight: 600;
            font-size: .875rem;
            text-decoration: none;
            transition: all .3s;
        }

        .btn-card-outline:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: rgba(26,74,138,.04);
        }

        /* ========================================================
           KEUNGGULAN
        ======================================================== */
        .feat-box {
            background: white;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem 1.75rem;
            transition: all .3s;
            height: 100%;
        }

        .feat-box:hover {
            border-color: rgba(0,51,102,.2);
            box-shadow: 0 16px 36px rgba(0,51,102,.08);
            transform: translateY(-4px);
        }

        .feat-icon-wrap {
            width: 52px; height: 52px;
            border-radius: 12px;
            background: rgba(0,51,102,.06);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: var(--navy);
            margin-bottom: 1.1rem;
        }

        .feat-box h5 {
            font-weight: 700;
            font-size: 1rem;
            color: var(--navy);
            margin-bottom: .5rem;
        }

        .feat-box p {
            font-size: .85rem;
            color: var(--muted);
            line-height: 1.7;
            margin: 0;
        }

        /* ========================================================
           LANGKAH PENDAFTARAN
        ======================================================== */
        .steps-section { background: var(--light); }

        .step-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem 1.5rem;
            text-align: center;
            height: 100%;
            transition: all .3s;
            position: relative;
        }

        .step-card:hover {
            box-shadow: 0 16px 36px rgba(0,51,102,.08);
            transform: translateY(-4px);
        }

        .step-num-badge {
            width: 56px; height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--navy), var(--accent));
            color: white;
            font-size: 1.3rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            box-shadow: 0 8px 20px rgba(0,51,102,.25);
            font-family: 'Playfair Display', serif;
        }

        .step-card h5 {
            font-weight: 700;
            color: var(--navy);
            font-size: 1rem;
            margin-bottom: .5rem;
        }

        .step-card p {
            font-size: .85rem;
            color: var(--muted);
            line-height: 1.7;
            margin: 0;
        }

        /* connector arrow */
        .step-connector {
            position: absolute;
            right: -20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            color: var(--gold);
            z-index: 2;
        }

        /* ========================================================
           TESTIMONIAL / QUOTE STRIP
        ======================================================== */
        .quote-strip {
            background: linear-gradient(160deg, var(--navy) 0%, var(--navy2) 100%);
            padding: 70px 0;
            position: relative;
            overflow: hidden;
        }

        .quote-strip::before {
            content: '"';
            position: absolute;
            font-family: 'Playfair Display', serif;
            font-size: 20rem;
            color: rgba(255,255,255,.04);
            top: -4rem;
            left: 1rem;
            line-height: 1;
            pointer-events: none;
        }

        .quote-text {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.2rem, 2.5vw, 1.7rem);
            color: white;
            font-style: italic;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }

        .quote-author {
            margin-top: 1.25rem;
            font-size: .85rem;
            color: var(--gold2);
            font-weight: 600;
            letter-spacing: .04em;
        }

        /* ========================================================
           CTA
        ======================================================== */
        .cta-section {
            background: white;
            padding: 90px 0;
        }

        .cta-box {
            background: linear-gradient(160deg, var(--navy), var(--navy2));
            border-radius: 24px;
            padding: 60px 50px;
            position: relative;
            overflow: hidden;
        }

        .cta-box::before {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,.06);
            top: -120px; right: -80px;
        }

        .cta-box::after {
            content: '';
            position: absolute;
            width: 250px; height: 250px;
            border-radius: 50%;
            border: 1px solid rgba(201,168,76,.15);
            bottom: -60px; left: -60px;
        }

        .cta-box h2 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.8rem, 3vw, 2.5rem);
            color: white;
            margin-bottom: .75rem;
        }

        .cta-box p {
            color: rgba(255,255,255,.65);
            font-size: .95rem;
            margin-bottom: 2rem;
        }

        .btn-cta {
            background: var(--gold);
            color: var(--navy);
            border: none;
            border-radius: 10px;
            padding: .9rem 2.25rem;
            font-weight: 700;
            font-size: .95rem;
            transition: all .3s;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            text-decoration: none;
        }

        .btn-cta:hover {
            background: var(--gold2);
            color: var(--navy);
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(201,168,76,.4);
        }

        .btn-cta-outline {
            background: transparent;
            color: rgba(255,255,255,.8);
            border: 1.5px solid rgba(255,255,255,.25);
            border-radius: 10px;
            padding: .9rem 2rem;
            font-weight: 600;
            font-size: .95rem;
            transition: all .3s;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            text-decoration: none;
            margin-left: .75rem;
        }

        .btn-cta-outline:hover {
            background: rgba(255,255,255,.08);
            border-color: rgba(255,255,255,.5);
            color: white;
        }

        /* ========================================================
           FOOTER
        ======================================================== */
        .footer {
            background: var(--navy3);
            color: rgba(255,255,255,.6);
            padding: 55px 0 30px;
        }

        .footer-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: white;
            margin-bottom: .4rem;
        }

        .footer-tagline {
            font-size: .8rem;
            color: rgba(255,255,255,.4);
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .footer-divider {
            width: 36px; height: 2px;
            background: var(--gold);
            border-radius: 2px;
            margin: 1rem 0;
        }

        .footer-links { list-style: none; padding: 0; margin: 0; }
        .footer-links li { margin-bottom: .5rem; }
        .footer-links a {
            font-size: .85rem;
            color: rgba(255,255,255,.5);
            text-decoration: none;
            transition: color .2s;
        }
        .footer-links a:hover { color: var(--gold2); }

        .footer-heading {
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: rgba(255,255,255,.4);
            margin-bottom: 1rem;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,.08);
            padding-top: 1.5rem;
            margin-top: 2.5rem;
            text-align: center;
            font-size: .78rem;
            color: rgba(255,255,255,.3);
        }

        .social-link {
            width: 36px; height: 36px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,.15);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,.5);
            font-size: .85rem;
            transition: all .2s;
            text-decoration: none;
            margin-right: .4rem;
        }
        .social-link:hover {
            border-color: var(--gold);
            color: var(--gold);
        }

        /* ========================================================
           SCROLL ANIMATION
        ======================================================== */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity .7s ease, transform .6s ease;
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .reveal-delay-1 { transition-delay: .1s; }
        .reveal-delay-2 { transition-delay: .2s; }
        .reveal-delay-3 { transition-delay: .3s; }
        .reveal-delay-4 { transition-delay: .4s; }

        /* ========================================================
           RESPONSIVE
        ======================================================== */
        @media (max-width: 991px) {
            .hero-emblem-wrap { margin-top: 3rem; }
            .hero-emblem-bg { width: 260px; height: 260px; }
            .hero-emblem-icon { font-size: 6rem; }
            .emblem-ring-1 { width: 320px; height: 320px; }
            .emblem-ring-2 { width: 380px; height: 380px; }
            .cta-box { padding: 40px 28px; }
        }

        @media (max-width: 767px) {
            .hero-stats-bar { max-width: 100%; }
            .btn-cta-outline { margin-left: 0; margin-top: .5rem; }
        }
    </style>
</head>
<body>

<!-- ============================================================
     NAVBAR
============================================================ -->
<?php include 'includes/navbar.php'; ?>
<!-- ============================================================
     HERO
============================================================ -->
<section class="hero" id="beranda">
    <div class="hero-circle hero-circle-1"></div>
    <div class="hero-circle hero-circle-2"></div>
    <div class="hero-circle hero-circle-3"></div>
    <div class="hero-glow"></div>

    <div class="container">
        <div class="row align-items-center g-5">

            <!-- Left -->
            <div class="col-lg-7 hero-content">
                <div class="hero-eyebrow">
                    <i class="fas fa-star" style="font-size:.65rem;"></i>
                    Penerimaan Mahasiswa Baru <?php echo date('Y'); ?>/<?php echo date('Y')+1; ?>
                </div>

                <h1 class="hero-title">
                    Wujudkan Impianmu<br>
                    Bersama <span class="gold-text">Universitas<br>Arten</span>
                </h1>

                <p class="hero-desc">
                    Portal resmi Penerimaan Mahasiswa Baru. Daftar secara online, cepat, dan terintegrasi. Mulai perjalanan akademikmu hari ini.
                </p>

                <div class="hero-actions">
                    <a href="user/registrasi.php" class="btn-hero-primary">
                        <i class="fas fa-user-plus"></i>
                        Daftar Sekarang
                    </a>
                    <a href="#pendaftaran" class="btn-hero-outline">
                        <i class="fas fa-info-circle"></i>
                        Selengkapnya
                    </a>
                </div>

                <div class="hero-stats-bar">
                    <div class="stat-cell">
                        <span class="stat-num">5K+</span>
                        <span class="stat-lbl">Mahasiswa</span>
                    </div>
                    <div class="stat-cell">
                        <span class="stat-num">50+</span>
                        <span class="stat-lbl">Program Studi</span>
                    </div>
                    <div class="stat-cell">
                        <span class="stat-num">98%</span>
                        <span class="stat-lbl">Kepuasan</span>
                    </div>
                    <div class="stat-cell">
                        <span class="stat-num">150+</span>
                        <span class="stat-lbl">Dosen Ahli</span>
                    </div>
                </div>
            </div>

            <!-- Right emblem -->
            <div class="col-lg-5 d-flex justify-content-center hero-emblem-wrap">
                <div class="emblem-ring emblem-ring-1"></div>
                <div class="emblem-ring emblem-ring-2"></div>
                <div class="hero-emblem-bg">
                    <i class="fas fa-university hero-emblem-icon"></i>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ============================================================
     AKSES PENDAFTARAN
============================================================ -->
<section class="section section-white" id="pendaftaran">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <div class="section-label">Portal Akses</div>
            <div class="gold-bar mx-auto"></div>
            <h2 class="section-title">Masuk ke Portal PMB</h2>
            <p class="section-desc mx-auto">Pilih portal yang sesuai dengan peran Anda untuk mengakses layanan Penerimaan Mahasiswa Baru.</p>
        </div>

        <div class="row justify-content-center g-4">

            <!-- Mahasiswa -->
            <div class="col-lg-5 col-md-6 reveal reveal-delay-1">
                <div class="access-card">
                    <div class="access-card-header">
                        <div class="access-card-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h3>Calon Mahasiswa</h3>
                        <p>Portal pendaftaran & login mahasiswa baru</p>
                    </div>
                    <div class="access-card-body">
                        <p>Mulai perjalanan akademikmu dengan mendaftar sebagai mahasiswa baru. Isi formulir online, pilih program studi, dan pantau status pendaftaranmu secara real-time.</p>
                        <a href="user/registrasi.php" class="btn-card-primary">
                            <i class="fas fa-user-plus"></i> Buat Akun Baru
                        </a>
                        <a href="user/index.php" class="btn-card-outline">
                            <i class="fas fa-sign-in-alt"></i> Sudah punya akun? Login
                        </a>
                    </div>
                </div>
            </div>

            <!-- Admin -->
            <div class="col-lg-5 col-md-6 reveal reveal-delay-2">
                <div class="access-card">
                    <div class="access-card-header">
                        <div class="access-card-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h3>Administrator</h3>
                        <p>Panel pengelolaan data & laporan PMB</p>
                    </div>
                    <div class="access-card-body">
                        <p>Panel khusus staf dan admin universitas untuk mengelola data pendaftaran, memverifikasi berkas, memantau statistik, dan menghasilkan laporan penerimaan secara komprehensif.</p>
                        <a href="admin/index.php" class="btn-card-primary">
                            <i class="fas fa-lock"></i> Login Administrator
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ============================================================
     KEUNGGULAN
============================================================ -->
<section class="section section-light" id="keunggulan">
    <div class="container">
        <div class="row align-items-end mb-5">
            <div class="col-lg-6 reveal">
                <div class="section-label">Mengapa Kami</div>
                <div class="gold-bar"></div>
                <h2 class="section-title">Keunggulan Universitas Arten</h2>
            </div>
            <div class="col-lg-6 reveal reveal-delay-1">
                <p class="section-desc">Universitas Arten berkomitmen menghadirkan pendidikan berkualitas tinggi, relevan dengan kebutuhan industri, dan berwawasan global.</p>
            </div>
        </div>

        <div class="row g-4">
            <?php
            $features = [
                ['fas fa-award',      'Akreditasi Unggul',    'Seluruh program studi telah mendapatkan akreditasi A/Unggul dari BAN-PT, menjamin kualitas pendidikan terbaik.'],
                ['fas fa-globe',      'Jaringan Global',      'Kerja sama aktif dengan 80+ universitas internasional dan perusahaan multinasional untuk beasiswa dan magang.'],
                ['fas fa-laptop-code','Kurikulum Adaptif',    'Kurikulum Merdeka Belajar berbasis proyek nyata, terintegrasi dengan kebutuhan industri masa kini.'],
                ['fas fa-building',   'Fasilitas Modern',     'Kampus dilengkapi laboratorium canggih, perpustakaan digital, studio kreatif, dan pusat riset terpadu.'],
                ['fas fa-hands-helping','Beasiswa Luas',      'Tersedia berbagai skema beasiswa prestasi, beasiswa kebutuhan, dan program bantuan biaya kuliah.'],
                ['fas fa-briefcase',  'Karir & Alumni',       'Jaringan alumni lebih dari 30.000 yang aktif di berbagai sektor industri nasional dan internasional.'],
            ];
            foreach($features as $i => $f): ?>
            <div class="col-lg-4 col-md-6 reveal reveal-delay-<?php echo ($i % 3) + 1; ?>">
                <div class="feat-box">
                    <div class="feat-icon-wrap">
                        <i class="<?php echo $f[0]; ?>"></i>
                    </div>
                    <h5><?php echo $f[1]; ?></h5>
                    <p><?php echo $f[2]; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ============================================================
     LANGKAH PENDAFTARAN
============================================================ -->
<section class="section section-white" id="langkah">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <div class="section-label">Alur Pendaftaran</div>
            <div class="gold-bar mx-auto"></div>
            <h2 class="section-title">Cara Mudah Mendaftar</h2>
            <p class="section-desc mx-auto">Empat langkah sederhana untuk memulai perjalanan akademikmu di Universitas Arten.</p>
        </div>

        <div class="row g-4 position-relative">
            <?php
            $steps = [
                ['Buat Akun',           'Registrasi online dengan data diri lengkap dan email aktif. Simpan No Test yang diberikan.'],
                ['Lengkapi Formulir',   'Login dan isi formulir pendaftaran: pilih program studi, upload berkas, dan data akademik.'],
                ['Ikuti Seleksi',       'Datang pada jadwal tes seleksi masuk sesuai program studi yang dipilih.'],
                ['Daftar Ulang',        'Peserta yang dinyatakan lulus wajib melakukan daftar ulang dalam batas waktu yang ditentukan.'],
            ];
            foreach($steps as $i => $s): ?>
            <div class="col-lg-3 col-md-6 reveal reveal-delay-<?php echo $i + 1; ?>">
                <div class="step-card">
                    <div class="step-num-badge"><?php echo $i+1; ?></div>
                    <h5><?php echo $s[0]; ?></h5>
                    <p><?php echo $s[1]; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ============================================================
     QUOTE STRIP
============================================================ -->
<div class="quote-strip">
    <div class="container text-center">
        <p class="quote-text reveal">
            "Pendidikan adalah investasi terbaik yang dapat Anda lakukan untuk masa depan.<br>
            Universitas Arten hadir untuk memastikan investasi itu bernilai seumur hidup."
        </p>
        <div class="quote-author reveal reveal-delay-1">— Rektor Universitas Arten</div>
    </div>
</div>


<!-- ============================================================
     CTA
============================================================ -->
<section class="cta-section">
    <div class="container">
        <div class="cta-box reveal">
            <div class="row align-items-center g-4" style="position:relative;z-index:2;">
                <div class="col-lg-8">
                    <h2>Siap Bergabung dengan Universitas Arten?</h2>
                    <p>Pendaftaran gelombang pertama segera dibuka. Kuota terbatas — daftarkan dirimu sekarang dan amankan tempat terbaikmu.</p>
                    <a href="user/registrasi.php" class="btn-cta">
                        <i class="fas fa-user-plus"></i> Daftar Sekarang
                    </a>
                    <a href="user/index.php" class="btn-cta-outline">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </div>
                <div class="col-lg-4 text-center d-none d-lg-block">
                    <i class="fas fa-graduation-cap" style="font-size:7rem;color:rgba(201,168,76,.25);"></i>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     FOOTER
============================================================ -->
<footer class="footer">
    <div class="container">
        <div class="row g-4">

            <div class="col-lg-4 col-md-6">
                <div class="footer-brand">
                    <i class="fas fa-graduation-cap me-2" style="color:var(--gold);"></i>
                    Universitas Arten
                </div>
                <div class="footer-tagline">Inspiring Future Leaders</div>
                <div class="footer-divider"></div>
                <p style="font-size:.84rem;line-height:1.7;color:rgba(255,255,255,.45);">
                    Jl. Pendidikan No. 45, Jakarta Pusat<br>
                    DKI Jakarta, Indonesia 10110
                </p>
                <div class="mt-3">
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 col-6">
                <div class="footer-heading">Navigasi</div>
                <ul class="footer-links">
                    <li><a href="#beranda">Beranda</a></li>
                    <li><a href="#pendaftaran">Pendaftaran</a></li>
                    <li><a href="#keunggulan">Keunggulan</a></li>
                    <li><a href="#langkah">Cara Daftar</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6 col-6">
                <div class="footer-heading">Portal</div>
                <ul class="footer-links">
                    <li><a href="user/registrasi.php">Daftar Akun</a></li>
                    <li><a href="user/index.php">Login Mahasiswa</a></li>
                    <li><a href="admin/index.php">Login Admin</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="footer-heading">Kontak</div>
                <ul class="footer-links">
                    <li><a href="mailto:pmb@arten.ac.id"><i class="fas fa-envelope me-2" style="color:var(--gold);opacity:.7;"></i>pmb@arten.ac.id</a></li>
                    <li><a href="tel:02112345678"><i class="fas fa-phone me-2" style="color:var(--gold);opacity:.7;"></i>(021) 1234-5678</a></li>
                    <li><span style="font-size:.84rem;color:rgba(255,255,255,.4);"><i class="fas fa-clock me-2" style="color:var(--gold);opacity:.7;"></i>Senin–Jumat, 08.00–16.00</span></li>
                </ul>
            </div>

        </div>

        <div class="footer-bottom">
            &copy; <?php echo date('Y'); ?> Universitas Arten &mdash; Sistem Informasi PMB. All rights reserved.
        </div>
    </div>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    // Scroll reveal
    const reveals = document.querySelectorAll('.reveal');
    function checkReveal() {
        const trigger = window.innerHeight * 0.88;
        reveals.forEach(el => {
            if (el.getBoundingClientRect().top < trigger) el.classList.add('visible');
        });
    }
    window.addEventListener('scroll', checkReveal);
    checkReveal();

    // Navbar shadow on scroll
    const navbar = document.getElementById('mainNavbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 40);
    });

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', function (e) {
            const id = this.getAttribute('href');
            if (id === '#') return;
            const target = document.querySelector(id);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
})();
</script>
</body>
</html>