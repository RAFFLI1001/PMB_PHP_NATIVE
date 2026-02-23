<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PMB - Portal Penerimaan Mahasiswa Baru | Arten Campus</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Animate CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        :root {
            --arten-primary: #2C3E50;
            --arten-secondary: #E74C3C;
            --arten-accent: #3498DB;
            --arten-light: #ECF0F1;
            --arten-dark: #1A252F;
            --arten-gradient: linear-gradient(135deg, #2C3E50 0%, #4A6491 100%);
            --arten-gradient-secondary: linear-gradient(135deg, #E74C3C 0%, #F39C12 100%);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: #333;
        }

        .navbar-brand {
            font-family: 'Montserrat', sans-serif;
            font-weight: 900;
            font-size: 1.8rem;
            background: var(--arten-gradient-secondary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
        }

        .navbar-brand i {
            margin-right: 10px;
            font-size: 1.8rem;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            padding: 0.5rem 0;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            color: var(--arten-dark) !important;
            margin: 0 10px;
            padding: 8px 16px !important;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            background: var(--arten-gradient);
            color: white !important;
            transform: translateY(-2px);
        }

        /* Button Styles */
        .btn-arten {
            background: var(--arten-gradient);
            color: white;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 50px;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(44, 62, 80, 0.2);
        }

        .btn-arten:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(44, 62, 80, 0.3);
            color: white;
            background: linear-gradient(135deg, #4A6491 0%, #2C3E50 100%);
        }

        .btn-arten-secondary {
            background: var(--arten-gradient-secondary);
            color: white;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 50px;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.2);
        }

        .btn-arten-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.3);
            color: white;
        }
    </style>
</head>
<body>
    <?php if (!isset($hideNavbar) || $hideNavbar !== true): ?>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-graduation-cap"></i>Arten Campus
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php#home">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php#pendaftaran">
                            <i class="fas fa-user-plus me-1"></i>Pendaftaran
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php#informasi">
                            <i class="fas fa-info-circle me-1"></i>Informasi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php#kontak">
                            <i class="fas fa-phone me-1"></i>Kontak
                        </a>
                    </li>
                    <li class="nav-item ms-3">
                        <a href="../user/registrasi.php" class="btn btn-arten btn-sm">
                            <i class="fas fa-rocket me-1"></i>Daftar Sekarang
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>