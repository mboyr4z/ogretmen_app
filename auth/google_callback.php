<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

// Zaten giriş yapmışsa yönlendir
if (isLoggedIn()) {
    header('Location: ' . SITE_URL . '/dashboard.php');
    exit;
}

function oauthError(string $msg): void {
    header('Location: ' . SITE_URL . '/login.php?error=' . urlencode($msg));
    exit;
}

// ── 1. CSRF state kontrolü ────────────────────────────────
$state = $_GET['state'] ?? '';
if (!$state || $state !== ($_SESSION['oauth_state'] ?? '')) {
    oauthError('Geçersiz oturum. Tekrar deneyin.');
}
unset($_SESSION['oauth_state']);

// ── 2. Hata kontrolü ─────────────────────────────────────
if (isset($_GET['error'])) {
    oauthError('Google girişi iptal edildi.');
}

$code = $_GET['code'] ?? '';
if (!$code) {
    oauthError('Yetkilendirme kodu alınamadı.');
}

// ── 3. Code → Access Token ───────────────────────────────
$tokenRes = httpPost('https://oauth2.googleapis.com/token', [
    'code'          => $code,
    'client_id'     => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'grant_type'    => 'authorization_code',
]);

if (empty($tokenRes['access_token'])) {
    oauthError('Token alınamadı. Lütfen tekrar deneyin.');
}

// ── 4. Kullanıcı bilgilerini al ───────────────────────────
$userInfo = httpGet(
    'https://www.googleapis.com/oauth2/v3/userinfo',
    $tokenRes['access_token']
);

$googleId = $userInfo['sub']     ?? '';
$email    = $userInfo['email']   ?? '';
$name     = $userInfo['name']    ?? '';
$avatar   = $userInfo['picture'] ?? '';

if (!$googleId || !$email) {
    oauthError('Google\'dan kullanıcı bilgisi alınamadı.');
}

// ── 5. Kullanıcıyı bul veya oluştur ──────────────────────
$db = getDB();

// Önce google_id ile ara
$stmt = $db->prepare("SELECT * FROM users WHERE google_id = ?");
$stmt->execute([$googleId]);
$user = $stmt->fetch();

if (!$user) {
    // google_id yoksa e-posta ile ara (normal hesap varsa bağla)
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Mevcut hesaba google_id bağla
        $db->prepare("UPDATE users SET google_id=?, avatar_url=? WHERE id=?")
           ->execute([$googleId, $avatar, $user['id']]);
    } else {
        // Yeni hesap oluştur (şifresiz, sadece Google ile giriş)
        $db->prepare("INSERT INTO users (name, email, google_id, avatar_url, password) VALUES (?,?,?,?,?)")
           ->execute([$name, $email, $googleId, $avatar, '']);
        $userId = $db->lastInsertId();
        $stmt   = $db->prepare("SELECT * FROM users WHERE id=?");
        $stmt->execute([$userId]);
        $user   = $stmt->fetch();
    }
}

// ── 6. Session başlat ────────────────────────────────────
$_SESSION['user_id'] = $user['id'];
header('Location: ' . SITE_URL . '/dashboard.php');
exit;

// ── Yardımcı fonksiyonlar ─────────────────────────────────
function httpPost(string $url, array $data): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($data),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT        => 10,
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res ?: '{}', true) ?? [];
}

function httpGet(string $url, string $token): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $token],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT        => 10,
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res ?: '{}', true) ?? [];
}
