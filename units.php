<?php
$pageTitle = 'Ünitelerim';
require_once __DIR__ . '/includes/header.php';

$db       = getDB();
$uid      = $_SESSION['user_id'];
$classId  = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;

// Sınıf listesi (dropdown için)
$allClasses = $db->prepare("SELECT * FROM classes WHERE user_id=? ORDER BY name");
$allClasses->execute([$uid]);
$allClasses = $allClasses->fetchAll();

// Seçili sınıf
$currentClass = null;
if ($classId) {
    $cs = $db->prepare("SELECT * FROM classes WHERE id=? AND user_id=?");
    $cs->execute([$classId, $uid]);
    $currentClass = $cs->fetch();
}

// Üniteler
$where = $classId ? "AND u.class_id=$classId" : '';
$stmt  = $db->prepare("
    SELECT u.*, c.name AS class_name,
        (SELECT COUNT(*) FROM questions WHERE unit_id=u.id) AS q_count
    FROM units u
    JOIN classes c ON c.id=u.class_id
    WHERE u.user_id=? $where
    ORDER BY c.name, u.order_num, u.name
");
$stmt->execute([$uid]);
$units = $stmt->fetchAll();
?>

<div class="topbar">
    <h2>📚 Ünitelerim <?= $currentClass ? '— ' . sanitize($currentClass['name']) : '' ?></h2>
    <?php if ($classId): ?>
    <div style="display:flex;gap:8px;">
        <a href="units.php" class="btn btn-secondary">← Sınıflar</a>
        <button class="btn btn-primary" onclick="openAddModal()">+ Yeni Ünite</button>
    </div>
    <?php endif; ?>
</div>

<div class="content-area">

<?php if (!$classId): ?>
<!-- SINIF SEÇİM EKRANI -->
<?php if (empty($allClasses)): ?>
<div class="empty-state">
    <div class="icon">🏫</div>
    <h3>Henüz sınıf yok</h3>
    <p>Önce bir sınıf oluşturun</p>
    <a href="classes.php" class="btn btn-primary" style="margin-top:16px;">+ Sınıf Ekle</a>
</div>
<?php else: ?>
<p style="color:var(--text-muted);font-size:13px;margin-bottom:20px;">Üniteleri görmek için bir sınıf seçin:</p>
<div class="grid grid-3">
<?php foreach ($allClasses as $c):
    $uCount = $db->prepare("SELECT COUNT(*) FROM units WHERE class_id=? AND user_id=?");
    $uCount->execute([$c['id'], $uid]);
    $uCount = (int)$uCount->fetchColumn();
?>
<a href="units.php?class_id=<?= $c['id'] ?>" class="item-card" style="text-decoration:none;cursor:pointer;">
    <div class="item-title">🏫 <?= sanitize($c['name']) ?></div>
    <div class="item-meta" style="margin-top:6px;">📚 <?= $uCount ?> ünite</div>
</a>
<?php endforeach; ?>
</div>
<?php endif; ?>

<?php else: ?>
<!-- ÜNİTE LİSTESİ -->
<?php if (empty($units)): ?>
<div class="empty-state">
    <div class="icon">📚</div>
    <h3>Bu sınıfta henüz ünite yok</h3>
    <p>İlk ünitenizi oluşturun</p>
    <button class="btn btn-primary" style="margin-top:16px;" onclick="openAddModal()">+ Ünite Ekle</button>
</div>
<?php else: ?>
<div class="grid grid-3">
<?php foreach ($units as $u): ?>
<div class="item-card" onclick="window.location='questions.php?unit_id=<?= $u['id'] ?>'">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
        <div>
            <div class="item-title"><?= sanitize($u['name']) ?></div>
            <div class="item-meta" style="margin-top:4px;">❓ <?= $u['q_count'] ?> soru</div>
        </div>
    </div>
    <div class="item-actions" onclick="event.stopPropagation()">
        <button class="btn btn-sm btn-secondary" onclick="openEditModal(<?= htmlspecialchars(json_encode($u), ENT_QUOTES) ?>)">✏️ Düzenle</button>
        <button class="btn btn-sm btn-danger" onclick="deleteUnit(<?= $u['id'] ?>, '<?= addslashes(sanitize($u['name'])) ?>')">🗑️ Sil</button>
        <a href="questions.php?unit_id=<?= $u['id'] ?>" class="btn btn-sm btn-primary" onclick="event.stopPropagation()">❓ Sorular →</a>
    </div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
<?php endif; ?>

</div>

<!-- ADD MODAL -->
<div class="modal-overlay" id="modal-add">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">➕ Yeni Ünite Ekle</span>
            <button class="modal-close" onclick="closeModal('modal-add')">✕</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Sınıf *</label>
                <select id="add-class" class="form-control">
                    <option value="">Sınıf seçin</option>
                    <?php foreach ($allClasses as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $classId==$c['id'] ? 'selected' : '' ?>><?= sanitize($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Ünite Adı *</label>
                <input type="text" id="add-name" class="form-control" placeholder="Örn: 1. Ünite - Hücreler">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modal-add')">İptal</button>
            <button class="btn btn-primary" onclick="addUnit()">💾 Kaydet</button>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="modal-overlay" id="modal-edit">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">✏️ Üniteyi Düzenle</span>
            <button class="modal-close" onclick="closeModal('modal-edit')">✕</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="edit-id">
            <div class="form-group">
                <label class="form-label">Sınıf *</label>
                <select id="edit-class" class="form-control">
                    <?php foreach ($allClasses as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= sanitize($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Ünite Adı *</label>
                <input type="text" id="edit-name" class="form-control">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modal-edit')">İptal</button>
            <button class="btn btn-primary" onclick="updateUnit()">💾 Güncelle</button>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    <?php if (empty($allClasses)): ?>
    showToast('Önce bir sınıf oluşturun!', 'warning');
    setTimeout(()=>window.location='classes.php', 1200);
    return;
    <?php endif; ?>
    openModal('modal-add');
}
function openEditModal(u) {
    document.getElementById('edit-id').value    = u.id;
    document.getElementById('edit-name').value  = u.name;
    document.getElementById('edit-class').value = u.class_id;
    openModal('modal-edit');
}
async function addUnit() {
    const classId = document.getElementById('add-class').value;
    const name    = document.getElementById('add-name').value.trim();
    if (!classId) { showToast('Sınıf seçin', 'error'); return; }
    if (!name)    { showToast('Ünite adı boş olamaz', 'error'); return; }
    const r = await apiCall('api/unit.php', { action:'add', class_id:classId, name });
    if (r.error) showToast(r.error, 'error');
    else { showToast('Ünite eklendi!', 'success'); setTimeout(()=>location.reload(),800); }
}
async function updateUnit() {
    const id      = document.getElementById('edit-id').value;
    const classId = document.getElementById('edit-class').value;
    const name    = document.getElementById('edit-name').value.trim();
    if (!name) { showToast('Ünite adı boş olamaz', 'error'); return; }
    const r = await apiCall('api/unit.php', { action:'update', id, class_id:classId, name });
    if (r.error) showToast(r.error, 'error');
    else { showToast('Güncellendi!', 'success'); setTimeout(()=>location.reload(),800); }
}
function deleteUnit(id, name) {
    confirmDelete(`"${name}" ünitesini silmek istediğinize emin misiniz? İçindeki tüm sorular da silinecek!`, async ()=>{
        const r = await apiCall('api/unit.php', { action:'delete', id });
        if (r.error) showToast(r.error, 'error');
        else { showToast('Ünite silindi', 'success'); setTimeout(()=>location.reload(),800); }
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
