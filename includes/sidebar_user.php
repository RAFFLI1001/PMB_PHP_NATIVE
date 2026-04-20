<?php

if (!isset($current_page))   $current_page   = '';
if (!isset($hide_sidebar))   $hide_sidebar   = false;
if (!isset($has_registered)) $has_registered = false;
if (!isset($has_daftar_ulang)) $has_daftar_ulang = false;
?>

<style>
    .sidebar {
        background: linear-gradient(180deg, #003366 0%, #002244 100%);
        height: 100vh;
        position: sticky;
        top: 0;
        overflow-y: auto;
    }

    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.85);
        padding: 12px 16px;
        border-left: 3px solid transparent;
        border-radius: 10px;
        margin: 4px 10px;
        transition: all .2s ease;
    }

    .sidebar .nav-link:hover {
        color: white;
        background: rgba(255, 255, 255, 0.1);
        border-left-color: #28a745;
    }

    .sidebar .nav-link.active {
        color: white;
        background: rgba(255, 255, 255, 0.15);
        border-left-color: #28a745;
    }

    .avatar-container {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        overflow: hidden;
        margin: auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-container i {
        font-size: 90px;
        color: white;
    }

    @media (max-width: 768px) {
        .sidebar {
            height: auto;
            position: relative;
        }
    }
</style>

<div class="col-md-3 col-lg-2 sidebar <?php echo $hide_sidebar ? 'd-none' : 'd-md-block'; ?>">
    <div class="position-sticky pt-4">

        <!-- Profile -->
        <div class="text-center mb-4 px-3">
            <div class="avatar-container mb-2">
                <?php if (!empty($user['foto'])): ?>
                    <img src="../uploads/profile/<?php echo htmlspecialchars($user['foto']); ?>" alt="Foto Profil">
                <?php else: ?>
                    <i class="fas fa-user-circle"></i>
                <?php endif; ?>
            </div>
            <h6 class="text-white mb-1"><?php echo htmlspecialchars($user['nama_lengkap']); ?></h6>
            <small class="text-white-50"><?php echo htmlspecialchars($user['email']); ?></small>
            <div class="mt-2">
                <span class="badge bg-info">Calon Mahasiswa</span>
            </div>
        </div>

        <!-- Menu -->
        <h6 class="text-white-50 mb-2 px-3" style="font-size:11px; letter-spacing:1px;">MENU UTAMA</h6>
        <ul class="nav flex-column mb-4 px-2">

            <li class="nav-item">
                <a href="dashboard.php" class="nav-link <?php echo $current_page == 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a href="profil.php" class="nav-link <?php echo $current_page == 'profil' ? 'active' : ''; ?>">
                    <i class="fas fa-user-edit me-2"></i>Profil Saya
                </a>
            </li>

            <?php if (!$has_registered): ?>
                <li class="nav-item">
                    <a href="pendaftaran.php" class="nav-link <?php echo $current_page == 'pendaftaran' ? 'active' : ''; ?>">
                        <i class="fas fa-file-alt me-2"></i>Pendaftaran
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($has_registered): ?>
                <li class="nav-item">
                    <a href="test.php" class="nav-link <?php echo $current_page == 'test' ? 'active' : ''; ?>">
                        <i class="fas fa-clipboard-list me-2"></i>Test Online
                    </a>
                </li>

                <li class="nav-item">
                    <a href="hasil.php" class="nav-link <?php echo $current_page == 'hasil' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-line me-2"></i>Hasil Test
                    </a>
                </li>

                <?php if (isset($data_pendaftaran['status']) && $data_pendaftaran['status'] == 'lulus' && !$has_daftar_ulang): ?>
                    <li class="nav-item">
                        <a href="daftar_ulang.php" class="nav-link <?php echo $current_page == 'daftar_ulang' ? 'active' : ''; ?>">
                            <i class="fas fa-redo me-2"></i>Daftar Ulang
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

        </ul>

        <!-- Kelengkapan Profil (khusus halaman profil) -->
        <?php if ($current_page == 'profil'): ?>
            <?php
            $completion  = 20;
            $completion += !empty($user['no_hp'])           ? 10 : 0;
            $completion += !empty($user['jenis_kelamin'])   ? 10 : 0;
            $completion += !empty($user['tempat_lahir'])    ? 10 : 0;
            $completion += !empty($user['tanggal_lahir'])   ? 10 : 0;
            $completion += !empty($user['alamat'])          ? 10 : 0;
            $completion += !empty($user['asal_sekolah'])    ? 10 : 0;
            $completion += !empty($user['jurusan_sekolah']) ? 10 : 0;
            $completion += !empty($user['tahun_lulus'])     ? 10 : 0;
            ?>
            <div class="card bg-dark border-0 mb-4 mx-3">
                <div class="card-body p-3">
                    <h6 class="text-white mb-2" style="font-size:13px;">Kelengkapan Profil</h6>
                    <div class="progress mb-2" style="height:8px;">
                        <div class="progress-bar bg-success" style="width: <?php echo $completion; ?>%"></div>
                    </div>
                    <small class="text-white-50"><?php echo $completion; ?>% lengkap</small>
                </div>
            </div>
        <?php endif; ?>

        <!-- Logout -->
        <div class="px-3 mt-2 mb-4">
            <a href="../logout.php" class="btn btn-sm btn-outline-light w-100">
                <i class="fas fa-sign-out-alt me-1"></i>Keluar
            </a>
        </div>

    </div>
</div>