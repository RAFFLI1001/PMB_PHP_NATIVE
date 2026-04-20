<?php
session_start();
require_once '../config/database.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cek login admin
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// ================= TAMBAH SOAL =================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['add'])) {
        $pertanyaan    = mysqli_real_escape_string($conn, $_POST['pertanyaan']);
        $pilihan_a     = mysqli_real_escape_string($conn, $_POST['pilihan_a']);
        $pilihan_b     = mysqli_real_escape_string($conn, $_POST['pilihan_b']);
        $pilihan_c     = mysqli_real_escape_string($conn, $_POST['pilihan_c']);
        $pilihan_d     = mysqli_real_escape_string($conn, $_POST['pilihan_d']);
        $jawaban_benar = $_POST['jawaban_benar'];
        $kategori      = mysqli_real_escape_string($conn, $_POST['kategori']);
        $id_jurusan    = intval($_POST['id_jurusan']);

        $query = "INSERT INTO soal_test 
        (pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar, kategori, id_jurusan) 
        VALUES 
        ('$pertanyaan','$pilihan_a','$pilihan_b','$pilihan_c','$pilihan_d','$jawaban_benar','$kategori','$id_jurusan')";

        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Soal berhasil ditambahkan!";
        } else {
            $_SESSION['error'] = "Gagal: " . mysqli_error($conn);
        }

        header("Location: soal_test.php");
        exit();
    }

    // ================= UPDATE SOAL =================
    if (isset($_POST['update'])) {
        $id_soal       = intval($_POST['id_soal']);
        $pertanyaan    = mysqli_real_escape_string($conn, $_POST['pertanyaan']);
        $pilihan_a     = mysqli_real_escape_string($conn, $_POST['pilihan_a']);
        $pilihan_b     = mysqli_real_escape_string($conn, $_POST['pilihan_b']);
        $pilihan_c     = mysqli_real_escape_string($conn, $_POST['pilihan_c']);
        $pilihan_d     = mysqli_real_escape_string($conn, $_POST['pilihan_d']);
        $jawaban_benar = $_POST['jawaban_benar'];
        $kategori      = mysqli_real_escape_string($conn, $_POST['kategori']);

        $query = "UPDATE soal_test SET 
            pertanyaan='$pertanyaan', pilihan_a='$pilihan_a', pilihan_b='$pilihan_b',
            pilihan_c='$pilihan_c', pilihan_d='$pilihan_d', jawaban_benar='$jawaban_benar',
            kategori='$kategori'
            WHERE id_soal=$id_soal";

        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Soal berhasil diperbarui!";
        } else {
            $_SESSION['error'] = "Gagal update: " . mysqli_error($conn);
        }

        header("Location: soal_test.php");
        exit();
    }

    // ================= UPDATE TIMER =================
    if (isset($_POST['update_timer'])) {
        $durasi = intval($_POST['durasi_menit']);
        mysqli_query($conn, "UPDATE pengaturan_ujian SET durasi_menit='$durasi'");
        $_SESSION['success'] = "Waktu ujian berhasil diubah!";
        header("Location: soal_test.php");
        exit();
    }
}

// ================= HAPUS =================
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM soal_test WHERE id_soal = $id");
    $_SESSION['success'] = "Soal berhasil dihapus";
    header("Location: soal_test.php");
    exit();
}

// ================= FILTER =================
$search          = '';
$filter_kategori = '';
$filter_jurusan  = '';
$where_conditions = [];
$query_params     = [];

if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_conditions[] = "(s.pertanyaan LIKE '%$search%' OR s.pilihan_a LIKE '%$search%' OR s.pilihan_b LIKE '%$search%')";
    $query_params['search'] = $search;
}

if (!empty($_GET['kategori'])) {
    $filter_kategori = mysqli_real_escape_string($conn, $_GET['kategori']);
    $where_conditions[] = "s.kategori = '$filter_kategori'";
    $query_params['kategori'] = $filter_kategori;
}

if (!empty($_GET['id_jurusan'])) {
    $filter_jurusan = intval($_GET['id_jurusan']);
    $where_conditions[] = "s.id_jurusan = '$filter_jurusan'";
    $query_params['id_jurusan'] = $filter_jurusan;
}

