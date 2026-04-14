<?php
$pageTitle = 'Online Sınav Yönetimi';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db_online_helpers.php';

$db  = getDB();
$uid = $_SESSION['user_id'];

// Tüm sınavlar
$stmt = $db->prepare("
    SELECT e.*, c.name AS class_name,
        (SELECT COUNT(*) FROM exam_questions WHERE exam_id=e.id) AS q_count,
        (SELECT COUNT(*) FROM exam_sessions WHERE exam_id=e.id AND status IN ('submitted','graded')) AS submission_count
    FROM exams e
    LEFT JOIN classes c ON c.id=e.class_id
    WHERE e.user_id=?
    ORDER BY e.created_at DESC
");
$stmt->execute([$uid]);
$exams = $stmt->fetchAll();
?>

<div class="topbar">
    <h2>🌐 Online Sınav Yönetimi</h2>
</div>

<div class="content-area">

<div class="alert alert-info">
    💡 Bir sınava online erişim açarak öğrencilere link gönderin. Öğrenciler isim, soyisim ve okul numarasıyla sınava girebilir.
</div>

<?php if (empty($exams)): ?>
<div class="empty-state">
    <div class="icon">📝</div>
    <h3>Henüz sınav yok</h3>
    <p>Önce <a href="exams.php">sınav oluşturun</a> ve sorular ekleyin.</p>
</div>
<?php else: ?>

<div class="grid grid-2">
<?php foreach ($exams as $e):
    $isOnline = (bool)$e['is_online'];
    $isOpen   = $isOnline && isExamOpen($e);
    $isEnded  = $isOnline && isExamEnded($e);
    $link     = SITE_URL . '/student/sinav.php?kod=' . $e['access_code'];
?>
<div class="card">
    <div class="card-header">
        <div>
            <span class="card-title"><?= sanitize($e['name']) ?></span>
            <?php if ($e['class_name']): ?>
            <span class="badge badge-blue" style="margin-left:6px;"><?= sanitize($e['class_name']) ?></span>
            <?php endif; ?>
        </div>
        <?php if (!$isOnline): ?>
            <span class="badge badge-yellow">⏸ Kapalı</span>
        <?php elseif ($isEnded): ?>
            <span class="badge badge-red">🔴 Sona Erdi</span>
        <?php elseif ($isOpen): ?>
            <span class="badge badge-green">🟢 Aktif</span>
        <?php else: ?>
            <span class="badge badge-blue">⏳ Bekliyor</span>
        <?php endif; ?>
    </div>
    <div class="card-body" style="padding:16px;">
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px;font-size:13px;color:var(--text-muted);">
            <span>❓ <?= $e['q_count'] ?> soru</span>
            <?php if ($e['duration']): ?><span>⏱ <?= $e['duration'] ?> dk</span><?php endif; ?>
            <span>📨 <?= $e['submission_count'] ?> teslim</span>
        </div>

        <?php if ($isOnline): ?>
        <!-- LİNK KUTUSU -->
        <div style="background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:10px 12px;margin-bottom:12px;">
            <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px;font-weight:600;">🔗 Sınav Linki</div>
            <div style="display:flex;align-items:center;gap:8px;">
                <code style="font-size:11px;flex:1;word-break:break-all;color:var(--primary);"><?= $link ?></code>
                <button class="btn btn-sm btn-secondary" onclick="copyLink('<?= $link ?>')">📋</button>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-bottom:12px;font-size:12px;">
            <?php if ($e['start_time']): ?>
            <div style="color:var(--text-muted);">▶ Başlangıç:<br><strong style="color:var(--text);"><?= date('d.m.Y H:i', strtotime($e['start_time'])) ?></strong></div>
            <?php endif; ?>
            <?php if ($e['end_time']): ?>
            <div style="color:var(--text-muted);">⏹ Bitiş:<br><strong style="color:var(--text);"><?= date('d.m.Y H:i', strtotime($e['end_time'])) ?></strong></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div style="display:flex;gap:6px;flex-wrap:wrap;">
            <?php if (!$isOnline): ?>
            <button class="btn btn-sm btn-primary" onclick="openPublishModal(<?= htmlspecialchars(json_encode($e), ENT_QUOTES) ?>)">🌐 Yayına Al</button>
            <?php else: ?>
            <button class="btn btn-sm btn-secondary" onclick="updateExamOnline(<?= htmlspecialchars(json_encode($e), ENT_QUOTES) ?>)">✏️ Ayarları Düzenle</button>
            <button class="btn btn-sm btn-danger" onclick="closeExam(<?= $e['id'] ?>)">🔒 Kapat</button>
            <?php endif; ?>
            <?php if ($e['submission_count'] > 0): ?>
            <a href="online_sonuclar.php?exam_id=<?= $e['id'] ?>" class="btn btn-sm btn-success">📊 Sonuçlar (<?= $e['submission_count'] ?>)</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div>

<!-- YAYINA AL MODAL -->
<div class="modal-overlay" id="modal-publish">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <span class="modal-title">🌐 Sınavı Yayına Al</span>
            <button class="modal-close" onclick="closeModal('modal-publish')">✕</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="pub-exam-id">
            <div class="alert alert-info" style="font-size:12px;">
                Sınav yayına alındığında öğrencilere bir link oluşturulur. Bu linki WhatsApp, e-posta veya herhangi bir yolla paylaşabilirsiniz.
            </div>
            <div class="form-group">
                <label class="form-label">Başlangıç Tarihi & Saati</label>
                <input type="datetime-local" id="pub-start" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Bitiş Tarihi & Saati *</label>
                <input type="datetime-local" id="pub-end" class="form-control">
                <small style="color:var(--text-muted);font-size:11px;">Bitiş saatinde sınav otomatik kapanır ve cevaplar gönderilir.</small>
            </div>
            <div class="form-group">
                <label class="form-label">Sonuçlar ne zaman görünsün?</label>
                <select id="pub-results" class="form-control">
                    <option value="after_end">Sınav süresi tamamen dolunca</option>
                    <option value="instant">Sınav biter bitmez anında</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modal-publish')">İptal</button>
            <button class="btn btn-primary" onclick="publishExam()">🚀 Yayına Al</button>
        </div>
    </div>
</div>

<script>
function openPublishModal(e) {
    document.getElementById('pub-exam-id').value = e.id;
    // Varsayılan: şu andan itibaren 7 gün
    const now  = new Date();
    const end  = new Date(now.getTime() + 7*24*60*60*1000);
    document.getElementById('pub-start').value = toLocal(now);
    document.getElementById('pub-end').value   = toLocal(end);
    openModal('modal-publish');
}
function updateExamOnline(e) {
    document.getElementById('pub-exam-id').value = e.id;
    document.getElementById('pub-start').value   = e.start_time ? e.start_time.replace(' ','T').slice(0,16) : '';
    document.getElementById('pub-end').value     = e.end_time   ? e.end_time.replace(' ','T').slice(0,16)   : '';
    document.getElementById('pub-results').value = e.results_visible || 'after_end';
    openModal('modal-publish');
}
function toLocal(d) {
    return new Date(d.getTime() - d.getTimezoneOffset()*60000).toISOString().slice(0,16);
}
async function publishExam() {
    const id      = document.getElementById('pub-exam-id').value;
    const start   = document.getElementById('pub-start').value;
    const end     = document.getElementById('pub-end').value;
    const results = document.getElementById('pub-results').value;
    if (!end) { showToast('Bitiş zamanı zorunlu', 'error'); return; }
    const r = await apiCall('api/online_exam.php', { action:'publish', id, start_time:start, end_time:end, results_visible:results });
    if (r.error) showToast(r.error, 'error');
    else { showToast('Sınav yayına alındı! 🎉', 'success'); setTimeout(()=>location.reload(), 900); }
}
async function closeExam(id) {
    confirmDelete('Sınavı kapatmak istediğinize emin misiniz? Öğrenciler artık erişemez.', async () => {
        const r = await apiCall('api/online_exam.php', { action:'close', id });
        if (r.error) showToast(r.error, 'error');
        else { showToast('Sınav kapatıldı', 'success'); setTimeout(()=>location.reload(), 800); }
    });
}
function copyLink(link) {
    navigator.clipboard.writeText(link).then(() => showToast('Link kopyalandı! 📋', 'success'));
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
