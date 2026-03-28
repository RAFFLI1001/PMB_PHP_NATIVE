<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PMB - Portal Penerimaan Mahasiswa Baru | Arten Campus</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Animate CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
        }

        /* NAVBAR */
        .navbar {
            background: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            padding: 14px 0;
        }

        .navbar-brand {
            font-family: 'Montserrat', sans-serif;
            font-weight: 900;
            font-size: 1.8rem;
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            margin: 0 8px;
        }

        .btn-arten {
            background: linear-gradient(135deg,#667eea,#764ba2);
            color: white;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 600;
            border: none;
        }

        .btn-arten:hover {
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

<?php if (!isset($hideNavbar) || $hideNavbar !== true): ?>
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="../index.php">
            <i class="fas fa-graduation-cap me-2"></i>Arten Campus
        </a>

        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="../index.php#home">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="../index.php#pendaftaran">Pendaftaran</a></li>
                <li class="nav-item"><a class="nav-link" href="../index.php#informasi">Informasi</a></li>
                <li class="nav-item ms-3">
                    <a href="../user/registrasi.php" class="btn btn-arten btn-sm">Daftar</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>