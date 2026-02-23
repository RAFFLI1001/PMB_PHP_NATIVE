<?php
require_once '../config/database.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if user has registered
$pendaftaran = mysqli_query($conn, "SELECT * FROM pendaftaran WHERE id_calon = $user_id");
if (mysqli_num_rows($pendaftaran) == 0) {
    header("Location: pendaftaran.php");
    exit();
}

$pendaftaran_data = mysqli_fetch_assoc($pendaftaran);

// Check if already completed test
if ($pendaftaran_data['status'] != 'pending') {
    header("Location: hasil.php");
    exit();
}

// Check if test is in progress
if (!isset($_SESSION['test_started'])) {
    // Start new test session
    $_SESSION['test_started'] = true;
    $_SESSION['start_time'] = time();
    $_SESSION['test_duration'] = 3600; // 60 minutes in seconds
    $_SESSION['answers'] = array();
    
    // Get random questions
    $questions_query = mysqli_query($conn, "SELECT * FROM soal_test ORDER BY RAND() LIMIT 20");
    $questions = array();
    while($row = mysqli_fetch_assoc($questions_query)) {
        $questions[] = $row;
    }
    $_SESSION['questions'] = $questions;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_test'])) {
    // Calculate score
    $score = 0;
    $total_questions = count($_SESSION['questions']);
    
    foreach ($_SESSION['questions'] as $index => $question) {
        $answer_key = 'question_' . $question['id_soal'];
        if (isset($_POST[$answer_key]) && $_POST[$answer_key] == $question['jawaban_benar']) {
            $score++;
        }
    }
    
    $final_score = ($score / $total_questions) * 100;
    $status = $final_score >= 70 ? 'lulus' : 'tidak_lulus';
    
    // Update database
    $query = "UPDATE pendaftaran SET nilai_test = $final_score, status = '$status' WHERE id_calon = $user_id";
    mysqli_query($conn, $query);
    
    // Clear test session
    unset($_SESSION['test_started']);
    unset($_SESSION['questions']);
    unset($_SESSION['answers']);
    unset($_SESSION['start_time']);
    
    // Redirect to results
    header("Location: hasil.php");
    exit();
}

// Calculate remaining time
$remaining_time = $_SESSION['test_duration'] - (time() - $_SESSION['start_time']);
if ($remaining_time < 0) $remaining_time = 0;

// Auto-submit when time is up
if ($remaining_time <= 0 && isset($_SESSION['test_started'])) {
    header("Location: test.php?timeup=1");
    exit();
}
?>
<?php include '../includes/header.php'; ?>

