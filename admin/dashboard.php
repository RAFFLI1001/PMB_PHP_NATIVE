<?php
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Get statistics
$total_maba = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM calon_mahasiswa"))['total'];
$total_pendaftar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran"))['total'];
$total_lulus = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran WHERE status='lulus'"))['total'];
$total_daftar_ulang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM daftar_ulang"))['total'];
$total_today = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran WHERE DATE(tanggal_daftar) = CURDATE()"))['total'];

// Get jurusan statistics
$jurusan_stats = [];
$jurusan_query = "SELECT j.nama_jurusan, COUNT(p.id_pendaftaran) as total 
                  FROM jurusan j 
                  LEFT JOIN pendaftaran p ON j.id_jurusan = p.id_jurusan 
                  GROUP BY j.id_jurusan";
$jurusan_result = mysqli_query($conn, $jurusan_query);
while ($row = mysqli_fetch_assoc($jurusan_result)) {
    $jurusan_stats[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - PMB UTN</title>

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

        .nav-link:hover,
        .nav-link.active {
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

        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--utn-primary);
            height: 100%;
        }

        .stat-card-success {
            border-left-color: var(--utn-success);
        }

        .stat-card-info {
            border-left-color: var(--utn-info);
        }

        .stat-card-warning {
            border-left-color: var(--utn-warning);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--utn-dark);
            margin-bottom: 0.5rem;
        }

        .stat-change {
            font-size: 0.875rem;
            color: var(--utn-success);
            font-weight: 500;
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

        /* Table Styles */
        .table th {
            font-weight: 600;
            color: var(--utn-dark);
            border-top: none;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--utn-success);
        }

        /* Quick Actions */
        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #F1F5F9;
            text-decoration: none;
            color: var(--utn-dark);
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            border-color: var(--utn-primary);
        }

        .action-icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--utn-primary);
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
                    <p>Universitas Arten</p>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-link active">
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
                        <h1 class="h3 mb-0">Dashboard Admin</h1>
                        <p class="text-muted mb-0">Portal Penerimaan Mahasiswa Baru</p>
                    </div>
                    
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="container-fluid py-4">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value"><?php echo $total_maba; ?></div>
                                <div class="text-muted">Total Calon Maba</div>
                            </div>
                            <div class="bg-primary text-white rounded-circle p-3">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="stat-change mt-2">
                            <i class="fas fa-arrow-up me-1"></i>
                            +<?php echo $total_today; ?> hari ini
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stat-card stat-card-success">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value"><?php echo $total_pendaftar; ?></div>
                                <div class="text-muted">Total Pendaftar</div>
                            </div>
                            <div class="bg-success text-white rounded-circle p-3">
                                <i class="fas fa-user-plus"></i>
                            </div>
                        </div>
                        <div class="stat-change mt-2">
                            <i class="fas fa-arrow-up me-1"></i>
                            +12% bulan ini
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stat-card stat-card-info">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value"><?php echo $total_lulus; ?></div>
                                <div class="text-muted">Lulus Test</div>
                            </div>
                            <div class="bg-info text-white rounded-circle p-3">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                        </div>
                        <div class="stat-change mt-2">
                            <i class="fas fa-arrow-up me-1"></i>
                            <?php echo $total_pendaftar > 0 ? round(($total_lulus / $total_pendaftar) * 100, 1) : 0; ?>%
                            kelulusan
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stat-card stat-card-warning">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value"><?php echo $total_daftar_ulang; ?></div>
                                <div class="text-muted">Daftar Ulang</div>
                            </div>
                            <div class="bg-warning text-white rounded-circle p-3">
                                <i class="fas fa-check-double"></i>
                            </div>
                        </div>
                        <div class="stat-change mt-2">
                            <i class="fas fa-arrow-up me-1"></i>
                            <?php echo $total_lulus > 0 ? round(($total_daftar_ulang / $total_lulus) * 100, 1) : 0; ?>%
                            konversi
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Content -->
            <div class="row">
                <!-- Recent Registrations -->
                <div class="col-lg-8 mb-4">
                    <div class="dashboard-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="card-title mb-0">Pendaftaran Terbaru</h3>
                            <a href="data_maba.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No. Test</th>
                                        <th>Nama</th>
                                        <th>Jurusan</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT p.*, cm.nama_lengkap, cm.no_test, j.nama_jurusan 
          FROM pendaftaran p
          JOIN calon_mahasiswa cm ON p.id_calon = cm.id_calon
          JOIN jurusan j ON p.id_jurusan = j.id_jurusan
          ORDER BY p.id_pendaftaran DESC LIMIT 5";
                                    $result = mysqli_query($conn, $query);

                                    if (mysqli_num_rows($result) > 0):
                                        while ($row = mysqli_fetch_assoc($result)):
                                            $status = $row['status'];
                                            $status_badge = $status == 'lulus' ? 'success' :
                                                ($status == 'tidak_lulus' ? 'danger' : 'warning');
                                            $status_text = ucfirst(str_replace('_', ' ', $status));
                                            ?>
                                            <tr>
                                                <td><strong><?php echo $row['no_test']; ?></strong></td>
                                                <td><?php echo $row['nama_lengkap']; ?></td>
                                                <td><?php echo $row['nama_jurusan']; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_daftar'])); ?></td>
                                                <td>
                                                    <span class="status-badge badge-<?php echo $status_badge; ?>">
                                                        <?php echo $status_text; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endwhile; else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-3">
                                                <i class="fas fa-users-slash fa-2x mb-2"></i>
                                                <p>Belum ada data pendaftaran</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Jurusan Statistics -->
                <div class="col-lg-4 mb-4">
                    <div class="dashboard-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="card-title mb-0">Statistik Jurusan</h3>
                            <span class="text-muted">Total: <?php echo $total_pendaftar; ?> pendaftar</span>
                        </div>

                        <div class="mb-3">
                            <?php
                            $colors = ['#003366', '#FF6B00', '#10B981', '#3B82F6', '#F59E0B'];
                            $i = 0;
                            foreach ($jurusan_stats as $jurusan):
                                $percentage = $total_pendaftar > 0 ? round(($jurusan['total'] / $total_pendaftar) * 100, 1) : 0;
                                $color = $colors[$i % count($colors)];
                                $i++;
                                ?>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <div class="rounded-circle"
                                            style="width: 12px; height: 12px; background: <?php echo $color; ?>"></div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <span><?php echo $jurusan['nama_jurusan']; ?></span>
                                            <strong><?php echo $jurusan['total']; ?></strong>
                                        </div>
                                        <div class="progress mt-1" style="height: 6px;">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: <?php echo $percentage; ?>%; background: <?php echo $color; ?>">
                                            </div>
                                        </div>
                                        <small class="text-muted"><?php echo $percentage; ?>% dari total</small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="text-center py-3 border-top">
                            <i class="fas fa-chart-pie fa-2x text-primary mb-2"></i>
                            <p class="text-muted mb-0">Visualisasi grafik statistik jurusan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->


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
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mainContent = document.querySelector('.main-content');

            sidebarToggle.addEventListener('click', function () {
                sidebar.classList.toggle('show');
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function (event) {
                if (window.innerWidth <= 992) {
                    if (!sidebar.contains(event.target) &&
                        !sidebarToggle.contains(event.target) &&
                        sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                    }
                }
            });

            // Update time every minute
            function updateTime() {
                const now = new Date();
                const timeElement = document.querySelector('.last-access');
                if (timeElement) {
                    const timeString = now.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    timeElement.textContent = timeString;
                }
            }

            setInterval(updateTime, 60000);
        });
    </script>
</body>

</html>