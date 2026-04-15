<?php
// Bu dosyayı kopyalayıp config.php yapın, kendi bilgilerinizi girin.
// config.php GitHub'a gitmez (.gitignore ile korunur)

$_host    = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_isLocal = ($_host === 'localhost' || str_starts_with($_host, '127.'));

define('DB_HOST', 'mysql.hostinger.com'); // Remote MySQL host
define('DB_USER', 'DB_KULLANICI');
define('DB_PASS', 'DB_SIFRE');
define('DB_NAME', 'DB_ADI');

define('SITE_URL', $_isLocal
    ? 'http://localhost/ogretmen_app'
    : 'https://kolayyazili.com'
);

define('SITE_NAME', 'SınıfPro');

define('FREE_MAX_CLASSES',    3);
define('FREE_MAX_UNITS',     10);
define('FREE_MAX_QUESTIONS', 50);
define('FREE_MAX_EXAMS',      5);

define('PREMIUM_MAX_CLASSES',   0);
define('PREMIUM_MAX_UNITS',     0);
define('PREMIUM_MAX_QUESTIONS', 0);
define('PREMIUM_MAX_EXAMS',     0);

define('GOOGLE_CLIENT_ID',     'CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'CLIENT_SECRET');
define('GOOGLE_REDIRECT_URI',  SITE_URL . '/auth/google_callback.php');

date_default_timezone_set('Europe/Istanbul');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
