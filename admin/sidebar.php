<?php
// Sidebar Admin
?>

<style>
/* SIDEBAR STYLE SAMA PERSIS DENGAN DASHBOARD */

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

.nav-icon {
    width: 24px;
    margin-right: 12px;
    font-size: 1.1rem;
}
</style>


<!-- SIDEBAR -->
<aside class="sidebar">

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