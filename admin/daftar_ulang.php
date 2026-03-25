<?php
require_once '../config/database.php';

// Check if admin is logged in
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Handle update status pembayaran
if (isset($_POST['update_status'])) {
    $id = intval($_POST['id_daftar_ulang']);
    $status = $_POST['status_pembayaran'];
    
    $query = "UPDATE daftar_ulang SET status_pembayaran = '$status' WHERE id_daftar_ulang = $id";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Status pembayaran berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Gagal memperbarui status: " . mysqli_error($conn);
    }
    header("Location: daftar_ulang.php");
    exit();
}

// Generate NIM
if (isset($_GET['generate_nim'])) {
    $id_pendaftaran = intval($_GET['generate_nim']);
    
    // Check if already has NIM
    $check = mysqli_query($conn, "SELECT * FROM daftar_ulang WHERE id_pendaftaran = $id_pendaftaran");
    if (mysqli_num_rows($check) == 0) {
        // Generate NIM: TAHUN+JURUSAN+NOURUT
        $year = date('y');
        $jurusan_query = mysqli_query($conn, "SELECT j.kode_jurusan FROM pendaftaran p 
                                            JOIN jurusan j ON p.id_jurusan = j.id_jurusan 
                                            WHERE p.id_pendaftaran = $id_pendaftaran");
        $jurusan = mysqli_fetch_assoc($jurusan_query);
        $kode_jurusan = $jurusan['kode_jurusan'] ?? '00';
        
        // Get sequence number for this year and jurusan
        $seq_query = mysqli_query($conn, 
            "SELECT COUNT(*) as total FROM daftar_ulang du 
             JOIN pendaftaran p ON du.id_pendaftaran = p.id_pendaftaran 
             WHERE YEAR(du.tanggal_daftar_ulang) = YEAR(CURDATE()) 
             AND p.id_jurusan = (SELECT id_jurusan FROM pendaftaran WHERE id_pendaftaran = $id_pendaftaran)");
        $seq = mysqli_fetch_assoc($seq_query);
        $sequence = str_pad($seq['total'] + 1, 3, '0', STR_PAD_LEFT);
        
        $nim = $year . $kode_jurusan . $sequence;
        
        // Insert to daftar_ulang
        $query = "INSERT INTO daftar_ulang (id_pendaftaran, tanggal_daftar_ulang, no_induk_mahasiswa, status_pembayaran) 
                  VALUES ($id_pendaftaran, CURDATE(), '$nim', 'belum')";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "NIM berhasil digenerate: <strong>$nim</strong>";
        } else {
            $_SESSION['error'] = "Gagal generate NIM: " . mysqli_error($conn);
        }
        header("Location: daftar_ulang.php");
        exit();
    }
}

// Get daftar ulang data with filtering
$search = '';
$filter_status = '';
$where_conditions = [];
$query_params = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_conditions[] = "(cm.nama_lengkap LIKE '%$search%' OR du.no_induk_mahasiswa LIKE '%$search%' OR p.no_test LIKE '%$search%')";
    $query_params['search'] = $search;
}

if (isset($_GET['status']) && !empty($_GET['status'])) {
    $filter_status = mysqli_real_escape_string($conn, $_GET['status']);
    $where_conditions[] = "du.status_pembayaran = '$filter_status'";
    $query_params['status'] = $filter_status;
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
$count_query = "SELECT COUNT(*) as total 
                FROM daftar_ulang du
                JOIN pendaftaran p ON du.id_pendaftaran = p.id_pendaftaran
                JOIN calon_mahasiswa cm ON p.id_calon = cm.id_calon
                $where";
$count_result = mysqli_query($conn, $count_query);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

// Get daftar ulang data
$query = "SELECT du.*, p.no_test, p.nilai_test, p.tanggal_daftar, 
                 cm.nama_lengkap, cm.email, cm.no_hp, cm.alamat, cm.asal_sekolah,
                 j.nama_jurusan, j.kode_jurusan
          FROM daftar_ulang du
          JOIN pendaftaran p ON du.id_pendaftaran = p.id_pendaftaran
          JOIN calon_mahasiswa cm ON p.id_calon = cm.id_calon
          JOIN jurusan j ON p.id_jurusan = j.id_jurusan
          $where
          ORDER BY du.tanggal_daftar_ulang DESC
          LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);

