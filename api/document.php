<?php
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$uid = $_SESSION['user_id'];
$db  = getDB();

// Dosya yükleme multipart POST, diğerleri JSON body
$isUpload = ($_POST['action'] ?? '') === 'upload';
if ($isUpload) {
    $action = 'upload';
    $input  = $_POST;
} else {
    $input  = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $input['action'] ?? '';
}

// ---- Yardımcılar ----

/** Ünitenin bu kullanıcıya ait olup olmadığını doğrula */
function ownsUnit(PDO $db, int $unitId, int $uid): bool {
    $s = $db->prepare("SELECT id FROM units WHERE id=? AND user_id=?");
    $s->execute([$unitId, $uid]);
    return (bool)$s->fetch();
}

/** Dökümanın bu kullanıcıya ait olup olmadığını doğrula */
function ownsDoc(PDO $db, int $docId, int $uid): ?array {
    $s = $db->prepare("SELECT * FROM unit_documents WHERE id=? AND user_id=?");
    $s->execute([$docId, $uid]);
    return $s->fetch() ?: null;
}

switch ($action) {

    // ============================================
    //  YÜKLE
    // ============================================
    case 'upload': {
        $unitId = (int)($input['unit_id'] ?? 0);
        if (!$unitId || !ownsUnit($db, $unitId, $uid)) {
            jsonResponse(['error' => 'Geçersiz ünite'], 403);
        }

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $errMap = [
                UPLOAD_ERR_INI_SIZE   => 'Dosya sunucu limitini aşıyor',
                UPLOAD_ERR_FORM_SIZE  => 'Dosya form limitini aşıyor',
                UPLOAD_ERR_PARTIAL    => 'Dosya yarım yüklendi',
                UPLOAD_ERR_NO_FILE    => 'Dosya seçilmedi',
                UPLOAD_ERR_NO_TMP_DIR => 'Geçici klasör yok',
                UPLOAD_ERR_CANT_WRITE => 'Dosya yazılamadı',
            ];
            $code = $_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE;
            jsonResponse(['error' => $errMap[$code] ?? 'Yükleme hatası'], 400);
        }

        $file    = $_FILES['file'];
        $maxSize = 50 * 1024 * 1024; // 50 MB
        if ($file['size'] > $maxSize) {
            jsonResponse(['error' => 'Dosya 50 MB\'ı geçemez'], 400);
        }

        // MIME doğrulama
        $finfo   = new finfo(FILEINFO_MIME_TYPE);
        $mime    = $finfo->file($file['tmp_name']);
        $allowed = [
            'application/pdf'                                                            => 'pdf',
            'image/jpeg'                                                                 => 'image',
            'image/png'                                                                  => 'image',
            'image/gif'                                                                  => 'image',
            'image/webp'                                                                 => 'image',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/vnd.ms-powerpoint'                                              => 'pptx',
        ];
        if (!isset($allowed[$mime])) {
            jsonResponse(['error' => 'Desteklenmeyen dosya türü. PDF, görsel (JPG/PNG/GIF/WEBP) veya PPTX yükleyebilirsiniz.'], 400);
        }
        $fileType = $allowed[$mime];

        // Klasör: uploads/documents/{user_id}/{unit_id}/
        $uploadDir = __DIR__ . "/../uploads/documents/{$uid}/u{$unitId}/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $origName    = basename($file['name']);
        $ext         = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $storedName  = uniqid('doc_', true) . '.' . $ext;
        $destPath    = $uploadDir . $storedName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            jsonResponse(['error' => 'Dosya kaydedilemedi'], 500);
        }

        // Sıra: son sıraya ekle
        $maxOrd = $db->prepare("SELECT COALESCE(MAX(order_num),0) FROM unit_documents WHERE unit_id=? AND user_id=?");
        $maxOrd->execute([$unitId, $uid]);
        $nextOrder = (int)$maxOrd->fetchColumn() + 1;

        $displayName = pathinfo($origName, PATHINFO_FILENAME);
        $relPath     = "uploads/documents/{$uid}/u{$unitId}/{$storedName}";

        $ins = $db->prepare("
            INSERT INTO unit_documents
                (user_id, unit_id, display_name, file_path, original_name, file_type, file_size, order_num)
            VALUES (?,?,?,?,?,?,?,?)
        ");
        $ins->execute([$uid, $unitId, $displayName, $relPath, $origName, $fileType, $file['size'], $nextOrder]);

        jsonResponse(['success' => true, 'doc' => [
            'id'            => (int)$db->lastInsertId(),
            'display_name'  => $displayName,
            'file_type'     => $fileType,
            'file_size'     => $file['size'],
            'order_num'     => $nextOrder,
            'file_path'     => $relPath,
            'original_name' => $origName,
        ]]);
        break;
    }

    // ============================================
    //  YENİDEN ADLANDIR
    // ============================================
    case 'rename': {
        $docId   = (int)($input['doc_id']      ?? 0);
        $newName = trim($input['display_name'] ?? '');
        if (!$newName)                       jsonResponse(['error' => 'Ad boş olamaz'], 400);
        if (!ownsDoc($db, $docId, $uid))     jsonResponse(['error' => 'Yetersiz yetki'], 403);

        $db->prepare("UPDATE unit_documents SET display_name=? WHERE id=? AND user_id=?")
           ->execute([$newName, $docId, $uid]);

        jsonResponse(['success' => true]);
        break;
    }

    // ============================================
    //  SIRALA (yukarı / aşağı)
    // ============================================
    case 'reorder': {
        $docId     = (int)($input['doc_id']   ?? 0);
        $direction = $input['direction']       ?? '';
        $doc       = ownsDoc($db, $docId, $uid);
        if (!$doc) jsonResponse(['error' => 'Yetersiz yetki'], 403);

        $unitId   = (int)$doc['unit_id'];
        $curOrder = (int)$doc['order_num'];

        $neighborStmt = $db->prepare(
            $direction === 'up'
                ? "SELECT * FROM unit_documents WHERE unit_id=? AND user_id=? AND order_num < ? ORDER BY order_num DESC LIMIT 1"
                : "SELECT * FROM unit_documents WHERE unit_id=? AND user_id=? AND order_num > ? ORDER BY order_num ASC  LIMIT 1"
        );
        $neighborStmt->execute([$unitId, $uid, $curOrder]);
        $neighbor = $neighborStmt->fetch();

        if (!$neighbor) jsonResponse(['success' => true, 'changed' => false]);

        $db->beginTransaction();
        $db->prepare("UPDATE unit_documents SET order_num=? WHERE id=?")->execute([(int)$neighbor['order_num'], $docId]);
        $db->prepare("UPDATE unit_documents SET order_num=? WHERE id=?")->execute([$curOrder, (int)$neighbor['id']]);
        $db->commit();

        jsonResponse(['success' => true, 'changed' => true, 'neighbor_id' => (int)$neighbor['id']]);
        break;
    }

    // ============================================
    //  SİL
    // ============================================
    case 'delete': {
        $docId = (int)($input['doc_id'] ?? 0);
        $doc   = ownsDoc($db, $docId, $uid);
        if (!$doc) jsonResponse(['error' => 'Yetersiz yetki'], 403);

        $absPath = __DIR__ . '/../' . $doc['file_path'];
        if (file_exists($absPath)) @unlink($absPath);

        $db->prepare("DELETE FROM unit_documents WHERE id=? AND user_id=?")->execute([$docId, $uid]);

        jsonResponse(['success' => true]);
        break;
    }

    // ============================================
    //  LİSTE (belirli bir ünite)
    // ============================================
    case 'list': {
        $unitId = (int)($input['unit_id'] ?? 0);
        if (!$unitId || !ownsUnit($db, $unitId, $uid)) jsonResponse(['error' => 'Geçersiz ünite'], 403);

        $stmt = $db->prepare("SELECT * FROM unit_documents WHERE unit_id=? AND user_id=? ORDER BY order_num ASC");
        $stmt->execute([$unitId, $uid]);
        jsonResponse(['success' => true, 'docs' => $stmt->fetchAll()]);
        break;
    }

    default:
        jsonResponse(['error' => 'Geçersiz işlem'], 400);
}
