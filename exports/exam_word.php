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

$testQs   = array_values(array_filter($questions, fn($q) => $q['type'] === 'test'));
$klasikQs = array_values(array_filter($questions, fn($q) => $q['type'] === 'klasik'));
$tfQs     = array_values(array_filter($questions, fn($q) => $q['type'] === 'true_false'));
$user     = getCurrentUser();

// Çerçeve ve sıralama parametreleri
$testFrame   = (int)($_GET['tf']  ?? 0);
$klasikFrame = (int)($_GET['kf']  ?? 0);
$rawOrder    = $_GET['order'] ?? 'test,tf,klasik';
$validKeys   = ['test','tf','klasik'];
$sectionOrder = array_filter(explode(',', $rawOrder), fn($k) => in_array($k, $validKeys));
if (count($sectionOrder) < 3) $sectionOrder = $validKeys;
$sectionOrder = array_values($sectionOrder);

// Boyut ve sütun parametreleri
$fontSize   = in_array((int)($_GET['fs'] ?? 11), [9,10,11,12,13]) ? (int)$_GET['fs'] : 11;
$testCols   = in_array((int)($_GET['test_cols']   ?? 1), [1,2,3]) ? (int)$_GET['test_cols']   : 1;
$klasikCols = in_array((int)($_GET['klasik_cols'] ?? 1), [1,2])   ? (int)$_GET['klasik_cols'] : 1;
$tfCols     = in_array((int)($_GET['tf_cols']     ?? 1), [1,2,3]) ? (int)$_GET['tf_cols']     : 1;
$qFs   = $fontSize . 'pt';
$optFs = max(8, $fontSize - 1) . 'pt';

// Word olarak indir?
$download = isset($_GET['download']);
if ($download) {
    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $exam['name']) . '_sinav.doc';
    header('Content-Type: application/msword; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache');
}

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
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:w="urn:schemas-microsoft-com:office:word"
      xmlns="http://www.w3.org/TR/REC-html40"
      lang="tr">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--[if gte mso 9]>
<xml><w:WordDocument>
  <w:View>Print</w:View>
  <w:Zoom>100</w:Zoom>
  <w:DoNotOptimizeForBrowser/>
</w:WordDocument></xml>
<![endif]-->
<title><?= $examTitle ?></title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;600;700&display=swap');
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Noto Sans', Calibri, Arial, sans-serif; font-size: <?= $qFs ?>; color: #000; background: #fff; }
.page { max-width: 210mm; margin: 0 auto; padding: 14mm 16mm 12mm; }