// Get statistics
$total_daftar_ulang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM daftar_ulang"))['total'];
$total_lunas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM daftar_ulang WHERE status_pembayaran = 'lunas'"))['total'];
$total_belum = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM daftar_ulang WHERE status_pembayaran = 'belum'"))['total'];
$total_lulus = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran WHERE status = 'lulus'"))['total'];
$total_belum_daftar = $total_lulus - $total_daftar_ulang;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Ulang - Admin PMB UTN</title>
    
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
        
        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--utn-primary);
            height: 100%;
        }
        
        .stats-card-success { border-left-color: var(--utn-success); }
        .stats-card-warning { border-left-color: var(--utn-warning); }
        .stats-card-danger { border-left-color: var(--utn-danger); }
        .stats-card-info { border-left-color: var(--utn-info); }
        
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
        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .badge-success { background: rgba(16, 185, 129, 0.1); color: var(--utn-success); }
        .badge-warning { background: rgba(245, 158, 11, 0.1); color: var(--utn-warning); }
        .badge-danger { background: rgba(239, 68, 68, 0.1); color: var(--utn-danger); }
        .badge-info { background: rgba(59, 130, 246, 0.1); color: var(--utn-info); }
        .badge-primary { background: rgba(0, 51, 102, 0.1); color: var(--utn-primary); }
        
        /* NIM Badge */
        .nim-badge {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            letter-spacing: 1px;
            background: linear-gradient(135deg, #003366 0%, #1A2B4D 100%);
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
        }
        
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
        
        /* Form Select Small */
        .form-select-sm {
            padding: 0.25rem 1.75rem 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: 4px;
            min-width: 100px;
        }
        
        /* Modal Styles */
        .modal-header {
            background: var(--utn-primary);
            color: white;
        }
        
        .modal-header .btn-close {
            filter: invert(1);
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
        
        /* Filter Tags */
        .filter-tag {
            background: #F1F5F9;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Bukti Pembayaran Preview */
        .bukti-preview {
            max-width: 200px;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #E2E8F0;
        }
        
        .bukti-preview img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        /* Progress Bar */
        .progress-percentage {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--utn-primary);
        }
        
        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }
        
        .action-card {
            background: #F8FAFC;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }
        
        .action-card:hover {
            background: white;
            border-color: var(--utn-primary);
            transform: translateY(-3px);
        }
        
        .action-card i {
            font-size: 1.5rem;
            color: var(--utn-primary);
            margin-bottom: 0.5rem;
        }
        
        .action-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--utn-dark);
        }
        
        /* Warning Card */
        .warning-card {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(245, 158, 11, 0.05) 100%);
            border: 1px solid rgba(245, 158, 11, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
        }
        
        /* Export Buttons */
        .export-buttons .btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
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
                    <p>Admin PMB Arten Campus</p>
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
            <a href="soal_test.php" class="nav-link">
                <i class="fas fa-question-circle nav-icon"></i>
                <span>Soal Test</span>
            </a>
            <a href="hasil_test.php" class="nav-link">
                <i class="fas fa-chart-bar nav-icon"></i>
                <span>Hasil Test</span>
            </a>
            <a href="daftar_ulang.php" class="nav-link active">
                <i class="fas fa-check-double nav-icon"></i>
                <span>Daftar Ulang</span>
            </a>
            <a href="pengaturan.php" class="nav-link">
                <i class="fas fa-cog nav-icon"></i>
                <span>Pengaturan</span>
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
                        <h1 class="h3 mb-0">Kelola Daftar Ulang</h1>
                        <p class="text-muted mb-0">Verifikasi pembayaran dan kelengkapan berkas mahasiswa baru</p>
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
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stats-value"><?php echo $total_daftar_ulang; ?></div>
                                <div class="stats-label">Sudah Daftar Ulang</div>
                            </div>
                            <div class="bg-primary text-white rounded-circle p-2">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="progress-percentage mt-2">
                            <?php echo $total_lulus > 0 ? round($total_daftar_ulang/$total_lulus*100, 1) : 0; ?>% dari yang lulus
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card stats-card-success">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stats-value"><?php echo $total_lunas; ?></div>
                                <div class="stats-label">Pembayaran Lunas</div>
                            </div>
                            <div class="bg-success text-white rounded-circle p-2">
                                <i class="fas fa-money-check-alt"></i>
                            </div>
                        </div>
                        <div class="progress-percentage mt-2">
                            <?php echo $total_daftar_ulang > 0 ? round($total_lunas/$total_daftar_ulang*100, 1) : 0; ?>% dari daftar ulang
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card stats-card-warning">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stats-value"><?php echo $total_belum_daftar; ?></div>
                                <div class="stats-label">Belum Daftar Ulang</div>
                            </div>
                            <div class="bg-warning text-white rounded-circle p-2">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="progress-percentage mt-2">
                            <?php echo $total_lulus > 0 ? round($total_belum_daftar/$total_lulus*100, 1) : 0; ?>% dari yang lulus
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card stats-card-danger">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stats-value"><?php echo $total_belum; ?></div>
                                <div class="stats-label">Belum Bayar</div>
                            </div>
                            <div class="bg-danger text-white rounded-circle p-2">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                        </div>
                        <div class="progress-percentage mt-2">
                            <?php echo $total_daftar_ulang > 0 ? round($total_belum/$total_daftar_ulang*100, 1) : 0; ?>% dari daftar ulang
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Two Column Layout -->
            <div class="row">
                <!-- Left Column: Search and Pending List -->
                <div class="col-lg-5 mb-4">
                    <!-- Search and Filter -->
                    <div class="search-filter-card">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <input type="text" 
                                       class="form-control" 
                                       placeholder="Cari NIM, nama, atau no. test..."
                                       value="<?php echo htmlspecialchars($search); ?>"
                                       id="searchInput">
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="filterStatus">
                                    <option value="">Semua Status</option>
                                    <option value="lunas" <?php echo $filter_status == 'lunas' ? 'selected' : ''; ?>>Lunas</option>
                                    <option value="belum" <?php echo $filter_status == 'belum' ? 'selected' : ''; ?>>Belum Bayar</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Filter Tags -->
                        <?php if(!empty($search) || !empty($filter_status)): ?>
                        <div class="mt-3">
                            <small class="text-muted me-2">Filter aktif:</small>
                            <?php if(!empty($search)): ?>
                                <span class="filter-tag me-2">
                                    <i class="fas fa-search"></i>
                                    "<?php echo htmlspecialchars($search); ?>"
                                    <a href="#" class="ms-1 text-danger clear-filter" data-filter="search">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            <?php endif; ?>
                            <?php if(!empty($filter_status)): ?>
                                <span class="filter-tag me-2">
                                    <i class="fas fa-filter"></i>
                                    Status: <?php echo $filter_status == 'lunas' ? 'Lunas' : 'Belum Bayar'; ?>
                                    <a href="#" class="ms-1 text-danger clear-filter" data-filter="status">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            <?php endif; ?>
                            <a href="daftar_ulang.php" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i>Reset Semua
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Belum Daftar Ulang -->
                    <div class="dashboard-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                Belum Daftar Ulang
                            </h3>
                            <span class="badge bg-warning"><?php echo $total_belum_daftar; ?> orang</span>
                        </div>
                        
                        <?php
                        $query2 = "SELECT p.*, cm.nama_lengkap, j.nama_jurusan 
                                  FROM pendaftaran p
                                  JOIN calon_mahasiswa cm ON p.id_calon = cm.id_calon
                                  JOIN jurusan j ON p.id_jurusan = j.id_jurusan
                                  WHERE p.status = 'lulus' 
                                  AND p.id_pendaftaran NOT IN (SELECT id_pendaftaran FROM daftar_ulang)
                                  ORDER BY p.tanggal_daftar DESC
                                  LIMIT 5";
                        $result2 = mysqli_query($conn, $query2);
                        ?>
                        
                        <?php if(mysqli_num_rows($result2) > 0): ?>
                            <div class="quick-actions">
                                <?php while($row2 = mysqli_fetch_assoc($result2)): ?>
                                <div class="action-card" onclick="generateNIM(<?php echo $row2['id_pendaftaran']; ?>, '<?php echo htmlspecialchars($row2['nama_lengkap']); ?>')">
                                    <i class="fas fa-id-card"></i>
                                    <div class="action-label"><?php echo htmlspecialchars($row2['nama_lengkap']); ?></div>
                                    <small class="text-muted"><?php echo $row2['nama_jurusan']; ?></small>
                                </div>
                                <?php endwhile; ?>
                            </div>
                            
                            <?php if($total_belum_daftar > 5): ?>
                            <div class="text-center mt-3">
                                <a href="#belumDaftar" class="btn btn-sm btn-outline-warning">
                                    Lihat <?php echo $total_belum_daftar - 5; ?> lainnya
                                </a>
                            </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="empty-state py-4">
                                <i class="fas fa-check-circle text-success"></i>
                                <p class="mb-0">Semua yang lulus sudah didaftarkan</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="dashboard-card">
                        <h3 class="card-title">
                            <i class="fas fa-bolt me-2"></i>Aksi Cepat
                        </h3>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary" onclick="exportToExcel()">
                                <i class="fas fa-file-excel me-2"></i>Export Data
                            </button>
                            <button class="btn btn-outline-success" onclick="printDaftarUlang()">
                                <i class="fas fa-print me-2"></i>Cetak Laporan
                            </button>
                            <a href="generate_kartu.php" class="btn btn-outline-info">
                                <i class="fas fa-id-card me-2"></i>Generate Kartu Mahasiswa
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Daftar Ulang Table -->
                <div class="col-lg-7 mb-4">
                    <div class="dashboard-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>Data Daftar Ulang
                            </h3>
                            <div class="text-muted">
                                Menampilkan <?php echo min($limit, $total_rows - $offset); ?> dari <?php echo $total_rows; ?> data
                            </div>
                        </div>
                        
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <div class="table-responsive">
<table class="table align-middle table-hover bg-white rounded overflow-hidden" id="daftarUlangTable">

    <thead class="table-light">
        <tr>
            <th class="ps-4">NIM</th>
            <th>Mahasiswa</th>
            <th>Jurusan</th>
            <th>Status Pembayaran</th>
            <th class="text-center">Bukti</th>
            <th class="text-center pe-4">Aksi</th>
        </tr>
    </thead>

    <tbody>

    <?php while($row = mysqli_fetch_assoc($result)): ?>

        <tr>
            <!-- NIM -->
            <td class="ps-4">
                <div class="fw-bold text-primary">
                    <?php echo $row['no_induk_mahasiswa']; ?>
                </div>
                <small class="text-muted">No Test: <?php echo $row['no_test']; ?></small>
            </td>

            <!-- DATA MAHASISWA -->
            <td>
                <div class="fw-semibold">
                    <?php echo htmlspecialchars($row['nama_lengkap']); ?>
                </div>

                <small class="text-muted d-block">
                    <?php echo $row['email']; ?>
                </small>

                <small class="text-muted d-block">
                    <?php echo $row['asal_sekolah']; ?>
                </small>
            </td>

            <!-- JURUSAN -->
            <td>
                <span class="badge bg-light text-dark px-3 py-2">
                    <?php echo $row['nama_jurusan']; ?>
                </span>
            </td>

            <!-- STATUS PEMBAYARAN -->
            <td>
                <form method="POST" action="">
                    <input type="hidden" name="id_daftar_ulang" value="<?php echo $row['id_daftar_ulang']; ?>">

                    <select name="status_pembayaran" 
                            class="form-select form-select-sm"
                            onchange="this.form.submit()">

                        <option value="belum" <?php if($row['status_pembayaran']=='belum') echo 'selected'; ?>>
                            Belum Bayar
                        </option>

                        <option value="lunas" <?php if($row['status_pembayaran']=='lunas') echo 'selected'; ?>>
                            Sudah Lunas
                        </option>

                    </select>

                    <input type="hidden" name="update_status" value="1">
                </form>
            </td>

            <!-- BUKTI -->
            <td class="text-center">
                <?php if($row['bukti_pembayaran']): ?>
                    <button class="btn btn-sm btn-outline-info"
                            onclick="showBukti('<?php echo $row['bukti_pembayaran']; ?>','<?php echo $row['nama_lengkap']; ?>')">
                        <i class="fas fa-eye"></i>
                    </button>
                <?php else: ?>
                    <span class="text-muted">Belum ada</span>
                <?php endif; ?>
            </td>

            <!-- AKSI -->
            <td class="text-center pe-4">
                <button class="btn btn-sm btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#detailModal<?php echo $row['id_daftar_ulang']; ?>">
                    Detail
                </button>
            </td>
        </tr>

    <?php endwhile; ?>

    </tbody>
