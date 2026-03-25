<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$score = 0;
$total = 0;

if(isset($_POST['jawaban'])){

    foreach($_POST['jawaban'] as $id_soal => $jawaban){

        $query = mysqli_query($conn,"SELECT jawaban_benar FROM soal_test WHERE id_soal='$id_soal'");
        $data = mysqli_fetch_assoc($query);

        if($jawaban == $data['jawaban_benar']){
            $score++;
        }

        $total++;
    }

}

$nilai = ($score / $total) * 100;

$status = $nilai >= 70 ? 'lulus' : 'tidak_lulus';

mysqli_query($conn,"UPDATE pendaftaran SET nilai_test='$nilai', status='$status' WHERE id_calon='$user_id'");

unset($_SESSION['test_verified']);

header("Location: hasil.php");
exit();
?>