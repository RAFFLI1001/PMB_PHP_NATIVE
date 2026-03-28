<?php
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $pertanyaan = mysqli_real_escape_string($conn, $_POST['pertanyaan']);
        $pilihan_a = mysqli_real_escape_string($conn, $_POST['pilihan_a']);
        $pilihan_b = mysqli_real_escape_string($conn, $_POST['pilihan_b']);
        $pilihan_c = mysqli_real_escape_string($conn, $_POST['pilihan_c']);
        $pilihan_d = mysqli_real_escape_string($conn, $_POST['pilihan_d']);
        $jawaban_benar = $_POST['jawaban_benar'];
        $kategori = $_POST['kategori'];
        
        $query = "INSERT INTO soal_test (pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar, kategori) 
                  VALUES ('$pertanyaan', '$pilihan_a', '$pilihan_b', '$pilihan_c', '$pilihan_d', '$jawaban_benar', '$kategori')";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Soal berhasil ditambahkan!";
        } else {
            $_SESSION['error'] = "Gagal menambah soal: " . mysqli_error($conn);
        }
        header("Location: soal_test.php");
        exit();
    }
    
    if (isset($_POST['update'])) {
        $id = $_POST['id_soal'];
        $pertanyaan = mysqli_real_escape_string($conn, $_POST['pertanyaan']);
        $pilihan_a = mysqli_real_escape_string($conn, $_POST['pilihan_a']);
        $pilihan_b = mysqli_real_escape_string($conn, $_POST['pilihan_b']);
        $pilihan_c = mysqli_real_escape_string($conn, $_POST['pilihan_c']);
        $pilihan_d = mysqli_real_escape_string($conn, $_POST['pilihan_d']);
        $jawaban_benar = $_POST['jawaban_benar'];
        $kategori = $_POST['kategori'];
        
        $query = "UPDATE soal_test SET 
                  pertanyaan = '$pertanyaan',
                  pilihan_a = '$pilihan_a',
                  pilihan_b = '$pilihan_b',
                  pilihan_c = '$pilihan_c',
                  pilihan_d = '$pilihan_d',
                  jawaban_benar = '$jawaban_benar',
                  kategori = '$kategori'
                  WHERE id_soal = $id";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Soal berhasil diperbarui!";
        } else {
            $_SESSION['error'] = "Gagal memperbarui soal: " . mysqli_error($conn);
        }
        header("Location: soal_test.php");
        exit();
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM soal_test WHERE id_soal = $id");
    $_SESSION['success'] = "Soal berhasil dihapus";
    header("Location: soal_test.php");
    exit();
}

// Get all questions with filtering
$search = '';
$filter_kategori = '';
$where_conditions = [];
$query_params = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_conditions[] = "(pertanyaan LIKE '%$search%' OR pilihan_a LIKE '%$search%' OR pilihan_b LIKE '%$search%')";
    $query_params['search'] = $search;
}

if (isset($_GET['kategori']) && !empty($_GET['kategori'])) {
    $filter_kategori = mysqli_real_escape_string($conn, $_GET['kategori']);
    $where_conditions[] = "kategori = '$filter_kategori'";
    $query_params['kategori'] = $filter_kategori;
}

