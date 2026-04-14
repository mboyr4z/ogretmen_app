<?php
require_once __DIR__ . '/auth.php';

// Sadece admin session değişkenlerini temizle
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);

header('Location: ' . SITE_URL . '/admin/login.php');
exit;
