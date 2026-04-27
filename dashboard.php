<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';

$db = getDB();
$uid = $_SESSION['user_id'];

// İstatistikler
$stats = [];
foreach (['classes','units','questions','exams'] as $t) {
    $s = $db->prepare("SELECT COUNT(*) FROM $t WHERE user_id = ?");
    $s->execute([$uid]);
    $stats[$t] = (int)$s->fetchColumn();
}

// Son sorular
$sq = $db->prepare("SELECT q.*, u.name AS unit_name FROM questions q JOIN units u ON u.id=q.unit_id WHERE q.user_id=? ORDER BY q.created_at DESC LIMIT 5");
$sq->execute([$uid]);
$recentQ = $sq->fetchAll();

// Son sınavlar
$se = $db->prepare("SELECT e.*, c.name AS class_name FROM exams e LEFT JOIN classes c ON c.id=e.class_id WHERE e.user_id=? ORDER BY e.created_at DESC LIMIT 5");
$se->execute([$uid]);
$recentE = $se->fetchAll();
?>

<div class="topbar">
    <h2>👋 Hoş geldiniz, <?= sanitize($user['name']) ?>!</h2>
    <div style="display:flex;gap:8px;">
        <a href="classes.php" class="btn btn-primary btn-sm">+ Yeni Sınıf</a>
    </div>
</div>

<div class="content-area">

<!-- STATS -->
<div class="grid grid-3" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:#dbeafe;">🏫</div>
        <div>
            <div class="stat-value"><?= $stats['classes'] ?></div>
            <div class="stat-label">Sınıf</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#ede9fe;">📚</div>
        <div>
            <div class="stat-value"><?= $stats['units'] ?></div>
            <div class="stat-label">Ünite</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#d1fae5;">❓</div>
        <div>
            <div class="stat-value"><?= $stats['questions'] ?></div>
            <div class="stat-label">Soru</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef3c7;">📝</div>
        <div>
            <div class="stat-value"><?= $stats['exams'] ?></div>
            <div class="stat-label">Sınav</div>
        </div>
    </div>
</div>

<!-- HIZLI ERİŞİM -->
<div class="card" style="margin-bottom:24px;">
    <div class="card-header"><span class="card-title">⚡ Hızlı Erişim</span></div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px;">
        <a href="classes.php" class="btn btn-secondary"><span>🏫</span> Sınıflarımı Yönet</a>
        <a href="units.php"   class="btn btn-secondary"><span>📚</span> Üniteleri Yönet</a>
        <a href="questions.php" class="btn btn-secondary"><span>❓</span> Soruları Yönet</a>
        <a href="exams.php"   class="btn btn-secondary"><span>📝</span> Sınavları Yönet</a>
    </div>
</div>

<!-- SON SINAVLAR -->
<div class="grid grid-2" style="align-items:start;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">📝 Son Sınavlar</span>
            <a href="exams.php" class="btn btn-sm btn-secondary">Tümü</a>
        </div>
        <div class="card-body" style="padding:0;">
            <?php if ($recentE): ?>
            <div class="table-wrap">
            <table>
            <thead><tr><th>Sınav Adı</th><th>Sınıf</th></tr></thead>
            <tbody>
            <?php foreach ($recentE as $e): ?>
            <tr>
                <td><a href="exams.php?id=<?= $e['id'] ?>" style="color:var(--primary);text-decoration:none;font-weight:600;"><?= sanitize($e['name']) ?></a></td>
                <td><?= $e['class_name'] ? sanitize($e['class_name']) : '<span style="color:var(--text-muted)">—</span>' ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            </table>
            </div>
            <?php else: ?>
            <div class="empty-state" style="padding:32px;">
                <div class="icon">📝</div>
                <p>Henüz sınav yok</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">❓ Son Sorular</span>
            <a href="questions.php" class="btn btn-sm btn-secondary">Tümü</a>
        </div>
        <div class="card-body" style="padding:0;">
            <?php if ($recentQ): ?>
            <div class="table-wrap">
            <table>
            <thead><tr><th>Soru</th><th>Tür</th><th>Ünite</th></tr></thead>
            <tbody>
            <?php foreach ($recentQ as $q): ?>
            <tr>
                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    <?= sanitize(mb_substr($q['title'], 0, 40, 'UTF-8')) ?>...
                </td>
                <td><span class="badge <?= $q['type']==='test' ? 'badge-blue' : 'badge-purple' ?>"><?= $q['type'] === 'test' ? 'Test' : 'Klasik' ?></span></td>
                <td><?= sanitize($q['unit_name']) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            </table>
            </div>
            <?php else: ?>
            <div class="empty-state" style="padding:32px;">
                <div class="icon">❓</div>
                <p>Henüz soru yok</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</div><!-- .content-area -->


<?php require_once __DIR__ . '/includes/footer.php'; ?>
