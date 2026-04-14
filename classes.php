<?php
$pageTitle = 'Sınıflarım';
require_once __DIR__ . '/includes/header.php';

$db  = getDB();
$uid = $_SESSION['user_id'];

// Sınıf listesi
$stmt = $db->prepare("
    SELECT c.*,
        (SELECT COUNT(*) FROM units WHERE class_id=c.id) AS unit_count
    FROM classes c WHERE c.user_id=? ORDER BY c.created_at DESC
");
$stmt->execute([$uid]);
$classes = $stmt->fetchAll();
?>

<div class="topbar">
    <h2>🏫 Sınıflarım</h2>
    <button class="btn btn-primary" onclick="openAddModal()">+ Yeni Sınıf</button>
</div>

<div class="content-area">

<?php if (empty($classes)): ?>
<div class="empty-state">
    <div class="icon">🏫</div>
    <h3>Henüz sınıf yok</h3>
    <p>İlk sınıfınızı oluşturun</p>
    <button class="btn btn-primary" style="margin-top:16px;" onclick="openAddModal()">+ Sınıf Ekle</button>
</div>
<?php else: ?>
<div class="grid grid-3">
<?php foreach ($classes as $c): ?>
<div class="item-card" onclick="window.location='units.php?class_id=<?= $c['id'] ?>'">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
        <div>
            <div class="item-title"><?= sanitize($c['name']) ?></div>
            <?php if ($c['grade']): ?>
            <div class="item-meta">🎓 <?= sanitize($c['grade']) ?>. Sınıf</div>
            <?php endif; ?>
            <div class="item-meta" style="margin-top:6px;">📚 <?= $c['unit_count'] ?> ünite</div>
        </div>
        <span class="badge badge-blue">#<?= $c['id'] ?></span>
    </div>
    <div class="item-actions" onclick="event.stopPropagation()">
        <button class="btn btn-sm btn-secondary" onclick="openEditModal(<?= htmlspecialchars(json_encode($c), ENT_QUOTES) ?>)">✏️ Düzenle</button>
        <button class="btn btn-sm btn-danger" onclick="deleteClass(<?= $c['id'] ?>, '<?= addslashes(sanitize($c['name'])) ?>')">🗑️ Sil</button>
        <a href="units.php?class_id=<?= $c['id'] ?>" class="btn btn-sm btn-primary">📚 Üniteler →</a>
    </div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>

</div>

<!-- ADD MODAL -->
<div class="modal-overlay" id="modal-add">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">➕ Yeni Sınıf Ekle</span>
            <button class="modal-close" onclick="closeModal('modal-add')">✕</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Sınıf Adı *</label>
                <input type="text" id="add-name" class="form-control" placeholder="Örn: 5-A Matematik, Biyoloji 11-B">
            </div>
            <div class="form-group">
                <label class="form-label">Sınıf Seviyesi (opsiyonel)</label>
                <select id="add-grade" class="form-control">
                    <option value="">Seçiniz</option>
                    <?php for($i=1;$i<=12;$i++) echo "<option value='$i'>$i. Sınıf</option>"; ?>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modal-add')">İptal</button>
            <button class="btn btn-primary" onclick="addClass()">💾 Kaydet</button>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="modal-overlay" id="modal-edit">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">✏️ Sınıfı Düzenle</span>
            <button class="modal-close" onclick="closeModal('modal-edit')">✕</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="edit-id">
            <div class="form-group">
                <label class="form-label">Sınıf Adı *</label>
                <input type="text" id="edit-name" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Sınıf Seviyesi</label>
                <select id="edit-grade" class="form-control">
                    <option value="">Seçiniz</option>
                    <?php for($i=1;$i<=12;$i++) echo "<option value='$i'>$i. Sınıf</option>"; ?>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modal-edit')">İptal</button>
            <button class="btn btn-primary" onclick="updateClass()">💾 Güncelle</button>
        </div>
    </div>
</div>

<script>
function openAddModal() { openModal('modal-add'); }

function openEditModal(c) {
    document.getElementById('edit-id').value   = c.id;
    document.getElementById('edit-name').value  = c.name;
    document.getElementById('edit-grade').value = c.grade || '';
    openModal('modal-edit');
}

async function addClass() {
    const name  = document.getElementById('add-name').value.trim();
    const grade = document.getElementById('add-grade').value;
    if (!name) { showToast('Sınıf adı boş olamaz', 'error'); return; }
    const r = await apiCall('api/class.php', { action:'add', name, grade });
    if (r.error)   showToast(r.error, 'error');
    else { showToast('Sınıf eklendi!', 'success'); setTimeout(()=>location.reload(),800); }
}

async function updateClass() {
    const id    = document.getElementById('edit-id').value;
    const name  = document.getElementById('edit-name').value.trim();
    const grade = document.getElementById('edit-grade').value;
    if (!name) { showToast('Sınıf adı boş olamaz', 'error'); return; }
    const r = await apiCall('api/class.php', { action:'update', id, name, grade });
    if (r.error)   showToast(r.error, 'error');
    else { showToast('Güncellendi!', 'success'); setTimeout(()=>location.reload(),800); }
}

function deleteClass(id, name) {
    confirmDelete(`"${name}" sınıfını silmek istediğinize emin misiniz? Tüm üniteler ve sorular da silinecek!`, async () => {
        const r = await apiCall('api/class.php', { action:'delete', id });
        if (r.error) showToast(r.error, 'error');
        else { showToast('Sınıf silindi', 'success'); setTimeout(()=>location.reload(),800); }
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
