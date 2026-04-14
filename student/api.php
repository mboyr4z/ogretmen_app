<?php
// api/online_exam.php dosyasına GET handler ekle
// Bu dosya api/online_exam.php'ye ek olarak student klasöründen çağrılır

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/db_online_helpers.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'get_exam') {
    $code = trim($_GET['code'] ?? '');
    $exam = getExamByCode($code);
    if (!$exam || !isExamOpen($exam)) {
        echo json_encode(['error'=>'Sınav aktif değil']);
        exit;
    }
    $db = getDB();
    $qs = $db->prepare("
        SELECT q.id, q.type, q.title, q.option_a, q.option_b, q.option_c,
               q.option_d, q.option_e, q.points, u.name AS unit_name
        FROM exam_questions eq
        JOIN questions q ON q.id=eq.question_id
        JOIN units u ON u.id=q.unit_id
        WHERE eq.exam_id=?
        ORDER BY eq.order_num, eq.id
    ");
    $qs->execute([$exam['id']]);
    echo json_encode(['exam'=>$exam, 'questions'=>$qs->fetchAll(JSON_UNESCAPED_UNICODE)]);
    exit;
}

// POST işlemleri için ana dosyaya yönlendir
require __DIR__ . '/../../api/online_exam.php';
