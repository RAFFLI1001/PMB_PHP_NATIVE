<?php
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Check if already has NIM
    $check = mysqli_query($conn, "SELECT * FROM daftar_ulang WHERE id_pendaftaran = $id");
    
    if (mysqli_num_rows($check) == 0) {
        // Get jurusan info
        $query = "SELECT j.kode_jurusan FROM pendaftaran p 
                  JOIN jurusan j ON p.id_jurusan = j.id_jurusan 
                  WHERE p.id_pendaftaran = $id";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);
            $kode_jurusan = $data['kode_jurusan'];
            
            // Generate NIM: TAHUN+JURUSAN+NOURUT (misal: 24TI001)
            $year = date('y');
            
            // Get next sequence number for this year and jurusan
            $seq_query = "SELECT COUNT(*) as total FROM daftar_ulang du
                         JOIN pendaftaran p ON du.id_pendaftaran = p.id_pendaftaran
                         JOIN jurusan j ON p.id_jurusan = j.id_jurusan
                         WHERE YEAR(du.tanggal_daftar_ulang) = YEAR(CURDATE())
                         AND j.kode_jurusan = '$kode_jurusan'";
            $seq_result = mysqli_query($conn, $seq_query);
            $seq_data = mysqli_fetch_assoc($seq_result);
            $sequence = str_pad($seq_data['total'] + 1, 3, '0', STR_PAD_LEFT);
            
            $nim = $year . $kode_jurusan . $sequence;
            
            // Insert to daftar_ulang
            $insert = "INSERT INTO daftar_ulang (id_pendaftaran, tanggal_daftar_ulang, no_induk_mahasiswa, status_pembayaran) 
                      VALUES ($id, CURDATE(), '$nim', 'belum')";
            
            if (mysqli_query($conn, $insert)) {
                $_SESSION['flash_message'] = "NIM berhasil digenerate: $nim";
            } else {
                $_SESSION['flash_error'] = "Gagal generate NIM: " . mysqli_error($conn);
            }
        } else {
            $_SESSION['flash_error'] = "Data jurusan tidak ditemukan";
        }
    } else {
        $_SESSION['flash_error'] = "Peserta sudah memiliki NIM";
    }
}

header("Location: hasil_test.php");
exit();
?>