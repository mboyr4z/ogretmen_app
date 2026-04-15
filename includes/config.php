<?php
// ============================================
//  ORTAM TESPİTİ
// ============================================
$_host    = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_isLocal = ($_host === 'localhost' || str_starts_with($_host, '127.'));

// ============================================
//  VERİTABANI — her iki ortamda canlı DB
// ============================================
define('DB_HOST', 'mysql.hostinger.com'); // Hostinger Remote MySQL host — hPanel > Veritabanları > Remote MySQL'den alın
define('DB_USER', 'BURAYA_DB_KULLANICI');
define('DB_PASS', 'BURAYA_DB_SIFRE');
define('DB_NAME', 'BURAYA_DB_ADI');

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

define('FREE_MAX_CLASSES',    3);
define('FREE_MAX_UNITS',     10);
define('FREE_MAX_QUESTIONS', 50);
define('FREE_MAX_EXAMS',      5);

define('PREMIUM_MAX_CLASSES',   0);
define('PREMIUM_MAX_UNITS',     0);
define('PREMIUM_MAX_QUESTIONS', 0);
define('PREMIUM_MAX_EXAMS',     0);

// ============================================
//  GOOGLE OAuth2 — redirect URI ortama göre
// ============================================
define('GOOGLE_CLIENT_ID',     'BURAYA_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'BURAYA_CLIENT_SECRET');
define('GOOGLE_REDIRECT_URI',  SITE_URL . '/auth/google_callback.php');

// ============================================
//  TIMEZONE
// ============================================
date_default_timezone_set('Europe/Istanbul');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
