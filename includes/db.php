<?php
require_once __DIR__ . '/config.php';

// ============================================
//  VERİTABANI BAĞLANTISI
// ============================================
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
            // MySQL timezone = Türkiye (+03:00)
            $pdo->exec("SET time_zone = '+03:00'");
        } catch (PDOException $e) {
            die(json_encode(['error' => 'Veritabanı bağlantı hatası: ' . $e->getMessage()]));
        }
    }
    return $pdo;
}

// ============================================
//  YARDIMCI FONKSİYONLAR
// ============================================
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/login.php');
        exit;
    }
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function isPremium($user = null) {
    if (!$user) $user = getCurrentUser();
    if (!$user) return false;
    return $user['membership'] === 'premium' &&
           ($user['premium_until'] === null || strtotime($user['premium_until']) > time());
}

function checkLimit($type, $userId) {
    $user = getCurrentUser();
    $premium = isPremium($user);
    $db = getDB();

    switch ($type) {
        case 'class':
            $max = $premium ? PREMIUM_MAX_CLASSES : FREE_MAX_CLASSES;
            if ($max === 0) return true;
            $stmt = $db->prepare("SELECT COUNT(*) FROM classes WHERE user_id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetchColumn() < $max;

        case 'unit':
            $max = $premium ? PREMIUM_MAX_UNITS : FREE_MAX_UNITS;
            if ($max === 0) return true;
            $stmt = $db->prepare("SELECT COUNT(*) FROM units WHERE user_id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetchColumn() < $max;

        case 'question':
            $max = $premium ? PREMIUM_MAX_QUESTIONS : FREE_MAX_QUESTIONS;
            if ($max === 0) return true;
            $stmt = $db->prepare("SELECT COUNT(*) FROM questions WHERE user_id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetchColumn() < $max;

        case 'exam':
            $max = $premium ? PREMIUM_MAX_EXAMS : FREE_MAX_EXAMS;
            if ($max === 0) return true;
            $stmt = $db->prepare("SELECT COUNT(*) FROM exams WHERE user_id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetchColumn() < $max;
    }
    return false;
}

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function jsonResponse($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function isAdmin($user = null) {
    if (!$user) $user = getCurrentUser();
    if (!$user) return false;
    return !empty($user['is_admin']);
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ' . SITE_URL . '/dashboard.php');
        exit;
    }
}

function getLimitInfo($userId) {
    $user = getCurrentUser();
    $premium = isPremium($user);
    $db = getDB();

    $counts = [];
    foreach (['classes','units','questions','exams'] as $table) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM $table WHERE user_id = ?");
        $stmt->execute([$userId]);
        $counts[$table] = (int)$stmt->fetchColumn();
    }

    return [
        'premium'   => $premium,
        'counts'    => $counts,
        'limits'    => [
            'classes'   => $premium ? PREMIUM_MAX_CLASSES   : FREE_MAX_CLASSES,
            'units'     => $premium ? PREMIUM_MAX_UNITS     : FREE_MAX_UNITS,
            'questions' => $premium ? PREMIUM_MAX_QUESTIONS : FREE_MAX_QUESTIONS,
            'exams'     => $premium ? PREMIUM_MAX_EXAMS     : FREE_MAX_EXAMS,
        ]
    ];
}
