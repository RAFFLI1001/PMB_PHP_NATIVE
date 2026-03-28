<?php
require_once '../config/database.php';

session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Header untuk download Excel
header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Data_Daftar_Ulang.xls");

echo "
<table border='1'>
<tr>
    <th>NIM</th>
    <th>No Test</th>
    <th>Nama Lengkap</th>
    <th>Email</th>
    <th>No HP</th>
    <th>Asal Sekolah</th>
    <th>Jurusan</th>
    <th>Status Pembayaran</th>
    <th>Tanggal Daftar Ulang</th>
</tr>
";

// Query data
$query = "
SELECT du.no_induk_mahasiswa, p.no_test, cm.nama_lengkap, cm.email, cm.no_hp,
       cm.asal_sekolah, j.nama_jurusan, du.status_pembayaran, du.tanggal_daftar_ulang
FROM daftar_ulang du
JOIN pendaftaran p ON du.id_pendaftaran = p.id_pendaftaran
JOIN calon_mahasiswa cm ON p.id_calon = cm.id_calon
JOIN jurusan j ON p.id_jurusan = j.id_jurusan
ORDER BY du.tanggal_daftar_ulang DESC
";

$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    echo "
    <tr>
        <td>{$row['no_induk_mahasiswa']}</td>
        <td>{$row['no_test']}</td>
        <td>{$row['nama_lengkap']}</td>
        <td>{$row['email']}</td>
        <td>{$row['no_hp']}</td>
        <td>{$row['asal_sekolah']}</td>
        <td>{$row['nama_jurusan']}</td>
        <td>{$row['status_pembayaran']}</td>
        <td>{$row['tanggal_daftar_ulang']}</td>
    </tr>
    ";
}

echo "</table>";
?>