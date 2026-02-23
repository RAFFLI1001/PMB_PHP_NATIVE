<?php
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Initialize variables
$success = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Update Profile
    if (isset($_POST['update_profile'])) {
        $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
        
        $query = "UPDATE admin SET 
                  nama_lengkap = '$nama_lengkap',
                  email = '$email',
                  no_hp = '$no_hp'
                  WHERE id_admin = " . $_SESSION['admin_id'];
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['admin_nama'] = $nama_lengkap;
            $success = "Profil berhasil diperbarui!";
        } else {
            $error = "Gagal memperbarui profil: " . mysqli_error($conn);
        }
    }
    
    // Change Password
    if (isset($_POST['change_password'])) {
        $current_password = md5($_POST['current_password']);
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Check current password
        $check_query = "SELECT * FROM admin WHERE id_admin = " . $_SESSION['admin_id'] . " AND password = '$current_password'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) == 1) {
            if ($new_password === $confirm_password) {
                $hashed_password = md5($new_password);
                $update_query = "UPDATE admin SET password = '$hashed_password' WHERE id_admin = " . $_SESSION['admin_id'];
                
                if (mysqli_query($conn, $update_query)) {
                    $success = "Password berhasil diubah!";
                } else {
                    $error = "Gagal mengubah password: " . mysqli_error($conn);
                }
            } else {
                $error = "Password baru tidak cocok!";
            }
        } else {
            $error = "Password saat ini salah!";
        }
    }
    
    // Update System Settings
    if (isset($_POST['update_system'])) {
        // In a real system, you would save these to a settings table
        $pendaftaran_buka = $_POST['pendaftaran_buka'];
        $pendaftaran_tutup = $_POST['pendaftaran_tutup'];
        $test_online_buka = $_POST['test_online_buka'];
        $test_online_tutup = $_POST['test_online_tutup'];
        $biaya_pendaftaran = $_POST['biaya_pendaftaran'];
        $biaya_daftar_ulang = $_POST['biaya_daftar_ulang'];
        
        // For demo, we'll just show success message
        $success = "Pengaturan sistem berhasil diperbarui!";
    }
    
    // Update Notification Settings
    if (isset($_POST['update_notification'])) {
        $notif_email = isset($_POST['notif_email']) ? 1 : 0;
        $notif_whatsapp = isset($_POST['notif_whatsapp']) ? 1 : 0;
        $notif_daftar_baru = isset($_POST['notif_daftar_baru']) ? 1 : 0;
        $notif_pembayaran = isset($_POST['notif_pembayaran']) ? 1 : 0;
        $notif_hasil_test = isset($_POST['notif_hasil_test']) ? 1 : 0;
        
        // For demo, we'll just show success message
        $success = "Pengaturan notifikasi berhasil diperbarui!";
    }
}

// Get admin data
$query = "SELECT * FROM admin WHERE id_admin = " . $_SESSION['admin_id'];
$result = mysqli_query($conn, $query);
$admin = mysqli_fetch_assoc($result);