/* ── BAŞLIK */
.exam-header { text-align:center; margin-bottom:10px; }
.school-name { font-size:<?= ($fontSize+2) ?>pt; font-weight:700; text-transform:uppercase; letter-spacing:.03em; }
.exam-meta   { font-size:<?= $optFs ?>; font-weight:600; margin-top:3px; }
.exam-title  { font-size:<?= $qFs ?>; font-weight:700; margin-top:4px; border-top:1.5px solid #000; border-bottom:1.5px solid #000; padding:4px 0; }

/* ── ÖĞRENCİ BİLGİ */
.info-table { width:100%; border-collapse:collapse; margin-bottom:12px; }
.info-table td { border:1.5px solid #000; padding:5px 10px 16px; font-size:<?= $optFs ?>; width:25%; }
.info-label { font-size:8pt; font-weight:700; display:block; margin-bottom:2px; }

/* ── BÖLÜM BAŞLIĞI */
.section-title {
    font-size:<?= $qFs ?>; font-weight:700; margin:12px 0 8px;
    padding-bottom:3px; border-bottom:1.5px solid #000;
    text-transform:uppercase; letter-spacing:.04em;
}

/* ── TEST TABLO */
.test-table { width:100%; border-collapse:collapse; margin-bottom:8px; }
.test-table td { width:<?= round(100/$testCols) ?>%; vertical-align:top; padding:6px 8px; }
.test-frame td { border:1.2px solid #000; }

/* ── SORU */
.q-num { display:inline-block; width:20px; height:20px; border:1.5px solid #000; border-radius:50%; text-align:center; line-height:18px; font-size:8.5pt; font-weight:700; margin-right:6px; }
.q-text { font-weight:600; font-size:<?= $qFs ?>; }

/* ── ŞIKLAR */
.opt-row { font-size:<?= $optFs ?>; line-height:1.5; padding-left:26px; }
.opt-key { font-weight:700; }

/* ── KLASİK */
.klasik-table { width:100%; border-collapse:collapse; margin-bottom:8px; }
.klasik-table td { width:<?= round(100/$klasikCols) ?>%; vertical-align:top; padding:6px 8px; }
.klasik-frame td { border:1.2px solid #000; }
.answer-line { border-bottom:1px solid #bbb; height:24px; margin-left:26px; }

/* ── D/Y */
.tf-table { width:100%; border-collapse:collapse; margin-bottom:8px; }
.tf-table td { width:<?= round(100/$tfCols) ?>%; padding:5px 8px; vertical-align:middle; }
.tf-check { display:inline-block; width:13px; height:13px; border:1.2px solid #000; margin-right:4px; vertical-align:middle; }

/* ── PRINT BAR */
.print-bar { position:fixed; top:0; left:0; right:0; background:#1d4ed8; color:#fff; display:flex; align-items:center; justify-content:space-between; padding:10px 20px; z-index:100; font-size:13px; }
.print-bar button, .print-bar a { background:#fff; color:#1d4ed8; border:none; padding:7px 18px; border-radius:5px; font-weight:700; cursor:pointer; font-size:13px; margin-left:8px; text-decoration:none; display:inline-block; }
.print-bar button.outline { background:transparent; color:#fff; border:1.5px solid rgba(255,255,255,.5); }
.print-spacer { height:46px; }
@media print {
    .print-bar, .print-spacer { display:none !important; }
    .page { padding:10mm 14mm; max-width:100%; }
}
</style>
</head>
<body>

<?php if (!$download): ?>
<div class="print-bar">
    <span>Önizleme: <strong><?= $examTitle ?></strong></span>
    <div>
        <button class="outline" onclick="window.close()">✕ Kapat</button>
        <a href="?<?= http_build_query(array_merge($_GET, ['download'=>'1'])) ?>">💾 .doc Olarak İndir</a>
        <button onclick="window.print()">🖨️ Yazdır / PDF Kaydet</button>
    </div>
</div>
<div class="print-spacer"></div>
<?php endif; ?>

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
    <table class="info-table">
    <tr>
        <td><span class="info-label">Adı Soyadı</span></td>
        <td style="width:18%;"><span class="info-label">Sınıf</span></td>
        <td style="width:14%;"><span class="info-label">Şube</span></td>
        <td style="width:18%;"><span class="info-label">Numara</span></td>
    </tr>
    </table>

    <?php
    $sectionLetter = 'A';
    $globalNum     = 1;

    foreach ($sectionOrder as $sec):

    // ── TEST ──────────────────────────────────────────────
    if ($sec === 'test' && !empty($testQs)):
    $chunks = array_chunk($testQs, $testCols);
    ?>
    <div class="section-title"><?= $sectionLetter++ ?> — Test Soruları</div>
    <table class="test-table <?= $testFrame ? 'test-frame' : '' ?>">
    <?php foreach ($chunks as $row): ?>
    <tr>
    <?php foreach ($row as $q): ?>
        <td style="width:<?= round(100/$testCols) ?>%;vertical-align:top;">
            <div><span class="q-num"><?= $globalNum++ ?></span><span class="q-text"><?= nl2br(htmlspecialchars($q['title'])) ?></span></div>
            <?php foreach ($optKeys as $k):
                $v = $q[$optCols[$k]];
                if (!$v) continue;
            ?>
            <div class="opt-row"><span class="opt-key"><?= $k ?>)</span> <?= htmlspecialchars($v) ?></div>
            <?php endforeach; ?>
        </td>
    <?php endforeach; ?>
    <?php if (count($row) < $testCols): ?>
        <?php for ($p = count($row); $p < $testCols; $p++): ?>
        <td style="width:<?= round(100/$testCols) ?>%;">&nbsp;</td>
        <?php endfor; ?>
    <?php endif; ?>
    </tr>
    <?php endforeach; ?>
    </table>

    <?php
    // ── DOĞRU / YANLIŞ ────────────────────────────────────
    elseif ($sec === 'tf' && !empty($tfQs)):
    ?>
    <div class="section-title"><?= $sectionLetter++ ?> — Doğru / Yanlış</div>
    <table class="tf-table">
    <?php
    $tfChunks = array_chunk($tfQs, $tfCols);
    foreach ($tfChunks as $tfRow): ?>
    <tr>
    <?php foreach ($tfRow as $q): ?>
        <td style="vertical-align:top;padding:5px 8px;width:<?= round(100/$tfCols) ?>%;">
            <span class="q-num"><?= $globalNum++ ?></span>
            <span class="q-text"><?= nl2br(htmlspecialchars($q['title'])) ?>
                <span style="font-weight:700;letter-spacing:.03em;">&nbsp;&nbsp;( D &nbsp;/&nbsp; Y )</span>
            </span>
        </td>
    <?php endforeach; ?>
    <?php if (count($tfRow) < $tfCols): ?>
        <?php for ($p = count($tfRow); $p < $tfCols; $p++): ?>
        <td style="width:<?= round(100/$tfCols) ?>%;">&nbsp;</td>
        <?php endfor; ?>
    <?php endif; ?>
    </tr>
    <?php endforeach; ?>
    </table>

    <?php
    // ── KLASİK ────────────────────────────────────────────
    elseif ($sec === 'klasik' && !empty($klasikQs)):
    ?>
    <div class="section-title"><?= $sectionLetter++ ?> — Klasik Sorular</div>
    <table class="klasik-table <?= $klasikFrame ? 'klasik-frame' : '' ?>">
    <?php
    $kChunks = array_chunk($klasikQs, $klasikCols);
    foreach ($kChunks as $kRow): ?>
    <tr>
    <?php foreach ($kRow as $q):
        $lines = max(4, min(10, (int)round($q['points'] / 2.5)));
    ?>
        <td style="vertical-align:top;width:<?= round(100/$klasikCols) ?>%;">
            <div style="margin-bottom:6px;"><span class="q-num"><?= $globalNum++ ?></span><span class="q-text"><?= nl2br(htmlspecialchars($q['title'])) ?></span></div>
            <?php for ($l = 0; $l < $lines; $l++): ?>
            <div class="answer-line"></div>
            <?php endfor; ?>
        </td>
    <?php endforeach; ?>
    <?php if (count($kRow) < $klasikCols): ?>
        <?php for ($p = count($kRow); $p < $klasikCols; $p++): ?>
        <td style="width:<?= round(100/$klasikCols) ?>%;">&nbsp;</td>
        <?php endfor; ?>
    <?php endif; ?>
    </tr>
    <?php endforeach; ?>
    </table>

    <?php endif; ?>
    <?php endforeach; // sectionOrder ?>

</div>
</body>
</html>
