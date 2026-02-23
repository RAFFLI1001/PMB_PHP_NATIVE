<div class="col-md-3 col-lg-2 sidebar">
    <div class="d-flex flex-column p-3">
        <div class="text-center mb-4">
            <i class="fas fa-user-circle fa-3x text-white mb-2"></i>
            <h6 class="text-white"><?php echo $_SESSION['admin_nama']; ?></h6>
            <small class="text-white-50">Administrator</small>
        </div>
        
        <h5 class="text-white mb-3">Menu Admin</h5>
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="data_maba.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'data_maba.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users me-2"></i>Data Calon Maba
                </a>
            </li>
            <li class="nav-item">
                <a href="soal_test.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'soal_test.php' ? 'active' : ''; ?>">
                    <i class="fas fa-question-circle me-2"></i>Soal Test
                </a>
            </li>
            <li class="nav-item">
                <a href="hasil_test.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'hasil_test.php' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar me-2"></i>Hasil Test
                </a>
            </li>
            <li class="nav-item">
                <a href="daftar_ulang.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'daftar_ulang.php' ? 'active' : ''; ?>">
                    <i class="fas fa-check-double me-2"></i>Daftar Ulang
                </a>
            </li>
            <li class="nav-item">
                <a href="../logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </li>
        </ul>
    </div>
</div>