$where = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// ================= PAGINATION =================
$limit      = 10;
$page       = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset     = ($page - 1) * $limit;

$count_query  = "SELECT COUNT(*) as total FROM soal_test s $where";
$count_result = mysqli_query($conn, $count_query);
$total_rows   = mysqli_fetch_assoc($count_result)['total'];
$total_pages  = ceil($total_rows / $limit);

// ================= DATA =================
$query = "SELECT s.*, j.nama_jurusan 
FROM soal_test s
LEFT JOIN jurusan j ON s.id_jurusan = j.id_jurusan 
$where 
ORDER BY s.id_soal DESC 
LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);

// ================= STATISTIK =================
$kategori_stats = [];
$kategori_query  = "SELECT kategori, COUNT(*) as total FROM soal_test GROUP BY kategori";
$kategori_result = mysqli_query($conn, $kategori_query);
while ($row = mysqli_fetch_assoc($kategori_result)) {
    $kategori_stats[] = $row;
}

// ================= TIMER =================
$cek_timer = mysqli_query($conn, "SHOW TABLES LIKE 'pengaturan_ujian'");
if (mysqli_num_rows($cek_timer) == 0) {
    mysqli_query($conn, "CREATE TABLE pengaturan_ujian (
        id INT AUTO_INCREMENT PRIMARY KEY,
        durasi_menit INT DEFAULT 60
    )");
    mysqli_query($conn, "INSERT INTO pengaturan_ujian (durasi_menit) VALUES (60)");
}

$timer_query = mysqli_query($conn, "SELECT * FROM pengaturan_ujian LIMIT 1");
$timer_data  = mysqli_fetch_assoc($timer_query);

// ================= JURUSAN UNTUK FILTER =================
$jurusan_list = [];
$jurusan_query = mysqli_query($conn, "SELECT * FROM jurusan ORDER BY nama_jurusan");
while ($j = mysqli_fetch_assoc($jurusan_query)) {
    $jurusan_list[] = $j;
}

