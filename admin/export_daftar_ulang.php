<?php
require_once '../config/database.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=daftar_ulang.xls");

echo "<table border='1'>";
echo "<tr>
        <th>NIM</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Asal Sekolah</th>
        <th>Jurusan</th>
        <th>Status Pembayaran</th>
      </tr>";

$query = mysqli_query($conn, "
    SELECT 
        cm.nim,
        cm.nama_lengkap,
        cm.email,
        cm.asal_sekolah,
        j.nama_jurusan,
        du.status_pembayaran
    FROM daftar_ulang du
    JOIN pendaftaran p ON du.id_pendaftaran = p.id_pendaftaran
    JOIN calon_mahasiswa cm ON p.id_calon = cm.id_calon
    JOIN jurusan j ON p.id_jurusan = j.id_jurusan
");

while ($row = mysqli_fetch_assoc($query)) {
    echo "<tr>
            <td>'".$row['nim']."</td>
            <td>".$row['nama_lengkap']."</td>
            <td>".$row['email']."</td>
            <td>".$row['asal_sekolah']."</td>
            <td>".$row['nama_jurusan']."</td>
            <td>".$row['status_pembayaran']."</td>
          </tr>";
}

echo "</table>";
?>