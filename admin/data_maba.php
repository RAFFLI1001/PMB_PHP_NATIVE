<?php
require_once '../config/database.php';
session_start();
// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM pendaftaran WHERE id_calon = $id");
    $delete = mysqli_query($conn, "DELETE FROM calon_mahasiswa WHERE id_calon = $id");
    if (!$delete) {
        die("Error delete: " . mysqli_error($conn));
    }
    $_SESSION['success'] = "Data berhasil dihapus";
    header("Location: data_maba.php");
    exit();
}

// ✅ PROSES EDIT (SIMPAN)
if (isset($_POST['edit'])) {
    $id     = intval($_POST['id_calon']);
    $nama   = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email  = mysqli_real_escape_string($conn, $_POST['email']);
    $no_hp  = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $asal   = mysqli_real_escape_string($conn, $_POST['asal_sekolah']);

    $sql = "UPDATE calon_mahasiswa 
            SET nama_lengkap='$nama', email='$email', no_hp='$no_hp', asal_sekolah='$asal'
            WHERE id_calon=$id";
    mysqli_query($conn, $sql);

    // Update password jika diisi
    if (!empty($_POST['password'])) {
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE calon_mahasiswa SET password='$pass' WHERE id_calon=$id");
    }

    // Update status pendaftaran jika ada
    if (!empty($_POST['status'])) {
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        mysqli_query($conn, "UPDATE pendaftaran SET status='$status' WHERE id_calon=$id");
    }

    $_SESSION['success'] = "Data berhasil diperbarui";
    header("Location: data_maba.php");
    exit();
}

