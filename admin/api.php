<?php
require_once __DIR__ . '/auth.php';
requireAdminAuth(); // Normal kullanıcı session'ından tamamen bağımsız

header('Content-Type: application/json');

$input  = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$db     = getAdminDB();

switch ($action) {

    // ============================================
    //  PREMİUM VER / GÜNCELLE
    // ============================================
    case 'set_premium': {
        $userId = (int)($input['user_id'] ?? 0);
        if (!$userId) { adminJsonResponse(['success'=>false, 'error'=>'Geçersiz kullanıcı ID']); }

        $until = null;
        $raw   = trim($input['premium_until'] ?? '');
        if ($raw !== '') {
            $dt = DateTime::createFromFormat('Y-m-d', $raw);
            if (!$dt) { adminJsonResponse(['success'=>false, 'error'=>'Geçersiz tarih formatı']); }
            $until = $dt->format('Y-m-d');
        }

        $db->prepare("UPDATE users SET membership='premium', premium_until=? WHERE id=?")
           ->execute([$until, $userId]);

        adminJsonResponse(['success'=>true]);
        break;
    }

    // ============================================
    //  PREMİUM KALDIR
    // ============================================
    case 'remove_premium': {
        $userId = (int)($input['user_id'] ?? 0);
        if (!$userId) { adminJsonResponse(['success'=>false, 'error'=>'Geçersiz kullanıcı ID']); }

        $db->prepare("UPDATE users SET membership='free', premium_until=NULL WHERE id=?")
           ->execute([$userId]);

        adminJsonResponse(['success'=>true]);
        break;
    }

    // ============================================
    //  KULLANICI SİL
    // ============================================
    case 'delete_user': {
        $userId = (int)($input['user_id'] ?? 0);
        if (!$userId) { adminJsonResponse(['success'=>false, 'error'=>'Geçersiz kullanıcı ID']); }

        // Admin kendi hesabını silemez
        if ($userId === (int)$_SESSION['admin_id']) {
            adminJsonResponse(['success'=>false, 'error'=>'Kendi hesabınızı silemezsiniz']);
        }

        // Başka bir admin silinemez
        $row = $db->prepare("SELECT is_admin FROM users WHERE id=?");
        $row->execute([$userId]);
        $target = $row->fetch();

        if (!$target) { adminJsonResponse(['success'=>false, 'error'=>'Kullanıcı bulunamadı']); }
        if ($target['is_admin']) { adminJsonResponse(['success'=>false, 'error'=>'Admin hesaplar silinemez']); }

        $db->prepare("DELETE FROM users WHERE id=?")->execute([$userId]);
        adminJsonResponse(['success'=>true]);
        break;
    }

    default:
        adminJsonResponse(['success'=>false, 'error'=>'Bilinmeyen işlem']);
}
