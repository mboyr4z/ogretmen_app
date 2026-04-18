<?php
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$uid    = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$db     = getDB();

switch ($action) {
    case 'add':
        if (!checkLimit('exam', $uid)) jsonResponse(['error'=>'Sınav limitine ulaştınız. Premium üyeliğe geçin!'], 403);
        $name       = trim($_POST['name'] ?? '');
        $classId    = (int)($_POST['class_id'] ?? 0) ?: null;
        $desc       = trim($_POST['description'] ?? '') ?: null;
        $duration   = (int)($_POST['duration'] ?? 0) ?: null;
        $template   = trim($_POST['template'] ?? 'karisik-duz');
        $schoolName = trim($_POST['school_name'] ?? '') ?: null;
        $acYear     = trim($_POST['academic_year'] ?? '') ?: null;
        $period     = max(1, min(2, (int)($_POST['period'] ?? 1)));
        $examNum    = max(1, min(3, (int)($_POST['exam_number'] ?? 1)));
        if (!$name) jsonResponse(['error'=>'Sınav adı boş olamaz'], 400);
        $stmt = $db->prepare("INSERT INTO exams (user_id,class_id,name,description,duration,template,school_name,academic_year,period,exam_number) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$uid,$classId,$name,$desc,$duration,$template,$schoolName,$acYear,$period,$examNum]);
        jsonResponse(['success'=>true, 'id'=>$db->lastInsertId()]);
        break;

    case 'update':
        $id         = (int)($_POST['id'] ?? 0);
        $name       = trim($_POST['name'] ?? '');
        $classId    = (int)($_POST['class_id'] ?? 0) ?: null;
        $desc       = trim($_POST['description'] ?? '') ?: null;
        $duration   = (int)($_POST['duration'] ?? 0) ?: null;
        $template   = trim($_POST['template'] ?? 'karisik-duz');
        $schoolName = trim($_POST['school_name'] ?? '') ?: null;
        $acYear     = trim($_POST['academic_year'] ?? '') ?: null;
        $period     = max(1, min(2, (int)($_POST['period'] ?? 1)));
        $examNum    = max(1, min(3, (int)($_POST['exam_number'] ?? 1)));
        if (!$name) jsonResponse(['error'=>'Sınav adı boş olamaz'], 400);
        $stmt = $db->prepare("UPDATE exams SET name=?,class_id=?,description=?,duration=?,template=?,school_name=?,academic_year=?,period=?,exam_number=? WHERE id=? AND user_id=?");
        $stmt->execute([$name,$classId,$desc,$duration,$template,$schoolName,$acYear,$period,$examNum,$id,$uid]);
        jsonResponse(['success'=>true]);
        break;

    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare("DELETE FROM exams WHERE id=? AND user_id=?");
        $stmt->execute([$id,$uid]);
        jsonResponse(['success'=>true]);
        break;

    case 'add_question':
        $examId = (int)($_POST['exam_id'] ?? 0);
        $qid    = (int)($_POST['question_id'] ?? 0);
        // Sahiplik kontrolü
        $es = $db->prepare("SELECT id FROM exams WHERE id=? AND user_id=?");
        $es->execute([$examId,$uid]);
        if (!$es->fetch()) jsonResponse(['error'=>'Sınav bulunamadı'], 404);
        $qs = $db->prepare("SELECT id FROM questions WHERE id=? AND user_id=?");
        $qs->execute([$qid,$uid]);
        if (!$qs->fetch()) jsonResponse(['error'=>'Soru bulunamadı'], 404);
        try {
            $stmt = $db->prepare("INSERT INTO exam_questions (exam_id,question_id) VALUES (?,?)");
            $stmt->execute([$examId,$qid]);
            jsonResponse(['success'=>true]);
        } catch (\PDOException $e) {
            jsonResponse(['error'=>'Bu soru zaten bu sınavda mevcut'], 409);
        }
        break;

    case 'remove_question':
        $examId = (int)($_POST['exam_id'] ?? 0);
        $qid    = (int)($_POST['question_id'] ?? 0);
        $es = $db->prepare("SELECT id FROM exams WHERE id=? AND user_id=?");
        $es->execute([$examId,$uid]);
        if (!$es->fetch()) jsonResponse(['error'=>'Yetkisiz'], 403);
        $stmt = $db->prepare("DELETE FROM exam_questions WHERE exam_id=? AND question_id=?");
        $stmt->execute([$examId,$qid]);
        jsonResponse(['success'=>true]);
        break;

    default:
        jsonResponse(['error'=>'Geçersiz işlem'], 400);
}
