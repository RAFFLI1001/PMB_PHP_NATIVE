
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
</head>
<style>
         .navbar {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(0,51,102,.08);
            padding: 14px 0;
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            transition: box-shadow .3s;
        }

        .navbar.scrolled {
            box-shadow: 0 4px 24px rgba(0,51,102,.1);
        }

        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.45rem;
            font-weight: 700;
            color: var(--navy) !important;
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .brand-emblem-nav {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: linear-gradient(160deg, var(--navy), var(--navy2));
            border: 2px solid var(--gold);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold);
            font-size: 1rem;
            flex-shrink: 0;
        }

        .nav-link {
            font-size: .875rem;
            font-weight: 500;
            color: #334155 !important;
            padding: .4rem .85rem !important;
            transition: color .2s;
        }

        .nav-link:hover { color: var(--navy) !important; }

        .btn-nav-daftar {
            background: linear-gradient(135deg, var(--navy), var(--accent));
            color: white !important;
            border-radius: 8px;
            padding: .45rem 1.2rem !important;
            font-weight: 600;
            font-size: .85rem;
            transition: all .3s;
            border: none;
        }

        .btn-nav-daftar:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0,51,102,.25);
            color: white !important;
        }
</style>
<body>
    <nav class="navbar navbar-expand-lg" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand" href="#">
            <div class="brand-emblem-nav">
                <i class="fas fa-graduation-cap"></i>
            </div>
            Universitas Arten
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav ms-auto align-items-center gap-1">
                <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="#pendaftaran">Pendaftaran</a></li>
                <li class="nav-item"><a class="nav-link" href="#keunggulan">Keunggulan</a></li>
                <li class="nav-item"><a class="nav-link" href="#langkah">Cara Daftar</a></li>
                <li class="nav-item ms-2 mt-2 mt-lg-0">
                    <a href="user/registrasi.php" class="nav-link btn-nav-daftar">
                        <i class="fas fa-user-plus me-1"></i>Daftar Sekarang
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
</body>
</html>