<div class="container py-4">
    <!-- Test Header -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="mb-0"><i class="fas fa-file-alt me-2"></i>Test Online PMB UTN</h4>
                </div>
                <div class="col-md-6 text-end">
                    <div id="timer" class="fs-4">
                        <i class="fas fa-clock me-1"></i>
                        <span id="minutes"><?php echo floor($remaining_time / 60); ?></span>:<span id="seconds"><?php echo $remaining_time % 60; ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <strong>No. Test:</strong> <?php echo $pendaftaran_data['no_test']; ?>
                </div>
                <div class="col-md-4">
                    <strong>Nama:</strong> <?php echo $_SESSION['user_nama']; ?>
                </div>
                <div class="col-md-4">
                    <strong>Total Soal:</strong> <?php echo count($_SESSION['questions']); ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Test Instructions -->
    <div class="alert alert-info mb-4">
        <h5><i class="fas fa-info-circle me-2"></i>Petunjuk Pengerjaan:</h5>
        <ol class="mb-0">
            <li>Test terdiri dari <?php echo count($_SESSION['questions']); ?> soal pilihan ganda</li>
            <li>Waktu pengerjaan: 60 menit</li>
            <li>Pilih satu jawaban yang paling benar</li>
            <li>Test akan otomatis tersubmit ketika waktu habis</li>
            <li>Anda tidak dapat mengulang test setelah selesai</li>
        </ol>
    </div>
    
    <!-- Test Questions Form -->
    <form method="POST" action="" id="testForm">
        <?php foreach ($_SESSION['questions'] as $index => $question): ?>
        <div class="card mb-3 question-card">
            <div class="card-body">
                <h5 class="card-title">Soal <?php echo $index + 1; ?></h5>
                <p class="card-text"><?php echo nl2br(htmlspecialchars($question['pertanyaan'])); ?></p>
                
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" 
                           name="question_<?php echo $question['id_soal']; ?>" 
                           id="q<?php echo $question['id_soal']; ?>_a" 
                           value="a"
                           <?php echo isset($_SESSION['answers'][$question['id_soal']]) && $_SESSION['answers'][$question['id_soal']] == 'a' ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="q<?php echo $question['id_soal']; ?>_a">
                        <strong>A.</strong> <?php echo htmlspecialchars($question['pilihan_a']); ?>
                    </label>
                </div>
                
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" 
                           name="question_<?php echo $question['id_soal']; ?>" 
                           id="q<?php echo $question['id_soal']; ?>_b" 
                           value="b"
                           <?php echo isset($_SESSION['answers'][$question['id_soal']]) && $_SESSION['answers'][$question['id_soal']] == 'b' ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="q<?php echo $question['id_soal']; ?>_b">
                        <strong>B.</strong> <?php echo htmlspecialchars($question['pilihan_b']); ?>
                    </label>
                </div>
                
                <?php if($question['pilihan_c']): ?>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" 
                           name="question_<?php echo $question['id_soal']; ?>" 
                           id="q<?php echo $question['id_soal']; ?>_c" 
                           value="c"
                           <?php echo isset($_SESSION['answers'][$question['id_soal']]) && $_SESSION['answers'][$question['id_soal']] == 'c' ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="q<?php echo $question['id_soal']; ?>_c">
                        <strong>C.</strong> <?php echo htmlspecialchars($question['pilihan_c']); ?>
                    </label>
                </div>
                <?php endif; ?>
                
                <?php if($question['pilihan_d']): ?>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" 
                           name="question_<?php echo $question['id_soal']; ?>" 
                           id="q<?php echo $question['id_soal']; ?>_d" 
                           value="d"
                           <?php echo isset($_SESSION['answers'][$question['id_soal']]) && $_SESSION['answers'][$question['id_soal']] == 'd' ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="q<?php echo $question['id_soal']; ?>_d">
                        <strong>D.</strong> <?php echo htmlspecialchars($question['pilihan_d']); ?>
                    </label>
                </div>
                <?php endif; ?>
                
                <div class="mt-3">
                    <span class="badge bg-info">Kategori: <?php echo $question['kategori']; ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <!-- Submit Button -->
        <div class="card mt-4">
            <div class="card-body text-center">
                <button type="submit" name="submit_test" class="btn btn-success btn-lg px-5" 
                        onclick="return confirm('Apakah Anda yakin ingin mengakhiri test?')">
                    <i class="fas fa-paper-plane me-2"></i>Selesai & Submit Jawaban
                </button>
                <p class="text-muted mt-2">Pastikan semua soal telah terjawab sebelum submit</p>
            </div>
        </div>
    </form>
</div>

<script>
// Timer countdown
function startTimer(duration) {
    var timer = duration, minutes, seconds;
    setInterval(function () {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        document.getElementById('minutes').textContent = minutes;
        document.getElementById('seconds').textContent = seconds;

        if (--timer < 0) {
            document.getElementById('testForm').submit();
        }
        
        // Change color when less than 5 minutes
        if (timer < 300) {
            document.getElementById('timer').classList.add('text-danger');
        }
    }, 1000);
}

// Start timer
startTimer(<?php echo $remaining_time; ?>);

// Auto-save answers
document.querySelectorAll('input[type="radio"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        var formData = new FormData(document.getElementById('testForm'));
        
        // Save to localStorage as backup
        var answers = {};
        document.querySelectorAll('input[type="radio"]:checked').forEach(function(checkedRadio) {
            answers[checkedRadio.name] = checkedRadio.value;
        });
        localStorage.setItem('test_answers', JSON.stringify(answers));
    });
});

// Load saved answers from localStorage on page load
window.addEventListener('load', function() {
    var savedAnswers = localStorage.getItem('test_answers');
    if (savedAnswers) {
        savedAnswers = JSON.parse(savedAnswers);
        for (var name in savedAnswers) {
            var radio = document.querySelector('input[name="' + name + '"][value="' + savedAnswers[name] + '"]');
            if (radio) radio.checked = true;
        }
    }
});

// Warn before leaving page
window.addEventListener('beforeunload', function (e) {
    if (document.getElementById('testForm')) {
        e.preventDefault();
        e.returnValue = 'Jawaban Anda akan hilang jika Anda meninggalkan halaman ini. Apakah Anda yakin?';
    }
});
</script>

<?php include '../includes/footer.php'; ?>