// Get system statistics
$total_maba = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM calon_mahasiswa"))['total'];
$total_admin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM admin"))['total'];
$total_jurusan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM jurusan"))['total'];
$total_soal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM soal_test"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Admin PMB UTN</title>
    
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
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .card-title i {
            color: var(--utn-primary);
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
        
        /* Form Styles */
        .form-control, .form-select {
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--utn-primary);
            box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--utn-dark);
            margin-bottom: 0.5rem;
        }
        
        .required::after {
            content: " *";
            color: var(--utn-danger);
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
        
        /* Settings Section */
        .settings-section {
            margin-bottom: 2rem;
        }
        
        .settings-section:last-child {
            margin-bottom: 0;
        }
        
        /* Profile Card */
        .profile-card {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--utn-primary) 0%, var(--utn-info) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            margin: 0 auto 1rem;
            border: 4px solid white;
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.2);
        }
        
        .profile-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--utn-dark);
            margin-bottom: 0.25rem;
        }
        
        .profile-role {
            color: #64748B;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
        
        /* Switch Toggle */
        .form-check-input:checked {
            background-color: var(--utn-primary);
            border-color: var(--utn-primary);
        }
        
        .form-check-input:focus {
            border-color: var(--utn-primary);
            box-shadow: 0 0 0 0.25rem rgba(0, 51, 102, 0.25);
        }
        
        /* Alert Messages */
        .alert-custom {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.5rem;
        }
        
        /* Tab Navigation */
        .nav-tabs-custom {
            border-bottom: 2px solid #F1F5F9;
            margin-bottom: 1.5rem;
        }
        
        .nav-tabs-custom .nav-link {
            border: none;
            color: #64748B;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: 8px 8px 0 0;
            margin-right: 0.5rem;
        }
        
        .nav-tabs-custom .nav-link:hover {
            color: var(--utn-primary);
            background-color: #F8FAFC;
        }
        
        .nav-tabs-custom .nav-link.active {
            color: var(--utn-primary);
            background-color: white;
            border-bottom: 3px solid var(--utn-primary);
        }
        
        /* System Info */
        .system-info {
            background: #F8FAFC;
            border-radius: 8px;
            padding: 1rem;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #F1F5F9;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            color: #64748B;
            font-weight: 500;
        }
        
        .info-value {
            color: var(--utn-dark);
            font-weight: 600;
        }
        
        /* Backup Card */
        .backup-card {
            background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%);
            border: 2px dashed #E2E8F0;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .backup-card:hover {
            border-color: var(--utn-primary);
            background: white;
            transform: translateY(-2px);
        }
        
        .backup-icon {
            font-size: 2.5rem;
            color: var(--utn-primary);
            margin-bottom: 1rem;
        }
        
        /* Password Strength */
        .password-strength {
            height: 4px;
            border-radius: 2px;
            background: #E2E8F0;
            margin-top: 0.5rem;
            overflow: hidden;
        }
        
        .strength-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s ease;
        }
        
        .strength-weak { background: var(--utn-danger); }
        .strength-medium { background: var(--utn-warning); }
        .strength-strong { background: var(--utn-success); }
        
        /* Activity Log */
        .activity-log {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .activity-item {
            display: flex;
            align-items: flex-start;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            background: #F8FAFC;
        }
        
        .activity-item:hover {
            background: #F1F5F9;
        }
        
        .activity-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--utn-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-title {
            font-weight: 500;
            color: var(--utn-dark);
            margin-bottom: 0.25rem;
        }
        
        .activity-time {
            font-size: 0.75rem;
            color: #64748B;
        }
        
        /* Danger Zone */
        .danger-zone {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.05) 100%);
            border: 2px solid rgba(239, 68, 68, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
        }
        
        .danger-zone .card-title {
            color: var(--utn-danger);
        }
        
        .danger-zone .card-title i {
            color: var(--utn-danger);
        }
        
        /* Help Text */
        .help-text {
            font-size: 0.875rem;
            color: #64748B;
            margin-top: 0.5rem;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .nav-tabs-custom {
                flex-direction: column;
            }
            
            .nav-tabs-custom .nav-link {
                margin-right: 0;
                margin-bottom: 0.5rem;
            }
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
            <a href="hasil_test.php" class="nav-link">
                <i class="fas fa-chart-bar nav-icon"></i>
                <span>Hasil Test</span>
            </a>
            <a href="daftar_ulang.php" class="nav-link">
                <i class="fas fa-check-double nav-icon"></i>
                <span>Daftar Ulang</span>
            </a>
            <a href="pengaturan.php" class="nav-link active">
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
                        <h1 class="h3 mb-0">Pengaturan Sistem</h1>
                        <p class="text-muted mb-0">Kelola pengaturan sistem dan profil administrator</p>
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
                                <li><a class="dropdown-item" href="pengaturan.php#profile"><i class="fas fa-user me-2"></i> Profil Saya</a></li>
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
            <?php if($success): ?>
                <div class="alert alert-success alert-custom mb-4">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-danger alert-custom mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stats-value"><?php echo $total_maba; ?></div>
                                <div class="stats-label">Total Calon Maba</div>
                            </div>
                            <div class="bg-primary text-white rounded-circle p-2">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card stats-card-info">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stats-value"><?php echo $total_admin; ?></div>
                                <div class="stats-label">Total Administrator</div>
                            </div>
                            <div class="bg-info text-white rounded-circle p-2">
                                <i class="fas fa-user-shield"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card stats-card-success">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stats-value"><?php echo $total_jurusan; ?></div>
                                <div class="stats-label">Program Studi</div>
                            </div>
                            <div class="bg-success text-white rounded-circle p-2">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card stats-card-warning">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stats-value"><?php echo $total_soal; ?></div>
                                <div class="stats-label">Total Soal Test</div>
                            </div>
                            <div class="bg-warning text-white rounded-circle p-2">
                                <i class="fas fa-question-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs nav-tabs-custom" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button">
                        <i class="fas fa-user me-1"></i> Profil Saya
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button">
                        <i class="fas fa-lock me-1"></i> Keamanan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="system-tab" data-bs-toggle="tab" data-bs-target="#system" type="button">
                        <i class="fas fa-cog me-1"></i> Sistem
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button">
                        <i class="fas fa-bell me-1"></i> Notifikasi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="backup-tab" data-bs-toggle="tab" data-bs-target="#backup" type="button">
                        <i class="fas fa-database me-1"></i> Backup
                    </button>
                </li>
            </ul>
            
            <!-- Tab Content -->
            <div class="tab-content" id="settingsTabsContent">
                
                <!-- Profile Tab -->
                <div class="tab-pane fade show active" id="profile" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <div class="profile-card">
                                <div class="profile-avatar">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <div class="profile-name"><?php echo htmlspecialchars($admin['nama_lengkap']); ?></div>
                                <div class="profile-role">Administrator PMB UTN</div>
                                <div class="mb-3">
                                    <span class="badge bg-primary">ID: <?php echo $admin['id_admin']; ?></span>
                                </div>
                                <div class="system-info">
                                    <div class="info-item">
                                        <span class="info-label">Username</span>
                                        <span class="info-value"><?php echo htmlspecialchars($admin['username']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Status</span>
                                        <span class="badge bg-success">Aktif</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Bergabung</span>
                                        <span class="info-value"><?php echo date('d M Y', strtotime($admin['created_at'])); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Recent Activity -->
                            <div class="dashboard-card mt-4">
                                <h3 class="card-title">
                                    <i class="fas fa-history"></i>Aktivitas Terbaru
                                </h3>
                                <div class="activity-log">
                                    <div class="activity-item">
                                        <div class="activity-icon">
                                            <i class="fas fa-sign-in-alt"></i>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-title">Login ke sistem</div>
                                            <div class="activity-time">Hari ini, <?php echo date('H:i'); ?></div>
                                        </div>
                                    </div>
                                    <div class="activity-item">
                                        <div class="activity-icon">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-title">Mengelola data calon maba</div>
                                            <div class="activity-time">Kemarin, 14:30</div>
                                        </div>
                                    </div>
                                    <div class="activity-item">
                                        <div class="activity-icon">
                                            <i class="fas fa-question-circle"></i>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-title">Menambah soal test baru</div>
                                            <div class="activity-time">2 hari yang lalu</div>
                                        </div>
                                    </div>
                                    <div class="activity-item">
                                        <div class="activity-icon">
                                            <i class="fas fa-chart-bar"></i>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-title">Melakukan analisis hasil test</div>
                                            <div class="activity-time">3 hari yang lalu</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-8 mb-4">
                            <div class="dashboard-card">
                                <h3 class="card-title">
                                    <i class="fas fa-edit"></i>Edit Profil
                                </h3>
                                <form method="POST" action="">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label required">Nama Lengkap</label>
                                            <input type="text" class="form-control" name="nama_lengkap" 
                                                   value="<?php echo htmlspecialchars($admin['nama_lengkap']); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Username</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($admin['username']); ?>" disabled>
                                            <small class="help-text">Username tidak dapat diubah</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label required">Email</label>
                                            <input type="email" class="form-control" name="email" 
                                                   value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Nomor HP</label>
                                            <input type="tel" class="form-control" name="no_hp" 
                                                   value="<?php echo htmlspecialchars($admin['no_hp']); ?>">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">Alamat</label>
                                            <textarea class="form-control" name="alamat" rows="3"><?php echo htmlspecialchars($admin['alamat'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="col-12 mt-3">
                                            <button type="submit" name="update_profile" class="btn btn-utn-primary">
                                                <i class="fas fa-save me-1"></i>Simpan Perubahan
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Password Tab -->
                <div class="tab-pane fade" id="password" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="dashboard-card">
                                <h3 class="card-title">
                                    <i class="fas fa-key"></i>Ubah Password
                                </h3>
                                <form method="POST" action="" id="passwordForm">
                                    <div class="mb-3">
                                        <label class="form-label required">Password Saat Ini</label>
                                        <input type="password" class="form-control" name="current_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label required">Password Baru</label>
                                        <input type="password" class="form-control" name="new_password" id="newPassword" required>
                                        <div class="password-strength mt-2">
                                            <div class="strength-bar" id="passwordStrength"></div>
                                        </div>
                                        <small class="help-text">Minimal 8 karakter dengan kombinasi huruf dan angka</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label required">Konfirmasi Password Baru</label>
                                        <input type="password" class="form-control" name="confirm_password" id="confirmPassword" required>
                                        <div class="text-danger small mt-1" id="passwordMatch"></div>
                                    </div>
                                    <div class="mt-4">
                                        <button type="submit" name="change_password" class="btn btn-utn-primary">
                                            <i class="fas fa-key me-1"></i>Ubah Password
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 mb-4">
                            <div class="dashboard-card">
                                <h3 class="card-title">
                                    <i class="fas fa-shield-alt"></i>Keamanan Akun
                                </h3>
                                <div class="system-info mb-4">
                                    <div class="info-item">
                                        <span class="info-label">Login Terakhir</span>
                                        <span class="info-value"><?php echo date('d M Y H:i'); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">IP Address</span>
                                        <span class="info-value"><?php echo $_SERVER['REMOTE_ADDR']; ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Browser</span>
                                        <span class="info-value"><?php echo $_SERVER['HTTP_USER_AGENT']; ?></span>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="fw-medium mb-3">Sesi Aktif</h6>
                                    <div class="alert alert-info alert-custom">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Anda saat ini login dari perangkat ini. Sesi akan berakhir setelah 30 menit tidak aktif.
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="fw-medium mb-3">Logout dari Perangkat Lain</h6>
                                    <button class="btn btn-outline-warning" onclick="logoutOtherDevices()">
                                        <i class="fas fa-sign-out-alt me-1"></i>Logout dari Semua Perangkat
                                    </button>
                                    <small class="help-text d-block mt-2">Aksi ini akan logout dari semua perangkat kecuali yang sedang aktif</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Danger Zone -->
                    <div class="danger-zone">
                        <h3 class="card-title">
                            <i class="fas fa-exclamation-triangle"></i>Zona Berbahaya
                        </h3>
                        <p class="mb-4">Hati-hati dengan aksi di bawah ini. Aksi ini tidak dapat dibatalkan.</p>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-medium mb-2">Hapus Akun</h6>
                                <p class="small text-muted mb-3">Menghapus akun akan menghapus semua data Anda dari sistem.</p>
                                <button class="btn btn-outline-danger" onclick="confirmDeleteAccount()">
                                    <i class="fas fa-trash me-1"></i>Hapus Akun Saya
                                </button>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-medium mb-2">Nonaktifkan Akun</h6>
                                <p class="small text-muted mb-3">Akun akan dinonaktifkan tetapi data tetap tersimpan.</p>
                                <button class="btn btn-outline-warning" onclick="confirmDeactivateAccount()">
                                    <i class="fas fa-ban me-1"></i>Nonaktifkan Akun
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- System Tab -->
                <div class="tab-pane fade" id="system" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="dashboard-card">
                                <h3 class="card-title">
                                    <i class="fas fa-calendar-alt"></i>Pengaturan Waktu
                                </h3>
                                <form method="POST" action="">
                                    <div class="mb-3">
                                        <label class="form-label required">Pendaftaran Dibuka</label>
                                        <input type="date" class="form-control" name="pendaftaran_buka" 
                                               value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label required">Pendaftaran Ditutup</label>
                                        <input type="date" class="form-control" name="pendaftaran_tutup" 
                                               value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label required">Test Online Dibuka</label>
                                        <input type="date" class="form-control" name="test_online_buka" 
                                               value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label required">Test Online Ditutup</label>
                                        <input type="date" class="form-control" name="test_online_tutup" 
                                               value="<?php echo date('Y-m-d', strtotime('+60 days')); ?>" required>
                                    </div>
                                    <div class="mt-4">
                                        <button type="submit" name="update_system" class="btn btn-utn-primary">
                                            <i class="fas fa-save me-1"></i>Simpan Pengaturan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 mb-4">
                            <div class="dashboard-card">
                                <h3 class="card-title">
                                    <i class="fas fa-money-bill-wave"></i>Pengaturan Biaya
                                </h3>
                                <form method="POST" action="">
                                    <div class="mb-3">
                                        <label class="form-label required">Biaya Pendaftaran (Rp)</label>
                                        <input type="number" class="form-control" name="biaya_pendaftaran" value="150000" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label required">Biaya Daftar Ulang (Rp)</label>
                                        <input type="number" class="form-control" name="biaya_daftar_ulang" value="5000000" required>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <h6 class="fw-medium mb-3">Metode Pembayaran</h6>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="paymentBank" checked>
                                            <label class="form-check-label" for="paymentBank">
                                                Transfer Bank
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="paymentVirtual">
                                            <label class="form-check-label" for="paymentVirtual">
                                                Virtual Account
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="paymentQris">
                                            <label class="form-check-label" for="paymentQris">
                                                QRIS
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <button type="submit" name="update_system" class="btn btn-utn-primary">
                                            <i class="fas fa-save me-1"></i>Simpan Pengaturan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <div class="col-12 mb-4">
                            <div class="dashboard-card">
                                <h3 class="card-title">
                                    <i class="fas fa-sliders-h"></i>Pengaturan Umum
                                </h3>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="maintenanceMode" checked>
                                            <label class="form-check-label" for="maintenanceMode">
                                                Mode Maintenance
                                            </label>
                                        </div>
                                        <small class="help-text">Nonaktifkan untuk umumkan website</small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="autoBackup" checked>
                                            <label class="form-check-label" for="autoBackup">
                                                Backup Otomatis
                                            </label>
                                        </div>
                                        <small class="help-text">Backup data setiap hari pukul 00:00</small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="emailVerification" checked>
                                            <label class="form-check-label" for="emailVerification">
                                                Verifikasi Email
                                            </label>
                                        </div>
                                        <small class="help-text">Wajib verifikasi email untuk pendaftaran</small>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <button class="btn btn-utn-primary">
                                            <i class="fas fa-save me-1"></i>Simpan Semua Pengaturan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Notifications Tab -->
                <div class="tab-pane fade" id="notifications" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="dashboard-card">
                                <h3 class="card-title">
                                    <i class="fas fa-envelope"></i>Pengaturan Notifikasi
                                </h3>
                                <form method="POST" action="">
                                    <div class="mb-4">
                                        <h6 class="fw-medium mb-3">Channel Notifikasi</h6>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="notif_email" id="notifEmail" checked>
                                            <label class="form-check-label" for="notifEmail">
                                                Email Notifikasi
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="notif_whatsapp" id="notifWhatsapp">
                                            <label class="form-check-label" for="notifWhatsapp">
                                                WhatsApp Notifikasi
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <h6 class="fw-medium mb-3">Jenis Notifikasi</h6>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="notif_daftar_baru" id="notifDaftar" checked>
                                            <label class="form-check-label" for="notifDaftar">
                                                Pendaftaran Baru
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="notif_pembayaran" id="notifPembayaran" checked>
                                            <label class="form-check-label" for="notifPembayaran">
                                                Pembayaran Baru
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="notif_hasil_test" id="notifHasil" checked>
                                            <label class="form-check-label" for="notifHasil">
                                                Hasil Test
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="notif_system" id="notifSystem" checked>
                                            <label class="form-check-label" for="notifSystem">
                                                Update Sistem
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <button type="submit" name="update_notification" class="btn btn-utn-primary">
                                            <i class="fas fa-save me-1"></i>Simpan Pengaturan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 mb-4">
                            <div class="dashboard-card">
                                <h3 class="card-title">
                                    <i class="fas fa-bell"></i>Test Notifikasi
                                </h3>
                                <p class="mb-4">Kirim test notifikasi untuk memastikan pengaturan berfungsi dengan baik.</p>
                                
                                <div class="mb-4">
                                    <label class="form-label mb-2">Test Email</label>
                                    <div class="input-group mb-3">
                                        <input type="email" class="form-control" placeholder="Email tujuan" value="<?php echo htmlspecialchars($admin['email']); ?>">
                                        <button class="btn btn-outline-primary" type="button" onclick="testEmailNotification()">
                                            <i class="fas fa-paper-plane"></i>Kirim
                                        </button>
                                    </div>
                                    <small class="help-text">Kirim test email ke alamat di atas</small>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label mb-2">Test WhatsApp</label>
                                    <div class="input-group mb-3">
                                        <input type="tel" class="form-control" placeholder="Nomor WhatsApp" value="<?php echo htmlspecialchars($admin['no_hp']); ?>">
                                        <button class="btn btn-outline-success" type="button" onclick="testWhatsAppNotification()">
                                            <i class="fab fa-whatsapp"></i>Kirim
                                        </button>
                                    </div>
                                    <small class="help-text">Kirim test WhatsApp ke nomor di atas</small>
                                </div>
                                
                                <div class="alert alert-info alert-custom">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Pastikan email dan nomor WhatsApp sudah benar sebelum mengirim test notifikasi.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Backup Tab -->
                <div class="tab-pane fade" id="backup" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="dashboard-card">
                                <h3 class="card-title">
                                    <i class="fas fa-download"></i>Backup Data
                                </h3>
                                <p class="mb-4">Buat backup data sistem untuk keamanan dan pemulihan.</p>
                                
                                <div class="backup-card mb-4" onclick="createBackup()">
                                    <div class="backup-icon">
                                        <i class="fas fa-database"></i>
                                    </div>
                                    <h5>Buat Backup Sekarang</h5>
                                    <p class="text-muted">Backup seluruh data sistem termasuk database dan file</p>
                                </div>
                                
                                <div class="system-info">
                                    <div class="info-item">
                                        <span class="info-label">Backup Terakhir</span>
                                        <span class="info-value"><?php echo date('d M Y H:i', strtotime('-1 day')); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Ukuran Database</span>
                                        <span class="info-value">15.2 MB</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Backup Otomatis</span>
                                        <span class="badge bg-success">Aktif</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Lokasi Penyimpanan</span>
                                        <span class="info-value">/backups/pmb_utn/</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 mb-4">
                            <div class="dashboard-card">
                                <h3 class="card-title">
                                    <i class="fas fa-upload"></i>Restore Data
                                </h3>
                                <p class="mb-4">Restore data dari backup sebelumnya.</p>
                                
                                <div class="mb-4">
                                    <label class="form-label mb-2">Pilih File Backup</label>
                                    <input type="file" class="form-control" id="backupFile" accept=".sql,.zip">
                                    <small class="help-text">Format yang didukung: SQL, ZIP (Max 100MB)</small>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label mb-2">Backup Tersedia</label>
                                    <select class="form-select" id="availableBackups">
                                        <option value="">Pilih backup dari server...</option>
                                        <option value="backup_20240127.sql">backup_20240127.sql (27 Jan 2024)</option>
                                        <option value="backup_20240126.sql">backup_20240126.sql (26 Jan 2024)</option>
                                        <option value="backup_20240125.sql">backup_20240125.sql (25 Jan 2024)</option>
                                    </select>
                                </div>
                                
                                <div class="alert alert-warning alert-custom">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Peringatan:</strong> Restore data akan menimpa data saat ini. Pastikan sudah membuat backup terbaru.
                                </div>
                                
                                <div class="mt-4">
                                    <button class="btn btn-utn-secondary me-2" onclick="restoreBackup()">
                                        <i class="fas fa-history me-1"></i>Restore
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteOldBackups()">
                                        <i class="fas fa-trash me-1"></i>Hapus Backup Lama
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 mb-4">
                            <div class="dashboard-card">
                                <h3 class="card-title">
                                    <i class="fas fa-cloud"></i>Cloud Backup
                                </h3>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <h6 class="fw-medium mb-3">Google Drive</h6>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="googleDriveSync" checked>
                                            <label class="form-check-label" for="googleDriveSync">
                                                Sinkronisasi ke Google Drive
                                            </label>
                                        </div>
                                        <button class="btn btn-outline-primary btn-sm" onclick="connectGoogleDrive()">
                                            <i class="fab fa-google-drive me-1"></i>Hubungkan Google Drive
                                        </button>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <h6 class="fw-medium mb-3">Jadwal Backup</h6>
                                        <div class="mb-2">
                                            <label class="form-label small">Frekuensi Backup</label>
                                            <select class="form-select form-select-sm">
                                                <option value="daily" selected>Setiap Hari</option>
                                                <option value="weekly">Setiap Minggu</option>
                                                <option value="monthly">Setiap Bulan</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="form-label small">Waktu Backup</label>
                                            <input type="time" class="form-control form-control-sm" value="00:00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <footer class="mt-4 pt-3 border-top text-center text-muted">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> PMB Universitas Teknologi Nusantara • Pengaturan Sistem</p>
                <small>Versi Sistem: 2.5.1 • Terakhir diupdate: <?php echo date('d/m/Y H:i'); ?></small>
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
            
            // Password strength checker
            const newPassword = document.getElementById('newPassword');
            const confirmPassword = document.getElementById('confirmPassword');
            const passwordStrength = document.getElementById('passwordStrength');
            const passwordMatch = document.getElementById('passwordMatch');
            
            if (newPassword) {
                newPassword.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;
                    
                    // Length check
                    if (password.length >= 8) strength++;
                    if (password.length >= 12) strength++;
                    
                    // Complexity checks
                    if (/[A-Z]/.test(password)) strength++;
                    if (/[0-9]/.test(password)) strength++;
                    if (/[^A-Za-z0-9]/.test(password)) strength++;
                    
                    // Update strength bar
                    let width = 0;
                    let colorClass = 'strength-weak';
                    
                    if (strength <= 2) {
                        width = 33;
                        colorClass = 'strength-weak';
                    } else if (strength <= 4) {
                        width = 66;
                        colorClass = 'strength-medium';
                    } else {
                        width = 100;
                        colorClass = 'strength-strong';
                    }
                    
                    passwordStrength.style.width = width + '%';
                    passwordStrength.className = 'strength-bar ' + colorClass;
                });
            }
            
            if (confirmPassword) {
                confirmPassword.addEventListener('input', function() {
                    const newPass = newPassword.value;
                    const confirmPass = this.value;
                    
                    if (confirmPass === '') {
                        passwordMatch.textContent = '';
                    } else if (newPass === confirmPass) {
                        passwordMatch.textContent = '✓ Password cocok';
                        passwordMatch.className = 'text-success small mt-1';
                    } else {
                        passwordMatch.textContent = '✗ Password tidak cocok';
                        passwordMatch.className = 'text-danger small mt-1';
                    }
                });
            }
            
            // Tab handling
            const hash = window.location.hash;
            if (hash) {
                const tab = document.querySelector(`[data-bs-target="${hash}"]`);
                if (tab) {
                    const bsTab = new bootstrap.Tab(tab);
                    bsTab.show();
                }
            }
            
            // Form validation
            const passwordForm = document.getElementById('passwordForm');
            if (passwordForm) {
                passwordForm.addEventListener('submit', function(e) {
                    const newPass = document.getElementById('newPassword').value;
                    const confirmPass = document.getElementById('confirmPassword').value;
                    
                    if (newPass !== confirmPass) {
                        e.preventDefault();
                        alert('Password baru dan konfirmasi password tidak cocok!');
                        return false;
                    }
                    
                    if (newPass.length < 8) {
                        e.preventDefault();
                        alert('Password minimal 8 karakter!');
                        return false;
                    }
                });
            }
        });
        
        // Test Functions
        function testEmailNotification() {
            alert('Test email telah dikirim!');
        }
        
        function testWhatsAppNotification() {
            alert('Test WhatsApp telah dikirim!');
        }
        
        function createBackup() {
            if (confirm('Buat backup data sekarang?')) {
                alert('Backup sedang diproses...');
                // In real implementation, you would make an AJAX call here
            }
        }
        
        function restoreBackup() {
            if (confirm('Restore data dari backup? Data saat ini akan ditimpa.')) {
                alert('Restore sedang diproses...');
                // In real implementation, you would make an AJAX call here
            }
        }
        
        function deleteOldBackups() {
            if (confirm('Hapus backup yang lebih dari 30 hari?')) {
                alert('Menghapus backup lama...');
                // In real implementation, you would make an AJAX call here
            }
        }
        
        function connectGoogleDrive() {
            alert('Membuka halaman otorisasi Google Drive...');
        }
        
        function logoutOtherDevices() {
            if (confirm('Logout dari semua perangkat lain?')) {
                alert('Logout dari perangkat lain...');
                // In real implementation, you would make an AJAX call here
            }
        }
        
        function confirmDeleteAccount() {
            if (confirm('Yakin ingin menghapus akun? Aksi ini tidak dapat dibatalkan!')) {
                const confirmation = prompt('Ketik "DELETE" untuk konfirmasi:');
                if (confirmation === 'DELETE') {
                    alert('Akun akan dihapus. Anda akan logout secara otomatis.');
                    // In real implementation, you would make an AJAX call here
                }
            }
        }
        
        function confirmDeactivateAccount() {
            if (confirm('Yakin ingin menonaktifkan akun?')) {
                alert('Akun akan dinonaktifkan.');
                // In real implementation, you would make an AJAX call here
            }
        }
    </script>
</body>
</html>