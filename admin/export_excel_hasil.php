<?php
require_once '../config/database.php';

// Header Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=hasil_test_maba.xls");

// Query data
$query = "SELECT p.no_test, cm.nama_lengkap, cm.email, cm.no_hp, cm.asal_sekolah,
                 j.nama_jurusan, p.nilai_test, p.status
          FROM pendaftaran p
          JOIN calon_mahasiswa cm ON p.id_calon = cm.id_calon
          JOIN jurusan j ON p.id_jurusan = j.id_jurusan
          ORDER BY p.nilai_test DESC";

$result = mysqli_query($conn, $query);
?>

<table border="1">
<tr>
    <th>No Test</th>
    <th>Nama Lengkap</th>
    <th>Email</th>
    <th>No HP</th>
    <th>Asal Sekolah</th>
    <th>Jurusan</th>
    <th>Nilai Test</th>
    <th>Status</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?php echo $row['no_test']; ?></td>
    <td><?php echo $row['nama_lengkap']; ?></td>
    <td><?php echo $row['email']; ?></td>
    <td><?php echo $row['no_hp']; ?></td>
    <td><?php echo $row['asal_sekolah']; ?></td>
    <td><?php echo $row['nama_jurusan']; ?></td>
    <td><?php echo $row['nilai_test']; ?></td>
    <td><?php echo ucfirst($row['status']); ?></td>
</tr>
<?php } ?>

</table>