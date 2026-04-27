<?php
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$uid    = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$db     = getDB();

switch ($action) {
    case 'add':
        $classId = (int)($_POST['class_id'] ?? 0);
        $name    = trim($_POST['name'] ?? '');
        if (!$name || !$classId) jsonResponse(['error'=>'Eksik alan'], 400);
        // Sınıf sahipliği kontrolü
        $cs = $db->prepare("SELECT id FROM classes WHERE id=? AND user_id=?");
        $cs->execute([$classId,$uid]);
        if (!$cs->fetch()) jsonResponse(['error'=>'Sınıf bulunamadı'], 404);
        $stmt = $db->prepare("INSERT INTO units (user_id,class_id,name) VALUES (?,?,?)");
        $stmt->execute([$uid,$classId,$name]);
        jsonResponse(['success'=>true, 'id'=>$db->lastInsertId()]);
        break;

    case 'update':
        $id      = (int)($_POST['id'] ?? 0);
        $classId = (int)($_POST['class_id'] ?? 0);
        $name    = trim($_POST['name'] ?? '');
        if (!$name) jsonResponse(['error'=>'Ünite adı boş olamaz'], 400);
        $stmt = $db->prepare("UPDATE units SET name=?,class_id=? WHERE id=? AND user_id=?");
        $stmt->execute([$name,$classId,$id,$uid]);
        jsonResponse(['success'=>true]);
        break;

    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare("DELETE FROM units WHERE id=? AND user_id=?");
        $stmt->execute([$id,$uid]);
        jsonResponse(['success'=>true]);
        break;

    default:
        jsonResponse(['error'=>'Geçersiz işlem'], 400);
}
