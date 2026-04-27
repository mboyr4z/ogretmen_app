<?php
// ============================================
//  ORTAM TESPİTİ
// ============================================
$_host    = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_isLocal = ($_host === 'localhost' || str_starts_with($_host, '127.'));

// ============================================
//  VERİTABANI — her iki ortamda canlı DB
// ============================================
define('DB_HOST', 'srv1790.hstgr.io'); // Hostinger Remote MySQL host — hPanel > Veritabanları > Remote MySQL'den alın
define('DB_USER', 'u640814747_boyraz');
define('DB_PASS', 'SecretKey12.Xd');
define('DB_NAME', 'u640814747_ogretmen_app');

// ============================================
//  SITE URL — ortama göre otomatik
// ============================================
define('SITE_URL', $_isLocal
    ? 'http://localhost/ogretmen_app'
    : 'https://kolayyazili.com'
);

// ============================================
//  UYGULAMA AYARLARI
// ============================================
define('SITE_NAME', 'SınıfPro');

define('FREE_MAX_CLASSES',    0);
define('FREE_MAX_UNITS',      0);
define('FREE_MAX_QUESTIONS',  0);
define('FREE_MAX_EXAMS',      0);

define('PREMIUM_MAX_CLASSES',   0);
define('PREMIUM_MAX_UNITS',     0);
define('PREMIUM_MAX_QUESTIONS', 0);
define('PREMIUM_MAX_EXAMS',     0);

// ============================================
//  GOOGLE OAuth2 — redirect URI ortama göre
// ============================================
define('GOOGLE_CLIENT_ID',     '912455363980-rsth0q92aeaq2h9qidrjfcntied2k1nd.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI',  SITE_URL . '/auth/google_callback.php');

// ============================================
//  TIMEZONE
// ============================================
date_default_timezone_set('Europe/Istanbul');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
