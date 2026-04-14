<?php
$pageTitle = 'Sınav Sonuçları';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db_online_helpers.php';

$db     = getDB();
$uid    = $_SESSION['user_id'];
$examId = (int)($_GET['exam_id'] ?? 0);

// Sınav bilgisi
$es = $db->prepare("SELECT e.*, c.name AS class_name FROM exams e LEFT JOIN classes c ON c.id=e.class_id WHERE e.id=? AND e.user_id=?");
$es->execute([$examId, $uid]);
$exam = $es->fetch();
if (!$exam) { echo '<div class="content-area"><div class="alert alert-error">Sınav bulunamadı.</div></div>'; require __DIR__.'/includes/footer.php'; exit; }

// Oturumlar
$sess = $db->prepare("
    SELECT es.*, er.test_score, er.classic_score, er.total_score, er.percentage, er.total_points
    FROM exam_sessions es
    LEFT JOIN exam_results er ON er.session_id=es.id
    WHERE es.exam_id=?
    ORDER BY es.submitted_at DESC
");
$sess->execute([$examId]);
$sessions = $sess->fetchAll();

// Sınav soruları
$qs = $db->prepare("
    SELECT q.id, q.type, q.title, q.points, q.correct_opt, u.name AS unit_name
    FROM exam_questions eq
    JOIN questions q ON q.id=eq.question_id
    JOIN units u ON u.id=q.unit_id
    WHERE eq.exam_id=?
    ORDER BY eq.order_num
");
$qs->execute([$examId]);
$examQuestions = $qs->fetchAll();
$testQs   = array_filter($examQuestions, fn($q) => $q['type']==='test');
$klasikQs = array_values(array_filter($examQuestions, fn($q) => $q['type']==='klasik'));
$totalMax = array_sum(array_column($examQuestions, 'points'));

$selectedSession = isset($_GET['session_id']) ? (int)$_GET['session_id'] : 0;
?>

<div class="topbar">
    <h2>📊 Sınav Sonuçları</h2>
    <div style="display:flex;gap:8px;">
        <a href="online_sinav.php" class="btn btn-secondary btn-sm">← Geri</a>
        <a href="exports/exam_excel.php?exam_id=<?= $examId ?>" class="btn btn-success btn-sm">📥 Excel İndir</a>
    </div>
</div>

<div class="content-area">

<div class="breadcrumb">
    <a href="online_sinav.php">Online Sınavlar</a>
    <span class="sep">›</span>
    <span><?= sanitize($exam['name']) ?></span>
</div>

<!-- ÖZET KARTLAR -->
<?php
$submitted = array_filter($sessions, fn($s) => in_array($s['status'],['submitted','graded']));
$graded    = array_filter($sessions, fn($s) => $s['status']==='graded');
$avgScore  = count($graded) > 0 ? round(array_sum(array_column(iterator_to_array((function($g){foreach($g as $x) yield $x;})($graded)), 'total_score')) / count($graded), 1) : 0;
// Daha temiz ortalama hesabı
$gradedArr = array_values($graded);
$avgScore  = count($gradedArr) > 0 ? round(array_sum(array_column($gradedArr,'percentage'))/count($gradedArr),1) : 0;
?>
<div class="grid grid-3" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:#dbeafe;">👥</div>
        <div><div class="stat-value"><?= count($submitted) ?></div><div class="stat-label">Teslim Eden</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#d1fae5;">✅</div>
        <div><div class="stat-value"><?= count($gradedArr) ?></div><div class="stat-label">Notlandırılan</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef3c7;">📈</div>
        <div><div class="stat-value">%<?= $avgScore ?></div><div class="stat-label">Sınıf Ortalaması</div></div>
    </div>
</div>

<div class="grid grid-2" style="align-items:start;">
<!-- ÖĞRENCI LİSTESİ -->
<div class="card">
    <div class="card-header"><span class="card-title">👥 Öğrenci Listesi</span></div>
    <div style="padding:0;">
    <?php if (empty($sessions)): ?>
        <div class="empty-state" style="padding:32px;">
            <div class="icon">📭</div>
            <p>Henüz sınava giren öğrenci yok</p>
        </div>
    <?php else: ?>
        <div class="table-wrap">
        <table>
        <thead><tr>
            <th>Öğrenci</th><th>No</th><th>Durum</th><th>Puan</th><th>İşlem</th>
        </tr></thead>
        <tbody>
        <?php foreach ($sessions as $s):
            $statusBadge = match($s['status']) {
                'submitted' => '<span class="badge badge-yellow">⏳ Bekliyor</span>',
                'graded'    => '<span class="badge badge-green">✅ Notlandı</span>',
                default     => '<span class="badge badge-blue">🔄 Devam</span>',
            };
        ?>
        <tr style="<?= $s['id']==$selectedSession ? 'background:var(--primary-light)' : '' ?>">
            <td><strong><?= sanitize($s['student_name'].' '.$s['student_surname']) ?></strong></td>
            <td><?= sanitize($s['student_no']) ?></td>
            <td><?= $statusBadge ?></td>
            <td>
                <?php if ($s['status']==='graded'): ?>
                <strong style="color:var(--primary);"><?= $s['total_score'] ?></strong>
                <span style="color:var(--text-muted);font-size:11px;">/ <?= $totalMax ?></span>
                <div style="font-size:11px;color:var(--text-muted);">%<?= $s['percentage'] ?></div>
                <?php else: ?>—<?php endif; ?>
            </td>
            <td>
                <?php if ($s['status']==='submitted' && !empty($klasikQs)): ?>
                <a href="online_sonuclar.php?exam_id=<?= $examId ?>&session_id=<?= $s['id'] ?>" class="btn btn-sm btn-primary">✏️ Not Ver</a>
                <?php elseif ($s['status']==='graded'): ?>
                <a href="online_sonuclar.php?exam_id=<?= $examId ?>&session_id=<?= $s['id'] ?>" class="btn btn-sm btn-secondary">👁️ Gör</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        </table>
        </div>
    <?php endif; ?>
    </div>
</div>

<!-- NOT VERME PANELİ -->
<div class="card" id="grading-panel">
<?php if ($selectedSession):
    // Oturumu bul
    $curSession = current(array_filter($sessions, fn($s) => $s['id']==$selectedSession));
    if ($curSession):
        // Cevaplar
        $answers = getSessionAnswers($selectedSession);
        $answerMap = [];
        foreach ($answers as $a) $answerMap[$a['question_id']] = $a;
?>
    <div class="card-header">
        <span class="card-title">✏️ <?= sanitize($curSession['student_name'].' '.$curSession['student_surname']) ?></span>
        <span class="badge badge-blue">No: <?= sanitize($curSession['student_no']) ?></span>
    </div>
    <div class="card-body" style="padding:16px;">

    <?php if (!empty($testQs)): ?>
    <!-- TEST SONUÇLARI (otomatik) -->
    <div style="background:var(--bg);border-radius:8px;padding:12px;margin-bottom:16px;">
        <div style="font-weight:700;font-size:13px;margin-bottom:8px;">🔵 Test Soruları (Otomatik)</div>
        <?php $testTotal = 0; foreach ($testQs as $q):
            $ans = $answerMap[$q['id']] ?? null;
            $isCorrect = $ans && $ans['is_correct'];
            $selected  = $ans['selected_opt'] ?? '—';
            if ($isCorrect) $testTotal += $q['points'];
        ?>
        <div style="display:flex;align-items:center;gap:8px;padding:6px 0;border-bottom:1px solid var(--border);font-size:12px;">
            <span style="<?= $isCorrect ? 'color:var(--success)' : 'color:var(--danger)' ?>;font-size:16px;"><?= $isCorrect ? '✓' : '✗' ?></span>
            <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= sanitize($q['title']) ?>"><?= sanitize(mb_substr($q['title'],0,50,'UTF-8')) ?>...</span>
            <span style="color:var(--text-muted);">Cevap: <strong><?= $selected ?></strong> / Doğru: <strong><?= $q['correct_opt'] ?></strong></span>
            <span style="<?= $isCorrect ? 'color:var(--success)' : 'color:var(--danger)' ?>;font-weight:700;"><?= $isCorrect ? '+'.$q['points'] : '0' ?>p</span>
        </div>
        <?php endforeach; ?>
        <div style="margin-top:8px;font-weight:700;color:var(--primary);">Test Puanı: <?= $testTotal ?> / <?= array_sum(array_column(iterator_to_array((function($tq){foreach($tq as $q) yield $q;})($testQs)),'points')) ?></div>
    </div>
    <?php endif; ?>

    <?php if (!empty($klasikQs)): ?>
    <!-- KLASİK SORULAR (hoça not verir) -->
    <div style="font-weight:700;font-size:13px;margin-bottom:10px;">📝 Klasik Sorular — Not Girin</div>
    <?php foreach ($klasikQs as $i => $q):
        $ans   = $answerMap[$q['id']] ?? null;
        $score = $ans['score'] ?? '';
    ?>
    <div style="background:var(--bg);border-radius:8px;padding:12px;margin-bottom:10px;">
        <div style="font-weight:600;font-size:13px;margin-bottom:6px;"><?= ($i+1) ?>. <?= sanitize($q['title']) ?></div>
        <div style="background:#fff;border:1px solid var(--border);border-radius:6px;padding:10px;font-size:13px;color:var(--text-muted);margin-bottom:8px;min-height:50px;">
            <?= $ans ? nl2br(sanitize($ans['answer'] ?? '(Cevap yazılmadı)')) : '<em>Cevap verilmedi</em>' ?>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <label style="font-size:12px;color:var(--text-muted);">Not (max <?= $q['points'] ?>p):</label>
            <input type="number" id="score-<?= $q['id'] ?>" class="form-control" style="width:80px;"
                   value="<?= $score ?>" min="0" max="<?= $q['points'] ?>" step="0.5"
                   onchange="saveGrade(<?= $selectedSession ?>, <?= $q['id'] ?>, this.value, <?= $q['points'] ?>)">
            <span style="font-size:12px;color:var(--text-muted);">/ <?= $q['points'] ?> p</span>
        </div>
    </div>
    <?php endforeach; ?>

    <button class="btn btn-success btn-block" onclick="finalizeGrade(<?= $selectedSession ?>)" style="margin-top:8px;">
        💾 Notu Kaydet & Tamamla
    </button>
    <?php else: ?>
    <div class="alert alert-success">✅ Bu sınavda klasik soru yok. Not otomatik hesaplandı.</div>
    <?php endif; ?>

    <?php if ($curSession['status']==='graded'): ?>
    <div style="margin-top:14px;background:var(--primary-light);border-radius:8px;padding:14px;text-align:center;">
        <div style="font-size:24px;font-weight:800;color:var(--primary);"><?= $curSession['total_score'] ?> / <?= $totalMax ?></div>
        <div style="font-size:13px;color:var(--text-muted);">Toplam Puan — %<?= $curSession['percentage'] ?></div>
    </div>
    <?php endif; ?>

    <?php endif; ?>
<?php else: ?>
    <div class="card-header"><span class="card-title">✏️ Not Verme Paneli</span></div>
    <div class="empty-state" style="padding:32px;">
        <div class="icon">👈</div>
        <p>Soldan bir öğrenci seçin</p>
    </div>
<?php endif; ?>
</div>

</div><!-- grid -->
</div><!-- content-area -->

<script>
async function saveGrade(sessionId, questionId, score, max) {
    score = Math.min(parseFloat(max), Math.max(0, parseFloat(score)||0));
    const fd = new FormData();
    fd.append('action','grade');
    fd.append('session_id', sessionId);
    fd.append('question_id', questionId);
    fd.append('score', score);
    const res  = await fetch('api/online_exam.php', {method:'POST',body:fd});
    const data = await res.json();
    if (data.error) alert(data.error);
}

async function finalizeGrade(sessionId) {
    const fd = new FormData();
    fd.append('action','finalize');
    fd.append('session_id', sessionId);
    const res  = await fetch('api/online_exam.php', {method:'POST',body:fd});
    const data = await res.json();
    if (data.error) showToast(data.error,'error');
    else { showToast(`✅ Not kaydedildi! Toplam: ${data.total} puan (%${data.pct})`, 'success'); setTimeout(()=>location.reload(),1200); }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
