<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

if (isLoggedIn()) { header('Location: ' . SITE_URL . '/dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($email && $password) {
        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $u = $stmt->fetch();
        if ($u && password_verify($password, $u['password'])) {
            $_SESSION['user_id'] = $u['id'];
            header('Location: ' . SITE_URL . '/dashboard.php');
            exit;
        } else {
            $error = 'E-posta veya şifre hatalı.';
        }
    } else {
        $error = 'Lütfen tüm alanları doldurun.';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>SınıfPro — Öğretmenler için Kolay Yazılı Hazırlama Aracı</title>
<meta name="description" content="Öğretmenler için kolay yazılı oluşturma aracıdır. Word programını kullanmanıza gerek kalmadan sınavınızı hazırlayıp PDF ya da Word indirebilirsiniz. Ayrıca kolaylıkla online test yayımlayabilir ve sınava giren öğrencilerin test sonuçlarını görüntüleyebilirsiniz.">
<meta property="og:title" content="SınıfPro — Öğretmenler için Kolay Yazılı Hazırlama Aracı">
<meta property="og:description" content="Öğretmenler için kolay yazılı oluşturma aracıdır. Word programını kullanmanıza gerek kalmadan sınavınızı hazırlayıp PDF ya da Word indirebilirsiniz.">
<meta property="og:type" content="website">
<meta property="og:url" content="https://kolayyazili.com">
<link rel="icon" href="<?= SITE_URL ?>/assets/images/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/app.css">
<style>
.btn-google {
    display: flex; align-items: center; justify-content: center;
    width: 100%; padding: 11px 16px;
    background: #fff; color: #3c4043;
    border: 1.5px solid #dadce0; border-radius: 8px;
    font-size: 14px; font-weight: 600; text-decoration: none;
    transition: box-shadow .15s, border-color .15s;
    margin-bottom: 4px;
}
.btn-google:hover { box-shadow: 0 2px 8px rgba(0,0,0,.12); border-color: #bbb; }
.auth-divider {
    display: flex; align-items: center; gap: 10px;
    color: var(--text-muted); font-size: 12px; margin: 14px 0;
}
.auth-divider::before, .auth-divider::after {
    content: ''; flex: 1; height: 1px; background: var(--border);
}
</style>
</head>
<body>
<div id="toast-container"></div>
<div class="auth-wrapper">
    <div class="auth-box">
        <div class="auth-logo">
            <img src="<?= SITE_URL ?>/assets/images/logo-icon.svg" alt="SınıfPro" width="64" height="64" style="margin-bottom:4px;">
            <h1>SınıfPro</h1>
            <p>Öğretmenler için sınav & soru yönetim sistemi</p>
        </div>
        <?php if ($error): ?>
        <div class="alert alert-error">❌ <?= sanitize($error) ?></div>
        <?php endif; ?>
        <a href="<?= SITE_URL ?>/auth/google.php" class="btn-google">
            <svg width="18" height="18" viewBox="0 0 48 48" style="margin-right:8px;flex-shrink:0"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
            Google ile Giriş Yap
        </a>
        <div class="auth-divider"><span>veya</span></div>
        <form method="POST">
            <div class="form-group">
                <label class="form-label">E-posta Adresi</label>
                <input type="email" name="email" class="form-control" placeholder="ornek@mail.com" required value="<?= sanitize($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Şifre</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">Giriş Yap</button>
        </form>
        <div style="text-align:center;margin-top:20px;color:var(--text-muted);font-size:13px;">
            Hesabınız yok mu? <a href="<?= SITE_URL ?>/register.php" style="color:var(--primary);font-weight:600;">Kayıt Ol</a>
        </div>
    </div>
</div>
<script src="<?= SITE_URL ?>/assets/js/app.js"></script>
</body>
</html>
