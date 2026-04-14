<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/auth.php';
requireAdminAuth();

$db = getAdminDB();

// ============================================
//  İSTATİSTİKLER
// ============================================
$s = [];
$s['total_users']   = (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$s['premium_users'] = (int)$db->query("SELECT COUNT(*) FROM users WHERE membership='premium'")->fetchColumn();
$s['free_users']    = $s['total_users'] - $s['premium_users'];
$s['admin_users']   = (int)$db->query("SELECT COUNT(*) FROM users WHERE is_admin=1")->fetchColumn();
$s['new_30d']       = (int)$db->query("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
$s['total_classes']   = (int)$db->query("SELECT COUNT(*) FROM classes")->fetchColumn();
$s['total_units']     = (int)$db->query("SELECT COUNT(*) FROM units")->fetchColumn();
$s['total_questions'] = (int)$db->query("SELECT COUNT(*) FROM questions")->fetchColumn();
$s['total_exams']     = (int)$db->query("SELECT COUNT(*) FROM exams")->fetchColumn();

// ============================================
//  KULLANICI LİSTESİ (arama + filtre)
// ============================================
$search = trim($_GET['q']      ?? '');
$filter = $_GET['filter']      ?? 'all';
$activeTab = $_GET['tab']      ?? 'stats';

$where  = [];
$params = [];

if ($search !== '') {
    $where[]  = "(name LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($filter === 'premium') { $where[] = "membership='premium'"; }
elseif ($filter === 'free'){ $where[] = "membership='free'"; }
elseif ($filter === 'admin'){ $where[] = "is_admin=1"; }

$wSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$usersStmt = $db->prepare("
    SELECT u.*,
        (SELECT COUNT(*) FROM classes   WHERE user_id=u.id) AS class_count,
        (SELECT COUNT(*) FROM questions WHERE user_id=u.id) AS question_count,
        (SELECT COUNT(*) FROM exams     WHERE user_id=u.id) AS exam_count
    FROM users u $wSql ORDER BY u.created_at DESC
");
$usersStmt->execute($params);
$users = $usersStmt->fetchAll();

// Premium kullanıcılar
$premiumUsers = $db->query("
    SELECT * FROM users WHERE membership='premium' ORDER BY premium_until ASC, name ASC
")->fetchAll();

require_once __DIR__ . '/header.php';
?>

<!-- TOPBAR -->
<div class="admin-topbar">
    <h2>
        <?php if ($activeTab === 'users'):   ?>👥 Kullanıcılar
        <?php elseif ($activeTab === 'premium'): ?>⭐ Premium Yönetimi
        <?php else: ?>📊 Dashboard
        <?php endif; ?>
    </h2>
    <span class="a-badge a-badge-red" style="padding:6px 12px;font-size:12px;">🛡️ Admin Panel</span>
</div>

<div class="admin-content">

<!-- TABS -->
<div class="a-tabs">
    <button class="a-tab <?= $activeTab==='stats'   ?'active':'' ?>" onclick="switchTab('stats')">📊 Dashboard</button>
    <button class="a-tab <?= $activeTab==='users'   ?'active':'' ?>" onclick="switchTab('users')">👥 Kullanıcılar <span style="opacity:.7;font-size:11px;">(<?= $s['total_users'] ?>)</span></button>
    <button class="a-tab <?= $activeTab==='premium' ?'active':'' ?>" onclick="switchTab('premium')">⭐ Premium</button>
</div>

<!-- ========== TAB: STATS ========== -->
<div id="tab-stats" class="a-tab-content <?= $activeTab==='stats'?'active':'' ?>">

    <div class="a-stat-grid">
        <div class="a-stat-card">
            <div class="a-stat-icon" style="background:rgba(59,130,246,.15);">👥</div>
            <div>
                <div class="a-stat-value" style="color:#60a5fa;"><?= $s['total_users'] ?></div>
                <div class="a-stat-label">Toplam Kullanıcı</div>
            </div>
        </div>
        <div class="a-stat-card">
            <div class="a-stat-icon" style="background:rgba(245,158,11,.15);">⭐</div>
            <div>
                <div class="a-stat-value" style="color:#fbbf24;"><?= $s['premium_users'] ?></div>
                <div class="a-stat-label">Premium Üye</div>
            </div>
        </div>
        <div class="a-stat-card">
            <div class="a-stat-icon" style="background:rgba(16,185,129,.15);">🆕</div>
            <div>
                <div class="a-stat-value" style="color:#34d399;"><?= $s['new_30d'] ?></div>
                <div class="a-stat-label">Son 30 Gün Kayıt</div>
            </div>
        </div>
        <div class="a-stat-card">
            <div class="a-stat-icon" style="background:rgba(220,38,38,.15);">🛡️</div>
            <div>
                <div class="a-stat-value" style="color:#f87171;"><?= $s['admin_users'] ?></div>
                <div class="a-stat-label">Admin Sayısı</div>
            </div>
        </div>
    </div>

    <div class="a-grid-2">
        <!-- Üyelik dağılımı -->
        <div class="a-card">
            <div class="a-card-header"><span class="a-card-title">👥 Üyelik Dağılımı</span></div>
            <div class="a-card-body">
                <?php
                $pct = fn($n) => $s['total_users'] > 0 ? round($n/$s['total_users']*100) : 0;
                $bars = [
                    ['Premium',  $s['premium_users'], '#f59e0b', $pct($s['premium_users'])],
                    ['Ücretsiz', $s['free_users'],    '#3b82f6', $pct($s['free_users'])],
                    ['Admin',    $s['admin_users'],   '#dc2626', $pct($s['admin_users'])],
                ];
                foreach ($bars as [$label, $count, $color, $p]): ?>
                <div style="margin-bottom:16px;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                        <span style="font-size:13px;font-weight:600;color:var(--text);"><?= $label ?></span>
                        <span style="font-size:12px;color:var(--text-muted);"><?= $count ?> / <?= $s['total_users'] ?> (<?= $p ?>%)</span>
                    </div>
                    <div class="a-progress-wrap">
                        <div class="a-progress-bar" style="width:<?= $p ?>%;background:<?= $color ?>;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- İçerik istatistikleri -->
        <div class="a-card">
            <div class="a-card-header"><span class="a-card-title">📚 Platform İçeriği</span></div>
            <div class="a-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <?php foreach ([
                        ['🏫','Sınıf',    $s['total_classes'],   '#3b82f6'],
                        ['📚','Ünite',    $s['total_units'],     '#8b5cf6'],
                        ['❓','Soru',     $s['total_questions'], '#10b981'],
                        ['📝','Sınav',    $s['total_exams'],     '#f59e0b'],
                    ] as [$ic,$lb,$val,$col]): ?>
                    <div style="text-align:center;padding:16px;background:var(--surface2);border-radius:10px;">
                        <div style="font-size:24px;margin-bottom:4px;"><?= $ic ?></div>
                        <div style="font-size:24px;font-weight:800;color:<?= $col ?>;"><?= $val ?></div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:2px;"><?= $lb ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Son kayıt olan kullanıcılar -->
        <div class="a-card" style="grid-column:1/-1;">
            <div class="a-card-header"><span class="a-card-title">🕐 Son Kayıt Olan Kullanıcılar</span></div>
            <div class="a-table-wrap">
                <?php
                $recent = $db->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 8")->fetchAll();
                ?>
                <table class="a-table">
                    <thead><tr><th>Kullanıcı</th><th>Üyelik</th><th>Kayıt Tarihi</th></tr></thead>
                    <tbody>
                    <?php foreach ($recent as $u): ?>
                    <tr>
                        <td>
                            <div class="name-cell"><?= adminSanitize($u['name']) ?><?php if($u['is_admin']): ?> <span class="a-badge a-badge-red" style="font-size:9px;">Admin</span><?php endif; ?></div>
                            <div class="muted"><?= adminSanitize($u['email']) ?></div>
                        </td>
                        <td><?= $u['membership']==='premium' ? '<span class="a-badge a-badge-yellow">⭐ Premium</span>' : '<span class="a-badge a-badge-blue">Ücretsiz</span>' ?></td>
                        <td class="muted"><?= date('d.m.Y H:i', strtotime($u['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ========== TAB: KULLANICILAR ========== -->
<div id="tab-users" class="a-tab-content <?= $activeTab==='users'?'active':'' ?>">
    <div class="a-card">
        <div class="a-card-header" style="flex-direction:column;align-items:flex-start;gap:12px;">
            <span class="a-card-title">Tüm Kullanıcılar</span>
            <div class="a-search-row" style="width:100%;">
                <form method="get" style="display:flex;gap:8px;flex:1;flex-wrap:wrap;">
                    <input type="hidden" name="tab" value="users">
                    <input type="hidden" name="filter" value="<?= adminSanitize($filter) ?>">
                    <input type="text" name="q" class="a-form-control" placeholder="İsim veya e-posta ara..."
                           value="<?= adminSanitize($search) ?>" style="flex:1;min-width:200px;max-width:320px;">
                    <button type="submit" class="a-btn a-btn-primary a-btn-sm">Ara</button>
                    <?php if ($search): ?>
                    <a href="?tab=users&filter=<?= $filter ?>" class="a-btn a-btn-ghost a-btn-sm">✕ Temizle</a>
                    <?php endif; ?>
                </form>
                <div class="a-filter-pills">
                    <?php foreach ([
                        ['all','Tümü'], ['premium','Premium'], ['free','Ücretsiz'], ['admin','Admin']
                    ] as [$f,$l]): ?>
                    <a href="?tab=users&filter=<?= $f ?><?= $search?'&q='.urlencode($search):'' ?>"
                       class="a-filter-pill <?= $filter===$f?'active':'' ?>"><?= $l ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="a-table-wrap">
        <?php if (empty($users)): ?>
            <div class="a-empty"><div class="icon">🔍</div><h3>Kullanıcı bulunamadı</h3></div>
        <?php else: ?>
            <table class="a-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kullanıcı</th>
                        <th>Üyelik</th>
                        <th>Premium Bitiş</th>
                        <th>İçerik</th>
                        <th>Kayıt</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $u): ?>
                <tr id="urow-<?= $u['id'] ?>">
                    <td class="muted"><?= $u['id'] ?></td>
                    <td>
                        <div class="name-cell">
                            <?= adminSanitize($u['name']) ?>
                            <?php if ($u['is_admin']): ?>
                            <span class="a-badge a-badge-red" style="font-size:9px;margin-left:4px;">Admin</span>
                            <?php endif; ?>
                        </div>
                        <div class="muted"><?= adminSanitize($u['email']) ?></div>
                    </td>
                    <td>
                        <?php if ($u['membership']==='premium'): ?>
                            <span class="a-badge a-badge-yellow">⭐ Premium</span>
                        <?php else: ?>
                            <span class="a-badge a-badge-blue">Ücretsiz</span>
                        <?php endif; ?>
                    </td>
                    <td class="muted"><?= $u['premium_until'] ? date('d.m.Y', strtotime($u['premium_until'])) : '—' ?></td>
                    <td>
                        <div style="display:flex;gap:10px;">
                            <span title="Sınıf" style="font-size:12px;"><b><?= $u['class_count'] ?></b> <span style="color:var(--text-muted);">sınıf</span></span>
                            <span title="Soru"  style="font-size:12px;"><b><?= $u['question_count'] ?></b> <span style="color:var(--text-muted);">soru</span></span>
                            <span title="Sınav" style="font-size:12px;"><b><?= $u['exam_count'] ?></b> <span style="color:var(--text-muted);">sınav</span></span>
                        </div>
                    </td>
                    <td class="muted"><?= date('d.m.Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <button class="a-btn a-btn-warning a-btn-sm"
                                onclick="openPremiumModal(<?= $u['id'] ?>, '<?= adminSanitize($u['name']) ?>', '<?= $u['membership'] ?>', '<?= $u['premium_until'] ?? '' ?>')">
                                ⭐ Premium
                            </button>
                            <?php if ($u['id'] != $_SESSION['admin_id'] && !$u['is_admin']): ?>
                            <button class="a-btn a-btn-danger a-btn-sm"
                                onclick="confirmDelete(<?= $u['id'] ?>, '<?= adminSanitize($u['name']) ?>')">
                                🗑
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        </div>
    </div>
</div>

<!-- ========== TAB: PREMİUM ========== -->
<div id="tab-premium" class="a-tab-content <?= $activeTab==='premium'?'active':'' ?>">

    <div class="a-grid-2" style="margin-bottom:20px;">
        <div class="a-card" style="border-left:3px solid #f59e0b;">
            <div class="a-card-body" style="display:flex;align-items:center;gap:16px;">
                <span style="font-size:36px;">⭐</span>
                <div>
                    <div style="font-size:26px;font-weight:800;color:#fbbf24;"><?= $s['premium_users'] ?></div>
                    <div style="font-size:12px;color:var(--text-muted);">Aktif Premium Üye</div>
                </div>
            </div>
        </div>
        <div class="a-card" style="border-left:3px solid #3b82f6;">
            <div class="a-card-body" style="display:flex;align-items:center;gap:16px;">
                <span style="font-size:36px;">👤</span>
                <div>
                    <div style="font-size:26px;font-weight:800;color:#60a5fa;"><?= $s['free_users'] ?></div>
                    <div style="font-size:12px;color:var(--text-muted);">Ücretsiz Üye</div>
                </div>
            </div>
        </div>
    </div>

    <div class="a-card">
        <div class="a-card-header"><span class="a-card-title">⭐ Premium Kullanıcılar</span></div>
        <div class="a-table-wrap">
        <?php if (empty($premiumUsers)): ?>
            <div class="a-empty"><div class="icon">⭐</div><h3>Henüz premium üye yok</h3></div>
        <?php else: ?>
            <table class="a-table">
                <thead>
                    <tr><th>Kullanıcı</th><th>Premium Bitiş</th><th>Durum</th><th>İşlemler</th></tr>
                </thead>
                <tbody>
                <?php foreach ($premiumUsers as $u):
                    $active = !$u['premium_until'] || strtotime($u['premium_until']) > time();
                ?>
                <tr id="prow-<?= $u['id'] ?>">
                    <td>
                        <div class="name-cell"><?= adminSanitize($u['name']) ?></div>
                        <div class="muted"><?= adminSanitize($u['email']) ?></div>
                    </td>
                    <td class="muted">
                        <?= $u['premium_until'] ? date('d.m.Y', strtotime($u['premium_until'])) : '<span class="a-badge a-badge-green">Süresiz</span>' ?>
                    </td>
                    <td>
                        <?= $active ? '<span class="a-badge a-badge-green">✓ Aktif</span>' : '<span class="a-badge a-badge-red">✗ Süresi Dolmuş</span>' ?>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <button class="a-btn a-btn-warning a-btn-sm"
                                onclick="openPremiumModal(<?= $u['id'] ?>, '<?= adminSanitize($u['name']) ?>', '<?= $u['membership'] ?>', '<?= $u['premium_until'] ?? '' ?>')">
                                ✏️ Düzenle
                            </button>
                            <button class="a-btn a-btn-danger a-btn-sm"
                                onclick="doRemovePremium(<?= $u['id'] ?>, '<?= adminSanitize($u['name']) ?>')">
                                ✗ Kaldır
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        </div>
    </div>
</div>

</div><!-- .admin-content -->

<!-- ========== PREMİUM MODAL ========== -->
<div class="a-modal-overlay" id="premiumOverlay" onclick="if(event.target===this)closePremiumModal()">
    <div class="a-modal">
        <h3>⭐ Premium Yönetimi</h3>
        <p id="premiumModalDesc"></p>
        <div class="a-form-group">
            <label class="a-form-label">Premium Bitiş Tarihi</label>
            <input type="date" id="premiumDate" class="a-form-control" min="<?= date('Y-m-d') ?>">
            <small style="color:var(--text-muted);font-size:11px;margin-top:4px;display:block;">Boş bırakırsanız süresiz premium verilir.</small>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button class="a-btn a-btn-success" onclick="savePremium()" style="flex:1;">✓ Premium Ver / Güncelle</button>
            <button class="a-btn a-btn-danger"  onclick="removePremiumFromModal()" style="flex:1;">✗ Kaldır</button>
            <button class="a-btn a-btn-ghost"   onclick="closePremiumModal()">İptal</button>
        </div>
    </div>
</div>

<!-- ========== SİLME MODAL ========== -->
<div class="a-modal-overlay" id="deleteOverlay" onclick="if(event.target===this)closeDeleteModal()">
    <div class="a-modal">
        <h3 style="color:#ef4444;">🗑️ Kullanıcıyı Sil</h3>
        <p id="deleteModalDesc"></p>
        <div class="a-alert a-alert-danger" style="margin-bottom:16px;">
            ⚠️ Bu işlem geri alınamaz! Kullanıcıya ait tüm içerik de silinecektir.
        </div>
        <div style="display:flex;gap:8px;">
            <button class="a-btn a-btn-danger" onclick="doDelete()" style="flex:1;">Evet, Sil</button>
            <button class="a-btn a-btn-ghost"  onclick="closeDeleteModal()" style="flex:1;">İptal</button>
        </div>
    </div>
</div>

<script>
// --- Tab switching ---
function switchTab(tab) {
    document.querySelectorAll('.a-tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.a-tab').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    event.currentTarget.classList.add('active');

    // Topbar başlığını güncelle
    const titles = { stats:'📊 Dashboard', users:'👥 Kullanıcılar', premium:'⭐ Premium Yönetimi' };
    document.querySelector('.admin-topbar h2').textContent = titles[tab] || '';
}

// URL'den aktif tab
(function() {
    const tab = new URLSearchParams(location.search).get('tab');
    if (!tab) return;
    const el = document.getElementById('tab-' + tab);
    if (!el) return;
    document.querySelectorAll('.a-tab-content').forEach(e => e.classList.remove('active'));
    document.querySelectorAll('.a-tab').forEach(e => e.classList.remove('active'));
    el.classList.add('active');
    const idx = ['stats','users','premium'].indexOf(tab);
    const tabs = document.querySelectorAll('.a-tab');
    if (idx >= 0 && tabs[idx]) tabs[idx].classList.add('active');
    const titles = { stats:'📊 Dashboard', users:'👥 Kullanıcılar', premium:'⭐ Premium Yönetimi' };
    document.querySelector('.admin-topbar h2').textContent = titles[tab] || '';
})();

// --- Premium modal ---
let curUserId = null, curUserName = null;

function openPremiumModal(id, name, membership, until) {
    curUserId   = id;
    curUserName = name;
    document.getElementById('premiumModalDesc').textContent = '"' + name + '" için premium ayarlarını düzenleyin.';
    document.getElementById('premiumDate').value = until ? until.substring(0,10) : '';
    document.getElementById('premiumOverlay').classList.add('open');
}
function closePremiumModal() {
    document.getElementById('premiumOverlay').classList.remove('open');
    curUserId = null;
}

function savePremium() {
    const until = document.getElementById('premiumDate').value;
    adminFetch({ action:'set_premium', user_id:curUserId, premium_until:until }, () => {
        showAdminToast('✅ Premium güncellendi!', 'success');
        closePremiumModal();
        setTimeout(() => location.reload(), 700);
    });
}
function doRemovePremium(id, name) {
    curUserId   = id;
    curUserName = name;
    removePremiumFromModal();
}
function removePremiumFromModal() {
    if (!curUserId) return;
    if (!confirm('"' + curUserName + '" kullanıcısının premiumunu kaldırmak istediğinize emin misiniz?')) return;
    adminFetch({ action:'remove_premium', user_id:curUserId }, () => {
        showAdminToast('✅ Premium kaldırıldı.', 'success');
        closePremiumModal();
        setTimeout(() => location.reload(), 700);
    });
}

// --- Silme ---
let deleteId = null;

function confirmDelete(id, name) {
    deleteId = id;
    document.getElementById('deleteModalDesc').textContent = '"' + name + '" kullanıcısını silmek istediğinize emin misiniz?';
    document.getElementById('deleteOverlay').classList.add('open');
}
function closeDeleteModal() {
    document.getElementById('deleteOverlay').classList.remove('open');
    deleteId = null;
}
function doDelete() {
    adminFetch({ action:'delete_user', user_id:deleteId }, () => {
        showAdminToast('✅ Kullanıcı silindi.', 'success');
        closeDeleteModal();
        const row = document.getElementById('urow-' + deleteId);
        if (row) row.style.opacity = '0';
        setTimeout(() => { if(row) row.remove(); }, 300);
    });
}

// --- Fetch helper ---
function adminFetch(data, onSuccess) {
    fetch('<?= SITE_URL ?>/admin/api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) { onSuccess(d); }
        else { showAdminToast('❌ ' + (d.error || 'Bilinmeyen hata'), 'error'); }
    })
    .catch(() => showAdminToast('❌ Sunucu bağlantı hatası', 'error'));
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
