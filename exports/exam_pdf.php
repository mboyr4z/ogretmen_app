<?php
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$db     = getDB();
$uid    = $_SESSION['user_id'];
$examId = (int)($_GET['id'] ?? 0);

$es = $db->prepare("SELECT e.*, c.name AS class_name, c.grade FROM exams e LEFT JOIN classes c ON c.id=e.class_id WHERE e.id=? AND e.user_id=?");
$es->execute([$examId, $uid]);
$exam = $es->fetch();
if (!$exam) { die('Sınav bulunamadı.'); }

$qs = $db->prepare("
    SELECT q.*, u.name AS unit_name, eq.order_num
    FROM exam_questions eq
    JOIN questions q ON q.id=eq.question_id
    JOIN units u ON u.id=q.unit_id
    WHERE eq.exam_id=?
    ORDER BY eq.order_num, eq.id
");
$qs->execute([$examId]);
$questions = $qs->fetchAll();

$testQs     = array_values(array_filter($questions, fn($q) => $q['type'] === 'test'));
$klasikQs   = array_values(array_filter($questions, fn($q) => $q['type'] === 'klasik'));
$tfQs       = array_values(array_filter($questions, fn($q) => $q['type'] === 'true_false'));
$user       = getCurrentUser();

// Çerçeve seçenekleri ve bölüm sırası GET'ten gelir
$testFrame   = (int)($_GET['tf']  ?? 0);
$klasikFrame = (int)($_GET['kf']  ?? 0);
$rawOrder    = $_GET['order'] ?? 'test,tf,klasik';
$validKeys   = ['test', 'tf', 'klasik'];
$sectionOrder = array_filter(explode(',', $rawOrder), fn($k) => in_array($k, $validKeys));
if (count($sectionOrder) < 3) $sectionOrder = $validKeys;
$sectionOrder = array_values($sectionOrder);

// Boyut ve sütun parametreleri
$fontSize   = in_array((int)($_GET['fs'] ?? 11), [9,10,11,12,13]) ? (int)$_GET['fs'] : 11;
$testCols   = in_array((int)($_GET['test_cols']   ?? 1), [1,2,3]) ? (int)$_GET['test_cols']   : 1;
$klasikCols = in_array((int)($_GET['klasik_cols'] ?? 1), [1,2])   ? (int)$_GET['klasik_cols'] : 1;
$tfCols     = in_array((int)($_GET['tf_cols']     ?? 1), [1,2,3]) ? (int)$_GET['tf_cols']     : 1;

$qFs     = $fontSize . 'pt';
$optFs   = max(8, $fontSize - 1) . 'pt';
$titleFs = ($fontSize + 1) . 'pt';

$examTitle  = htmlspecialchars($exam['name']);
$schoolName = $exam['school_name'] ? htmlspecialchars($exam['school_name']) : '';
$acYear     = $exam['academic_year'] ? htmlspecialchars($exam['academic_year']) : '';
$period     = (int)($exam['period'] ?? 1);
$examNum    = (int)($exam['exam_number'] ?? 1);
$gradeInfo  = $exam['grade'] ? $exam['grade'] . '. Sınıf' : ($exam['class_name'] ? htmlspecialchars($exam['class_name']) : '');

$optKeys = ['a','b','c','d','e'];
$optCols = ['a'=>'option_a','b'=>'option_b','c'=>'option_c','d'=>'option_d','e'=>'option_e'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title><?= $examTitle ?></title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;600;700&display=swap');
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Noto Sans', Arial, sans-serif; font-size: <?= $qFs ?>; color: #000; background: #fff; }
.page { max-width: 210mm; margin: 0 auto; padding: 14mm 16mm 12mm; }

/* ── BAŞLIK ─────────────────────────────────────────── */
.exam-header { text-align:center; margin-bottom:10px; }
.school-name { font-size:<?= $titleFs ?>; font-weight:700; text-transform:uppercase; letter-spacing:.03em; }
.exam-meta   { font-size:<?= $optFs ?>; font-weight:600; margin-top:3px; }
.exam-title  { font-size:<?= $qFs ?>; font-weight:700; margin-top:4px; border-top:1.5px solid #000; border-bottom:1.5px solid #000; padding:4px 0; }

/* ── ÖĞRENCİ BİLGİ KUTUSU ────────────────────────────── */
.info-row { display:flex; border:1.5px solid #000; margin-bottom:12px; }
.info-cell { flex:1; padding:5px 10px 16px; border-right:1px solid #000; font-size:<?= $optFs ?>; }
.info-cell:last-child { border-right:none; }
.info-cell strong { display:block; font-size:8pt; margin-bottom:2px; }

/* ── BÖLÜM BAŞLIĞI ───────────────────────────────────── */
.section-title {
    font-size:<?= $qFs ?>; font-weight:700; margin:12px 0 8px;
    padding-bottom:3px; border-bottom:1.5px solid #000;
    text-transform:uppercase; letter-spacing:.04em;
}

/* ── TEST GRID ───────────────────────────────────────── */
.test-grid { display:grid; grid-template-columns:repeat(<?= $testCols ?>,1fr); gap:6px 14px; }

/* ── SORU KUTUSU (düz) ───────────────────────────────── */
.q-box { padding:5px 0; break-inside:avoid; page-break-inside:avoid; }

/* ── SORU KUTUSU (çerçeveli) ─────────────────────────── */
.q-box-frame {
    border:1.2px solid #000; border-radius:2px;
    padding:6px 8px; break-inside:avoid; page-break-inside:avoid; margin-bottom:4px;
}

.q-head  { display:flex; align-items:flex-start; gap:6px; margin-bottom:4px; }
.q-num   { min-width:20px; height:20px; border:1.5px solid #000; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:8.5pt; font-weight:700; flex-shrink:0; }
.q-text  { font-weight:600; font-size:<?= $qFs ?>; line-height:1.4; flex:1; }

/* ── ŞIKLAR: a) b) c) d) e) ─────────────────────────── */
.opts { padding-left:26px; display:grid; gap:1px; }
.opt  { font-size:<?= $optFs ?>; line-height:1.4; }
.opt-key { font-weight:700; }

/* ── KLASİK ─────────────────────────────────────────── */
.klasik-list { display:grid; grid-template-columns:repeat(<?= $klasikCols ?>,1fr); gap:6px 14px; }
.q-box-k     { padding:7px 0; break-inside:avoid; page-break-inside:avoid; }
.q-box-k-frame { border:1.2px solid #000; border-radius:2px; padding:6px 8px; break-inside:avoid; page-break-inside:avoid; margin-bottom:4px; }
.answer-lines { margin-top:8px; padding-left:26px; }
.answer-line  { border-bottom:1px solid #bbb; height:24px; }

/* ── DOĞRU / YANLIŞ ─────────────────────────────────── */
.tf-list { display:grid; grid-template-columns:repeat(<?= $tfCols ?>,1fr); gap:4px 14px; }

/* ── FOOTER ──────────────────────────────────────────── */
.exam-footer { margin-top:18px; border-top:1px solid #000; padding-top:5px; display:flex; justify-content:space-between; font-size:8.5pt; }

/* ── PRINT BAR ───────────────────────────────────────── */
.print-bar { position:fixed; top:0; left:0; right:0; background:#111; color:#fff; display:flex; align-items:center; justify-content:space-between; padding:10px 20px; z-index:100; font-size:13px; }
.print-bar button { background:#fff; color:#111; border:none; padding:7px 18px; border-radius:5px; font-weight:700; cursor:pointer; font-size:13px; margin-left:8px; }
.print-bar button.outline { background:transparent; color:#fff; border:1.5px solid rgba(255,255,255,.5); }
.print-spacer { height:46px; }
@media print {
    .print-bar, .print-spacer { display:none !important; }
    .page { padding:10mm 14mm; max-width:100%; }
    .test-grid   { grid-template-columns:repeat(<?= $testCols ?>,1fr) !important; }
    .klasik-list { grid-template-columns:repeat(<?= $klasikCols ?>,1fr) !important; }
    .tf-list     { grid-template-columns:repeat(<?= $tfCols ?>,1fr) !important; }
}
</style>
</head>
<body>

<div class="print-bar">
    <span>Önizleme: <strong><?= $examTitle ?></strong></span>
    <div>
        <button class="outline" onclick="window.close()">✕ Kapat</button>
        <button onclick="window.print()">🖨️ Yazdır / PDF Kaydet</button>
    </div>
</div>
<div class="print-spacer"></div>

<div class="page">

    <!-- BAŞLIK -->
    <div class="exam-header">
        <?php if ($schoolName): ?>
        <div class="school-name"><?= $schoolName ?></div>
        <?php endif; ?>
        <div class="exam-meta">
            <?php
            $metaParts = [];
            if ($acYear)    $metaParts[] = $acYear . ' Eğitim-Öğretim Yılı';
            if ($gradeInfo) $metaParts[] = $gradeInfo;
            if ($period)    $metaParts[] = $period . '. Dönem ' . $examNum . '. Yazılı';
            echo implode(' &nbsp;|&nbsp; ', $metaParts);
            ?>
        </div>
        <div class="exam-title"><?= $examTitle ?> Dersi Yazılı Sınavı</div>
    </div>

    <!-- ÖĞRENCİ BİLGİLERİ -->
    <div class="info-row">
        <div class="info-cell"><strong>Adı Soyadı</strong></div>
        <div class="info-cell" style="max-width:90px;flex:0 0 90px;"><strong>Sınıf</strong></div>
        <div class="info-cell" style="max-width:70px;flex:0 0 70px;"><strong>Şube</strong></div>
        <div class="info-cell" style="max-width:90px;flex:0 0 90px;"><strong>Numara</strong></div>
    </div>

    <?php
    $sectionLetter = 'A';
    $globalNum     = 1;

    foreach ($sectionOrder as $sec):

    // ── TEST SORULARI ─────────────────────────────────────────────────────────
    if ($sec === 'test' && !empty($testQs)):
    ?>
    <div class="section-title"><?= $sectionLetter++ ?> — Test Soruları</div>
    <div class="test-grid">
    <?php foreach ($testQs as $q):
        $boxClass = $testFrame ? 'q-box-frame' : 'q-box';
    ?>
    <div class="<?= $boxClass ?>">
        <div class="q-head">
            <div class="q-num"><?= $globalNum++ ?></div>
            <div class="q-text"><?= nl2br(htmlspecialchars($q['title'])) ?></div>
        </div>
        <div class="opts">
        <?php foreach ($optKeys as $k):
            $v = $q[$optCols[$k]];
            if (!$v) continue;
        ?>
            <div class="opt"><span class="opt-key"><?= $k ?>)</span> <?= htmlspecialchars($v) ?></div>
        <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
    </div>

    <?php
    // ── DOĞRU/YANLIŞ SORULARI ─────────────────────────────────────────────────
    elseif ($sec === 'tf' && !empty($tfQs)):
    ?>
    <div class="section-title"><?= $sectionLetter++ ?> — Doğru / Yanlış</div>
    <div class="tf-list">
    <?php foreach ($tfQs as $q): ?>
    <div class="q-box" style="display:flex;align-items:flex-start;gap:6px;padding:5px 0;break-inside:avoid;page-break-inside:avoid;">
        <div class="q-num"><?= $globalNum++ ?></div>
        <div style="font-weight:600;font-size:10pt;line-height:1.5;">
            <?= nl2br(htmlspecialchars($q['title'])) ?>
            <span style="font-weight:700;letter-spacing:.03em;">&nbsp;&nbsp;( D &nbsp;/&nbsp; Y )</span>
        </div>
    </div>
    <?php endforeach; ?>
    </div>

    <?php
    // ── KLASİK SORULAR ────────────────────────────────────────────────────────
    elseif ($sec === 'klasik' && !empty($klasikQs)):
    ?>
    <div class="section-title"><?= $sectionLetter++ ?> — Klasik Sorular</div>
    <div class="klasik-list">
    <?php foreach ($klasikQs as $q):
        $lines    = max(4, min(10, (int)round($q['points'] / 2.5)));
        $boxClass = $klasikFrame ? 'q-box-k-frame' : 'q-box-k';
    ?>
    <div class="<?= $boxClass ?>">
        <div class="q-head">
            <div class="q-num"><?= $globalNum++ ?></div>
            <div class="q-text"><?= nl2br(htmlspecialchars($q['title'])) ?></div>
        </div>
        <div class="answer-lines">
            <?php for ($l = 0; $l < $lines; $l++): ?><div class="answer-line"></div><?php endfor; ?>
        </div>
    </div>
    <?php endforeach; ?>
    </div>

    <?php endif; ?>
    <?php endforeach; // sectionOrder ?>


</div>
</body>
</html>
