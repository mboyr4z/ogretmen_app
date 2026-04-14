<?php
require_once __DIR__ . '/db.php';
requireLogin();
$user = getCurrentUser();
$premium = isPremium($user);
$initial = mb_substr($user['name'], 0, 1, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($pageTitle) ? sanitize($pageTitle) . ' — ' : '' ?>SınıfPro</title>
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/app.css">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>">
</head>
<body>
<div id="toast-container"></div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>
<div class="layout">
<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-mark">🎓</div>
        <span>SınıfPro</span>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Ana Menü</div>
            <a href="<?= SITE_URL ?>/dashboard.php" class="nav-item" data-page="dashboard.php">
                <span class="icon">🏠</span> Dashboard
            </a>
            <a href="<?= SITE_URL ?>/classes.php" class="nav-item" data-page="classes.php">
                <span class="icon">🏫</span> Sınıflarım
            </a>
            <a href="<?= SITE_URL ?>/units.php" class="nav-item" data-page="units.php">
                <span class="icon">📚</span> Ünitelerim
            </a>
            <a href="<?= SITE_URL ?>/questions.php" class="nav-item" data-page="questions.php">
                <span class="icon">❓</span> Sorularım
            </a>
            <a href="<?= SITE_URL ?>/exams.php" class="nav-item" data-page="exams.php">
                <span class="icon">📝</span> Sınavlarım</a>
            <a href="<?= SITE_URL ?>/online_sinav.php" class="nav-item" data-page="online_sinav.php">
                <span class="icon">🌐</span> Online Sınav
            </a>
            <a href="<?= SITE_URL ?>/documents.php" class="nav-item" data-page="documents.php">
                <span class="icon">📂</span> Dökümanlar
            </a>
        </div>
<div class="nav-section">
            <div class="nav-section-title">Hesap</div>
            <?php if (!$premium): ?>
            <a href="<?= SITE_URL ?>/premium.php" class="nav-item" data-page="premium.php">
                <span class="icon">⭐</span> Premium Yap
            </a>
            <?php endif; ?>
            <a href="<?= SITE_URL ?>/profile.php" class="nav-item" data-page="profile.php">
                <span class="icon">👤</span> Profilim
            </a>
            <a href="<?= SITE_URL ?>/logout.php" class="nav-item">
                <span class="icon">🚪</span> Çıkış Yap
            </a>
        </div>
    </nav>
    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar"><?= $initial ?></div>
            <div class="user-info">
                <div class="name"><?= sanitize($user['name']) ?></div>
                <?php if ($premium): ?>
                    <span class="badge-premium">⭐ Premium</span>
                <?php else: ?>
                    <span class="badge-free">Ücretsiz</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</aside>

<!-- MAIN -->
<div class="main-content">
<script>
// Topbar oluşturulduğunda hamburger butonu otomatik enjekte et
document.addEventListener('DOMContentLoaded', () => {
    const topbar = document.querySelector('.topbar');
    if (!topbar || document.getElementById('hamburgerBtn')) return;
    const btn = document.createElement('button');
    btn.id = 'hamburgerBtn';
    btn.className = 'hamburger';
    btn.setAttribute('aria-label', 'Menü');
    btn.innerHTML = '<span></span>';
    topbar.insertBefore(btn, topbar.firstChild);
});
</script>
