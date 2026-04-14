<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/SimpleXlsx.php';
requireLogin();

$db     = getDB();
$uid    = $_SESSION['user_id'];
$examId = (int)($_GET['exam_id'] ?? 0);

// Sınav bilgisi
$es = $db->prepare("SELECT e.*, c.name AS class_name FROM exams e LEFT JOIN classes c ON c.id = e.class_id WHERE e.id = ? AND e.user_id = ?");
$es->execute([$examId, $uid]);
$exam = $es->fetch();
if (!$exam) die('Sınav bulunamadı.');

// Öğrenci sonuçları
$sess = $db->prepare("
    SELECT es.student_name, es.student_surname, er.percentage
    FROM exam_sessions es
    LEFT JOIN exam_results er ON er.session_id = es.id
    WHERE es.exam_id = ? AND es.status IN ('submitted','graded')
    ORDER BY er.percentage IS NULL ASC, er.percentage DESC
");
$sess->execute([$examId]);
$sessions = $sess->fetchAll();

// XLSX
$xlsx = new SimpleXlsx($exam['name']);
$xlsx->setColWidth(0, 25);  // Ad Soyad
$xlsx->setColWidth(1, 12);  // Not

$xlsx->addRow(['Ad Soyad', 'Not (%)'], SimpleXlsx::STYLE_HEADER, 20);

foreach ($sessions as $s) {
    $name = trim($s['student_name'] . ' ' . $s['student_surname']);
    $pct  = isset($s['percentage']) ? (float)$s['percentage'] : null;

    if ($pct !== null) {
        $notCell = ['%' . number_format($pct, 1), $pct >= 50 ? SimpleXlsx::STYLE_GREEN : SimpleXlsx::STYLE_RED];
    } else {
        $notCell = ['—', SimpleXlsx::STYLE_CENTER];
    }

    $xlsx->addRow([$name, $notCell]);
}

$filename = $exam['name'] . '_sonuclar.xlsx';
$xlsx->download($filename);
