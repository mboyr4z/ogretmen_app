<?php
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$db     = getDB();
$uid    = $_SESSION['user_id'];
$examId = (int)($_GET['id'] ?? 0);

$es = $db->prepare("SELECT e.*, c.name AS class_name FROM exams e LEFT JOIN classes c ON c.id=e.class_id WHERE e.id=? AND e.user_id=?");
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
$totalPoints = array_sum(array_column($questions, 'points'));
$user = getCurrentUser();

$filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $exam['name']) . '_sinav.doc';

header('Content-Type: application/msword; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, no-store, must-revalidate');

// Şık yardımcı fonksiyonu
function renderOpts($q) {
    $keys = ['A','B','C','D','E'];
    $out = '';
    foreach ($keys as $k) {
        $v = $q['option_'.strtolower($k)];
        if (!$v) continue;
        $out .= '<div style="display:flex;align-items:flex-start;gap:5pt;margin-bottom:3pt;font-size:10.5pt;">
            <span style="min-width:16pt;height:16pt;border:1.5pt solid #374151;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:8pt;font-weight:700;flex-shrink:0;">'.$k.'</span>
            <span>'.htmlspecialchars($v).'</span>
        </div>';
    }
    return $out;
}
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:w="urn:schemas-microsoft-com:office:word"
      xmlns="http://www.w3.org/TR/REC-html40">
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
<style>
  body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; color: #111; margin: 1.8cm 2cm; }

  /* HEADER */
  .exam-title { font-size: 17pt; font-weight: 700; color: #1d4ed8; border-bottom: 3pt solid #1d4ed8; padding-bottom: 6pt; margin-bottom: 4pt; }
  .exam-meta  { font-size: 9pt; color: #555; margin-bottom: 12pt; }

  /* BİLGİ KUTUSU */
  .info-table { width: 100%; border-collapse: collapse; margin-bottom: 12pt; }
  .info-table td {
      border: 1pt solid #aaa; padding: 4pt 8pt 14pt;
      font-size: 9pt; color: #555; width: 25%;
  }
  .info-label { font-size: 8pt; color: #888; font-weight: 700; display: block; margin-bottom: 2pt; }

  /* BÖLÜM BAŞLIĞI */
  .section-hdr {
      background: #1d4ed8; color: #fff;
      padding: 5pt 10pt; font-size: 10pt; font-weight: 700;
      margin: 12pt 0 8pt; border-radius: 3pt;
  }

  /* TEST: 2 SÜTUN TABLO */
  .test-table { width: 100%; border-collapse: collapse; margin-bottom: 8pt; }
  .test-table td {
      width: 50%; vertical-align: top;
      border: 1pt solid #d1d5db;
      padding: 8pt 10pt;
  }
  .q-num-cell {
      display: inline-flex; align-items: center; justify-content: center;
      width: 20pt; height: 20pt;
      background: #1d4ed8; color: #fff;
      border-radius: 50%; font-size: 9pt; font-weight: 700;
      margin-right: 6pt; flex-shrink: 0;
  }
  .q-title { font-weight: 700; font-size: 10.5pt; }
  .q-unit-lbl { font-size: 8pt; color: #9ca3af; font-style: italic; margin: 3pt 0 5pt 26pt; }
  .q-pts-lbl { font-size: 8pt; color: #888; }
  .opts-wrap { margin-left: 26pt; }

  /* KLASİK: TAM GENİŞLİK */
  .klasik-table { width: 100%; border-collapse: collapse; margin-bottom: 8pt; }
  .klasik-table td { border: 1pt solid #d1d5db; padding: 9pt 12pt; vertical-align: top; }
  .answer-line { border-bottom: 1pt solid #d1d5db; height: 20pt; }

  /* FOOTER */
  .footer-table { width: 100%; border-collapse: collapse; margin-top: 18pt; border-top: 1pt solid #e5e7eb; padding-top: 6pt; }
  .footer-table td { font-size: 8.5pt; color: #9ca3af; padding-top: 6pt; }
</style>
</head>
<body>

<!-- BAŞLIK -->
<div class="exam-title"><?= htmlspecialchars($exam['name']) ?></div>
<div class="exam-meta">
    <?php if ($exam['class_name']): ?><?= htmlspecialchars($exam['class_name']) ?> &nbsp;|&nbsp; <?php endif; ?>
    <?php if ($exam['duration']): ?>Süre: <?= $exam['duration'] ?> dk &nbsp;|&nbsp; <?php endif; ?>
    <?= count($questions) ?> soru &nbsp;|&nbsp; <?= $totalPoints ?> puan &nbsp;|&nbsp;
    Öğretmen: <?= htmlspecialchars($user['name']) ?> &nbsp;|&nbsp; <?= date('d.m.Y') ?>
</div>

<!-- ÖĞRENCİ BİLGİ KUTUSU -->
<table class="info-table">
<tr>
    <td><span class="info-label">Adı Soyadı</span></td>
    <td><span class="info-label">Sınıf / Şube</span></td>
    <td style="width:15%;"><span class="info-label">Numara</span></td>
    <td style="width:12%;"><span class="info-label">Puan</span></td>
</tr>
</table>

<?php if ($exam['description']): ?>
<div style="background:#eff6ff;border-left:4pt solid #1d4ed8;padding:7pt 12pt;font-size:9.5pt;color:#1e40af;margin-bottom:10pt;">
    <?= htmlspecialchars($exam['description']) ?>
</div>
<?php endif; ?>

<?php if (!empty($testQs)): ?>
<!-- A BÖLÜMÜ: TEST — 2'li Tablo -->
<div class="section-hdr">A BÖLÜMÜ — TEST SORULARI (<?= count($testQs) ?> soru)</div>
<table class="test-table">
<?php
$chunks = array_chunk($testQs, 2);
foreach ($chunks as $pair):
    $left  = $pair[0];
    $right = $pair[1] ?? null;
    $leftIdx  = array_search($left,  $testQs) + 1;
    $rightIdx = $right ? array_search($right, $testQs) + 1 : null;
?>
<tr>
    <!-- SOL SORU -->
    <td>
        <div style="display:flex;align-items:flex-start;gap:0;">
            <span class="q-num-cell"><?= $leftIdx ?></span>
            <span class="q-title"><?= nl2br(htmlspecialchars($left['title'])) ?></span>
            <span class="q-pts-lbl" style="margin-left:4pt;white-space:nowrap;">&nbsp;<?= $left['points'] ?>p</span>
        </div>
        <div class="q-unit-lbl"><?= htmlspecialchars($left['unit_name']) ?></div>
        <div class="opts-wrap"><?= renderOpts($left) ?></div>
    </td>
    <!-- SAĞ SORU (varsa) -->
    <td>
    <?php if ($right): ?>
        <div style="display:flex;align-items:flex-start;">
            <span class="q-num-cell"><?= $rightIdx ?></span>
            <span class="q-title"><?= nl2br(htmlspecialchars($right['title'])) ?></span>
            <span class="q-pts-lbl" style="margin-left:4pt;white-space:nowrap;">&nbsp;<?= $right['points'] ?>p</span>
        </div>
        <div class="q-unit-lbl"><?= htmlspecialchars($right['unit_name']) ?></div>
        <div class="opts-wrap"><?= renderOpts($right) ?></div>
    <?php else: ?>
        &nbsp;
    <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

<?php if (!empty($klasikQs)): ?>
<!-- B BÖLÜMÜ: KLASİK — TAM GENİŞLİK -->
<div class="section-hdr">B BÖLÜMÜ — KLASİK SORULAR (<?= count($klasikQs) ?> soru)</div>
<table class="klasik-table">
<?php foreach ($klasikQs as $i => $q):
    $num   = count($testQs) + $i + 1;
    $lines = max(3, min(7, (int)round($q['points'] / 4)));
?>
<tr>
    <td>
        <div style="display:flex;align-items:flex-start;gap:0;margin-bottom:4pt;">
            <span class="q-num-cell"><?= $num ?></span>
            <span class="q-title"><?= nl2br(htmlspecialchars($q['title'])) ?></span>
            <span class="q-pts-lbl" style="margin-left:4pt;white-space:nowrap;">&nbsp;<?= $q['points'] ?>p</span>
        </div>
        <div class="q-unit-lbl"><?= htmlspecialchars($q['unit_name']) ?></div>
        <div style="margin-left:26pt;">
            <?php for ($l = 0; $l < $lines; $l++): ?>
            <div class="answer-line"></div>
            <?php endfor; ?>
        </div>
    </td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

<!-- FOOTER -->
<table class="footer-table">
<tr>
    <td><?= htmlspecialchars($user['name']) ?></td>
    <td align="center"><?= htmlspecialchars($exam['name']) ?></td>
    <td align="right">Toplam <?= $totalPoints ?> Puan &nbsp;|&nbsp; <?= date('d.m.Y') ?></td>
</tr>
</table>

</body>
</html>
