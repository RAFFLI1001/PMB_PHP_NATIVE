<?php
require_once '../config/database.php';

session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Handle update status pembayaran
if (isset($_POST['update_status'])) {
    $id     = intval($_POST['id_daftar_ulang']);
    $status = $_POST['status_pembayaran'];
    $query  = "UPDATE daftar_ulang SET status_pembayaran = '$status' WHERE id_daftar_ulang = $id";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Status pembayaran berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Gagal memperbarui status: " . mysqli_error($conn);
    }
    header("Location: daftar_ulang.php");
    exit();
}

if (isset($_POST['verifikasi'])) {
    $id     = intval($_POST['id_daftar_ulang']);
    $status = $_POST['verifikasi'];

    mysqli_query($conn, "UPDATE daftar_ulang SET status_verifikasi = '$status' WHERE id_daftar_ulang = $id");

    if ($status == 'diterima') {
        $data     = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT cm.id_calon 
            FROM daftar_ulang du
            JOIN pendaftaran p ON du.id_pendaftaran = p.id_pendaftaran
            JOIN calon_mahasiswa cm ON p.id_calon = cm.id_calon
            WHERE du.id_daftar_ulang = $id
        "));
        $id_calon = $data['id_calon'];
        $nim      = "UAR" . date("Y") . str_pad($id_calon, 4, "0", STR_PAD_LEFT);
        mysqli_query($conn, "UPDATE calon_mahasiswa SET nim = '$nim' WHERE id_calon = $id_calon");
    }

    header("Location: daftar_ulang.php");
    exit();
}

// Search & filter
$search        = '';
$filter_status = '';
$filter_verifikasi = '';
$where_conditions = [];
$query_params  = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_conditions[] = "(cm.nama_lengkap LIKE '%$search%' OR cm.nim LIKE '%$search%' OR p.no_test LIKE '%$search%')";
    $query_params['search'] = $search;
}

if (isset($_GET['status']) && !empty($_GET['status'])) {
    $filter_status = mysqli_real_escape_string($conn, $_GET['status']);
    $where_conditions[] = "du.status_pembayaran = '$filter_status'";
    $query_params['status'] = $filter_status;
}

if (isset($_GET['verifikasi']) && !empty($_GET['verifikasi'])) {
    $filter_verifikasi = mysqli_real_escape_string($conn, $_GET['verifikasi']);
    $where_conditions[] = "du.status_verifikasi = '$filter_verifikasi'";
    $query_params['verifikasi'] = $filter_verifikasi;
}

$where = '';
if (!empty($where_conditions)) {
    $where = 'WHERE ' . implode(' AND ', $where_conditions);
}

