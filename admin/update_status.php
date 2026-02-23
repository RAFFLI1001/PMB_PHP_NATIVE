<?php
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';

if ($id > 0 && in_array($status, ['lulus', 'tidak_lulus'])) {
    $query = "UPDATE pendaftaran SET status = '$status' WHERE id_pendaftaran = $id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['flash_message'] = "Status berhasil diubah menjadi " . ($status == 'lulus' ? 'LULUS' : 'TIDAK LULUS');
    } else {
        $_SESSION['flash_error'] = "Gagal mengubah status: " . mysqli_error($conn);
    }
}

header("Location: hasil_test.php");
exit();
?>