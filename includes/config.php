<?php
// ============================================
//  VERİTABANI AYARLARI
// ============================================
define('DB_HOST', 'localhost');          // Hostinger'da genelde localhost kalır
define('DB_USER', 'u640814747_boyraz'); // hPanel > Veritabanları > MySQL Kullanıcısı
define('DB_PASS', 'Karizmatik12.Xd');     // Veritabanı şifreniz
define('DB_NAME', 'u640814747_ogretmen_app');       // hPanel > Veritabanları > Veritabanı adı

// ============================================
//  UYGULAMA AYARLARI
// ============================================
define('SITE_NAME', 'SınıfPro');
define('SITE_URL', 'https://kolayyazili.com'); // örn: https://sinifpro.com — sonda / olmadan

// Normal üye limitleri
define('FREE_MAX_CLASSES',   3);
define('FREE_MAX_UNITS',    10);
define('FREE_MAX_QUESTIONS', 50);
define('FREE_MAX_EXAMS',     5);

// Premium üye limitleri (0 = sınırsız)
define('PREMIUM_MAX_CLASSES',   0);
define('PREMIUM_MAX_UNITS',     0);
define('PREMIUM_MAX_QUESTIONS', 0);
define('PREMIUM_MAX_EXAMS',     0);

// ============================================
//  GOOGLE OAuth2
// ============================================
define('GOOGLE_CLIENT_ID',     '912455363980-b12chjos2vsl55nneaa2jh27tfvobfgt.apps.googleusercontent.com');      // Google Console'dan
define('GOOGLE_CLIENT_SECRET', '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');  // Google Console'dan
define('GOOGLE_REDIRECT_URI',  SITE_URL . '/auth/google_callback.php');

// ============================================
//  TIMEZONE — Türkiye saati
// ============================================
date_default_timezone_set('Europe/Istanbul');

// Session başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