// Nama jurusan untuk filter tag
$nama_jurusan_filter = '';
if (!empty($filter_jurusan)) {
    foreach ($jurusan_list as $j) {
        if ($j['id_jurusan'] == $filter_jurusan) {
            $nama_jurusan_filter = $j['nama_jurusan'];
            break;
        }
    }
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
            overflow-y: auto;
        }

        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0 !important; }
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .admin-profile { display: flex; align-items: center; gap: 1rem; }

        .admin-avatar {
            width: 50px; height: 50px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            border: 2px solid rgba(255,255,255,0.2);
            flex-shrink: 0;
        }

        .admin-info h5 { font-size: 1rem; font-weight: 600; margin-bottom: 0.25rem; }
        .admin-info p  { font-size: 0.875rem; opacity: 0.8; margin: 0; }

        .sidebar-nav { padding: 1rem; }

        .nav-link {
            display: flex; align-items: center;
            padding: 0.875rem 1rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .nav-icon { width: 24px; margin-right: 12px; font-size: 1.1rem; }

        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        @media (max-width: 992px) { .main-content { margin-left: 0; } }

        .top-nav {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: sticky; top: 0; z-index: 100;
        }

        .sidebar-toggle {
            display: none;
            position: fixed; bottom: 20px; right: 20px;
            width: 50px; height: 50px;
            border-radius: 50%;
            background: var(--utn-primary);
            color: white; border: none;
            z-index: 1001;
            box-shadow: 0 4px 12px rgba(0,51,102,0.3);
        }

        @media (max-width: 992px) {
            .sidebar-toggle { display: flex; align-items: center; justify-content: center; }
        }

        .dashboard-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }

        .card-title {
            color: var(--utn-dark);
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #F1F5F9;
        }

        .search-filter-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }

        .form-control, .form-select {
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--utn-primary);
            box-shadow: 0 0 0 3px rgba(0,51,102,0.1);
        }

        .btn-utn-primary {
            background: var(--utn-primary);
            color: white; border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px; font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-utn-primary:hover {
            background: #004080; color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,51,102,0.2);
        }

        .btn-utn-secondary {
            background: var(--utn-secondary);
            color: white; border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px; font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-utn-secondary:hover {
            background: #FF8C42; color: white;
            transform: translateY(-2px);
        }

        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-left: 4px solid var(--utn-primary);
            height: 100%;
        }

        .stats-value { font-size: 1.5rem; font-weight: 700; color: var(--utn-dark); margin-bottom: 0.25rem; }
        .stats-label { font-size: 0.875rem; color: #64748B; }

        /* Table */
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .data-table thead { background: #F8FAFC; }
        .data-table th {
            padding: 1rem; font-weight: 600; color: var(--utn-dark);
            border-bottom: 2px solid #F1F5F9;
            text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;
        }
        .data-table td {
            padding: 1rem; border-bottom: 1px solid #F1F5F9;
            color: #475569; vertical-align: middle;
        }
        .data-table tbody tr { transition: background 0.3s ease; }
        .data-table tbody tr:hover { background: #F8FAFC; }

        /* Badges */
        .badge-category {
            padding: 0.25rem 0.75rem; border-radius: 20px;
            font-size: 0.75rem; font-weight: 600;
        }
        .badge-primary { background: rgba(0,51,102,0.1); color: var(--utn-primary); }
        .badge-success { background: rgba(16,185,129,0.1); color: var(--utn-success); }
        .badge-warning { background: rgba(245,158,11,0.1); color: var(--utn-warning); }
        .badge-danger  { background: rgba(239,68,68,0.1);  color: var(--utn-danger); }
        .badge-info    { background: rgba(59,130,246,0.1);  color: var(--utn-info); }
        .badge-jurusan { background: rgba(139,92,246,0.1);  color: #7C3AED; }

        /* Action Buttons */
        .btn-action {
            width: 36px; height: 36px; border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            margin: 0 2px; transition: all 0.3s ease;
        }
        .btn-action:hover { transform: translateY(-2px); }

        /* Pagination */
        .pagination-custom .page-link {
            color: var(--utn-primary); border: 1px solid #E2E8F0;
            border-radius: 6px; margin: 0 2px;
        }
        .pagination-custom .page-item.active .page-link {
            background: var(--utn-primary); border-color: var(--utn-primary); color: white;
        }
        .pagination-custom .page-link:hover { background: #F1F5F9; border-color: var(--utn-primary); }

        /* Modal */
        .modal-header { background: var(--utn-primary); color: white; }
        .modal-header .btn-close { filter: invert(1); }

        .form-label { font-weight: 500; color: var(--utn-dark); margin-bottom: 0.5rem; }
        .required::after { content: " *"; color: var(--utn-danger); }

        /* Question Preview */
        .question-preview { background: #F8FAFC; border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem; }
        .option {
            background: white; border: 1px solid #E2E8F0; border-radius: 6px;
            padding: 0.75rem 1rem; margin-bottom: 0.5rem;
            display: flex; align-items: center;
        }
        .option.correct { border-color: var(--utn-success); background: rgba(16,185,129,0.05); }
        .option-label {
            width: 30px; height: 30px; border-radius: 50%; background: #E2E8F0;
            display: flex; align-items: center; justify-content: center;
            margin-right: 1rem; font-weight: 600; flex-shrink: 0;
        }
        .option.correct .option-label { background: var(--utn-success); color: white; }

        /* Empty State */
        .empty-state { text-align: center; padding: 3rem 1rem; color: #94A3B8; }
        .empty-state i { font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; }

        /* Alert */
        .alert-custom { border-radius: 10px; border: none; padding: 1rem 1.5rem; }

        /* Kategori Progress */
        .kategori-progress { background: #F8FAFC; border-radius: 8px; padding: 1rem; }
        .kategori-item {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 0.75rem; padding-bottom: 0.75rem;
            border-bottom: 1px solid #F1F5F9;
        }
        .kategori-item:last-child { margin-bottom: 0; border-bottom: none; }

        /* Tombol Tambah Soal */
        .btn-add-soal {
            background: linear-gradient(135deg, var(--utn-primary), #1A4A8A);
            color: white; border: none;
            padding: 0.75rem 1.75rem;
            border-radius: 10px; font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,51,102,0.3);
            display: inline-flex; align-items: center; gap: 0.5rem;
        }
        .btn-add-soal:hover {
            background: linear-gradient(135deg, #004080, #1E5BA8);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,51,102,0.4);
        }
        .btn-add-soal i { font-size: 1rem; }

        /* Modal Tambah Soal - lebih lebar */
        .modal-tambah .modal-dialog { max-width: 720px; }
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
                    <p>Universitas Arten</p>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-link">
                <i class="fas fa-tachometer-alt nav-icon"></i><span>Dashboard</span>
            </a>
            <a href="data_maba.php" class="nav-link">
                <i class="fas fa-users nav-icon"></i><span>Data Calon Maba</span>
            </a>
            <a href="soal_test.php" class="nav-link active">
                <i class="fas fa-question-circle nav-icon"></i><span>Soal Test</span>
            </a>
            <a href="hasil_test.php" class="nav-link">
                <i class="fas fa-chart-bar nav-icon"></i><span>Hasil Test</span>
            </a>
            <a href="daftar_ulang.php" class="nav-link">
                <i class="fas fa-check-double nav-icon"></i><span>Daftar Ulang</span>
            </a>
            <a href="../logout.php" class="nav-link mt-4">
                <i class="fas fa-sign-out-alt nav-icon"></i><span>Logout</span>
            </a>
        </nav>
    </aside>

    <!-- Mobile Toggle -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Nav -->
        <header class="top-nav">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Manajemen Soal Test</h1>
                        <p class="text-muted mb-0">Kelola soal ujian penerimaan mahasiswa baru</p>
                    </div>
                    
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="container-fluid py-4">

            <!-- Alerts -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-custom mb-4">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-custom mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Stats Cards -->
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
                        <div class="stats-value"><?php echo count($kategori_stats); ?></div>
                        <div class="stats-label">Kategori Soal</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card" style="border-left-color: var(--utn-warning);">
                        <?php $avg = count($kategori_stats) > 0 ? round($total_soal / count($kategori_stats), 1) : 0; ?>
                        <div class="stats-value"><?php echo $avg; ?></div>
                        <div class="stats-label">Rata-rata per Kategori</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card" style="border-left-color: var(--utn-info);">
                        <div class="stats-value"><?php echo count($jurusan_list); ?></div>
                        <div class="stats-label">Total Jurusan</div>
                    </div>
                </div>
            </div>

            <!-- Layout Row -->
            <div class="row">

                <!-- Left Column: Kategori Stats + Pengaturan Timer -->
                <div class="col-lg-4 mb-4">

                    <!-- Distribusi Kategori -->
                    <div class="dashboard-card">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie me-2"></i>Distribusi Kategori
                        </h3>
                        <div class="kategori-progress">
                            <?php if (!empty($kategori_stats)): ?>
                                <?php foreach ($kategori_stats as $kat):
                                    $pct = $total_soal > 0 ? round(($kat['total'] / $total_soal) * 100) : 0;
                                ?>
                                <div class="kategori-item">
                                    <div>
                                        <div class="fw-medium"><?php echo $kat['kategori']; ?></div>
                                        <small class="text-muted"><?php echo $pct; ?>% dari total</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold"><?php echo $kat['total']; ?></div>
                                        <div class="progress mt-1" style="width: 100px; height: 6px;">
                                            <div class="progress-bar" role="progressbar" style="width: <?php echo $pct; ?>%;"></div>
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

                    <!-- Pengaturan Waktu Ujian -->
                    <div class="dashboard-card">
                        <h3 class="card-title">
                            <i class="fas fa-clock me-2"></i>Pengaturan Waktu Ujian
                        </h3>
                        <form method="POST" class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Durasi Ujian (Menit)</label>
                                <input type="number" name="durasi_menit" class="form-control"
                                       value="<?php echo $timer_data['durasi_menit']; ?>" required min="1">
                            </div>
                            <div class="col-12">
                                <button type="submit" name="update_timer" class="btn btn-utn-primary w-100">
                                    <i class="fas fa-save me-1"></i>Simpan Waktu
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

                <!-- Right Column: Daftar Soal -->
                <div class="col-lg-8 mb-4">

                    <!-- Search, Filter & Tombol Tambah -->
                    <div class="search-filter-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-semibold text-dark">
                                <i class="fas fa-filter me-2 text-muted"></i>Filter Soal
                            </h5>
                            <!-- ===== TOMBOL TAMBAH SOAL ===== -->
                            <button class="btn-add-soal" data-bs-toggle="modal" data-bs-target="#modalTambahSoal">
                                <i class="fas fa-plus-circle"></i> Tambah Soal
                            </button>
                        </div>

                        <form method="GET" action="" class="row g-2">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="search"
                                       placeholder="Cari pertanyaan..."
                                       value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="kategori">
                                    <option value="">Semua Kategori</option>
                                    <?php
                                    $kategori_opts = ['Umum','Matematika','Bahasa Indonesia','Bahasa Inggris','Logika','IPA','IPS'];
                                    foreach ($kategori_opts as $opt):
                                    ?>
                                    <option value="<?php echo $opt; ?>" <?php echo $filter_kategori == $opt ? 'selected' : ''; ?>>
                                        <?php echo $opt; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="id_jurusan">
                                    <option value="">Semua Jurusan</option>
                                    <?php foreach ($jurusan_list as $j): ?>
                                    <option value="<?php echo $j['id_jurusan']; ?>"
                                        <?php echo $filter_jurusan == $j['id_jurusan'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($j['nama_jurusan']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-utn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Cari
                                </button>
                            </div>
                        </form>

                        <!-- Filter Tags -->
                        <?php if (!empty($search) || !empty($filter_kategori) || !empty($filter_jurusan)): ?>
                        <div class="mt-3">
                            <small class="text-muted">Filter aktif:</small>
                            <?php if (!empty($search)): ?>
                                <span class="badge bg-light text-dark me-2">
                                    <i class="fas fa-search me-1"></i>"<?php echo htmlspecialchars($search); ?>"
                                    <a href="?<?php echo http_build_query(array_diff_key($query_params, ['search'=>''])); ?>"
                                       class="ms-1 text-danger"><i class="fas fa-times"></i></a>
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($filter_kategori)): ?>
                                <span class="badge bg-light text-dark me-2">
                                    <i class="fas fa-tag me-1"></i><?php echo $filter_kategori; ?>
                                    <a href="?<?php echo http_build_query(array_diff_key($query_params, ['kategori'=>''])); ?>"
                                       class="ms-1 text-danger"><i class="fas fa-times"></i></a>
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($filter_jurusan)): ?>
                                <span class="badge bg-light text-dark me-2">
                                    <i class="fas fa-graduation-cap me-1"></i><?php echo htmlspecialchars($nama_jurusan_filter); ?>
                                    <a href="?<?php echo http_build_query(array_diff_key($query_params, ['id_jurusan'=>''])); ?>"
                                       class="ms-1 text-danger"><i class="fas fa-times"></i></a>
                                </span>
                            <?php endif; ?>
                            <a href="soal_test.php" class="badge bg-danger text-white text-decoration-none ms-1">
                                <i class="fas fa-times me-1"></i>Hapus Semua Filter
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Daftar Soal -->
                    <div class="dashboard-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>Daftar Soal
                            </h3>
                            <div class="text-muted small">
                                Menampilkan <?php echo min($limit, max(0, $total_rows - $offset)); ?> dari <?php echo $total_rows; ?> soal
                            </div>
                        </div>

                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th width="50">ID</th>
                                            <th>Pertanyaan</th>
                                            <th width="110">Kategori</th>
                                            <th width="110">Jurusan</th>
                                            <th width="70">Jwb</th>
                                            <th width="120">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($soal = mysqli_fetch_assoc($result)):
                                            $jwb_map = ['a'=>'A','b'=>'B','c'=>'C','d'=>'D'];
                                            $jwb_text = $jwb_map[$soal['jawaban_benar']] ?? '-';
                                        ?>
                                        <tr>
                                            <td><span class="badge bg-light text-dark">#<?php echo $soal['id_soal']; ?></span></td>
                                            <td>
                                                <div class="fw-medium" style="max-width:260px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                                    <?php echo htmlspecialchars($soal['pertanyaan']); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge-category badge-primary">
                                                    <?php echo htmlspecialchars($soal['kategori']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge-category badge-jurusan" style="font-size:0.7rem;">
                                                    <?php echo htmlspecialchars($soal['nama_jurusan'] ?? '-'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge-category badge-success"><?php echo $jwb_text; ?></span>
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

                                        <!-- ===== PREVIEW MODAL ===== -->
                                        <div class="modal fade" id="previewModal<?php echo $soal['id_soal']; ?>" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            <i class="fas fa-eye me-2"></i>Preview Soal #<?php echo $soal['id_soal']; ?>
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="question-preview">
                                                            <h6 class="fw-bold mb-3"><?php echo htmlspecialchars($soal['pertanyaan']); ?></h6>
                                                            <?php
                                                            $opts = ['a'=>$soal['pilihan_a'],'b'=>$soal['pilihan_b'],'c'=>$soal['pilihan_c'],'d'=>$soal['pilihan_d']];
                                                            foreach ($opts as $k => $v):
                                                                if (!empty($v)):
                                                            ?>
                                                            <div class="option <?php echo $k == $soal['jawaban_benar'] ? 'correct' : ''; ?>">
                                                                <div class="option-label"><?php echo strtoupper($k); ?></div>
                                                                <div><?php echo htmlspecialchars($v); ?></div>
                                                            </div>
                                                            <?php endif; endforeach; ?>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-md-4">
                                                                <span class="fw-medium me-2">Kategori:</span>
                                                                <span class="badge-category badge-primary"><?php echo $soal['kategori']; ?></span>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <span class="fw-medium me-2">Jurusan:</span>
                                                                <span class="badge-category badge-jurusan"><?php echo htmlspecialchars($soal['nama_jurusan'] ?? '-'); ?></span>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <span class="fw-medium me-2">Jawaban:</span>
                                                                <span class="badge-category badge-success"><?php echo strtoupper($soal['jawaban_benar']); ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ===== EDIT MODAL ===== -->
                                        <div class="modal fade" id="editModal<?php echo $soal['id_soal']; ?>" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            <i class="fas fa-edit me-2"></i>Edit Soal #<?php echo $soal['id_soal']; ?>
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id_soal" value="<?php echo $soal['id_soal']; ?>">
                                                            <div class="mb-3">
                                                                <label class="form-label required">Pertanyaan</label>
                                                                <textarea class="form-control" name="pertanyaan" rows="3" required><?php echo htmlspecialchars($soal['pertanyaan']); ?></textarea>
                                                            </div>
                                                            <div class="row g-2 mb-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label required">Pilihan A</label>
                                                                    <input type="text" class="form-control" name="pilihan_a" required value="<?php echo htmlspecialchars($soal['pilihan_a']); ?>">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label required">Pilihan B</label>
                                                                    <input type="text" class="form-control" name="pilihan_b" required value="<?php echo htmlspecialchars($soal['pilihan_b']); ?>">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Pilihan C</label>
                                                                    <input type="text" class="form-control" name="pilihan_c" value="<?php echo htmlspecialchars($soal['pilihan_c']); ?>">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Pilihan D</label>
                                                                    <input type="text" class="form-control" name="pilihan_d" value="<?php echo htmlspecialchars($soal['pilihan_d']); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="row g-3">
                                                                <div class="col-md-4">
                                                                    <label class="form-label required">Jawaban Benar</label>
                                                                    <select class="form-select" name="jawaban_benar" required>
                                                                        <?php foreach(['a','b','c','d'] as $opt): ?>
                                                                        <option value="<?php echo $opt; ?>" <?php echo $soal['jawaban_benar'] == $opt ? 'selected' : ''; ?>>
                                                                            Pilihan <?php echo strtoupper($opt); ?>
                                                                        </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <label class="form-label required">Kategori</label>
                                                                    <select class="form-select" name="kategori" required>
                                                                        <?php foreach(['Umum','Matematika','Bahasa Indonesia','Bahasa Inggris','Logika','IPA','IPS'] as $kat): ?>
                                                                        <option value="<?php echo $kat; ?>" <?php echo $soal['kategori'] == $kat ? 'selected' : ''; ?>>
                                                                            <?php echo $kat; ?>
                                                                        </option>
                                                                        <?php endforeach; ?>
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

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                            <nav class="mt-4">
                                <ul class="pagination justify-content-center pagination-custom">
                                    <?php
                                    $base_url = '?' . (!empty($query_params) ? http_build_query($query_params) . '&' : '');
                                    if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?php echo $base_url; ?>page=<?php echo $page-1; ?>">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    <?php endif;
                                    $sp = max(1, $page - 2);
                                    $ep = min($total_pages, $page + 2);
                                    for ($i = $sp; $i <= $ep; $i++): ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="<?php echo $base_url; ?>page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor;
                                    if ($page < $total_pages): ?>
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
                                <p class="text-muted">Mulai tambahkan soal baru atau ubah filter pencarian</p>
                                <?php if (!empty($search) || !empty($filter_kategori) || !empty($filter_jurusan)): ?>
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


    <!-- ============================================================ -->
    <!-- ===== MODAL TAMBAH SOAL (dipindah ke sini, di luar main) ===== -->
    <!-- ============================================================ -->
    <div class="modal fade modal-tambah" id="modalTambahSoal" tabindex="-1" aria-labelledby="labelTambahSoal" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="labelTambahSoal">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Soal Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="" id="addQuestionForm">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label required">Pertanyaan</label>
                            <textarea class="form-control" name="pertanyaan" rows="3" required
                                      placeholder="Masukkan pertanyaan di sini..."></textarea>
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
                                <label class="form-label">Pilihan C <small class="text-muted">(opsional)</small></label>
                                <input type="text" class="form-control" name="pilihan_c"
                                       placeholder="Masukkan pilihan C">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pilihan D <small class="text-muted">(opsional)</small></label>
                                <input type="text" class="form-control" name="pilihan_d"
                                       placeholder="Masukkan pilihan D">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label required">Jawaban Benar</label>
                                <select class="form-select" name="jawaban_benar" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="a">Pilihan A</option>
                                    <option value="b">Pilihan B</option>
                                    <option value="c">Pilihan C</option>
                                    <option value="d">Pilihan D</option>
                                </select>
                            </div>
                            <div class="col-md-4">
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
                            <div class="col-md-4">
                                <label class="form-label required">Jurusan</label>
                                <select name="id_jurusan" class="form-select" required>
                                    <option value="">-- Pilih Jurusan --</option>
                                    <?php foreach ($jurusan_list as $j): ?>
                                    <option value="<?php echo $j['id_jurusan']; ?>">
                                        <?php echo htmlspecialchars($j['nama_jurusan']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-1"></i>Reset
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="submit" name="add" class="btn btn-utn-primary">
                            <i class="fas fa-save me-1"></i>Simpan Soal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ===== END MODAL TAMBAH SOAL ===== -->


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Sidebar Toggle Mobile
            const sidebar       = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');

            sidebarToggle.addEventListener('click', function () {
                sidebar.classList.toggle('show');
            });

            document.addEventListener('click', function (e) {
                if (window.innerWidth <= 992) {
                    if (!sidebar.contains(e.target) &&
                        !sidebarToggle.contains(e.target) &&
                        sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                    }
                }
            });

            // Form validation modal tambah soal
            const addForm = document.getElementById('addQuestionForm');
            if (addForm) {
                addForm.addEventListener('submit', function (e) {
                    const jawaban  = addForm.querySelector('select[name="jawaban_benar"]');
                    const kategori = addForm.querySelector('select[name="kategori"]');
                    const jurusan  = addForm.querySelector('select[name="id_jurusan"]');

                    if (!jawaban.value || !kategori.value || !jurusan.value) {
                        e.preventDefault();
                        alert('Harap lengkapi semua field yang wajib diisi (bertanda *)');
                        return false;
                    }
                });
            }

            // Auto-dismiss alert setelah 4 detik
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function (alert) {
                setTimeout(function () {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function () { alert.remove(); }, 500);
                }, 4000);
            });
        });
    </script>
</body>
</html>