<?php
$pageTitle = 'Sınavlarım';
require_once __DIR__ . '/includes/header.php';

$db  = getDB();
$uid = $_SESSION['user_id'];

// Seçili sınav
$examId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Tüm sınavlar
$stmt = $db->prepare("SELECT e.*, c.name AS class_name, (SELECT COUNT(*) FROM exam_questions WHERE exam_id=e.id) AS q_count FROM exams e LEFT JOIN classes c ON c.id=e.class_id WHERE e.user_id=? ORDER BY e.created_at DESC");
$stmt->execute([$uid]);
$exams = $stmt->fetchAll();

// Sınıflar
$allClasses = $db->prepare("SELECT * FROM classes WHERE user_id=? ORDER BY name");
$allClasses->execute([$uid]);
$allClasses = $allClasses->fetchAll();

// Seçili sınav detayı
$currentExam = null;
$examQuestions = [];
if ($examId) {
    $es = $db->prepare("SELECT e.*, c.name AS class_name FROM exams e LEFT JOIN classes c ON c.id=e.class_id WHERE e.id=? AND e.user_id=?");
    $es->execute([$examId, $uid]);
    $currentExam = $es->fetch();
    if ($currentExam) {
        $qs = $db->prepare("
            SELECT q.*, u.name AS unit_name, eq.order_num
            FROM exam_questions eq
            JOIN questions q ON q.id=eq.question_id
            JOIN units u ON u.id=q.unit_id
            WHERE eq.exam_id=?
            ORDER BY eq.order_num, eq.id
        ");
        $qs->execute([$examId]);
        $examQuestions = $qs->fetchAll();
    }
}
?>

<div class="topbar">
    <h2>📝 Sınavlarım</h2>
    <button class="btn btn-primary" onclick="openAddModal()">+ Yeni Sınav</button>
</div>

<div class="content-area">

<?php if ($currentExam): ?>
<!-- SINAV DETAYI -->
<div class="breadcrumb">
    <a href="exams.php">Sınavlarım</a>
    <span class="sep">›</span>
    <span><?= sanitize($currentExam['name']) ?></span>
</div>

<div class="card" style="margin-bottom:24px;">
    <div class="card-header">
        <div>
            <span class="card-title"><?= sanitize($currentExam['name']) ?></span>
            <?php if ($currentExam['class_name']): ?>
            <span class="badge badge-blue" style="margin-left:8px;">🏫 <?= sanitize($currentExam['class_name']) ?></span>
            <?php endif; ?>
            <?php if ($currentExam['duration']): ?>
            <span class="badge badge-yellow" style="margin-left:4px;">⏱️ <?= $currentExam['duration'] ?> dk</span>
            <?php endif; ?>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="exports/exam_pdf.php?id=<?= $examId ?>" class="btn btn-danger btn-sm" target="_blank">📄 PDF İndir</a>
            <a href="exports/exam_word.php?id=<?= $examId ?>" class="btn btn-primary btn-sm">📘 Word İndir</a>
            <a href="exams.php" class="btn btn-secondary btn-sm">← Geri</a>
        </div>
    </div>
    <?php if ($currentExam['description']): ?>
    <div style="padding:12px 24px;background:var(--bg);border-bottom:1px solid var(--border);font-size:13px;color:var(--text-muted);">
        <?= sanitize($currentExam['description']) ?>
    </div>
    <?php endif; ?>
    <div class="card-body" style="padding:16px;">
    <?php if (empty($examQuestions)): ?>
        <div class="empty-state" style="padding:32px;">
            <div class="icon">❓</div>
            <h3>Bu sınavda soru yok</h3>
            <p>Sorular sayfasından "Sınava Ekle" butonunu kullanın</p>
            <a href="questions.php" class="btn btn-primary" style="margin-top:12px;">❓ Sorulara Git</a>
        </div>
    <?php else: ?>
        <?php $n = 1; foreach ($examQuestions as $q): ?>
        <div class="question-item">
            <div class="question-num"><?= $n++ ?></div>
            <div class="question-content">
                <div class="question-title"><?= sanitize($q['title']) ?></div>
                <div style="display:flex;gap:6px;margin-top:6px;flex-wrap:wrap;">
                    <span class="badge <?= $q['type']==='test' ? 'badge-blue':'badge-purple' ?>"><?= $q['type']==='test'?'Test':'Klasik' ?></span>
                    <span class="badge badge-green">💯 <?= $q['points'] ?> puan</span>
                    <span class="badge badge-yellow">📚 <?= sanitize($q['unit_name']) ?></span>
                </div>
                <?php if ($q['type']==='test' && $q['option_a']): ?>
                <div class="question-options" style="margin-top:8px;">
                    <?php foreach(['A','B','C','D','E'] as $k):
                        $v = $q['option_'.strtolower($k)];
                        if (!$v) continue;
                        $isC = $q['correct_opt']===$k; ?>
                    <div class="question-option <?= $isC?'correct':'' ?>">
                        <div class="opt-label"><?= $k ?></div>
                        <span><?= sanitize($v) ?></span>
                        <?php if ($isC): ?><span>✓</span><?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="question-actions">
                <button class="btn btn-sm btn-danger" onclick="removeFromExam(<?= $examId ?>,<?= $q['id'] ?>)" title="Sınavdan Çıkar">✕</button>
            </div>
        </div>
        <?php endforeach; ?>
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px;align-items:center;">
            <span style="color:var(--text-muted);font-size:13px;">Toplam: <strong><?= count($examQuestions) ?> soru</strong> — 
            <?= array_sum(array_column($examQuestions,'points')) ?> puan</span>
        </div>
    <?php endif; ?>
    </div>
</div>

<?php else: ?>
<!-- SINAV LİSTESİ -->
<?php if (empty($exams)): ?>
<div class="empty-state">
    <div class="icon">📝</div>
    <h3>Henüz sınav yok</h3>
    <p>İlk sınavınızı oluşturun</p>
    <button class="btn btn-primary" style="margin-top:16px;" onclick="openAddModal()">+ Sınav Ekle</button>
</div>
<?php else: ?>
<div class="grid grid-3">
<?php foreach ($exams as $e): ?>
<div class="item-card" onclick="window.location='exams.php?id=<?= $e['id'] ?>'">
    <div class="item-title"><?= sanitize($e['name']) ?></div>
    <?php if ($e['class_name']): ?>
    <div class="item-meta">🏫 <?= sanitize($e['class_name']) ?></div>
    <?php endif; ?>
    <div class="item-meta" style="margin-top:4px;">❓ <?= $e['q_count'] ?> soru<?= $e['duration'] ? ' · ⏱️ '.$e['duration'].' dk' : '' ?></div>
    <div class="item-actions" onclick="event.stopPropagation()">
        <button class="btn btn-sm btn-secondary" onclick="openEditModal(<?= htmlspecialchars(json_encode($e),ENT_QUOTES) ?>)">✏️</button>
        <button class="btn btn-sm btn-danger"    onclick="deleteExam(<?= $e['id'] ?>,'<?= addslashes(sanitize($e['name'])) ?>')">🗑️</button>
        <a href="exports/exam_pdf.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-danger" target="_blank" onclick="event.stopPropagation()">📄 PDF</a>
        <a href="exports/exam_word.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-primary" onclick="event.stopPropagation()">📘 Word</a>
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
            <span class="modal-title">➕ Yeni Sınav Ekle</span>
            <button class="modal-close" onclick="closeModal('modal-add')">✕</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Sınav Adı *</label>
                <input type="text" id="add-name" class="form-control" placeholder="Örn: 1. Dönem Matematik Sınavı">
            </div>
            <div class="form-group">
                <label class="form-label">Sınıf (opsiyonel)</label>
                <select id="add-class" class="form-control">
                    <option value="">Sınıf seçin</option>
                    <?php foreach ($allClasses as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= sanitize($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Açıklama (opsiyonel)</label>
                <textarea id="add-desc" class="form-control" rows="2" placeholder="Sınav hakkında kısa açıklama"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Süre (dakika, opsiyonel)</label>
                <input type="number" id="add-duration" class="form-control" placeholder="Örn: 40">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modal-add')">İptal</button>
            <button class="btn btn-primary" onclick="addExam()">💾 Kaydet</button>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="modal-overlay" id="modal-edit">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">✏️ Sınavı Düzenle</span>
            <button class="modal-close" onclick="closeModal('modal-edit')">✕</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="edit-id">
            <div class="form-group">
                <label class="form-label">Sınav Adı *</label>
                <input type="text" id="edit-name" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Sınıf</label>
                <select id="edit-class" class="form-control">
                    <option value="">Sınıf seçin</option>
                    <?php foreach ($allClasses as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= sanitize($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Açıklama</label>
                <textarea id="edit-desc" class="form-control" rows="2"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Süre (dakika)</label>
                <input type="number" id="edit-duration" class="form-control">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modal-edit')">İptal</button>
            <button class="btn btn-primary" onclick="updateExam()">💾 Güncelle</button>
        </div>
    </div>
</div>

<script>
function openAddModal() { openModal('modal-add'); }
function openEditModal(e) {
    document.getElementById('edit-id').value       = e.id;
    document.getElementById('edit-name').value     = e.name;
    document.getElementById('edit-class').value    = e.class_id || '';
    document.getElementById('edit-desc').value     = e.description || '';
    document.getElementById('edit-duration').value = e.duration || '';
    openModal('modal-edit');
}
async function addExam() {
    const name     = document.getElementById('add-name').value.trim();
    const classId  = document.getElementById('add-class').value;
    const desc     = document.getElementById('add-desc').value;
    const duration = document.getElementById('add-duration').value;
    if (!name) { showToast('Sınav adı boş olamaz','error'); return; }
    const r = await apiCall('api/exam.php', { action:'add', name, class_id:classId, description:desc, duration });
    if (r.error) showToast(r.error,'error');
    else { showToast('Sınav eklendi!','success'); setTimeout(()=>location.reload(),800); }
}
async function updateExam() {
    const id       = document.getElementById('edit-id').value;
    const name     = document.getElementById('edit-name').value.trim();
    const classId  = document.getElementById('edit-class').value;
    const desc     = document.getElementById('edit-desc').value;
    const duration = document.getElementById('edit-duration').value;
    if (!name) { showToast('Sınav adı boş olamaz','error'); return; }
    const r = await apiCall('api/exam.php', { action:'update', id, name, class_id:classId, description:desc, duration });
    if (r.error) showToast(r.error,'error');
    else { showToast('Güncellendi!','success'); setTimeout(()=>location.reload(),800); }
}
function deleteExam(id, name) {
    confirmDelete(`"${name}" sınavını silmek istediğinize emin misiniz?`, async ()=>{
        const r = await apiCall('api/exam.php', { action:'delete', id });
        if (r.error) showToast(r.error,'error');
        else { showToast('Sınav silindi','success'); setTimeout(()=>location.reload(),800); }
    });
}
async function removeFromExam(examId, qid) {
    confirmDelete('Bu soruyu sınavdan çıkarmak istiyor musunuz?', async ()=>{
        const r = await apiCall('api/exam.php', { action:'remove_question', exam_id:examId, question_id:qid });
        if (r.error) showToast(r.error,'error');
        else { showToast('Sınavdan çıkarıldı','success'); setTimeout(()=>location.reload(),800); }
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
