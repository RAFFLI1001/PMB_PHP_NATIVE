<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM calon_mahasiswa WHERE id_calon = $user_id";
$user = mysqli_fetch_assoc(mysqli_query($conn,$query));

$pendaftaran = mysqli_query($conn,"SELECT p.*, j.nama_jurusan FROM pendaftaran p
LEFT JOIN jurusan j ON p.id_jurusan=j.id_jurusan
WHERE p.id_calon=$user_id");

$data_pendaftaran = mysqli_fetch_assoc($pendaftaran);


/* =================
CEK SUDAH PERNAH TEST
================= */

if($data_pendaftaran['nilai_test'] != NULL){
    header("Location: hasil.php");
    exit();
}


/* =================
VERIFIKASI NO TEST
================= */

if(!isset($_SESSION['test_verified'])){

    if(isset($_POST['cek_notest'])){

        if($_POST['no_test']==$data_pendaftaran['no_test']){

            $_SESSION['test_verified']=true;
            header("Location:test.php");
            exit();

        }else{
            $error="No Test tidak sesuai!";
        }

    }

}

$hideNavbar=true;
include '../includes/header.php';
?>

<div class="container-fluid">
<div class="row">

<!-- SIDEBAR -->

<div class="col-md-3 col-lg-2 sidebar d-md-block">
<div class="position-sticky pt-4">

<div class="text-center mb-4 px-3">

<div class="mb-3 avatar-container">

<?php if(!empty($user['foto'])): ?>
<img src="../uploads/profile/<?php echo $user['foto']; ?>">
<?php else: ?>
<i class="fas fa-user-circle"></i>
<?php endif; ?>

</div>

<h6 class="text-white mb-1"><?php echo $user['nama_lengkap']; ?></h6>
<small class="text-white-50"><?php echo $user['email']; ?></small>

<div class="mt-2">
<span class="badge bg-info">Calon Mahasiswa</span>
</div>

</div>

<h6 class="text-white-50 mb-2 px-3">MENU UTAMA</h6>

<ul class="nav flex-column mb-4 px-2">

<li class="nav-item">
<a href="dashboard.php" class="nav-link">
<i class="fas fa-tachometer-alt me-2"></i>Dashboard
</a>
</li>

<li class="nav-item">
<a href="profil.php" class="nav-link">
<i class="fas fa-user-edit me-2"></i>Profil Saya
</a>
</li>

<li class="nav-item">
<a href="test.php" class="nav-link active">
<i class="fas fa-clipboard-list me-2"></i>Test Online
</a>
</li>

<li class="nav-item">
<a href="hasil.php" class="nav-link">
<i class="fas fa-chart-line me-2"></i>Hasil Test
</a>
</li>

</ul>

<div class="px-3 mt-4">
<a href="../logout.php" class="btn btn-sm btn-outline-light w-100">
<i class="fas fa-sign-out-alt me-1"></i>Keluar
</a>
</div>

</div>
</div>


<!-- MAIN CONTENT -->

<div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-3">

<?php if(!isset($_SESSION['test_verified'])){ ?>

<!-- INPUT NO TEST -->

<div class="card shadow">

<div class="card-header bg-primary text-white">
<h4><i class="fas fa-lock me-2"></i>Masukkan No Test</h4>
</div>

<div class="card-body">

<?php if(isset($error)){ ?>

<div class="alert alert-danger">
<?php echo $error; ?>
</div>

<?php } ?>

<form method="POST">

<div class="mb-3">

<label>No Test</label>

<input type="text" name="no_test" class="form-control" required>

</div>

<button type="submit" name="cek_notest" class="btn btn-primary">
Mulai Test
</button>

</form>

</div>
</div>


<?php } else { ?>

<!-- HALAMAN SOAL -->

<div class="card shadow">

<div class="card-header bg-success text-white">
<h4><i class="fas fa-question-circle me-2"></i>Soal Test</h4>
</div>

<div class="card-body">

<form method="POST" action="submit_test.php">

<?php
$soal = mysqli_query($conn,"SELECT * FROM soal_test ORDER BY RAND() LIMIT 10");

$no=1;
while($s=mysqli_fetch_assoc($soal)){
?>

<div class="mb-4">

<p><b><?php echo $no++; ?>. <?php echo $s['pertanyaan']; ?></b></p>

<div class="form-check">
<input class="form-check-input" type="radio" name="jawaban[<?php echo $s['id_soal']; ?>]" value="a">
<label class="form-check-label"><?php echo $s['pilihan_a']; ?></label>
</div>

<div class="form-check">
<input class="form-check-input" type="radio" name="jawaban[<?php echo $s['id_soal']; ?>]" value="b">
<label class="form-check-label"><?php echo $s['pilihan_b']; ?></label>
</div>

<div class="form-check">
<input class="form-check-input" type="radio" name="jawaban[<?php echo $s['id_soal']; ?>]" value="c">
<label class="form-check-label"><?php echo $s['pilihan_c']; ?></label>
</div>

<div class="form-check">
<input class="form-check-input" type="radio" name="jawaban[<?php echo $s['id_soal']; ?>]" value="d">
<label class="form-check-label"><?php echo $s['pilihan_d']; ?></label>
</div>

</div>

<hr>

<?php } ?>

<button class="btn btn-success">
Kirim Jawaban
</button>

</form>

</div>
</div>

<?php } ?>

</div>
</div>
</div>

<style>

.sidebar{
background:linear-gradient(180deg,#003366 0%,#002244 100%);
height:100vh;
position:sticky;
top:0;
}

.sidebar .nav-link{
color:rgba(255,255,255,0.85);
padding:12px 16px;
border-left:3px solid transparent;
border-radius:10px;
margin:4px 10px;
}

.sidebar .nav-link:hover{
color:white;
background:rgba(255,255,255,0.1);
border-left-color:#28a745;
}

.sidebar .nav-link.active{
color:white;
background:rgba(255,255,255,0.15);
border-left-color:#28a745;
}

.avatar-container{
width:90px;
height:90px;
border-radius:50%;
overflow:hidden;
margin:auto;
display:flex;
align-items:center;
justify-content:center;
}

.avatar-container img{
width:100%;
height:100%;
object-fit:cover;
}

.avatar-container i{
font-size:90px;
color:white;
}

</style>

<?php include '../includes/footer.php'; ?>