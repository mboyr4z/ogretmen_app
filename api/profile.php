<?php
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$uid    = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$db     = getDB();

switch ($action) {
    // Demo: premium aktif et (gerçek projede ödeme entegrasyonu olur)
    case 'activate_premium':
        $until = date('Y-m-d', strtotime('+1 month'));
        $stmt  = $db->prepare("UPDATE users SET membership='premium', premium_until=? WHERE id=?");
        $stmt->execute([$until, $uid]);
        jsonResponse(['success'=>true]);
        break;

    default:
        jsonResponse(['error'=>'Geçersiz işlem'], 400);
}