</table>
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
                                <i class="fas fa-clipboard-check"></i>
                                <h4 class="mb-2">Belum ada data daftar ulang</h4>
                                <p class="text-muted">Mulai generate NIM untuk mahasiswa yang lulus</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
                <footer class="mt-4 pt-3 border-top text-center text-muted">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> PMB Universitas Teknologi Nusantara • Kelola Daftar Ulang</p>
                    <small>Persentase daftar ulang: <?php echo $total_lulus > 0 ? round($total_daftar_ulang/$total_lulus*100, 1) : 0; ?>% • 
                        Persentase lunas: <?php echo $total_daftar_ulang > 0 ? round($total_lunas/$total_daftar_ulang*100, 1) : 0; ?>% • 
                        Terakhir diperbarui: <?php echo date('d/m/Y H:i'); ?></small>
                </footer>
        </div>
    </main>
    
    <!-- Bukti Modal -->
    <div class="modal fade" id="buktiModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="buktiModalTitle">Bukti Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="buktiImage" src="" class="img-fluid" style="max-height: 500px;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a id="downloadBukti" href="#" class="btn btn-primary" download>
                        <i class="fas fa-download me-1"></i>Download
                    </a>
                </div>
            </div>
        </div>
    </div>
    
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
            
            // Filter functionality
            const searchInput = document.getElementById('searchInput');
            const filterStatus = document.getElementById('filterStatus');
            
            function applyFilter() {
                const params = new URLSearchParams();
                
                if (searchInput.value) params.set('search', searchInput.value);
                if (filterStatus.value) params.set('status', filterStatus.value);
                
                window.location.href = 'daftar_ulang.php?' + params.toString();
            }
            
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    applyFilter();
                }
            });
            
            filterStatus.addEventListener('change', function() {
                applyFilter();
            });
            
            // Clear individual filters
            document.querySelectorAll('.clear-filter').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const filter = this.dataset.filter;
                    const params = new URLSearchParams(window.location.search);
                    params.delete(filter);
                    window.location.href = 'daftar_ulang.php?' + params.toString();
                });
            });
            
            // Auto-submit status changes
            document.querySelectorAll('.status-select').forEach(select => {
                select.addEventListener('change', function() {
                    this.closest('.status-form').submit();
                });
            });
            
            // Table search
            const tableSearch = document.createElement('input');
            tableSearch.type = 'text';
            tableSearch.className = 'form-control mb-3';
            tableSearch.placeholder = 'Cari di dalam tabel...';
            tableSearch.id = 'tableSearch';
            
            const tableContainer = document.querySelector('.table-responsive');
            if (tableContainer) {
                tableContainer.parentNode.insertBefore(tableSearch, tableContainer);
                
                tableSearch.addEventListener('keyup', function() {
                    const filter = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#daftarUlangTable tbody tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(filter) ? '' : 'none';
                    });
                });
            }
        });
        
        // Functions
        function showBukti(filename, nama) {
            const imageUrl = '../assets/uploads/bukti/' + filename;
            document.getElementById('buktiImage').src = imageUrl;
            document.getElementById('buktiModalTitle').textContent = 'Bukti Pembayaran - ' + nama;
            document.getElementById('downloadBukti').href = imageUrl;
            
            const modal = new bootstrap.Modal(document.getElementById('buktiModal'));
            modal.show();
        }
        
        function generateNIM(id, nama) {
            if (confirm('Generate NIM untuk ' + nama + '?')) {
                window.location.href = '?generate_nim=' + id;
            }
        }
        
        function exportToExcel() {
            alert('Export data ke Excel (implementasi menggunakan PHPExcel)');
        }
        
        function printDaftarUlang() {
            window.print();
        }
        
        // Confirm generate NIM for all pending
        function generateAllNIM() {
            if (confirm('Generate NIM untuk semua yang belum daftar ulang?')) {
                // Implement batch generation
                alert('Fitur batch generation akan diimplementasikan');
            }
        }
    </script>
</body>
</html>