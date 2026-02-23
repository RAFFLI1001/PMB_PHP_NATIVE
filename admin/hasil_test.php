<?php
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Filter options
$search = '';
$filter_jurusan = '';
$filter_status = '';
$where_conditions = [];
$query_params = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_conditions[] = "(cm.nama_lengkap LIKE '%$search%' OR cm.email LIKE '%$search%' OR p.no_test LIKE '%$search%')";
    $query_params['search'] = $search;
}

if (isset($_GET['jurusan']) && !empty($_GET['jurusan'])) {
    $filter_jurusan = mysqli_real_escape_string($conn, $_GET['jurusan']);
    $where_conditions[] = "p.id_jurusan = '$filter_jurusan'";
    $query_params['jurusan'] = $filter_jurusan;
}

if (isset($_GET['status']) && !empty($_GET['status'])) {
    $filter_status = mysqli_real_escape_string($conn, $_GET['status']);
    $where_conditions[] = "p.status = '$filter_status'";
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
                FROM pendaftaran p
                JOIN calon_mahasiswa cm ON p.id_calon = cm.id_calon
                $where";
$count_result = mysqli_query($conn, $count_query);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

// Get results
$query = "SELECT p.*, cm.nama_lengkap, cm.email, cm.no_hp, cm.asal_sekolah, 
                 j.nama_jurusan, j.kode_jurusan,
                 (SELECT COUNT(*) FROM daftar_ulang du WHERE du.id_pendaftaran = p.id_pendaftaran) as sudah_daftar_ulang
          FROM pendaftaran p
          JOIN calon_mahasiswa cm ON p.id_calon = cm.id_calon
          JOIN jurusan j ON p.id_jurusan = j.id_jurusan
          $where
          ORDER BY p.nilai_test DESC, p.tanggal_daftar DESC
          LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);

// Get jurusan for filter
$jurusan_query = "SELECT * FROM jurusan ORDER BY nama_jurusan";
$jurusan_result = mysqli_query($conn, $jurusan_query);

// Get statistics
$total_peserta = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran"))['total'];
$total_lulus = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran WHERE status = 'lulus'"))['total'];
$total_tidak_lulus = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran WHERE status = 'tidak_lulus'"))['total'];
$total_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran WHERE status = 'pending'"))['total'];

