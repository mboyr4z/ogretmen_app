<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/db_online_helpers.php';

// POST veya GET'ten action oku (get_exam GET ile gelir)
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$db     = getDB();

// Öğrenci tarafı için session gerekmez, hoca tarafı için gerekir
$teacherActions = ['publish','close','grade','finalize'];
if (in_array($action, $teacherActions)) {
    requireLogin();
    $uid = $_SESSION['user_id'];
}

switch ($action) {

    // ---- HOCA: Sınavı yayına al ----
    case 'publish':
        $id      = (int)($_POST['id'] ?? 0);
        $start   = $_POST['start_time'] ?: null;
        $end     = $_POST['end_time']   ?: null;
        $results = in_array($_POST['results_visible'] ?? '', ['instant','after_end'])
                   ? $_POST['results_visible'] : 'after_end';

        // Sahiplik kontrolü
        $es = $db->prepare("SELECT id FROM exams WHERE id=? AND user_id=?");
        $es->execute([$id, $uid]);
        if (!$es->fetch()) jsonResponse(['error'=>'Sınav bulunamadı'], 404);

        $code = generateAccessCode();
        $stmt = $db->prepare("UPDATE exams SET is_online=1, access_code=?, start_time=?, end_time=?, results_visible=? WHERE id=?");
        $stmt->execute([$code, $start, $end, $results, $id]);
        jsonResponse(['success'=>true, 'code'=>$code]);

    // ---- HOCA: Sınavı kapat ----
    case 'close':
        $id = (int)($_POST['id'] ?? 0);
        $es = $db->prepare("SELECT id FROM exams WHERE id=? AND user_id=?");
        $es->execute([$id, $uid]);
        if (!$es->fetch()) jsonResponse(['error'=>'Yetki yok'], 403);
        $db->prepare("UPDATE exams SET is_online=0 WHERE id=?")->execute([$id]);
        jsonResponse(['success'=>true]);

    // ---- ÖĞRENCİ: Sınava giriş ----
    case 'student_start':
        $code    = trim($_POST['code'] ?? '');
        $name    = trim($_POST['name'] ?? '');
        $surname = trim($_POST['surname'] ?? '');
        $no      = trim($_POST['student_no'] ?? '');

        if (!$code || !$name || !$surname || !$no)
            jsonResponse(['error'=>'Tüm alanları doldurun'], 400);

        $exam = getExamByCode($code);
        if (!$exam)       jsonResponse(['error'=>'Geçersiz sınav kodu'], 404);
        if (!isExamOpen($exam)) jsonResponse(['error'=>'Sınav şu an aktif değil'], 403);

        // Aynı öğrenci tekrar girmesin
        $dup = $db->prepare("SELECT id,status FROM exam_sessions WHERE exam_id=? AND student_no=?");
        $dup->execute([$exam['id'], $no]);
        $existing = $dup->fetch();
        if ($existing && $existing['status'] === 'submitted')
            jsonResponse(['error'=>'Bu okul numarasıyla sınav zaten tamamlandı'], 409);
        if ($existing)
            jsonResponse(['session_id'=>$existing['id'], 'resumed'=>true]);

        $ins = $db->prepare("INSERT INTO exam_sessions (exam_id,student_name,student_surname,student_no,ip_address) VALUES (?,?,?,?,?)");
        $ins->execute([$exam['id'], $name, $surname, $no, $_SERVER['REMOTE_ADDR'] ?? '']);
        jsonResponse(['session_id'=>$db->lastInsertId(), 'resumed'=>false]);

    // ---- ÖĞRENCİ: Cevabı kaydet (otomatik) ----
    case 'save_answer':
        $sessionId  = (int)($_POST['session_id'] ?? 0);
        $questionId = (int)($_POST['question_id'] ?? 0);
        $answer     = trim($_POST['answer'] ?? '');
        $selected   = strtoupper(trim($_POST['selected_opt'] ?? '')) ?: null;

        // Oturum kontrolü
        $ss = $db->prepare("SELECT es.*, e.end_time FROM exam_sessions es JOIN exams e ON e.id=es.exam_id WHERE es.id=?");
        $ss->execute([$sessionId]);
        $session = $ss->fetch();
        if (!$session || $session['status'] === 'submitted') jsonResponse(['error'=>'Geçersiz oturum'], 400);

        // Doğru mu?
        $qs = $db->prepare("SELECT type, correct_opt FROM questions WHERE id=?");
        $qs->execute([$questionId]);
        $q = $qs->fetch();
        $isCorrect = null;
        if ($q && $q['type'] === 'test' && $selected)
            $isCorrect = ($selected === $q['correct_opt']) ? 1 : 0;

        // UPSERT
        $existing = $db->prepare("SELECT id FROM exam_answers WHERE session_id=? AND question_id=?");
        $existing->execute([$sessionId, $questionId]);
        if ($existing->fetch()) {
            $db->prepare("UPDATE exam_answers SET answer=?, selected_opt=?, is_correct=? WHERE session_id=? AND question_id=?")
               ->execute([$answer, $selected, $isCorrect, $sessionId, $questionId]);
        } else {
            $db->prepare("INSERT INTO exam_answers (session_id,question_id,answer,selected_opt,is_correct) VALUES (?,?,?,?,?)")
               ->execute([$sessionId, $questionId, $answer, $selected, $isCorrect]);
        }
        jsonResponse(['success'=>true]);

    // ---- ÖĞRENCİ: Sınavı gönder ----
    case 'submit':
        $sessionId = (int)($_POST['session_id'] ?? 0);
        $ss = $db->prepare("SELECT * FROM exam_sessions WHERE id=?");
        $ss->execute([$sessionId]);
        $session = $ss->fetch();
        if (!$session || $session['status'] === 'submitted') jsonResponse(['error'=>'Zaten gönderildi'], 400);

        $db->prepare("UPDATE exam_sessions SET status='submitted', submitted_at=NOW() WHERE id=?")->execute([$sessionId]);

        // Test puanını hesapla
        $testScore = calcTestScore($sessionId);

        // Klasik sorular var mı kontrol
        $hasClassic = $db->prepare("
            SELECT COUNT(*) FROM exam_answers ea
            JOIN questions q ON q.id=ea.question_id
            WHERE ea.session_id=? AND q.type='klasik'
        ");
        $hasClassic->execute([$sessionId]);
        $classicCount = (int)$hasClassic->fetchColumn();

        // Toplam puan
        $totalPts = $db->prepare("
            SELECT SUM(q.points) FROM exam_questions eq
            JOIN questions q ON q.id=eq.question_id
            JOIN exam_sessions es ON es.exam_id=eq.exam_id
            WHERE es.id=?
        ");
        $totalPts->execute([$sessionId]);
        $maxPoints = (float)($totalPts->fetchColumn() ?? 100);

        if ($classicCount === 0) {
            // Sadece test var, hemen sonuç kaydet
            $pct = $maxPoints > 0 ? round($testScore / $maxPoints * 100, 2) : 0;
            $ins = $db->prepare("INSERT INTO exam_results (session_id,test_score,classic_score,total_score,total_points,percentage) VALUES (?,?,0,?,?,?) ON DUPLICATE KEY UPDATE test_score=VALUES(test_score),total_score=VALUES(total_score),percentage=VALUES(percentage)");
            $ins->execute([$sessionId, $testScore, $testScore, $maxPoints, $pct]);
            $db->prepare("UPDATE exam_sessions SET status='graded' WHERE id=?")->execute([$sessionId]);
        }

        jsonResponse(['success'=>true, 'test_score'=>$testScore, 'needs_grading'=>$classicCount > 0]);

    // ---- HOCA: Klasik soru not ver ----
    case 'grade':
        $sessionId  = (int)($_POST['session_id'] ?? 0);
        $questionId = (int)($_POST['question_id'] ?? 0);
        $score      = (float)($_POST['score'] ?? 0);

        $db->prepare("UPDATE exam_answers SET score=? WHERE session_id=? AND question_id=?")
           ->execute([$score, $sessionId, $questionId]);
        jsonResponse(['success'=>true]);

    // ---- HOCA: Notlandırmayı tamamla ----
    case 'finalize':
        $sessionId = (int)($_POST['session_id'] ?? 0);

        // Sınav sahipliği kontrol
        $ss = $db->prepare("SELECT es.*, e.user_id FROM exam_sessions es JOIN exams e ON e.id=es.exam_id WHERE es.id=?");
        $ss->execute([$sessionId]);
        $session = $ss->fetch();
        if (!$session || $session['user_id'] != $uid) jsonResponse(['error'=>'Yetki yok'], 403);

        $testScore    = calcTestScore($sessionId);
        $classicScore = (float)$db->prepare("
            SELECT COALESCE(SUM(ea.score),0) FROM exam_answers ea
            JOIN questions q ON q.id=ea.question_id
            WHERE ea.session_id=? AND q.type='klasik'
        ")->execute([$sessionId]) ? $db->query("SELECT COALESCE(SUM(ea.score),0) FROM exam_answers ea JOIN questions q ON q.id=ea.question_id WHERE ea.session_id=$sessionId AND q.type='klasik'")->fetchColumn() : 0;

        // Düzeltilmiş hesaplama
        $stmt = $db->prepare("
            SELECT COALESCE(SUM(ea.score),0) as cs
            FROM exam_answers ea
            JOIN questions q ON q.id=ea.question_id
            WHERE ea.session_id=? AND q.type='klasik'
        ");
        $stmt->execute([$sessionId]);
        $classicScore = (float)$stmt->fetchColumn();

        $totalScore = $testScore + $classicScore;

        $pts = $db->prepare("
            SELECT SUM(q.points) FROM exam_questions eq
            JOIN questions q ON q.id=eq.question_id
            JOIN exam_sessions es ON es.exam_id=eq.exam_id
            WHERE es.id=?
        ");
        $pts->execute([$sessionId]);
        $maxPoints = (float)($pts->fetchColumn() ?? 100);

        $pct = $maxPoints > 0 ? round($totalScore / $maxPoints * 100, 2) : 0;

        $ins = $db->prepare("INSERT INTO exam_results (session_id,test_score,classic_score,total_score,total_points,percentage)
            VALUES (?,?,?,?,?,?)
            ON DUPLICATE KEY UPDATE test_score=VALUES(test_score),classic_score=VALUES(classic_score),total_score=VALUES(total_score),percentage=VALUES(percentage)");
        $ins->execute([$sessionId, $testScore, $classicScore, $totalScore, $maxPoints, $pct]);
        $db->prepare("UPDATE exam_sessions SET status='graded' WHERE id=?")->execute([$sessionId]);
        jsonResponse(['success'=>true, 'total'=>$totalScore, 'pct'=>$pct]);

    // ---- ÖĞRENCİ: Sınav verilerini getir (GET) ----
    case 'get_exam':
        $code = trim($_GET['code'] ?? $_POST['code'] ?? '');
        if (!$code) jsonResponse(['error' => 'Sınav kodu eksik'], 400);

        $exam = getExamByCode($code);
        if (!$exam)             jsonResponse(['error' => 'Sınav bulunamadı'], 404);
        if (!isExamOpen($exam)) jsonResponse(['error' => 'Sınav şu an aktif değil'], 403);

        $qs = $db->prepare("
            SELECT q.*, u.name AS unit_name
            FROM exam_questions eq
            JOIN questions q ON q.id = eq.question_id
            JOIN units u     ON u.id = q.unit_id
            WHERE eq.exam_id = ?
            ORDER BY eq.order_num ASC, eq.id ASC
        ");
        $qs->execute([$exam['id']]);
        jsonResponse(['exam' => $exam, 'questions' => $qs->fetchAll()]);

    default:
        jsonResponse(['error' => 'Geçersiz işlem'], 400);
}