// Build WHERE clause
$where = '';
if (!empty($where_conditions)) {
    $where = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Get total count
$count_query = "SELECT COUNT(*) as total FROM soal_test $where";
$count_result = mysqli_query($conn, $count_query);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

// Get data
$query = "SELECT * FROM soal_test $where ORDER BY kategori, id_soal LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Get kategori statistics
$kategori_stats = [];
$kategori_query = "SELECT kategori, COUNT(*) as total FROM soal_test GROUP BY kategori ORDER BY total DESC";
$kategori_result = mysqli_query($conn, $kategori_query);
while($row = mysqli_fetch_assoc($kategori_result)) {
    $kategori_stats[] = $row;
}

$cek_timer = mysqli_query($conn,"SHOW TABLES LIKE 'pengaturan_ujian'");
if(mysqli_num_rows($cek_timer) == 0){
    mysqli_query($conn,"CREATE TABLE pengaturan_ujian (
        id INT AUTO_INCREMENT PRIMARY KEY,
        durasi_menit INT NOT NULL DEFAULT 60
    )");

    mysqli_query($conn,"INSERT INTO pengaturan_ujian (durasi_menit) VALUES (60)");
}

/* =====================================================
   AMBIL WAKTU UJIAN
===================================================== */
$timer_query = mysqli_query($conn,"SELECT * FROM pengaturan_ujian LIMIT 1");
$timer_data = mysqli_fetch_assoc($timer_query);

/* =====================================================
   UPDATE WAKTU UJIAN
===================================================== */
if(isset($_POST['update_timer'])){
    $durasi = intval($_POST['durasi_menit']);
    mysqli_query($conn,"UPDATE pengaturan_ujian SET durasi_menit='$durasi'");
    $_SESSION['success'] = "Waktu ujian berhasil diubah!";
    header("Location: soal_test.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Soal Test - Admin PMB</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --utn-primary: #003366;
            --utn-secondary: #FF6B00;
            --utn-success: #10B981;
            --utn-info: #3B82F6;
            --utn-warning: #F59E0B;
            --utn-danger: #EF4444;
            --utn-dark: #1E293B;
            --utn-light: #F8FAFC;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F1F5F9;
            color: #334155;
        }
        
        /* Fixed Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: linear-gradient(135deg, #003366 0%, #1A2B4D 100%);
            color: white;
            z-index: 1000;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0 !important;
            }
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .admin-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .admin-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        .admin-info h5 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .admin-info p {
            font-size: 0.875rem;
            opacity: 0.8;
            margin: 0;
        }
        
        .sidebar-nav {
            padding: 1rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.875rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
        }
        
        .nav-icon {
            width: 24px;
            margin-right: 12px;
            font-size: 1.1rem;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
            }
        }
        
        /* Top Navigation */
        .top-nav {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        /* Mobile Toggle Button */
        .sidebar-toggle {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--utn-primary);
            color: white;
            border: none;
            z-index: 1001;
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.3);
        }
        
        @media (max-width: 992px) {
            .sidebar-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }
        
        /* Dashboard Cards */
        .dashboard-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }
        
        .card-title {
            color: var(--utn-dark);
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #F1F5F9;
        }
        
        /* Search and Filter Section */
        .search-filter-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }
        
        .form-control, .form-select {
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--utn-primary);
            box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
        }
        
        /* Action Buttons */
        .btn-utn-primary {
            background: var(--utn-primary);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-utn-primary:hover {
            background: #004080;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.2);
        }
        
        .btn-utn-secondary {
            background: var(--utn-secondary);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-utn-secondary:hover {
            background: #FF8C42;
            color: white;
            transform: translateY(-2px);
        }
        
        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--utn-primary);
            height: 100%;
        }
        
        .stats-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--utn-dark);
            margin-bottom: 0.25rem;
        }
        
        .stats-label {
            font-size: 0.875rem;
            color: #64748B;
        }
        
        /* Table Styles */
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .data-table thead {
            background: #F8FAFC;
        }
        
        .data-table th {
            padding: 1rem;
            font-weight: 600;
            color: var(--utn-dark);
            border-bottom: 2px solid #F1F5F9;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        
        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid #F1F5F9;
            color: #475569;
            vertical-align: middle;
        }
        
        .data-table tbody tr {
            transition: background 0.3s ease;
        }
        
        .data-table tbody tr:hover {
            background: #F8FAFC;
        }
        
        /* Status Badges */
        .badge-category {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-primary { background: rgba(0, 51, 102, 0.1); color: var(--utn-primary); }
        .badge-success { background: rgba(16, 185, 129, 0.1); color: var(--utn-success); }
        .badge-warning { background: rgba(245, 158, 11, 0.1); color: var(--utn-warning); }
        .badge-danger { background: rgba(239, 68, 68, 0.1); color: var(--utn-danger); }
        .badge-info { background: rgba(59, 130, 246, 0.1); color: var(--utn-info); }
        
        /* Action Buttons Small */
        .btn-action {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 2px;
            transition: all 0.3s ease;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
        }
        
        /* Pagination */
        .pagination-custom .page-link {
            color: var(--utn-primary);
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            margin: 0 2px;
        }
        
        .pagination-custom .page-item.active .page-link {
            background: var(--utn-primary);
            border-color: var(--utn-primary);
            color: white;
        }
        
        .pagination-custom .page-link:hover {
            background: #F1F5F9;
            border-color: var(--utn-primary);
        }
        
        /* Modal Styles */
        .modal-header {
            background: var(--utn-primary);
            color: white;
        }
        
        .modal-header .btn-close {
            filter: invert(1);
        }
        
        /* Form Styles */
        .form-label {
            font-weight: 500;
            color: var(--utn-dark);
            margin-bottom: 0.5rem;
        }
        
        .required::after {
            content: " *";
            color: var(--utn-danger);
        }
        
        /* Question Preview */
        .question-preview {
            background: #F8FAFC;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .option {
            background: white;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }
        
        .option.correct {
            border-color: var(--utn-success);
            background: rgba(16, 185, 129, 0.05);
        }
        
        .option-label {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-weight: 600;
        }
        
        .option.correct .option-label {
            background: var(--utn-success);
            color: white;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #94A3B8;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        /* Alert Messages */
        .alert-custom {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.5rem;
        }
        
        /* Kategori Progress */
        .kategori-progress {
            background: #F8FAFC;
            border-radius: 8px;
            padding: 1rem;
        }
        
        .kategori-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #F1F5F9;
        }
        
        .kategori-item:last-child {
            margin-bottom: 0;
            border-bottom: none;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="admin-profile">
                <div class="admin-avatar">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="admin-info">
                    <h5><?php echo $_SESSION['admin_nama'] ?? 'Administrator'; ?></h5>
                    <p>Universitas Admin</p>
                </div>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-link">
                <i class="fas fa-tachometer-alt nav-icon"></i>
                <span>Dashboard</span>
            </a>
            <a href="data_maba.php" class="nav-link">
                <i class="fas fa-users nav-icon"></i>
                <span>Data Calon Maba</span>
            </a>
            <a href="soal_test.php" class="nav-link active">
                <i class="fas fa-question-circle nav-icon"></i>
                <span>Soal Test</span>
            </a>
            <a href="hasil_test.php" class="nav-link">
                <i class="fas fa-chart-bar nav-icon"></i>
                <span>Hasil Test</span>
            </a>
            <a href="daftar_ulang.php" class="nav-link">
                <i class="fas fa-check-double nav-icon"></i>
                <span>Daftar Ulang</span>
            </a>
           
            <a href="../logout.php" class="nav-link mt-4">
                <i class="fas fa-sign-out-alt nav-icon"></i>
                <span>Logout</span>
            </a>
        </nav>
    </aside>
    
    <!-- Mobile Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navigation -->
        <header class="top-nav">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Manajemen Soal Test</h1>
                        <p class="text-muted mb-0">Kelola soal ujian penerimaan mahasiswa baru</p>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn btn-link text-dark p-0" title="Notifikasi">
                            <i class="fas fa-bell"></i>
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-link text-dark text-decoration-none dropdown-toggle p-0" 
                                    data-bs-toggle="dropdown">
                                <i class="fas fa-user-shield me-2"></i>
                                <?php echo $_SESSION['admin_nama'] ?? 'Admin'; ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="pengaturan.php"><i class="fas fa-cog me-2"></i> Pengaturan</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Main Content -->
        <div class="container-fluid py-4">
            <!-- Success/Error Messages -->
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-custom mb-4">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-custom mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card">
                        <?php
                        $total_soal = mysqli_fetch_assoc(mysqli_query($conn, 
                            "SELECT COUNT(*) as total FROM soal_test"))['total'];
                        ?>
                        <div class="stats-value"><?php echo $total_soal; ?></div>
                        <div class="stats-label">Total Soal</div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card" style="border-left-color: var(--utn-success);">
                        <?php
                        $kategori_count = count($kategori_stats);
                        ?>
                        <div class="stats-value"><?php echo $kategori_count; ?></div>
                        <div class="stats-label">Kategori Soal</div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card" style="border-left-color: var(--utn-warning);">
                        <?php
                        $avg_per_kategori = $kategori_count > 0 ? round($total_soal / $kategori_count, 1) : 0;
                        ?>
                        <div class="stats-value"><?php echo $avg_per_kategori; ?></div>
                        <div class="stats-label">Rata-rata per Kategori</div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card" style="border-left-color: var(--utn-info);">
                        <div class="stats-value"><?php echo $limit; ?></div>
                        <div class="stats-label">Soal per Halaman</div>
                    </div>
                </div>
            </div>
            
            <!-- Two Column Layout -->
            <div class="row">
                <!-- Left Column: Tambah Soal dan Kategori Stats -->
                <div class="col-lg-5 mb-4">
                    <!-- Add Question Form -->
                    <div class="dashboard-card">
                        <h3 class="card-title">
                            <i class="fas fa-plus-circle me-2"></i>Tambah Soal Baru
                        </h3>
                        <form method="POST" action="" id="addQuestionForm">
                            <div class="mb-3">
                                <label class="form-label required">Pertanyaan</label>
                                <textarea class="form-control" name="pertanyaan" rows="3" required 
                                          placeholder="Masukkan pertanyaan disini..."></textarea>
                            </div>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Pilihan A</label>
                                    <input type="text" class="form-control" name="pilihan_a" required 
                                           placeholder="Masukkan pilihan A">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Pilihan B</label>
                                    <input type="text" class="form-control" name="pilihan_b" required 
                                           placeholder="Masukkan pilihan B">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pilihan C</label>
                                    <input type="text" class="form-control" name="pilihan_c" 
                                           placeholder="Masukkan pilihan C (opsional)">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pilihan D</label>
                                    <input type="text" class="form-control" name="pilihan_d" 
                                           placeholder="Masukkan pilihan D (opsional)">
                                </div>
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Jawaban Benar</label>
                                    <select class="form-select" name="jawaban_benar" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="a">Pilihan A</option>
                                        <option value="b">Pilihan B</option>
                                        <option value="c">Pilihan C</option>
                                        <option value="d">Pilihan D</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label required">Kategori</label>
                                    <select class="form-select" name="kategori" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="Umum">Umum</option>
                                        <option value="Matematika">Matematika</option>
                                        <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                                        <option value="Bahasa Inggris">Bahasa Inggris</option>
                                        <option value="Logika">Logika</option>
                                        <option value="IPA">IPA</option>
                                        <option value="IPS">IPS</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" name="add" class="btn btn-utn-primary me-2">
                                    <i class="fas fa-save me-1"></i>Simpan Soal
                                </button>
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo me-1"></i>Reset Form
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Kategori Statistics -->
                    <div class="dashboard-card">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie me-2"></i>Distribusi Kategori
                        </h3>
                        <div class="kategori-progress">
                            <?php if(!empty($kategori_stats)): ?>
                                <?php foreach($kategori_stats as $kategori): 
                                    $percentage = $total_soal > 0 ? round(($kategori['total'] / $total_soal) * 100) : 0;
                                ?>
                                <div class="kategori-item">
                                    <div>
                                        <div class="fw-medium"><?php echo $kategori['kategori']; ?></div>
                                        <small class="text-muted"><?php echo $percentage; ?>% dari total</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold"><?php echo $kategori['total']; ?></div>
                                        <div class="progress mt-1" style="width: 100px; height: 6px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?php echo $percentage; ?>%;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-3 text-muted">
                                    <i class="fas fa-chart-pie fa-2x mb-2"></i>
                                    <p>Belum ada data kategori</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Daftar Soal dan Filter -->
                <div class="col-lg-7 mb-4">
                    <!-- Search and Filter -->
                    <div class="search-filter-card">
                        <form method="GET" action="" class="row g-2">
                            <div class="col-md-6">
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Cari pertanyaan atau pilihan..."
                                       value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" name="kategori">
                                    <option value="">Semua Kategori</option>
                                    <option value="Umum" <?php echo $filter_kategori == 'Umum' ? 'selected' : ''; ?>>Umum</option>
                                    <option value="Matematika" <?php echo $filter_kategori == 'Matematika' ? 'selected' : ''; ?>>Matematika</option>
                                    <option value="Bahasa Indonesia" <?php echo $filter_kategori == 'Bahasa Indonesia' ? 'selected' : ''; ?>>Bahasa Indonesia</option>
                                    <option value="Bahasa Inggris" <?php echo $filter_kategori == 'Bahasa Inggris' ? 'selected' : ''; ?>>Bahasa Inggris</option>
                                    <option value="Logika" <?php echo $filter_kategori == 'Logika' ? 'selected' : ''; ?>>Logika</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-utn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Cari
                                </button>
                            </div>
                        </form>
                        
                        <!-- Filter Tags -->
                        <?php if(!empty($search) || !empty($filter_kategori)): ?>
                        <div class="mt-3">
                            <small class="text-muted">Filter aktif:</small>
                            <?php if(!empty($search)): ?>
                                <span class="badge bg-light text-dark me-2">
                                    <i class="fas fa-search me-1"></i>"<?php echo htmlspecialchars($search); ?>"
                                    <a href="?<?php echo http_build_query(array_diff_key($query_params, ['search'=>''])) ?>" 
                                       class="ms-1 text-danger">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            <?php endif; ?>
                            <?php if(!empty($filter_kategori)): ?>
                                <span class="badge bg-light text-dark me-2">
                                    <i class="fas fa-filter me-1"></i>Kategori: <?php echo $filter_kategori; ?>
                                    <a href="?<?php echo http_build_query(array_diff_key($query_params, ['kategori'=>''])) ?>" 
                                       class="ms-1 text-danger">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Daftar Soal -->
                    <div class="dashboard-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>Daftar Soal
                            </h3>
                            <div class="text-muted">
                                Menampilkan <?php echo min($limit, $total_rows - $offset); ?> dari <?php echo $total_rows; ?> soal
                            </div>
                        </div>
                        
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th width="60">ID</th>
                                            <th>Pertanyaan</th>
                                            <th width="120">Kategori</th>
                                            <th width="80">Jawaban</th>
                                            <th width="140">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($soal = mysqli_fetch_assoc($result)): 
                                            $jawaban_map = ['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'];
                                            $jawaban_text = isset($jawaban_map[$soal['jawaban_benar']]) ? $jawaban_map[$soal['jawaban_benar']] : '-';
                                        ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark">#<?php echo $soal['id_soal']; ?></span>
                                            </td>
                                            <td>
                                                <div class="fw-medium text-truncate" style="max-width: 300px;">
                                                    <?php echo htmlspecialchars($soal['pertanyaan']); ?>
                                                </div>
                                                <small class="text-muted">
                                                    <?php echo strlen($soal['pertanyaan']) > 50 ? substr(htmlspecialchars($soal['pertanyaan']), 0, 50) . '...' : ''; ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge-category badge-primary">
                                                    <?php echo $soal['kategori']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge-category badge-success">
                                                    <?php echo $jawaban_text; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <button class="btn-action btn btn-sm btn-outline-info" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#previewModal<?php echo $soal['id_soal']; ?>"
                                                            title="Preview">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn-action btn btn-sm btn-outline-warning" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editModal<?php echo $soal['id_soal']; ?>"
                                                            title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="?delete=<?php echo $soal['id_soal']; ?>" 
                                                       class="btn-action btn btn-sm btn-outline-danger"
                                                       onclick="return confirm('Yakin ingin menghapus soal ini?')"
                                                       title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        
                                        <!-- Preview Modal -->
                                        <div class="modal fade" id="previewModal<?php echo $soal['id_soal']; ?>" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Preview Soal #<?php echo $soal['id_soal']; ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="question-preview">
                                                            <h6 class="fw-bold mb-3"><?php echo htmlspecialchars($soal['pertanyaan']); ?></h6>
                                                            
                                                            <?php 
                                                            $options = [
                                                                'a' => $soal['pilihan_a'],
                                                                'b' => $soal['pilihan_b'],
                                                                'c' => $soal['pilihan_c'],
                                                                'd' => $soal['pilihan_d']
                                                            ];
                                                            
                                                            foreach($options as $key => $option):
                                                                if(!empty($option)):
                                                            ?>
                                                            <div class="option <?php echo $key == $soal['jawaban_benar'] ? 'correct' : ''; ?>">
                                                                <div class="option-label"><?php echo strtoupper($key); ?></div>
                                                                <div><?php echo htmlspecialchars($option); ?></div>
                                                            </div>
                                                            <?php 
                                                                endif;
                                                            endforeach; 
                                                            ?>
                                                        </div>
                                                        
                                                        <div class="row mt-3">
                                                            <div class="col-md-6">
                                                                <div class="d-flex align-items-center">
                                                                    <span class="fw-medium me-2">Kategori:</span>
                                                                    <span class="badge-category badge-primary"><?php echo $soal['kategori']; ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="d-flex align-items-center">
                                                                    <span class="fw-medium me-2">Jawaban Benar:</span>
                                                                    <span class="badge-category badge-success"><?php echo strtoupper($soal['jawaban_benar']); ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editModal<?php echo $soal['id_soal']; ?>" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Soal #<?php echo $soal['id_soal']; ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST" action="">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id_soal" value="<?php echo $soal['id_soal']; ?>">
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label required">Pertanyaan</label>
                                                                <textarea class="form-control" name="pertanyaan" rows="3" required><?php echo htmlspecialchars($soal['pertanyaan']); ?></textarea>
                                                            </div>
                                                            
                                                            <div class="row g-2 mb-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label required">Pilihan A</label>
                                                                    <input type="text" class="form-control" name="pilihan_a" required 
                                                                           value="<?php echo htmlspecialchars($soal['pilihan_a']); ?>">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label required">Pilihan B</label>
                                                                    <input type="text" class="form-control" name="pilihan_b" required 
                                                                           value="<?php echo htmlspecialchars($soal['pilihan_b']); ?>">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Pilihan C</label>
                                                                    <input type="text" class="form-control" name="pilihan_c" 
                                                                           value="<?php echo htmlspecialchars($soal['pilihan_c']); ?>">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Pilihan D</label>
                                                                    <input type="text" class="form-control" name="pilihan_d" 
                                                                           value="<?php echo htmlspecialchars($soal['pilihan_d']); ?>">
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="row g-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label required">Jawaban Benar</label>
                                                                    <select class="form-select" name="jawaban_benar" required>
                                                                        <option value="a" <?php echo $soal['jawaban_benar'] == 'a' ? 'selected' : ''; ?>>Pilihan A</option>
                                                                        <option value="b" <?php echo $soal['jawaban_benar'] == 'b' ? 'selected' : ''; ?>>Pilihan B</option>
                                                                        <option value="c" <?php echo $soal['jawaban_benar'] == 'c' ? 'selected' : ''; ?>>Pilihan C</option>
                                                                        <option value="d" <?php echo $soal['jawaban_benar'] == 'd' ? 'selected' : ''; ?>>Pilihan D</option>
                                                                    </select>
                                                                </div>
                                                                
                                                                <div class="col-md-6">
                                                                    <label class="form-label required">Kategori</label>
                                                                    <select class="form-select" name="kategori" required>
                                                                        <option value="Umum" <?php echo $soal['kategori'] == 'Umum' ? 'selected' : ''; ?>>Umum</option>
                                                                        <option value="Matematika" <?php echo $soal['kategori'] == 'Matematika' ? 'selected' : ''; ?>>Matematika</option>
                                                                        <option value="Bahasa Indonesia" <?php echo $soal['kategori'] == 'Bahasa Indonesia' ? 'selected' : ''; ?>>Bahasa Indonesia</option>
                                                                        <option value="Bahasa Inggris" <?php echo $soal['kategori'] == 'Bahasa Inggris' ? 'selected' : ''; ?>>Bahasa Inggris</option>
                                                                        <option value="Logika" <?php echo $soal['kategori'] == 'Logika' ? 'selected' : ''; ?>>Logika</option>
                                                                        <option value="IPA" <?php echo $soal['kategori'] == 'IPA' ? 'selected' : ''; ?>>IPA</option>
                                                                        <option value="IPS" <?php echo $soal['kategori'] == 'IPS' ? 'selected' : ''; ?>>IPS</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                <i class="fas fa-times me-1"></i>Batal
                                                            </button>
                                                            <button type="submit" name="update" class="btn btn-utn-primary">
                                                                <i class="fas fa-save me-1"></i>Simpan Perubahan
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pengaturan Waktu Ujian -->
<div class="dashboard-card">
    <h3 class="card-title">
        <i class="fas fa-clock me-2"></i>Pengaturan Waktu Ujian
    </h3>

    <form method="POST" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Durasi Ujian (Menit)</label>
            <input type="number" name="durasi_menit" class="form-control"
                   value="<?php echo $timer_data['durasi_menit']; ?>" required>
        </div>

        <div class="col-md-6 d-flex align-items-end">
            <button type="submit" name="update_timer" class="btn btn-utn-primary">
                <i class="fas fa-save me-1"></i>Simpan Waktu
            </button>
        </div>
    </form>
</div>
                            <!-- Pagination -->
                            <?php if($total_pages > 1): ?>
                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center pagination-custom">
                                    <?php 
                                    $base_url = '?' . (!empty($query_params) ? http_build_query($query_params) . '&' : '');
                                    
                                    // Previous button
                                    if($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?php echo $base_url; ?>page=<?php echo $page-1; ?>">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    // Page numbers
                                    $start_page = max(1, $page - 2);
                                    $end_page = min($total_pages, $page + 2);
                                    
                                    for($i = $start_page; $i <= $end_page; $i++): ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="<?php echo $base_url; ?>page=<?php echo $i; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php // Next button
                                    if($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?php echo $base_url; ?>page=<?php echo $page+1; ?>">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                            <?php endif; ?>
                            
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-question-circle"></i>
                                <h4 class="mb-2">Tidak ada soal ditemukan</h4>
                                <p class="text-muted">Mulai tambahkan soal baru atau coba ubah filter pencarian</p>
                                <?php if(!empty($search) || !empty($filter_kategori)): ?>
                                    <a href="soal_test.php" class="btn btn-utn-primary mt-2">
                                        <i class="fas fa-times me-1"></i>Reset Filter
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
            </div>
            
            <!-- Footer -->
           <footer class="mt-4 pt-3 border-top text-center text-muted">
                <p class="mb-0">PMB Universitas Arten</p>
                <small>Terakhir diakses: <?php echo date('d/m/Y H:i'); ?></small>
            </footer>
        </div>
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar Toggle for Mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 992) {
                    if (!sidebar.contains(event.target) && 
                        !sidebarToggle.contains(event.target) && 
                        sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                    }
                }
            });
            
            // Form validation
            const addForm = document.getElementById('addQuestionForm');
            if (addForm) {
                addForm.addEventListener('submit', function(e) {
                    const jawaban = addForm.querySelector('select[name="jawaban_benar"]');
                    const kategori = addForm.querySelector('select[name="kategori"]');
                    
                    if (!jawaban.value || !kategori.value) {
                        e.preventDefault();
                        alert('Harap pilih jawaban benar dan kategori');
                        jawaban.focus();
                        return false;
                    }
                });
            }
            
            // Auto-resize textareas
            const textareas = document.querySelectorAll('textarea');
            textareas.forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
                
                // Trigger initial resize
                textarea.dispatchEvent(new Event('input'));
            });
        });
    </script>
</body>
</html>