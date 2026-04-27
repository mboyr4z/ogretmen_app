<?php
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$uid    = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$db     = getDB();

switch ($action) {
    case 'add':
        $unitId  = (int)($_POST['unit_id'] ?? 0);
        $type    = in_array($_POST['type']??'', ['test','klasik','true_false']) ? $_POST['type'] : 'test';
        $title   = trim($_POST['title'] ?? '');
        $points  = max(1, min(100, (int)($_POST['points'] ?? 10)));
        if (!$title || !$unitId) jsonResponse(['error'=>'Eksik alan'], 400);
        // Ünite sahipliği
        $us = $db->prepare("SELECT id FROM units WHERE id=? AND user_id=?");
        $us->execute([$unitId,$uid]);
        if (!$us->fetch()) jsonResponse(['error'=>'Ünite bulunamadı'], 404);

        $optA = trim($_POST['option_a'] ?? '');
        $optB = trim($_POST['option_b'] ?? '');
        $optC = trim($_POST['option_c'] ?? '');
        $optD = trim($_POST['option_d'] ?? '') ?: null;
        $optE = trim($_POST['option_e'] ?? '') ?: null;
        $correct = in_array($_POST['correct_opt']??'',['A','B','C','D','E','D_tf','Y_tf']) ? $_POST['correct_opt'] : null;
        if ($type === 'true_false') {
            $optA = 'Doğru'; $optB = 'Yanlış';
            $optC = null; $optD = null; $optE = null;
            $correct = in_array($_POST['correct_opt']??'',['A','B']) ? $_POST['correct_opt'] : 'A';
        }
        $answer  = trim($_POST['answer'] ?? '') ?: null;
        $content = $title; // content = title for now

        $stmt = $db->prepare("INSERT INTO questions (user_id,unit_id,type,title,content,option_a,option_b,option_c,option_d,option_e,correct_opt,answer,points) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$uid,$unitId,$type,$title,$content,$optA,$optB,$optC,$optD,$optE,$correct,$answer,$points]);
        jsonResponse(['success'=>true, 'id'=>$db->lastInsertId()]);
        break;

    case 'update':
        $id      = (int)($_POST['id'] ?? 0);
        $unitId  = (int)($_POST['unit_id'] ?? 0);
        $type    = in_array($_POST['type']??'', ['test','klasik','true_false']) ? $_POST['type'] : 'test';
        $title   = trim($_POST['title'] ?? '');
        $points  = max(1, min(100, (int)($_POST['points'] ?? 10)));
        if (!$title) jsonResponse(['error'=>'Soru metni boş olamaz'], 400);

        $optA = trim($_POST['option_a'] ?? '');
        $optB = trim($_POST['option_b'] ?? '');
        $optC = trim($_POST['option_c'] ?? '');
        $optD = trim($_POST['option_d'] ?? '') ?: null;
        $optE = trim($_POST['option_e'] ?? '') ?: null;
        $correct = in_array($_POST['correct_opt']??'',['A','B','C','D','E']) ? $_POST['correct_opt'] : null;
        if ($type === 'true_false') {
            $optA = 'Doğru'; $optB = 'Yanlış';
            $optC = null; $optD = null; $optE = null;
            $correct = in_array($_POST['correct_opt']??'',['A','B']) ? $_POST['correct_opt'] : 'A';
        }
        $answer  = trim($_POST['answer'] ?? '') ?: null;

        $stmt = $db->prepare("UPDATE questions SET unit_id=?,type=?,title=?,content=?,option_a=?,option_b=?,option_c=?,option_d=?,option_e=?,correct_opt=?,answer=?,points=? WHERE id=? AND user_id=?");
        $stmt->execute([$unitId,$type,$title,$title,$optA,$optB,$optC,$optD,$optE,$correct,$answer,$points,$id,$uid]);
        jsonResponse(['success'=>true]);
        break;

    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare("DELETE FROM questions WHERE id=? AND user_id=?");
        $stmt->execute([$id,$uid]);
        jsonResponse(['success'=>true]);
        break;

    default:
        jsonResponse(['error'=>'Geçersiz işlem'], 400);
}