// ✅ PROSES TAMBAH
if (isset($_POST['tambah'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $no_hp    = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $asal     = mysqli_real_escape_string($conn, $_POST['asal_sekolah']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    mysqli_query($conn, "INSERT INTO calon_mahasiswa 
    (nama_lengkap, email, no_hp, asal_sekolah, password, created_at) 
    VALUES ('$nama', '$email', '$no_hp', '$asal', '$password', NOW())");

    $id_calon = mysqli_insert_id($conn);
    $no_test  = "UR-" . date('Y') . "-" . str_pad($id_calon, 4, '0', STR_PAD_LEFT);

    mysqli_query($conn, "UPDATE calon_mahasiswa SET no_test='$no_test' WHERE id_calon=$id_calon");
    mysqli_query($conn, "INSERT INTO pendaftaran (id_calon, status, tanggal_daftar) VALUES ($id_calon, 'pending', NOW())");

    $_SESSION['success'] = "Data + No Test berhasil dibuat";
    header("Location: data_maba.php");
    exit();
}

// Search and filter
$search        = '';
$filter_status = '';
$where_conditions = [];
$query_params  = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_conditions[] = "(nama_lengkap LIKE '%$search%' OR email LIKE '%$search%' OR no_hp LIKE '%$search%')";
    $query_params['search'] = $search;
}

if (isset($_GET['status']) && !empty($_GET['status'])) {
    $filter_status = mysqli_real_escape_string($conn, $_GET['status']);
    $where_conditions[] = "p.status = '$filter_status'";
    $query_params['status'] = $filter_status;
}

$where = '';
if (!empty($where_conditions)) {
    $where = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Pagination
$limit  = 10;
$page   = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$count_result = mysqli_query($conn, "SELECT COUNT(*) as total 
    FROM calon_mahasiswa cm
    LEFT JOIN pendaftaran p ON cm.id_calon = p.id_calon
    $where");
$total_rows  = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

$result = mysqli_query($conn, "SELECT cm.*, p.status, p.tanggal_daftar, cm.no_test,
         j.nama_jurusan as jurusan_dipilih
    FROM calon_mahasiswa cm
    LEFT JOIN pendaftaran p ON cm.id_calon = p.id_calon
    LEFT JOIN jurusan j ON p.id_jurusan = j.id_jurusan
    $where 
    ORDER BY cm.id_calon DESC 
    LIMIT $limit OFFSET $offset");

// Ambil semua jurusan untuk form edit
$jurusan_result = mysqli_query($conn, "SELECT * FROM jurusan ORDER BY nama_jurusan ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Calon Maba - Admin PMB UTN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
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
        body { font-family: 'Inter', sans-serif; background-color: #F1F5F9; color: #334155; }
        .sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: 280px; background: linear-gradient(135deg, #003366 0%, #1A2B4D 100%); color: white; z-index: 1000; box-shadow: 4px 0 20px rgba(0,0,0,0.1); transition: transform 0.3s ease; }
        @media (max-width: 992px) { .sidebar { transform: translateX(-100%); } .sidebar.show { transform: translateX(0); } .main-content { margin-left: 0 !important; } }
        .sidebar-header { padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .admin-profile { display: flex; align-items: center; gap: 1rem; }
        .admin-avatar { width: 50px; height: 50px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; border: 2px solid rgba(255,255,255,0.2); }
        .admin-info h5 { font-size: 1rem; font-weight: 600; margin-bottom: 0.25rem; }
        .admin-info p { font-size: 0.875rem; opacity: 0.8; margin: 0; }
        .sidebar-nav { padding: 1rem; }
        .nav-link { display: flex; align-items: center; padding: 0.875rem 1rem; color: rgba(255,255,255,0.8); text-decoration: none; border-radius: 8px; margin-bottom: 0.5rem; transition: all 0.3s ease; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: white; }
        .nav-link.active { background: rgba(255,255,255,0.15); }
        .nav-icon { width: 24px; margin-right: 12px; font-size: 1.1rem; }
        .main-content { margin-left: 280px; min-height: 100vh; transition: margin-left 0.3s ease; }
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
        .top-nav { background: white; padding: 1rem 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 100; }
        .sidebar-toggle { display: none; position: fixed; bottom: 20px; right: 20px; width: 50px; height: 50px; border-radius: 50%; background: var(--utn-primary); color: white; border: none; z-index: 1001; box-shadow: 0 4px 12px rgba(0,51,102,0.3); }
        @media (max-width: 992px) { .sidebar-toggle { display: flex; align-items: center; justify-content: center; } }
        .dashboard-card { background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.05); margin-bottom: 1.5rem; }
        .card-title { color: var(--utn-dark); font-weight: 600; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #F1F5F9; }
        .search-filter-card { background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.05); margin-bottom: 1.5rem; }
        .form-control, .form-select { border: 1px solid #E2E8F0; border-radius: 8px; padding: 0.75rem 1rem; }
        .form-control:focus, .form-select:focus { border-color: var(--utn-primary); box-shadow: 0 0 0 3px rgba(0,51,102,0.1); }
        .btn-utn-primary { background: var(--utn-primary); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 500; transition: all 0.3s ease; }
        .btn-utn-primary:hover { background: #004080; color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,51,102,0.2); }
        .btn-utn-secondary { background: #1A4A8A; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 500; transition: all 0.3s ease; }
        .btn-utn-secondary:hover { background: #153d75; color: white; transform: translateY(-2px); }
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .data-table thead { background: #F8FAFC; }
        .data-table th { padding: 1rem; font-weight: 600; color: var(--utn-dark); border-bottom: 2px solid #F1F5F9; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; }
        .data-table td { padding: 1rem; border-bottom: 1px solid #F1F5F9; color: #475569; vertical-align: middle; }
        .data-table tbody tr:hover { background: #F8FAFC; }
        .status-badge { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .badge-primary { background: rgba(0,51,102,0.1); color: var(--utn-primary); }
        .badge-success { background: rgba(16,185,129,0.1); color: var(--utn-success); }
        .badge-warning { background: rgba(245,158,11,0.1); color: var(--utn-warning); }
        .badge-danger { background: rgba(239,68,68,0.1); color: var(--utn-danger); }
        .badge-info { background: rgba(59,130,246,0.1); color: var(--utn-info); }
        .btn-action { width: 36px; height: 36px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; margin: 0 2px; transition: all 0.3s ease; }
        .btn-action:hover { transform: translateY(-2px); }
        .pagination-custom .page-link { color: var(--utn-primary); border: 1px solid #E2E8F0; border-radius: 6px; margin: 0 2px; }
        .pagination-custom .page-item.active .page-link { background: var(--utn-primary); border-color: var(--utn-primary); color: white; }
        .pagination-custom .page-link:hover { background: #F1F5F9; border-color: var(--utn-primary); }
        .stats-card { background: white; border-radius: 12px; padding: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border-left: 4px solid var(--utn-primary); }
        .stats-value { font-size: 1.5rem; font-weight: 700; color: var(--utn-dark); margin-bottom: 0.25rem; }
        .stats-label { font-size: 0.875rem; color: #64748B; }
        .empty-state { text-align: center; padding: 3rem 1rem; color: #94A3B8; }
        .empty-state i { font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; }
        .alert-custom { border-radius: 10px; border: none; padding: 1rem 1.5rem; }

        /* View Detail Styling */
        .detail-section { margin-bottom: 1.25rem; }
        .detail-section .section-title { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; color: #94A3B8; font-weight: 600; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 1px solid #F1F5F9; }
        .detail-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 0.4rem 0; }
        .detail-label { font-size: 0.85rem; color: #64748B; flex: 0 0 40%; }
        .detail-value { font-size: 0.875rem; color: #1E293B; font-weight: 500; flex: 0 0 58%; text-align: right; }
        .avatar-circle { width: 60px; height: 60px; border-radius: 50%; background: rgba(0,51,102,0.1); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; color: var(--utn-primary); }
    </style>
</head>

<!-- ===== MODAL TAMBAH ===== -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-user-plus me-2 text-primary"></i>Tambah Calon Mahasiswa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" name="nama_lengkap" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">No HP</label>
              <input type="text" name="no_hp" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Asal Sekolah</label>
              <input type="text" name="asal_sekolah" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="tambah" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ===== MODAL LIHAT DETAIL ===== -->
<div class="modal fade" id="modalView" tabindex="-1">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-id-card me-2 text-primary"></i>Detail Calon Mahasiswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="modalViewBody">
        <div class="text-center py-4">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="mt-2 text-muted">Memuat data...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-warning" id="btnSwitchEdit">
          <i class="fas fa-edit me-1"></i>Edit Data
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ===== MODAL EDIT ===== -->
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" id="formEdit">
        <input type="hidden" name="id_calon" id="edit_id">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-user-edit me-2 text-warning"></i>Edit Calon Mahasiswa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" name="nama_lengkap" id="edit_nama" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" id="edit_email" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">No HP</label>
              <input type="text" name="no_hp" id="edit_no_hp" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Asal Sekolah</label>
              <input type="text" name="asal_sekolah" id="edit_asal_sekolah" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Status Pendaftaran</label>
              <select name="status" id="edit_status" class="form-select">
                <option value="pending">Pending</option>
                <option value="lulus">Lulus</option>
                <option value="tidak_lulus">Tidak Lulus</option>
                <option value="daftar_ulang">Daftar Ulang</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Password Baru <small class="text-muted">(kosongkan jika tidak diubah)</small></label>
              <input type="password" name="password" id="edit_password" class="form-control" placeholder="Isi untuk mengubah password">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="edit" class="btn btn-warning text-white"><i class="fas fa-save me-1"></i>Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<body>
    <!-- Sidebar -->
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
            <a href="dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt nav-icon"></i><span>Dashboard</span></a>
            <a href="data_maba.php" class="nav-link active"><i class="fas fa-users nav-icon"></i><span>Data Calon Maba</span></a>
            <a href="soal_test.php" class="nav-link"><i class="fas fa-question-circle nav-icon"></i><span>Soal Test</span></a>
            <a href="hasil_test.php" class="nav-link"><i class="fas fa-chart-bar nav-icon"></i><span>Hasil Test</span></a>
            <a href="daftar_ulang.php" class="nav-link"><i class="fas fa-check-double nav-icon"></i><span>Daftar Ulang</span></a>
            <a href="../logout.php" class="nav-link mt-4"><i class="fas fa-sign-out-alt nav-icon"></i><span>Logout</span></a>
        </nav>
    </aside>

    <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>

    <main class="main-content">
        <header class="top-nav">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Data Calon Mahasiswa Baru</h1>
                        <p class="text-muted mb-0">Kelola data calon mahasiswa baru</p>
                    </div>
                </div>
            </div>
        </header>

        <div class="container-fluid py-4">
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-custom mb-4">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card">
                        <?php $total_calon = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM calon_mahasiswa"))['total']; ?>
                        <div class="stats-value"><?php echo $total_calon; ?></div>
                        <div class="stats-label">Total Calon Maba</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card" style="border-left-color: var(--utn-success);">
                        <?php $total_pendaftar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT id_calon) as total FROM pendaftaran"))['total']; ?>
                        <div class="stats-value"><?php echo $total_pendaftar; ?></div>
                        <div class="stats-label">Sudah Mendaftar</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card" style="border-left-color: var(--utn-warning);">
                        <?php $total_baru = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM calon_mahasiswa WHERE DATE(created_at) = CURDATE()"))['total']; ?>
                        <div class="stats-value"><?php echo $total_baru; ?></div>
                        <div class="stats-label">Pendaftar Hari Ini</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card" style="border-left-color: var(--utn-info);">
                        <?php $total_aktivitas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran WHERE DATE(tanggal_daftar) = CURDATE()"))['total']; ?>
                        <div class="stats-value"><?php echo $total_aktivitas; ?></div>
                        <div class="stats-label">Aktivitas Hari Ini</div>
                    </div>
                </div>
            </div>

            <!-- Search & Filter -->
            <div class="search-filter-card">
                <div class="row g-3">
                    <div class="col-md-8">
                        <form method="GET" action="" class="row g-2">
                            <div class="col-md-7">
                                <input type="text" class="form-control" name="search"
                                    placeholder="Cari nama lengkap, email, atau nomor HP..."
                                    value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="pending" <?php echo $filter_status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="lulus" <?php echo $filter_status == 'lulus' ? 'selected' : ''; ?>>Lulus</option>
                                    <option value="tidak_lulus" <?php echo $filter_status == 'tidak_lulus' ? 'selected' : ''; ?>>Tidak Lulus</option>
                                    <option value="daftar_ulang" <?php echo $filter_status == 'daftar_ulang' ? 'selected' : ''; ?>>Daftar Ulang</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-utn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Cari
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-utn-secondary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="fas fa-plus me-1"></i>Tambah Calon Maba
                        </button>
                    </div>
                </div>
                <?php if(!empty($search) || !empty($filter_status)): ?>
                <div class="mt-3">
                    <small class="text-muted">Filter aktif:</small>
                    <?php if(!empty($search)): ?>
                        <span class="badge bg-light text-dark me-2">
                            <i class="fas fa-search me-1"></i>"<?php echo htmlspecialchars($search); ?>"
                            <a href="?<?php echo http_build_query(array_diff_key($query_params, ['search'=>''])) ?>" class="ms-1 text-danger"><i class="fas fa-times"></i></a>
                        </span>
                    <?php endif; ?>
                    <?php if(!empty($filter_status)): ?>
                        <span class="badge bg-light text-dark me-2">
                            <i class="fas fa-filter me-1"></i>Status: <?php echo ucfirst(str_replace('_', ' ', $filter_status)); ?>
                            <a href="?<?php echo http_build_query(array_diff_key($query_params, ['status'=>''])) ?>" class="ms-1 text-danger"><i class="fas fa-times"></i></a>
                        </span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Table -->
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="card-title mb-0">Daftar Calon Mahasiswa</h3>
                    <div class="text-muted">Total: <strong><?php echo $total_rows; ?></strong> data</div>
                </div>

                <?php if(mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Lengkap</th>
                                <th>Kontak</th>
                                <th>Asal Sekolah</th>
                                <th>Status</th>
                                <th>Jurusan</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)):
                                $status = $row['status'] ?? 'belum_daftar';
                                $status_class = $status == 'lulus' ? 'success' :
                                               ($status == 'tidak_lulus' ? 'danger' :
                                               ($status == 'pending' ? 'warning' :
                                               ($status == 'daftar_ulang' ? 'info' : 'primary')));
                                $status_text = $status == 'belum_daftar' ? 'Belum Daftar' :
                                               ucfirst(str_replace('_', ' ', $status));
                                // Encode data untuk JS (digunakan tombol view & edit)
                                $row_json = htmlspecialchars(json_encode([
                                    'id_calon'      => $row['id_calon'],
                                    'nama_lengkap'  => $row['nama_lengkap'],
                                    'email'         => $row['email'],
                                    'no_hp'         => $row['no_hp'] ?? '',
                                    'asal_sekolah'  => $row['asal_sekolah'] ?? '',
                                    'no_test'       => $row['no_test'] ?? '-',
                                    'status'        => $status,
                                    'status_text'   => $status_text,
                                    'jurusan'       => $row['jurusan_dipilih'] ?? '-',
                                    'created_at'    => $row['created_at'],
                                    'tanggal_daftar'=> $row['tanggal_daftar'] ?? '',
                                ]), ENT_QUOTES);
                            ?>
                            <tr>
                                <td><span class="badge bg-light text-dark">#<?php echo $row['id_calon']; ?></span></td>
                                <td>
                                    <div class="fw-medium"><?php echo htmlspecialchars($row['nama_lengkap']); ?></div>
                                    <small class="text-muted">No Test: <?php echo $row['no_test'] ? $row['no_test'] : '#'; ?></small>
                                </td>
                                <td>
                                    <div><?php echo htmlspecialchars($row['email']); ?></div>
                                    <small class="text-muted"><?php echo $row['no_hp'] ?: '-'; ?></small>
                                </td>
                                <td><?php echo $row['asal_sekolah'] ? htmlspecialchars($row['asal_sekolah']) : '-'; ?></td>
                                <td><span class="status-badge badge-<?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                                <td>
                                    <?php if($row['jurusan_dipilih']): ?>
                                        <span class="badge bg-light text-dark"><?php echo $row['jurusan_dipilih']; ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></div>
                                    <small class="text-muted">
                                        <?php echo $row['tanggal_daftar'] ? 'Daftar: ' . date('d/m/Y', strtotime($row['tanggal_daftar'])) : ''; ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <!-- Tombol View (pakai modal) -->
                                        <button type="button"
                                            class="btn-action btn btn-sm btn-outline-primary"
                                            title="Lihat Detail"
                                            onclick="bukaModalView(<?php echo $row_json; ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <!-- Tombol Edit (pakai modal) -->
                                        <button type="button"
                                            class="btn-action btn btn-sm btn-outline-warning"
                                            title="Edit"
                                            onclick="bukaModalEdit(<?php echo $row_json; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <!-- Tombol Hapus -->
                                        <a href="?delete=<?php echo $row['id_calon']; ?>"
                                           class="btn-action btn btn-sm btn-outline-danger"
                                           title="Hapus"
                                           onclick="return confirm('Yakin ingin menghapus data ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
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
                        <?php $base_url = '?' . (!empty($query_params) ? http_build_query($query_params) . '&' : ''); ?>
                        <?php if($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo $base_url; ?>page=<?php echo $page-1; ?>"><i class="fas fa-chevron-left"></i></a>
                            </li>
                        <?php endif; ?>
                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page   = min($total_pages, $page + 2);
                        for($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="<?php echo $base_url; ?>page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo $base_url; ?>page=<?php echo $page+1; ?>"><i class="fas fa-chevron-right"></i></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>

                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-users-slash"></i>
                    <h4 class="mb-2">Tidak ada data ditemukan</h4>
                    <p class="text-muted">Coba ubah kata kunci pencarian atau filter yang digunakan</p>
                    <?php if(!empty($search) || !empty($filter_status)): ?>
                        <a href="data_maba.php" class="btn btn-utn-primary mt-2"><i class="fas fa-times me-1"></i>Reset Filter</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <footer class="mt-4 pt-3 border-top text-center text-muted">
                <p class="mb-0">PMB Universitas Arten</p>
                <small>Terakhir diakses: <?php echo date('d/m/Y H:i'); ?></small>
            </footer>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
    // ==================== FUNGSI MODAL VIEW ====================
    var currentViewData = null;

    function bukaModalView(data) {
        currentViewData = data;

        var statusClass = {
            'lulus'       : 'badge-success',
            'tidak_lulus' : 'badge-danger',
            'pending'     : 'badge-warning',
            'daftar_ulang': 'badge-info',
            'belum_daftar': 'badge-primary'
        }[data.status] || 'badge-primary';

        var inisial = data.nama_lengkap
            .split(' ')
            .slice(0, 2)
            .map(function(w){ return w[0]; })
            .join('').toUpperCase();

        var tglDaftar = data.tanggal_daftar
            ? '<div>' + formatTanggal(data.tanggal_daftar) + '</div>'
            : '<span class="text-muted">-</span>';

        var html = `
            <div class="text-center mb-3">
                <div class="avatar-circle mx-auto mb-2">${inisial}</div>
                <h5 class="mb-0 fw-semibold">${data.nama_lengkap}</h5>
                <small class="text-muted">${data.no_test}</small>
            </div>
            <hr>
            <div class="detail-section">
                <div class="section-title">Informasi Pribadi</div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-envelope me-2 text-primary"></i>Email</span>
                    <span class="detail-value">${data.email}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-phone me-2 text-success"></i>No HP</span>
                    <span class="detail-value">${data.no_hp || '-'}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-school me-2 text-warning"></i>Asal Sekolah</span>
                    <span class="detail-value">${data.asal_sekolah || '-'}</span>
                </div>
            </div>
            <div class="detail-section">
                <div class="section-title">Informasi Pendaftaran</div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-tag me-2 text-info"></i>Status</span>
                    <span class="detail-value"><span class="status-badge ${statusClass}">${data.status_text}</span></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-graduation-cap me-2 text-primary"></i>Jurusan</span>
                    <span class="detail-value">${data.jurusan || '-'}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-calendar me-2 text-secondary"></i>Tgl Daftar</span>
                    <span class="detail-value">${tglDaftar}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-clock me-2 text-secondary"></i>Dibuat</span>
                    <span class="detail-value">${formatTanggal(data.created_at)}</span>
                </div>
            </div>
        `;

        document.getElementById('modalViewBody').innerHTML = html;
        new bootstrap.Modal(document.getElementById('modalView')).show();
    }

    // Tombol "Edit Data" di dalam modal view
    document.getElementById('btnSwitchEdit').addEventListener('click', function() {
        bootstrap.Modal.getInstance(document.getElementById('modalView')).hide();
        setTimeout(function() {
            bukaModalEdit(currentViewData);
        }, 400);
    });

    // ==================== FUNGSI MODAL EDIT ====================
    function bukaModalEdit(data) {
        document.getElementById('edit_id').value          = data.id_calon;
        document.getElementById('edit_nama').value        = data.nama_lengkap;
        document.getElementById('edit_email').value       = data.email;
        document.getElementById('edit_no_hp').value       = data.no_hp || '';
        document.getElementById('edit_asal_sekolah').value= data.asal_sekolah || '';
        document.getElementById('edit_password').value    = '';

        var selectStatus = document.getElementById('edit_status');
        for(var i = 0; i < selectStatus.options.length; i++){
            if(selectStatus.options[i].value === data.status){
                selectStatus.selectedIndex = i;
                break;
            }
        }
        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    }

    // ==================== HELPER ====================
    function formatTanggal(dateStr) {
        if(!dateStr) return '-';
        var d = new Date(dateStr);
        if(isNaN(d)) return dateStr;
        return d.toLocaleDateString('id-ID', {day:'2-digit', month:'2-digit', year:'numeric'});
    }

    // ==================== SIDEBAR & DATATABLE ====================
    document.addEventListener('DOMContentLoaded', function() {
        var sidebar       = document.getElementById('sidebar');
        var sidebarToggle = document.getElementById('sidebarToggle');

        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });

        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 992) {
                if (!sidebar.contains(event.target) &&
                    !sidebarToggle.contains(event.target) &&
                    sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                }
            }
        });

        $('.data-table').DataTable({
            "paging": false,
            "searching": false,
            "info": false,
            "ordering": true,
            "language": {
                "emptyTable": "Tidak ada data yang tersedia",
                "zeroRecords": "Tidak ada data yang cocok",
                "infoEmpty": "Menampilkan 0 data",
                "infoFiltered": "(disaring dari _MAX_ total data)"
            }
        });
    });
    </script>
</body>
</html>