$limit  = 10;
$page   = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$count_result = mysqli_query($conn, "SELECT COUNT(*) as total 
    FROM daftar_ulang du
    JOIN pendaftaran p ON du.id_pendaftaran = p.id_pendaftaran
    JOIN calon_mahasiswa cm ON p.id_calon = cm.id_calon
    $where");
$total_rows  = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

$result = mysqli_query($conn, "SELECT 
    du.id_daftar_ulang, du.status_pembayaran, du.status_verifikasi,
    du.bukti_pembayaran, du.upload_ktp, du.upload_kk,
    p.no_test,
    cm.nama_lengkap, cm.email, cm.asal_sekolah, cm.nim,
    j.nama_jurusan
FROM daftar_ulang du
JOIN pendaftaran p ON du.id_pendaftaran = p.id_pendaftaran
JOIN calon_mahasiswa cm ON p.id_calon = cm.id_calon
JOIN jurusan j ON p.id_jurusan = j.id_jurusan
$where
ORDER BY du.id_daftar_ulang DESC
LIMIT $limit OFFSET $offset");

$total_daftar_ulang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM daftar_ulang"))['total'];
$total_lunas        = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM daftar_ulang WHERE status_pembayaran = 'lunas'"))['total'];
$total_belum        = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM daftar_ulang WHERE status_pembayaran = 'belum'"))['total'];
$total_lulus        = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran WHERE status = 'lulus'"))['total'];
$total_belum_daftar = $total_lulus - $total_daftar_ulang;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Ulang - Admin PMB</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --utn-primary:   #003366;
            --utn-secondary: #1A4A8A;
            --utn-success:   #10B981;
            --utn-info:      #3B82F6;
            --utn-warning:   #F59E0B;
            --utn-danger:    #EF4444;
            --utn-dark:      #1E293B;
        }

        body { font-family: 'Inter', sans-serif; background-color: #F1F5F9; color: #334155; }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed; top: 0; left: 0; height: 100vh; width: 280px;
            background: linear-gradient(135deg, #003366 0%, #1A2B4D 100%);
            color: white; z-index: 1000; box-shadow: 4px 0 20px rgba(0,0,0,.1);
            transition: transform .3s ease;
        }
        @media (max-width:992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0 !important; }
        }
        .sidebar-header { padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,.1); }
        .admin-profile  { display: flex; align-items: center; gap: 1rem; }
        .admin-avatar   { width:50px;height:50px;border-radius:50%;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;font-size:1.5rem;border:2px solid rgba(255,255,255,.2); }
        .admin-info h5  { font-size:1rem;font-weight:600;margin-bottom:.25rem; }
        .admin-info p   { font-size:.875rem;opacity:.8;margin:0; }
        .sidebar-nav    { padding:1rem; }
        .nav-link       { display:flex;align-items:center;padding:.875rem 1rem;color:rgba(255,255,255,.8);text-decoration:none;border-radius:8px;margin-bottom:.5rem;transition:all .3s ease; }
        .nav-link:hover,.nav-link.active { background:rgba(255,255,255,.1);color:white; }
        .nav-link.active { background:rgba(255,255,255,.15); }
        .nav-icon { width:24px;margin-right:12px;font-size:1.1rem; }

        /* ── Main ── */
        .main-content { margin-left:280px; min-height:100vh; transition:margin-left .3s ease; }
        @media (max-width:992px){ .main-content{margin-left:0;} }

        .top-nav { background:white;padding:1rem 2rem;box-shadow:0 1px 3px rgba(0,0,0,.1);position:sticky;top:0;z-index:100; }

        .sidebar-toggle {
            display:none;position:fixed;bottom:20px;right:20px;
            width:50px;height:50px;border-radius:50%;
            background:var(--utn-primary);color:white;border:none;
            z-index:1001;box-shadow:0 4px 12px rgba(0,51,102,.3);
        }
        @media (max-width:992px){ .sidebar-toggle{display:flex;align-items:center;justify-content:center;} }

        /* ── Cards ── */
        .dashboard-card {
            background:white;border-radius:12px;padding:1.5rem;
            box-shadow:0 4px 20px rgba(0,0,0,.05);margin-bottom:1.5rem;
        }
        .search-filter-card {
            background:white;border-radius:12px;padding:1.5rem;
            box-shadow:0 4px 20px rgba(0,0,0,.05);margin-bottom:1.5rem;
        }
        .card-title { color:var(--utn-dark);font-weight:600;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid #F1F5F9; }

        /* ── Stats ── */
        .stats-card {
            background:white;border-radius:12px;padding:1.25rem;
            box-shadow:0 2px 8px rgba(0,0,0,.05);border-left:4px solid var(--utn-primary);height:100%;
        }
        .stats-card-success { border-left-color:var(--utn-success); }
        .stats-card-warning { border-left-color:var(--utn-warning); }
        .stats-card-danger  { border-left-color:var(--utn-danger);  }
        .stats-card-info    { border-left-color:var(--utn-info);    }
        .stats-icon {
            width:42px;height:42px;border-radius:10px;
            display:flex;align-items:center;justify-content:center;font-size:1.1rem;
        }
        .stats-value { font-size:1.75rem;font-weight:700;color:var(--utn-dark);line-height:1; }
        .stats-label { font-size:.8rem;color:#64748B;margin-top:.25rem; }
        .stats-sub   { font-size:.75rem;font-weight:600;color:var(--utn-primary);margin-top:.5rem; }

        /* ── Form ── */
        .form-control,.form-select { border:1px solid #E2E8F0;border-radius:8px;padding:.65rem 1rem;font-size:.9rem; }
        .form-control:focus,.form-select:focus { border-color:var(--utn-primary);box-shadow:0 0 0 3px rgba(0,51,102,.1); }
        .form-label { font-size:.85rem;font-weight:500;color:#475569;margin-bottom:.35rem; }

        /* ── Buttons ── */
        .btn-utn-primary   { background:var(--utn-primary);color:white;border:none;padding:.65rem 1.4rem;border-radius:8px;font-weight:500;transition:all .3s ease;font-size:.9rem; }
        .btn-utn-primary:hover   { background:#004080;color:white;transform:translateY(-1px); }
        .btn-utn-secondary { background:#1A4A8A;color:white;border:none;padding:.65rem 1.4rem;border-radius:8px;font-weight:500;transition:all .3s ease;font-size:.9rem; }
        .btn-utn-secondary:hover { background:#153d75;color:white;transform:translateY(-1px); }

        /* ── Table ── */
        .data-table { width:100%;border-collapse:separate;border-spacing:0; }
        .data-table thead { background:#F8FAFC; }
        .data-table th { padding:.875rem 1rem;font-weight:600;color:var(--utn-dark);border-bottom:2px solid #F1F5F9;text-transform:uppercase;font-size:.7rem;letter-spacing:.06em;white-space:nowrap; }
        .data-table td { padding:.875rem 1rem;border-bottom:1px solid #F1F5F9;color:#475569;vertical-align:middle; }
        .data-table tbody tr:hover { background:#F8FAFC; }

        /* ── Badges / Status ── */
        .status-badge { padding:.3rem .8rem;border-radius:20px;font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;display:inline-block; }
        .badge-success { background:rgba(16,185,129,.12);color:#059669; }
        .badge-warning { background:rgba(245,158,11,.12);color:#D97706; }
        .badge-danger  { background:rgba(239,68,68,.12);color:#DC2626;  }
        .badge-info    { background:rgba(59,130,246,.12);color:#2563EB; }
        .badge-primary { background:rgba(0,51,102,.1);color:var(--utn-primary); }

        /* ── NIM ── */
        .nim-tag {
            font-family: 'Courier New', monospace;
            font-size:.8rem;font-weight:700;letter-spacing:.5px;
            background:linear-gradient(135deg,#003366,#1A2B4D);
            color:white;padding:.3rem .65rem;border-radius:6px;display:inline-block;
        }
        .nim-empty { font-size:.8rem;color:#94A3B8;font-style:italic; }

        /* ── File buttons ── */
        .btn-file {
            width:34px;height:34px;border-radius:8px;
            display:inline-flex;align-items:center;justify-content:center;
            font-size:.85rem;transition:all .25s ease;border-width:1.5px;
        }
        .btn-file:hover { transform:translateY(-2px); }

        /* ── Verifikasi buttons ── */
        .btn-verif {
            padding:.3rem .75rem;border-radius:6px;font-size:.78rem;font-weight:600;
            border:none;cursor:pointer;transition:all .25s ease;
        }
        .btn-terima { background:rgba(16,185,129,.15);color:#059669; }
        .btn-terima:hover { background:#059669;color:white; }
        .btn-tolak  { background:rgba(239,68,68,.12);color:#DC2626; }
        .btn-tolak:hover  { background:#DC2626;color:white; }

        /* ── Pagination ── */
        .pagination-custom .page-link { color:var(--utn-primary);border:1px solid #E2E8F0;border-radius:6px;margin:0 2px; }
        .pagination-custom .page-item.active .page-link { background:var(--utn-primary);border-color:var(--utn-primary);color:white; }
        .pagination-custom .page-link:hover { background:#F1F5F9;border-color:var(--utn-primary); }

        /* ── Empty state ── */
        .empty-state { text-align:center;padding:3rem 1rem;color:#94A3B8; }
        .empty-state i { font-size:3rem;margin-bottom:1rem;opacity:.4; }

        /* ── Alert ── */
        .alert-custom { border-radius:10px;border:none;padding:1rem 1.5rem; }

        /* ── Modal header override ── */
        .modal-header-dark { background:var(--utn-primary);color:white; }
        .modal-header-dark .btn-close { filter:invert(1); }
    </style>
</head>
<body>

<!-- ============================= SIDEBAR ============================= -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="admin-profile">
            <div class="admin-avatar"><i class="fas fa-user-shield"></i></div>
            <div class="admin-info">
                <h5><?php echo $_SESSION['admin_nama'] ?? 'Administrator'; ?></h5>
                <p>Universitas Arten</p>
            </div>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php"   class="nav-link"><i class="fas fa-tachometer-alt nav-icon"></i><span>Dashboard</span></a>
        <a href="data_maba.php"   class="nav-link"><i class="fas fa-users nav-icon"></i><span>Data Calon Maba</span></a>
        <a href="soal_test.php"   class="nav-link"><i class="fas fa-question-circle nav-icon"></i><span>Soal Test</span></a>
        <a href="hasil_test.php"  class="nav-link"><i class="fas fa-chart-bar nav-icon"></i><span>Hasil Test</span></a>
        <a href="daftar_ulang.php" class="nav-link active"><i class="fas fa-check-double nav-icon"></i><span>Daftar Ulang</span></a>
        <a href="../logout.php"   class="nav-link mt-4"><i class="fas fa-sign-out-alt nav-icon"></i><span>Logout</span></a>
    </nav>
</aside>

<button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>

<!-- ============================= MAIN ============================= -->
<main class="main-content">

    <!-- Top Nav -->
    <header class="top-nav">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Kelola Daftar Ulang</h1>
                    <p class="text-muted mb-0 small">Verifikasi pembayaran dan kelengkapan berkas mahasiswa baru</p>
                </div>
                <a href="export_daftar_ulang.php?<?php echo http_build_query($_GET); ?>"
                   class="btn btn-sm btn-success d-flex align-items-center gap-2">
                    <i class="fas fa-file-excel"></i><span>Export Excel</span>
                </a>
            </div>
        </div>
    </header>

    <div class="container-fluid py-4">

        <!-- Alert -->
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-custom mb-4">
                <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-custom mb-4">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- ── Statistics ── -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stats-value"><?php echo $total_daftar_ulang; ?></div>
                            <div class="stats-label">Sudah Daftar Ulang</div>
                        </div>
                        <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="stats-sub">
                        <?php echo $total_lulus > 0 ? round($total_daftar_ulang/$total_lulus*100,1) : 0; ?>% dari yang lulus
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stats-card stats-card-success">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stats-value"><?php echo $total_lunas; ?></div>
                            <div class="stats-label">Pembayaran Lunas</div>
                        </div>
                        <div class="stats-icon" style="background:rgba(16,185,129,.1);color:#10B981;">
                            <i class="fas fa-money-check-alt"></i>
                        </div>
                    </div>
                    <div class="stats-sub" style="color:#059669;">
                        <?php echo $total_daftar_ulang > 0 ? round($total_lunas/$total_daftar_ulang*100,1) : 0; ?>% dari daftar ulang
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stats-card stats-card-warning">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stats-value"><?php echo $total_belum_daftar; ?></div>
                            <div class="stats-label">Belum Daftar Ulang</div>
                        </div>
                        <div class="stats-icon" style="background:rgba(245,158,11,.1);color:#F59E0B;">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="stats-sub" style="color:#D97706;">
                        <?php echo $total_lulus > 0 ? round($total_belum_daftar/$total_lulus*100,1) : 0; ?>% dari yang lulus
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stats-card stats-card-danger">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stats-value"><?php echo $total_belum; ?></div>
                            <div class="stats-label">Belum Bayar</div>
                        </div>
                        <div class="stats-icon" style="background:rgba(239,68,68,.1);color:#EF4444;">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                    </div>
                    <div class="stats-sub" style="color:#DC2626;">
                        <?php echo $total_daftar_ulang > 0 ? round($total_belum/$total_daftar_ulang*100,1) : 0; ?>% dari daftar ulang
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Search & Filter ── -->
        <div class="search-filter-card">
            <form method="GET" action="">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Cari Mahasiswa</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted" style="font-size:.85rem;"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0"
                                   placeholder="Nama, NIM, atau No Test..."
                                   value="<?php echo htmlspecialchars($search); ?>"
                                   style="border-left:none;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status Verifikasi</label>
                        <select name="verifikasi" class="form-select">
                            <option value="">Semua Verifikasi</option>
                            <option value="menunggu"  <?php if($filter_verifikasi=='menunggu')  echo 'selected'; ?>>Menunggu</option>
                            <option value="diterima"  <?php if($filter_verifikasi=='diterima')  echo 'selected'; ?>>Diterima</option>
                            <option value="ditolak"   <?php if($filter_verifikasi=='ditolak')   echo 'selected'; ?>>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status Pembayaran</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="lunas" <?php if($filter_status=='lunas') echo 'selected'; ?>>Lunas</option>
                            <option value="belum" <?php if($filter_status=='belum') echo 'selected'; ?>>Belum Bayar</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-utn-primary flex-fill">
                            <i class="fas fa-search me-1"></i>Cari
                        </button>
                        <a href="daftar_ulang.php" class="btn btn-outline-secondary" title="Reset Filter">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </div>

                <!-- Active filter tags -->
                <?php if(!empty($search) || !empty($filter_status) || !empty($filter_verifikasi)): ?>
                <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
                    <small class="text-muted">Filter aktif:</small>
                    <?php if(!empty($search)): ?>
                        <span class="badge bg-light text-dark border">
                            <i class="fas fa-search me-1 text-primary"></i>"<?php echo htmlspecialchars($search); ?>"
                        </span>
                    <?php endif; ?>
                    <?php if(!empty($filter_verifikasi)): ?>
                        <span class="badge bg-light text-dark border">
                            <i class="fas fa-shield-alt me-1 text-info"></i>Verifikasi: <?php echo ucfirst($filter_verifikasi); ?>
                        </span>
                    <?php endif; ?>
                    <?php if(!empty($filter_status)): ?>
                        <span class="badge bg-light text-dark border">
                            <i class="fas fa-money-bill me-1 text-success"></i>Bayar: <?php echo ucfirst($filter_status); ?>
                        </span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- ── Data Table ── -->
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="card-title mb-0">
                    <i class="fas fa-list-alt me-2 text-primary"></i>Data Daftar Ulang
                </h3>
                <span class="text-muted small">
                    Menampilkan <strong><?php echo ($total_rows > 0) ? min($limit, $total_rows - $offset) : 0; ?></strong>
                    dari <strong><?php echo $total_rows; ?></strong> data
                </span>
            </div>

            <?php if(mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="data-table" id="daftarUlangTable">
                    <thead>
                        <tr>
                            <th>NIM</th>
                            <th>Mahasiswa</th>
                            <th>Jurusan</th>
                            <th>Status Bayar</th>
                            <th class="text-center">Bukti</th>
                            <th class="text-center">KTP/Pelajar</th>
                            <th class="text-center">Kartu Keluarga</th>
                            <th>Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)):
                        $lunas     = $row['status_pembayaran'] == 'lunas';
                        $verif     = $row['status_verifikasi'] ?? 'menunggu';
                    ?>
                    <tr>
                        <!-- NIM -->
                        <td>
                            <?php if(!empty($row['nim'])): ?>
                                <span class="nim-tag"><?php echo $row['nim']; ?></span>
                            <?php else: ?>
                                <span class="nim-empty">Belum ada</span>
                            <?php endif; ?>
                        </td>

                        <!-- Mahasiswa -->
                        <td>
                            <div class="fw-semibold text-dark" style="font-size:.9rem;">
                                <?php echo htmlspecialchars($row['nama_lengkap']); ?>
                            </div>
                            <small class="text-muted d-block"><?php echo $row['email']; ?></small>
                            <small class="text-muted"><?php echo $row['asal_sekolah']; ?></small>
                        </td>

                        <!-- Jurusan -->
                        <td>
                            <span class="badge bg-light text-dark px-3 py-2" style="font-size:.8rem;border:1px solid #E2E8F0;">
                                <?php echo $row['nama_jurusan']; ?>
                            </span>
                        </td>

                        <!-- Status Pembayaran -->
                        <td>
                            <?php if($lunas): ?>
                                <span class="status-badge badge-success"><i class="fas fa-check me-1"></i>Lunas</span>
                            <?php else: ?>
                                <span class="status-badge badge-danger"><i class="fas fa-times me-1"></i>Belum Bayar</span>
                            <?php endif; ?>
                        </td>

                        <!-- Bukti Pembayaran -->
                        <td class="text-center">
                            <?php if($row['bukti_pembayaran']): ?>
                                <button class="btn-file btn btn-outline-info" title="Lihat Bukti Pembayaran"
                                    onclick="showFile('<?php echo $row['bukti_pembayaran']; ?>','Bukti Pembayaran','<?php echo htmlspecialchars($row['nama_lengkap']); ?>')">
                                    <i class="fas fa-receipt"></i>
                                </button>
                            <?php else: ?>
                                <span class="text-muted" style="font-size:.8rem;">—</span>
                            <?php endif; ?>
                        </td>

                        <!-- KTP -->
                        <td class="text-center">
                            <?php if($row['upload_ktp']): ?>
                                <button class="btn-file btn btn-outline-success" title="Lihat KTP/Kartu Pelajar"
                                    onclick="showFile('<?php echo $row['upload_ktp']; ?>','KTP / Kartu Pelajar','<?php echo htmlspecialchars($row['nama_lengkap']); ?>')">
                                    <i class="fas fa-id-card"></i>
                                </button>
                            <?php else: ?>
                                <span class="text-muted" style="font-size:.8rem;">—</span>
                            <?php endif; ?>
                        </td>

                        <!-- KK -->
                        <td class="text-center">
                            <?php if($row['upload_kk']): ?>
                                <button class="btn-file btn btn-outline-warning" title="Lihat Kartu Keluarga"
                                    onclick="showFile('<?php echo $row['upload_kk']; ?>','Kartu Keluarga','<?php echo htmlspecialchars($row['nama_lengkap']); ?>')">
                                    <i class="fas fa-users"></i>
                                </button>
                            <?php else: ?>
                                <span class="text-muted" style="font-size:.8rem;">—</span>
                            <?php endif; ?>
                        </td>

                        <!-- Verifikasi -->
                        <td>
                            <?php if($verif == 'menunggu'): ?>
                                <form method="POST" class="d-flex gap-1">
                                    <input type="hidden" name="id_daftar_ulang" value="<?php echo $row['id_daftar_ulang']; ?>">
                                    <button type="submit" name="verifikasi" value="diterima" class="btn-verif btn-terima">
                                        <i class="fas fa-check me-1"></i>Terima
                                    </button>
                                    <button type="submit" name="verifikasi" value="ditolak" class="btn-verif btn-tolak">
                                        <i class="fas fa-times me-1"></i>Tolak
                                    </button>
                                </form>
                            <?php elseif($verif == 'diterima'): ?>
                                <span class="status-badge badge-success"><i class="fas fa-check-circle me-1"></i>Diterima</span>
                            <?php else: ?>
                                <span class="status-badge badge-danger"><i class="fas fa-times-circle me-1"></i>Ditolak</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if($total_pages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center pagination-custom">
                    <?php $base_url = '?' . (!empty($query_params) ? http_build_query($query_params) . '&' : ''); ?>
                    <?php if($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo $base_url; ?>page=<?php echo $page-1; ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php for($i = max(1,$page-2); $i <= min($total_pages,$page+2); $i++): ?>
                        <li class="page-item <?php echo $i==$page ? 'active':''; ?>">
                            <a class="page-link" href="<?php echo $base_url; ?>page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if($page < $total_pages): ?>
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
                <h5 class="mb-1">Belum ada data daftar ulang</h5>
                <p class="text-muted small">Data akan muncul setelah mahasiswa melakukan daftar ulang</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <footer class="mt-2 pt-3 border-top text-center text-muted">
            <p class="mb-0 small">PMB Universitas Arten</p>
            <small>Terakhir diakses: <?php echo date('d/m/Y H:i'); ?></small>
        </footer>

    </div><!-- /container -->
</main>

<!-- ============================= MODAL FILE VIEWER ============================= -->
<div class="modal fade" id="buktiModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-dark">
                <h5 class="modal-title" id="buktiModalTitle">
                    <i class="fas fa-file-image me-2"></i>Lihat Dokumen
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-3">
                <img id="buktiImage" src="" class="img-fluid rounded" style="max-height:520px;object-fit:contain;">
            </div>
            <div class="modal-footer justify-content-between">
                <small class="text-muted" id="buktiNama"></small>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Tutup
                    </button>
                    <a id="downloadBukti" href="#" class="btn btn-sm btn-utn-secondary" download>
                        <i class="fas fa-download me-1"></i>Download
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar       = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');

    sidebarToggle.addEventListener('click', function () {
        sidebar.classList.toggle('show');
    });

    document.addEventListener('click', function (e) {
        if (window.innerWidth <= 992 &&
            !sidebar.contains(e.target) &&
            !sidebarToggle.contains(e.target) &&
            sidebar.classList.contains('show')) {
            sidebar.classList.remove('show');
        }
    });
});

function showFile(filename, title, nama) {
    const fileUrl = '../assets/uploads/daftar_ulang/' + filename;
    document.getElementById('buktiImage').src         = fileUrl;
    document.getElementById('buktiModalTitle').innerHTML = '<i class="fas fa-file-image me-2"></i>' + title;
    document.getElementById('buktiNama').textContent  = nama;
    document.getElementById('downloadBukti').href     = fileUrl;
    new bootstrap.Modal(document.getElementById('buktiModal')).show();
}
</script>
</body>
</html>