// Get nilai statistics
$nilai_stats = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT AVG(nilai_test) as rata_rata, MAX(nilai_test) as tertinggi, MIN(nilai_test) as terendah 
     FROM pendaftaran WHERE nilai_test IS NOT NULL"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Test - Admin PMB UTN</title>
    
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
        
        .stats-change {
            font-size: 0.75rem;
            color: var(--utn-success);
            font-weight: 600;
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
        
        /* Progress Bar */
        .progress-thin {
            height: 20px;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .progress-bar-success { background: var(--utn-success); }
        .progress-bar-warning { background: var(--utn-warning); }
        .progress-bar-danger { background: var(--utn-danger); }
        
        /* Value Display */
        .value-display {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--utn-dark);
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
        
        /* Chart Container */
        .chart-container {
            background: #F8FAFC;
            border-radius: 8px;
            padding: 1rem;
            height: 100%;
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
                    <p>Admin PMB UTN</p>
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
            <a href="hasil_test.php" class="nav-link active">
                <i class="fas fa-chart-bar nav-icon"></i>
                <span>Hasil Test</span>
            </a>
            <a href="daftar_ulang.php" class="nav-link">
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
                        <h1 class="h3 mb-0">Hasil Test Calon Mahasiswa</h1>
                        <p class="text-muted mb-0">Analisis dan evaluasi hasil ujian masuk</p>
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
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stats-value"><?php echo $total_peserta; ?></div>
                                <div class="stats-label">Total Peserta Test</div>
                            </div>
                            <div class="bg-primary text-white rounded-circle p-2">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="stats-change mt-2">
                            <i class="fas fa-chart-line me-1"></i>
                            <?php echo $total_peserta > 0 ? round($total_lulus/$total_peserta*100, 1) : 0; ?>% kelulusan
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card stats-card-success">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stats-value"><?php echo $total_lulus; ?></div>
                                <div class="stats-label">Lulus Test</div>
                            </div>
                            <div class="bg-success text-white rounded-circle p-2">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="stats-change mt-2">
                            <i class="fas fa-arrow-up me-1"></i>
                            <?php echo $total_peserta > 0 ? round($total_lulus/$total_peserta*100, 1) : 0; ?>% dari total
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card stats-card-warning">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stats-value"><?php echo $total_pending; ?></div>
                                <div class="stats-label">Belum Test</div>
                            </div>
                            <div class="bg-warning text-white rounded-circle p-2">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="stats-change mt-2">
                            <i class="fas fa-clock me-1"></i>
                            Menunggu jadwal test
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card stats-card-danger">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stats-value"><?php echo $total_tidak_lulus; ?></div>
                                <div class="stats-label">Tidak Lulus</div>
                            </div>
                            <div class="bg-danger text-white rounded-circle p-2">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                        <div class="stats-change mt-2">
                            <i class="fas fa-chart-bar me-1"></i>
                            <?php echo $total_peserta > 0 ? round($total_tidak_lulus/$total_peserta*100, 1) : 0; ?>% dari total
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Search and Filter Section -->
            <div class="search-filter-card">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" 
                               class="form-control" 
                               name="search" 
                               placeholder="Cari nama, email, atau no. test..."
                               value="<?php echo htmlspecialchars($search); ?>"
                               id="searchInput">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filterJurusan">
                            <option value="">Semua Jurusan</option>
                            <?php while($jurusan = mysqli_fetch_assoc($jurusan_result)): ?>
                            <option value="<?php echo $jurusan['id_jurusan']; ?>" 
                                    <?php echo $filter_jurusan == $jurusan['id_jurusan'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($jurusan['nama_jurusan']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filterStatus">
                            <option value="">Semua Status</option>
                            <option value="pending" <?php echo $filter_status == 'pending' ? 'selected' : ''; ?>>Belum Test</option>
                            <option value="lulus" <?php echo $filter_status == 'lulus' ? 'selected' : ''; ?>>Lulus</option>
                            <option value="tidak_lulus" <?php echo $filter_status == 'tidak_lulus' ? 'selected' : ''; ?>>Tidak Lulus</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-utn-primary w-100" id="applyFilter">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                    </div>
                </div>
                
                <!-- Filter Tags -->
                <?php if(!empty($search) || !empty($filter_jurusan) || !empty($filter_status)): ?>
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
                    <?php if(!empty($filter_jurusan)): 
                        $jurusan_name = '';
                        mysqli_data_seek($jurusan_result, 0);
                        while($jurusan = mysqli_fetch_assoc($jurusan_result)) {
                            if($jurusan['id_jurusan'] == $filter_jurusan) {
                                $jurusan_name = $jurusan['nama_jurusan'];
                                break;
                            }
                        }
                    ?>
                        <span class="filter-tag me-2">
                            <i class="fas fa-graduation-cap"></i>
                            <?php echo htmlspecialchars($jurusan_name); ?>
                            <a href="#" class="ms-1 text-danger clear-filter" data-filter="jurusan">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    <?php endif; ?>
                    <?php if(!empty($filter_status)): ?>
                        <span class="filter-tag me-2">
                            <i class="fas fa-flag"></i>
                            <?php echo ucfirst(str_replace('_', ' ', $filter_status)); ?>
                            <a href="#" class="ms-1 text-danger clear-filter" data-filter="status">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    <?php endif; ?>
                    <a href="hasil_test.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-redo me-1"></i>Reset Semua
                    </a>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Two Column Layout -->
            <div class="row">
                <!-- Left Column: Statistics and Charts -->
                <div class="col-lg-4 mb-4">
                    <!-- Nilai Statistics -->
                    <div class="dashboard-card">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line me-2"></i>Statistik Nilai
                        </h3>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <div class="value-display text-primary">
                                        <?php echo number_format($nilai_stats['rata_rata'] ?? 0, 2); ?>
                                    </div>
                                    <div class="stats-label">Rata-rata Nilai</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <div class="value-display text-success">
                                        <?php echo number_format($nilai_stats['tertinggi'] ?? 0, 2); ?>
                                    </div>
                                    <div class="stats-label">Nilai Tertinggi</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="chart-container">
                            <h6 class="fw-medium mb-3">Distribusi Status</h6>
                            <div class="progress progress-thin mb-3">
                                <div class="progress-bar progress-bar-success" 
                                     style="width: <?php echo $total_peserta > 0 ? ($total_lulus/$total_peserta*100) : 0; ?>%">
                                </div>
                                <div class="progress-bar progress-bar-danger" 
                                     style="width: <?php echo $total_peserta > 0 ? ($total_tidak_lulus/$total_peserta*100) : 0; ?>%">
                                </div>
                                <div class="progress-bar progress-bar-warning" 
                                     style="width: <?php echo $total_peserta > 0 ? ($total_pending/$total_peserta*100) : 0; ?>%">
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="badge bg-success me-1"></span>
                                    <small>Lulus: <?php echo $total_lulus; ?> (<?php echo $total_peserta > 0 ? round($total_lulus/$total_peserta*100, 1) : 0; ?>%)</small>
                                </div>
                                <div>
                                    <span class="badge bg-warning me-1"></span>
                                    <small>Pending: <?php echo $total_pending; ?></small>
                                </div>
                                <div>
                                    <span class="badge bg-danger me-1"></span>
                                    <small>Tidak: <?php echo $total_tidak_lulus; ?></small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h6 class="fw-medium mb-2">Ekspor Data</h6>
                            <div class="export-buttons">
                                <button class="btn btn-sm btn-outline-success me-2" onclick="exportToExcel()">
                                    <i class="fas fa-file-excel me-1"></i>Excel
                                </button>
                                <button class="btn btn-sm btn-outline-danger me-2" onclick="exportToPDF()">
                                    <i class="fas fa-file-pdf me-1"></i>PDF
                                </button>
                                <button class="btn btn-sm btn-outline-primary" onclick="printTable()">
                                    <i class="fas fa-print me-1"></i>Print
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="dashboard-card">
                        <h3 class="card-title">
                            <i class="fas fa-bolt me-2"></i>Aksi Cepat
                        </h3>
                        <div class="d-grid gap-2">
                            <a href="generate_laporan.php" class="btn btn-outline-primary">
                                <i class="fas fa-file-alt me-2"></i>Generate Laporan
                            </a>
                            <a href="rekap_nilai.php" class="btn btn-outline-success">
                                <i class="fas fa-chart-pie me-2"></i>Rekap Nilai
                            </a>
                            <a href="pengumuman.php" class="btn btn-outline-warning">
                                <i class="fas fa-bullhorn me-2"></i>Buat Pengumuman
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Results Table -->
                <div class="col-lg-8 mb-4">
                    <div class="dashboard-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-table me-2"></i>Daftar Hasil Test
                            </h3>
                            <div class="text-muted">
                                Menampilkan <?php echo min($limit, $total_rows - $offset); ?> dari <?php echo $total_rows; ?> hasil
                            </div>
                        </div>
                        
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <div class="table-responsive">
                                <table class="data-table" id="resultsTable">
                                    <thead>
                                        <tr>
                                            <th width="100">No. Test</th>
                                            <th>Nama</th>
                                            <th width="120">Jurusan</th>
                                            <th width="120">Nilai</th>
                                            <th width="100">Status</th>
                                            <th width="80">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = mysqli_fetch_assoc($result)): 
                                            $status = $row['status'];
                                            $status_class = $status == 'lulus' ? 'success' : 
                                                           ($status == 'tidak_lulus' ? 'danger' : 'warning');
                                            $status_text = ucfirst(str_replace('_', ' ', $status));
                                        ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark">#<?php echo $row['no_test']; ?></span>
                                            </td>
                                            <td>
                                                <div class="fw-medium"><?php echo htmlspecialchars($row['nama_lengkap']); ?></div>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($row['email']); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    <?php echo htmlspecialchars($row['nama_jurusan']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if($row['nilai_test'] !== null): ?>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 me-2 progress-thin">
                                                            <div class="progress-bar 
                                                                <?php echo $row['nilai_test'] >= 70 ? 'bg-success' : 'bg-danger'; ?>" 
                                                                role="progressbar" 
                                                                style="width: <?php echo min($row['nilai_test'], 100); ?>%;">
                                                            </div>
                                                        </div>
                                                        <span class="fw-bold"><?php echo number_format($row['nilai_test'], 2); ?></span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="status-badge badge-<?php echo $status_class; ?>">
                                                    <?php echo $status_text; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <button class="btn-action btn btn-sm btn-outline-info" 
                                                            onclick="showDetail(<?php echo $row['id_pendaftaran']; ?>)"
                                                            title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <?php if($status == 'pending'): ?>
                                                    <button class="btn-action btn btn-sm btn-outline-success" 
                                                            onclick="updateStatus(<?php echo $row['id_pendaftaran']; ?>, 'lulus')"
                                                            title="Set Lulus">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button class="btn-action btn btn-sm btn-outline-danger" 
                                                            onclick="updateStatus(<?php echo $row['id_pendaftaran']; ?>, 'tidak_lulus')"
                                                            title="Set Tidak Lulus">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    <?php elseif($status == 'lulus' && $row['sudah_daftar_ulang'] == 0): ?>
                                                    <button class="btn-action btn btn-sm btn-outline-primary" 
                                                            onclick="generateNIM(<?php echo $row['id_pendaftaran']; ?>)"
                                                            title="Generate NIM">
                                                        <i class="fas fa-id-card"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
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
                                <i class="fas fa-chart-bar"></i>
                                <h4 class="mb-2">Tidak ada hasil test</h4>
                                <p class="text-muted">Tidak ada data hasil test dengan filter yang dipilih</p>
                                <?php if(!empty($search) || !empty($filter_jurusan) || !empty($filter_status)): ?>
                                    <a href="hasil_test.php" class="btn btn-utn-primary mt-2">
                                        <i class="fas fa-redo me-1"></i>Reset Filter
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <footer class="mt-4 pt-3 border-top text-center text-muted">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> PMB Universitas Teknologi Nusantara • Hasil Test Calon Mahasiswa</p>
                <small>Rata-rata nilai: <?php echo number_format($nilai_stats['rata_rata'] ?? 0, 2); ?> • 
                       Tingkat kelulusan: <?php echo $total_peserta > 0 ? round($total_lulus/$total_peserta*100, 1) : 0; ?>% • 
                       Terakhir diperbarui: <?php echo date('d/m/Y H:i'); ?></small>
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
            
            // Filter functionality
            const applyFilterBtn = document.getElementById('applyFilter');
            const searchInput = document.getElementById('searchInput');
            const filterJurusan = document.getElementById('filterJurusan');
            const filterStatus = document.getElementById('filterStatus');
            
            applyFilterBtn.addEventListener('click', function() {
                const params = new URLSearchParams();
                
                if (searchInput.value) params.set('search', searchInput.value);
                if (filterJurusan.value) params.set('jurusan', filterJurusan.value);
                if (filterStatus.value) params.set('status', filterStatus.value);
                
                window.location.href = 'hasil_test.php?' + params.toString();
            });
            
            // Clear individual filters
            document.querySelectorAll('.clear-filter').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const filter = this.dataset.filter;
                    const params = new URLSearchParams(window.location.search);
                    params.delete(filter);
                    window.location.href = 'hasil_test.php?' + params.toString();
                });
            });
            
            // Enter key to search
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    applyFilterBtn.click();
                }
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
                    const rows = document.querySelectorAll('#resultsTable tbody tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(filter) ? '' : 'none';
                    });
                });
            }
        });
        
        // Functions for detail, update status, etc.
        function showDetail(id) {
            alert('Detail untuk ID ' + id + ' (implementasi AJAX)');
        }
        
        function updateStatus(id, status) {
            if (confirm('Yakin ingin mengubah status menjadi ' + status + '?')) {
                window.location.href = `update_status.php?id=${id}&status=${status}`;
            }
        }
        
        function generateNIM(id) {
            if (confirm('Generate NIM untuk peserta ini?')) {
                window.location.href = `generate_nim.php?id=${id}`;
            }
        }
        
        function exportToExcel() {
            alert('Export ke Excel (implementasi menggunakan PHPExcel atau library lain)');
        }
        
        function exportToPDF() {
            alert('Export ke PDF (implementasi menggunakan TCPDF atau DomPDF)');
        }
        
        function printTable() {
            window.print();
        }
    </script>
</body>
</html>