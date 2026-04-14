<?php
$pageTitle = 'Profilim';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db   = getDB();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pw   = trim($_POST['password'] ?? '');
    $pw2  = trim($_POST['password2'] ?? '');

    if (!$name || !$email) {
        $error = 'Ad ve e-posta boş olamaz.';
    } else {
        $params = [$name,$email];
        $sql    = "UPDATE users SET name=?,email=?";
        if ($pw) {
            if ($pw !== $pw2) { $error = 'Şifreler eşleşmiyor.'; goto done; }
            if (strlen($pw)<6) { $error = 'Şifre en az 6 karakter.'; goto done; }
            $sql .= ",password=?";
            $params[] = password_hash($pw, PASSWORD_BCRYPT);
        }
        $sql .= " WHERE id=?";
        $params[] = $_SESSION['user_id'];
        $db->prepare($sql)->execute($params);
        $success = 'Profil güncellendi!';
        $user = getCurrentUser(); // refresh
    }
    done:;
}
?>

<div class="topbar">
    <h2>👤 Profilim</h2>
</div>

<div class="content-area" style="max-width:560px;">

<?php if ($success): ?><div class="alert alert-success">✅ <?= sanitize($success) ?></div><?php endif; ?>
<?php if ($error):   ?><div class="alert alert-error">❌ <?= sanitize($error) ?></div><?php endif; ?>

<div class="card">
    <div class="card-header">
        <span class="card-title">Hesap Bilgileri</span>
        <span class="badge <?= $premium ? 'badge-yellow':'badge-blue' ?>"><?= $premium ? '⭐ Premium':'Ücretsiz' ?></span>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Ad Soyad</label>
                <input type="text" name="name" class="form-control" value="<?= sanitize($user['name']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">E-posta</label>
                <input type="email" name="email" class="form-control" value="<?= sanitize($user['email']) ?>" required>
            </div>
            <hr style="border:none;border-top:1px solid var(--border);margin:20px 0;">
            <p style="font-size:12px;color:var(--text-muted);margin-bottom:14px;">Şifre değiştirmek istemiyorsanız boş bırakın.</p>
            <div class="form-group">
                <label class="form-label">Yeni Şifre</label>
                <input type="password" name="password" class="form-control" placeholder="En az 6 karakter">
            </div>
            <div class="form-group">
                <label class="form-label">Şifre Tekrar</label>
                <input type="password" name="password2" class="form-control" placeholder="Şifreyi tekrarlayın">
            </div>
            <button type="submit" class="btn btn-primary">💾 Kaydet</button>
        </form>
    </div>
</div>

<div class="card" style="margin-top:20px;">
    <div class="card-header"><span class="card-title">📊 Kullanım İstatistikleri</span></div>
    <div class="card-body" style="padding:16px 24px;">
        <?php
        $db = getDB();
        $uid2 = $_SESSION['user_id'];
        $limits = getLimitInfo($uid2);
        ?>
        <div id="p-lbar-classes"></div>
        <div id="p-lbar-units"></div>
        <div id="p-lbar-questions"></div>
        <div id="p-lbar-exams"></div>
        <?php if (!$premium): ?>
        <a href="premium.php" class="btn btn-primary btn-block" style="margin-top:12px;">⭐ Premium'a Geç</a>
        <?php endif; ?>
    </div>
</div>

</div>

<script>
const limits = <?= json_encode($limits, JSON_UNESCAPED_UNICODE) ?>;
renderLimitBar('p-lbar-classes',   '🏫 Sınıf',  limits.counts.classes,   limits.limits.classes);
renderLimitBar('p-lbar-units',     '📚 Ünite',  limits.counts.units,     limits.limits.units);
renderLimitBar('p-lbar-questions', '❓ Soru',   limits.counts.questions, limits.limits.questions);
renderLimitBar('p-lbar-exams',     '📝 Sınav',  limits.counts.exams,     limits.limits.exams);
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
