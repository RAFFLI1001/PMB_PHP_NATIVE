<?php
require_once '../config/database.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get test results
$query = "SELECT p.*, j.nama_jurusan, 
          (SELECT COUNT(*) FROM daftar_ulang du WHERE du.id_pendaftaran = p.id_pendaftaran) as sudah_daftar_ulang
          FROM pendaftaran p
          JOIN jurusan j ON p.id_jurusan = j.id_jurusan
          WHERE p.id_calon = $user_id";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: pendaftaran.php");
    exit();
}

$data = mysqli_fetch_assoc($result);
?>
<?php include '../includes/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-white 
                    <?php echo $data['status'] == 'lulus' ? 'bg-success' : 
                           ($data['status'] == 'tidak_lulus' ? 'bg-danger' : 'bg-warning'); ?>">
                    <h4 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        <?php if($data['status'] == 'lulus'): ?>
                            SELAMAT! Anda LULUS Seleksi
                        <?php elseif($data['status'] == 'tidak_lulus'): ?>
                            Hasil Seleksi
                        <?php else: ?>
                            Status Pendaftaran
                        <?php endif; ?>
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Status Banner -->
                    <div class="text-center mb-4">
                        <?php if($data['status'] == 'lulus'): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle fa-3x mb-3"></i>
                                <h3>Selamat! Anda LULUS Seleksi PMB UTN</h3>
                                <p class="lead">Silakan lakukan daftar ulang untuk menyelesaikan proses pendaftaran</p>
                            </div>
                        <?php elseif($data['status'] == 'tidak_lulus'): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-times-circle fa-3x mb-3"></i>
                                <h3>Maaf, Anda TIDAK LULUS Seleksi</h3>
                                <p>Silakan coba lagi di periode berikutnya</p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-clock fa-3x mb-3"></i>
                                <h3>Menunggu Hasil Test</h3>
                                <p>Silakan selesaikan test online untuk melihat hasil seleksi</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Results Details -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6><i class="fas fa-id-card me-2"></i>Data Pendaftaran</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">No. Test</th>
                                            <td><?php echo $data['no_test']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Nama</th>
                                            <td><?php echo $_SESSION['user_nama']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Jurusan</th>
                                            <td><?php echo $data['nama_jurusan']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Daftar</th>
                                            <td><?php echo date('d F Y', strtotime($data['tanggal_daftar'])); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6><i class="fas fa-chart-line me-2"></i>Hasil Test</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Status</th>
                                            <td>
                                                <?php 
                                                $status = $data['status'];
                                                $badge_color = $status == 'lulus' ? 'success' : 
                                                              ($status == 'tidak_lulus' ? 'danger' : 'warning');
                                                ?>
                                                <span class="badge bg-<?php echo $badge_color; ?> fs-6">
                                                    <?php echo ucfirst(str_replace('_', ' ', $status)); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        
                                        <?php if($data['nilai_test']): ?>
                                        <tr>
                                            <th>Nilai</th>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                                        <div class="progress-bar 
                                                            <?php echo $data['nilai_test'] >= 70 ? 'bg-success' : 'bg-danger'; ?>" 
                                                            role="progressbar" 
                                                            style="width: <?php echo min($data['nilai_test'], 100); ?>%;">
                                                        </div>
                                                    </div>
                                                    <strong><?php echo number_format($data['nilai_test'], 2); ?></strong>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Keterangan</th>
                                            <td>
                                                <?php if($data['nilai_test'] >= 70): ?>
                                                    <span class="text-success">Lulus (Nilai ≥ 70)</span>
                                                <?php else: ?>
                                                    <span class="text-danger">Tidak Lulus (Nilai < 70)</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php else: ?>
                                        <tr>
                                            <th>Nilai</th>
                                            <td><span class="badge bg-secondary">Belum ada</span></td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Next Steps -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-list-check me-2"></i>Langkah Selanjutnya</h5>
                        </div>
                        <div class="card-body">
                            <?php if($data['status'] == 'pending'): ?>
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-circle me-2"></i>Silakan ikuti test online</h6>
                                <p>Anda belum mengikuti test online. Silakan klik tombol di bawah untuk mulai test.</p>
                                <a href="test.php" class="btn btn-warning">
                                    <i class="fas fa-play-circle me-1"></i>Mulai Test Online
                                </a>
                            </div>
                            
                            <?php elseif($data['status'] == 'lulus'): ?>
                                <?php if($data['sudah_daftar_ulang'] > 0): ?>
                                <div class="alert alert-success">
                                    <h6><i class="fas fa-check-circle me-2"></i>Pendaftaran Selesai</h6>
                                    <p>Selamat! Anda telah menyelesaikan seluruh proses pendaftaran.</p>
                                    <p>Informasi lebih lanjut akan dikirimkan via email.</p>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-arrow-right me-2"></i>Lakukan Daftar Ulang</h6>
                                    <p>Silakan lakukan daftar ulang untuk menyelesaikan proses pendaftaran.</p>
                                    <a href="daftar_ulang.php" class="btn btn-primary">
                                        <i class="fas fa-check-double me-1"></i>Daftar Ulang Sekarang
                                    </a>
                                </div>
                                <?php endif; ?>
                                
                            <?php elseif($data['status'] == 'tidak_lulus'): ?>
                            <div class="alert alert-info">
                                <h6><i class="fas fa-redo me-2"></i>Ikuti Periode Berikutnya</h6>
                                <p>Anda dapat mendaftar kembali di periode berikutnya. Terus semangat!</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="mt-4 text-center">
                        <a href="dashboard.php" class="btn btn-secondary me-2">
                            <i class="fas fa-home me-1"></i>Kembali ke Dashboard
                        </a>
                        
                        <?php if($data['status'] == 'pending'): ?>
                        <a href="test.php" class="btn btn-primary">
                            <i class="fas fa-play me-1"></i>Mulai Test
                        </a>
                        <?php elseif($data['status'] == 'lulus' && $data['sudah_daftar_ulang'] == 0): ?>
                        <a href="daftar_ulang.php" class="btn btn-success">
                            <i class="fas fa-check-double me-1"></i>Daftar Ulang
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>