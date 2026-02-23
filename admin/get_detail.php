<?php
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    exit('Access denied');
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $query = "SELECT p.*, cm.*, j.nama_jurusan, j.kode_jurusan,
              (SELECT COUNT(*) FROM daftar_ulang du WHERE du.id_pendaftaran = p.id_pendaftaran) as sudah_daftar_ulang,
              (SELECT no_induk_mahasiswa FROM daftar_ulang du WHERE du.id_pendaftaran = p.id_pendaftaran LIMIT 1) as nim
              FROM pendaftaran p
              JOIN calon_mahasiswa cm ON p.id_calon = cm.id_calon
              JOIN jurusan j ON p.id_jurusan = j.id_jurusan
              WHERE p.id_pendaftaran = $id";
    
    $result = mysqli_query($conn, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        ?>
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr><th>No. Test</th><td><?php echo $row['no_test']; ?></td></tr>
                    <tr><th>Nama Lengkap</th><td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td></tr>
                    <tr><th>Email</th><td><?php echo $row['email']; ?></td></tr>
                    <tr><th>No. HP</th><td><?php echo $row['no_hp']; ?></td></tr>
                    <tr><th>Jurusan</th><td><?php echo $row['nama_jurusan']; ?> (<?php echo $row['kode_jurusan']; ?>)</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr><th>Status</th>
                        <td>
                            <?php 
                            $status = $row['status'];
                            $badge_color = $status == 'lulus' ? 'success' : 
                                          ($status == 'tidak_lulus' ? 'danger' : 'warning');
                            ?>
                            <span class="badge bg-<?php echo $badge_color; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $status)); ?>
                            </span>
                        </td>
                    </tr>
                    <tr><th>Nilai Test</th>
                        <td>
                            <?php if($row['nilai_test']): ?>
                                <span class="fs-5"><?php echo number_format($row['nilai_test'], 2); ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Belum test</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr><th>Tanggal Daftar</th><td><?php echo date('d F Y', strtotime($row['tanggal_daftar'])); ?></td></tr>
                    <tr><th>Daftar Ulang</th>
                        <td>
                            <?php if($row['sudah_daftar_ulang'] > 0): ?>
                                <span class="badge bg-success">Sudah</span>
                                <?php if($row['nim']): ?>
                                    <br><small>NIM: <?php echo $row['nim']; ?></small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge bg-warning">Belum</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <?php if($row['asal_sekolah'] || $row['alamat']): ?>
        <div class="row mt-3">
            <div class="col-12">
                <h6>Informasi Lainnya</h6>
                <table class="table table-sm">
                    <?php if($row['asal_sekolah']): ?>
                    <tr><th width="30%">Asal Sekolah</th><td><?php echo htmlspecialchars($row['asal_sekolah']); ?></td></tr>
                    <?php endif; ?>
                    <?php if($row['jurusan_sekolah']): ?>
                    <tr><th>Jurusan Sekolah</th><td><?php echo htmlspecialchars($row['jurusan_sekolah']); ?></td></tr>
                    <?php endif; ?>
                    <?php if($row['tahun_lulus']): ?>
                    <tr><th>Tahun Lulus</th><td><?php echo $row['tahun_lulus']; ?></td></tr>
                    <?php endif; ?>
                    <?php if($row['alamat']): ?>
                    <tr><th>Alamat</th><td><?php echo nl2br(htmlspecialchars($row['alamat'])); ?></td></tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
        <?php endif; ?>
        <?php
    } else {
        echo '<div class="alert alert-danger">Data tidak ditemukan</div>';
    }
} else {
    echo '<div class="alert alert-danger">ID tidak valid</div>';
}
?>