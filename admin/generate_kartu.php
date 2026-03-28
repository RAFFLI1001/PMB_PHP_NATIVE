<?php
require_once '../config/database.php';

session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// ambil semua mahasiswa yang sudah daftar ulang
$query = "
SELECT du.no_induk_mahasiswa, cm.nama_lengkap, cm.email, cm.no_hp, 
       cm.asal_sekolah, j.nama_jurusan
FROM daftar_ulang du
JOIN pendaftaran p ON du.id_pendaftaran = p.id_pendaftaran
JOIN calon_mahasiswa cm ON p.id_calon = cm.id_calon
JOIN jurusan j ON p.id_jurusan = j.id_jurusan
ORDER BY cm.nama_lengkap ASC
";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Generate Kartu Mahasiswa</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background:#f1f5f9;
    font-family: 'Segoe UI', sans-serif;
}

/* KARTU */
.kartu-wrapper{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(330px,1fr));
    gap:20px;
}

.kartu-mhs{
    width:100%;
    background:white;
    border-radius:14px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
    overflow:hidden;
    border:2px solid #003366;
}

/* HEADER */
.kartu-header{
    background:#003366;
    color:white;
    padding:12px 15px;
    font-weight:600;
    font-size:14px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

/* BODY */
.kartu-body{
    padding:15px;
}

.nama-mhs{
    font-size:18px;
    font-weight:700;
    color:#003366;
}

.data-mhs{
    font-size:14px;
    margin-top:6px;
    color:#334155;
}

.nim{
    margin-top:10px;
    font-weight:700;
    background:#003366;
    color:white;
    padding:6px 10px;
    border-radius:8px;
    display:inline-block;
    letter-spacing:1px;
}

/* PRINT */
@media print{
    body{
        background:white;
    }
    .no-print{
        display:none;
    }
}
</style>
</head>

<body class="p-4">

<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <h3 class="fw-bold">Generate Kartu Mahasiswa </h3>

    <button class="btn btn-primary" onclick="window.print()">
        Print Semua Kartu
    </button>
</div>

<div class="kartu-wrapper">

<?php while($row = mysqli_fetch_assoc($result)): ?>

<div class="kartu-mhs">

    <div class="kartu-header">
        <span> PMB Arten Campus</span>
        <span>Kartu Mahasiswa</span>
    </div>

    <div class="kartu-body">

        <div class="nama-mhs">
            <?php echo htmlspecialchars($row['nama_lengkap']); ?>
        </div>

        <div class="data-mhs">
            Jurusan : <?php echo $row['nama_jurusan']; ?>
        </div>

        <div class="data-mhs">
            Email : <?php echo $row['email']; ?>
        </div>

        <div class="data-mhs">
            No HP : <?php echo $row['no_hp']; ?>
        </div>

        <div class="nim">
            NIM : <?php echo $row['no_induk_mahasiswa']; ?>
        </div>

    </div>

</div>

<?php endwhile; ?>

</div>

</body>
</html>