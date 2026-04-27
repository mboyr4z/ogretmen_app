<?php
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$uid    = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$db     = getDB();

switch ($action) {
    case 'add':
        $name  = trim($_POST['name'] ?? '');
        $grade = trim($_POST['grade'] ?? '') ?: null;
        if (!$name) jsonResponse(['error'=>'Sınıf adı boş olamaz'], 400);
        $stmt = $db->prepare("INSERT INTO classes (user_id,name,grade) VALUES (?,?,?)");
        $stmt->execute([$uid,$name,$grade]);
        jsonResponse(['success'=>true, 'id'=>$db->lastInsertId()]);
        break;

    case 'update':
        $id    = (int)($_POST['id'] ?? 0);
        $name  = trim($_POST['name'] ?? '');
        $grade = trim($_POST['grade'] ?? '') ?: null;
        if (!$name) jsonResponse(['error'=>'Sınıf adı boş olamaz'], 400);
        $stmt = $db->prepare("UPDATE classes SET name=?,grade=? WHERE id=? AND user_id=?");
        $stmt->execute([$name,$grade,$id,$uid]);
        jsonResponse(['success'=>true]);
        break;

    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare("DELETE FROM classes WHERE id=? AND user_id=?");
        $stmt->execute([$id,$uid]);
        jsonResponse(['success'=>true]);
        break;

    default:
        jsonResponse(['error'=>'Geçersiz işlem'], 400);
}
