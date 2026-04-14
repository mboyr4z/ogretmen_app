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
$user     = getCurrentUser();

// Başlık: sınav adından ders + sınıf çıkar
$examTitle = htmlspecialchars($exam['name']);
$gradeInfo = $exam['grade'] ? $exam['grade'] . '. Sınıf' : ($exam['class_name'] ? htmlspecialchars($exam['class_name']) : '');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title><?= $examTitle ?></title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;600;700&display=swap');

* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Noto Sans', Arial, sans-serif; font-size: 11pt; color: #000; background: #fff; }

.page { max-width: 210mm; margin: 0 auto; padding: 16mm 16mm 14mm; }

/* BAŞLIK */
.exam-header {
    border-bottom: 2px solid #000;
    padding-bottom: 8px;
    margin-bottom: 12px;
    text-align: center;
}
.exam-title  { font-size: 15pt; font-weight: 700; }
.exam-grade  { font-size: 11pt; font-weight: 600; margin-top: 2px; }

/* ÖĞRENCİ BİLGİ KUTUSU */
.info-row {
    display: flex;
    border: 1.5px solid #000;
    margin-bottom: 14px;
}
.info-cell {
    flex: 1;
    padding: 5px 10px 16px;
    border-right: 1px solid #000;
    font-size: 9pt;
}
.info-cell:last-child { border-right: none; }
.info-cell strong { display: block; font-size: 8pt; margin-bottom: 2px; }

/* BÖLÜM BAŞLIĞI */
.section-title {
    font-size: 10.5pt;
    font-weight: 700;
    margin: 14px 0 10px;
    padding-bottom: 3px;
    border-bottom: 1.5px solid #000;
    text-transform: uppercase;
    letter-spacing: .04em;
}

/* TEST: 2 SÜTUN */
.test-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px 16px;
    margin-bottom: 4px;
}

.q-box {
    padding: 6px 0;
    break-inside: avoid;
    page-break-inside: avoid;
}
.q-head {
    display: flex;
    align-items: flex-start;
    gap: 6px;
    margin-bottom: 4px;
}
.q-num {
    min-width: 20px; height: 20px;
    border: 1.5px solid #000;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 8.5pt; font-weight: 700; flex-shrink: 0;
}
.q-text { font-weight: 600; font-size: 10pt; line-height: 1.4; flex: 1; }

/* Şıklar */
.opts { padding-left: 26px; display: grid; gap: 1px; }
.opt  { display: flex; align-items: flex-start; gap: 5px; font-size: 9.5pt; line-height: 1.35; }
.opt-key {
    min-width: 16px; height: 16px;
    border: 1px solid #000;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 7.5pt; font-weight: 700; flex-shrink: 0; margin-top: 1px;
}

/* KLASİK: TAM GENİŞLİK */
.klasik-list { display: grid; grid-template-columns: 1fr; gap: 0; }

.q-box-k {
    padding: 8px 0;
    break-inside: avoid;
    page-break-inside: avoid;
}
.answer-lines { margin-top: 8px; padding-left: 26px; }
.answer-line  { border-bottom: 1px solid #ccc; height: 24px; }

/* FOOTER */
.exam-footer {
    margin-top: 20px;
    border-top: 1px solid #000;
    padding-top: 6px;
    display: flex;
    justify-content: space-between;
    font-size: 8.5pt;
}

/* PRINT BAR */
.print-bar {
    position: fixed; top: 0; left: 0; right: 0;
    background: #111; color: #fff;
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 20px; z-index: 100; font-size: 13px;
}
.print-bar button {
    background: #fff; color: #111;
    border: none; padding: 7px 18px;
    border-radius: 5px; font-weight: 700; cursor: pointer; font-size: 13px; margin-left: 8px;
}
.print-bar button.outline { background: transparent; color: #fff; border: 1.5px solid rgba(255,255,255,.5); }
.print-spacer { height: 46px; }

@media print {
    .print-bar, .print-spacer { display: none !important; }
    .page { padding: 12mm 14mm; max-width: 100%; }
    .test-grid { grid-template-columns: 1fr 1fr !important; }
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

    <div class="exam-header">
        <div class="exam-title"><?= $examTitle ?></div>
        <?php if ($gradeInfo): ?>
        <div class="exam-grade"><?= $gradeInfo ?></div>
        <?php endif; ?>
    </div>

    <div class="info-row">
        <div class="info-cell"><strong>Adı Soyadı</strong></div>
        <div class="info-cell" style="max-width:100px;flex:0 0 100px;"><strong>Sınıf</strong></div>
        <div class="info-cell" style="max-width:80px;flex:0 0 80px;"><strong>Şube</strong></div>
        <div class="info-cell" style="max-width:100px;flex:0 0 100px;"><strong>Numara</strong></div>
    </div>

    <?php if ($exam['description']): ?>
    <p style="font-size:9.5pt;margin-bottom:10px;"><?= htmlspecialchars($exam['description']) ?></p>
    <?php endif; ?>

    <!-- A BÖLÜMÜ: TEST -->
    <?php if (!empty($testQs)): ?>
    <div class="section-title">A — Test Soruları</div>
    <div class="test-grid">
    <?php foreach ($testQs as $i => $q):
        $opts = ['A'=>$q['option_a'],'B'=>$q['option_b'],'C'=>$q['option_c'],'D'=>$q['option_d'],'E'=>$q['option_e']];
    ?>
    <div class="q-box">
        <div class="q-head">
            <div class="q-num"><?= $i+1 ?></div>
            <div class="q-text"><?= nl2br(htmlspecialchars($q['title'])) ?></div>
        </div>
        <div class="opts">
        <?php foreach ($opts as $k => $v): if (!$v) continue; ?>
            <div class="opt">
                <div class="opt-key"><?= $k ?></div>
                <span><?= htmlspecialchars($v) ?></span>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- B BÖLÜMÜ: KLASİK -->
    <?php if (!empty($klasikQs)): ?>
    <div class="section-title">B — Klasik Sorular</div>
    <div class="klasik-list">
    <?php foreach ($klasikQs as $i => $q):
        $num   = count($testQs) + $i + 1;
        $lines = max(4, min(8, (int)round($q['points'] / 3)));
    ?>
    <div class="q-box-k">
        <div class="q-head">
            <div class="q-num"><?= $num ?></div>
            <div class="q-text"><?= nl2br(htmlspecialchars($q['title'])) ?></div>
        </div>
        <div class="answer-lines">
            <?php for ($l = 0; $l < $lines; $l++): ?><div class="answer-line"></div><?php endfor; ?>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="exam-footer">
        <span><?= htmlspecialchars($user['name']) ?></span>
        <span><?= $examTitle ?></span>
        <span><?= date('d.m.Y') ?></span>
    </div>

</div>
</body>
</html>
