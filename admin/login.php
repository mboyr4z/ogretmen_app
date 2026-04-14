<?php
require_once __DIR__ . '/auth.php';

// Zaten giriş yaptıysa direkt panele git
if (isAdminLoggedIn()) {
    header('Location: ' . SITE_URL . '/admin/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'E-posta ve şifre gereklidir.';
    } else {
        $db   = getAdminDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND is_admin = 1 LIMIT 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            // Normal kullanıcı session'ından tamamen bağımsız admin session
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id']        = $admin['id'];
            $_SESSION['admin_name']      = $admin['name'];
            header('Location: ' . SITE_URL . '/admin/index.php');
            exit;
        } else {
            $error = 'E-posta veya şifre hatalı, ya da bu hesap admin değil.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Girişi — SınıfPro</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #0f172a;
    background-image: radial-gradient(ellipse at 20% 50%, rgba(220,38,38,.15) 0%, transparent 60%),
                      radial-gradient(ellipse at 80% 20%, rgba(185,28,28,.1) 0%, transparent 50%);
    padding: 24px;
}
.login-box {
    background: #1e293b;
    border: 1px solid #334155;
    border-radius: 20px;
    padding: 48px 40px;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 25px 50px rgba(0,0,0,.5);
}
.login-logo {
    text-align: center;
    margin-bottom: 36px;
}
.shield-icon {
    width: 64px; height: 64px;
    background: linear-gradient(135deg, #dc2626, #991b1b);
    border-radius: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    margin-bottom: 16px;
    box-shadow: 0 8px 24px rgba(220,38,38,.4);
}
.login-logo h1 {
    font-size: 22px;
    font-weight: 800;
    color: #f1f5f9;
    margin-bottom: 4px;
}
.login-logo p {
    color: #64748b;
    font-size: 13px;
}
.admin-badge {
    display: inline-block;
    background: rgba(220,38,38,.15);
    color: #f87171;
    border: 1px solid rgba(220,38,38,.3);
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    margin-bottom: 8px;
}
.form-group { margin-bottom: 18px; }
.form-label {
    display: block;
    margin-bottom: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #94a3b8;
}
.form-control {
    width: 100%;
    padding: 12px 14px;
    background: #0f172a;
    border: 1px solid #334155;
    border-radius: 10px;
    font-size: 14px;
    color: #f1f5f9;
    font-family: inherit;
    outline: none;
    transition: border-color .2s;
}
.form-control:focus {
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220,38,38,.15);
}
.form-control::placeholder { color: #475569; }
.btn-admin {
    width: 100%;
    padding: 13px;
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 4px 14px rgba(220,38,38,.4);
    margin-top: 8px;
}
.btn-admin:hover { background: linear-gradient(135deg, #b91c1c, #991b1b); transform: translateY(-1px); }
.btn-admin:active { transform: translateY(0); }
.error-box {
    background: rgba(220,38,38,.1);
    border: 1px solid rgba(220,38,38,.3);
    color: #fca5a5;
    border-radius: 10px;
    padding: 12px 14px;
    font-size: 13px;
    margin-bottom: 20px;
}
.back-link {
    text-align: center;
    margin-top: 24px;
}
.back-link a {
    color: #475569;
    font-size: 12px;
    text-decoration: none;
    transition: color .2s;
}
.back-link a:hover { color: #94a3b8; }
.divider {
    border: none;
    border-top: 1px solid #1e293b;
    margin: 24px 0;
}
</style>
</head>
<body>

<div class="login-box">
    <div class="login-logo">
        <div class="shield-icon">🛡️</div>
        <div class="admin-badge">Admin Portalı</div>
        <h1>SınıfPro</h1>
        <p>Yönetici girişi yapın</p>
    </div>

    <?php if ($error): ?>
    <div class="error-box">⚠️ <?= adminSanitize($error) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="on">
        <div class="form-group">
            <label class="form-label" for="email">E-posta</label>
            <input type="email" id="email" name="email" class="form-control"
                   placeholder="admin@sinifpro.com"
                   value="<?= isset($_POST['email']) ? adminSanitize($_POST['email']) : '' ?>"
                   required autofocus>
        </div>
        <div class="form-group">
            <label class="form-label" for="password">Şifre</label>
            <input type="password" id="password" name="password" class="form-control"
                   placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn-admin">🔐 Giriş Yap</button>
    </form>

    <div class="back-link">
        <a href="<?= SITE_URL ?>/login.php">← Kullanıcı girişine dön</a>
    </div>
</div>

</body>
</html>
