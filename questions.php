<?php
$pageTitle = 'Sorularım';
require_once __DIR__ . '/includes/header.php';

$db     = getDB();
$uid    = $_SESSION['user_id'];
$unitId = isset($_GET['unit_id']) ? (int)$_GET['unit_id'] : 0;

// Tüm üniteler (dropdown için)
$allUnits = $db->prepare("SELECT u.*, c.name AS class_name FROM units u JOIN classes c ON c.id=u.class_id WHERE u.user_id=? ORDER BY c.name, u.name");
$allUnits->execute([$uid]);
$allUnits = $allUnits->fetchAll();

// Seçili ünite
$currentUnit = null;
if ($unitId) {
    $us = $db->prepare("SELECT u.*, c.name AS class_name FROM units u JOIN classes c ON c.id=u.class_id WHERE u.id=? AND u.user_id=?");
    $us->execute([$unitId, $uid]);
    $currentUnit = $us->fetch();
}

// Sınavlar (ekleme için)
$allExams = $db->prepare("SELECT * FROM exams WHERE user_id=? ORDER BY name");
$allExams->execute([$uid]);
$allExams = $allExams->fetchAll();

// Sorular (üniteye göre grupla)
$where = $unitId ? "AND q.unit_id=$unitId" : '';
$stmt  = $db->prepare("
    SELECT q.*, u.name AS unit_name, c.name AS class_name
    FROM questions q
    JOIN units u ON u.id=q.unit_id
    JOIN classes c ON c.id=u.class_id
    WHERE q.user_id=? $where
    ORDER BY u.id, q.created_at
");
$stmt->execute([$uid]);
$questions = $stmt->fetchAll();

// Grupla üniteye göre
$byUnit = [];
foreach ($questions as $q) {
    $byUnit[$q['unit_id']]['name']      = $q['unit_name'];
    $byUnit[$q['unit_id']]['class']     = $q['class_name'];
    $byUnit[$q['unit_id']]['questions'][] = $q;
}
?>

<div class="topbar">
    <h2>❓ Sorularım <?= $currentUnit ? '— ' . sanitize($currentUnit['name']) : '' ?></h2>
    <?php if ($unitId): ?>
    <div style="display:flex;gap:8px;">
        <a href="units.php?class_id=<?= $currentUnit['class_id'] ?? '' ?>" class="btn btn-secondary">← Üniteler</a>
        <button class="btn btn-primary" onclick="openAddModal()">+ Yeni Soru</button>
    </div>
    <?php endif; ?>
</div>

<div class="content-area">

<?php if (!$unitId): ?>
<!-- ÜNİTE SEÇİM EKRANI -->
<?php if (empty($allUnits)): ?>
<div class="empty-state">
    <div class="icon">📚</div>
    <h3>Henüz ünite yok</h3>
    <p>Önce bir ünite oluşturun</p>
    <a href="units.php" class="btn btn-primary" style="margin-top:16px;">📚 Ünitelere Git</a>
</div>
<?php else:
    // Üniteleri sınıfa göre grupla
    $unitsByClass = [];
    foreach ($allUnits as $u) {
        $unitsByClass[$u['class_name']][] = $u;
    }
?>
<p style="color:var(--text-muted);font-size:13px;margin-bottom:20px;">Soruları görmek için bir ünite seçin:</p>
<?php foreach ($unitsByClass as $className => $classUnits): ?>
<div style="margin-bottom:24px;">
    <div style="font-weight:700;font-size:13px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px;padding-bottom:6px;border-bottom:1px solid var(--border);">
        🏫 <?= sanitize($className) ?>
    </div>
    <div class="grid grid-3">
    <?php foreach ($classUnits as $u): ?>
    <a href="questions.php?unit_id=<?= $u['id'] ?>" class="item-card" style="text-decoration:none;cursor:pointer;">
        <div class="item-title">📚 <?= sanitize($u['name']) ?></div>
        <div class="item-meta" style="margin-top:6px;">❓ <?= $db->query("SELECT COUNT(*) FROM questions WHERE unit_id={$u['id']}")->fetchColumn() ?> soru</div>
    </a>
    <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<?php else: ?>
<!-- SORU LİSTESİ -->
<?php if (empty($byUnit)): ?>
<div class="empty-state">
    <div class="icon">❓</div>
    <h3>Bu ünitede henüz soru yok</h3>
    <p>İlk sorunuzu oluşturun</p>
    <button class="btn btn-primary" style="margin-top:16px;" onclick="openAddModal()">+ Soru Ekle</button>
</div>
<?php else:
    $globalNum = 1;
    foreach ($byUnit as $uid2 => $group):
?>
<div class="card" style="margin-bottom:24px;">
    <div class="card-header">
        <div>
            <span class="card-title">📚 <?= sanitize($group['name']) ?></span>
            <span style="color:var(--text-muted);font-size:12px;margin-left:8px;">— <?= sanitize($group['class']) ?></span>
        </div>
        <button class="btn btn-sm btn-primary" onclick="setUnitAndOpenModal(<?= $uid2 ?>)">+ Soru Ekle</button>
    </div>
    <div class="card-body" style="padding:16px;">
    <?php foreach ($group['questions'] as $q): ?>
    <?php if ($q['type'] === 'true_false'): ?>
    <!-- D/Y SORU — inline format -->
    <div class="question-item">
        <div class="question-num"><?= $globalNum++ ?></div>
        <div class="question-content">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <span class="question-title" style="margin:0;"><?= sanitize($q['title']) ?></span>
                <span style="font-size:13px;font-weight:700;color:var(--text-muted);white-space:nowrap;">( D &nbsp;/&nbsp; Y )</span>
                <span style="font-size:12px;padding:2px 8px;border-radius:4px;background:<?= $q['correct_opt']==='A' ? '#d1fae5' : '#fee2e2' ?>;color:<?= $q['correct_opt']==='A' ? '#065f46' : '#991b1b' ?>;font-weight:700;">
                    Cevap: <?= $q['correct_opt']==='A' ? 'Doğru' : 'Yanlış' ?>
                </span>
            </div>
            <div style="display:flex;gap:8px;margin-top:5px;flex-wrap:wrap;">
                <span class="badge badge-orange">✅ Doğru/Yanlış</span>
                <span class="badge badge-green">💯 <?= $q['points'] ?> puan</span>
            </div>
        </div>
        <div class="question-actions">
            <button class="btn btn-sm btn-secondary" onclick="openEditModal(<?= htmlspecialchars(json_encode($q), ENT_QUOTES) ?>)" title="Düzenle">✏️</button>
            <button class="btn btn-sm btn-success" onclick="openAddToExamModal(<?= $q['id'] ?>)" title="Sınava Ekle">📝</button>
            <button class="btn btn-sm btn-danger" onclick="deleteQuestion(<?= $q['id'] ?>)" title="Sil">🗑️</button>
        </div>
    </div>
    <?php else: ?>
    <div class="question-item">
        <div class="question-num"><?= $globalNum++ ?></div>
        <div class="question-content">
            <div class="question-title"><?= sanitize($q['title']) ?></div>
            <div style="display:flex;gap:8px;margin-top:6px;flex-wrap:wrap;">
                <span class="badge <?= $q['type']==='test' ? 'badge-blue' : 'badge-purple' ?>">
                    <?= $q['type']==='test' ? '🔵 Test' : '📝 Klasik' ?>
                </span>
                <span class="badge badge-green">💯 <?= $q['points'] ?> puan</span>
            </div>
            <?php if ($q['type']==='test' && $q['option_a']): ?>
            <div class="question-options" style="margin-top:10px;">
                <?php
                $opts = ['A'=>$q['option_a'],'B'=>$q['option_b'],'C'=>$q['option_c'],'D'=>$q['option_d'],'E'=>$q['option_e']];
                foreach ($opts as $k=>$v): if (!$v) continue;
                $isCorrect = ($q['correct_opt'] === $k);
                ?>
                <div class="question-option <?= $isCorrect ? 'correct' : '' ?>">
                    <div class="opt-label"><?= $k ?></div>
                    <span><?= sanitize($v) ?></span>
                    <?php if ($isCorrect): ?><span>✓</span><?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php elseif ($q['type']==='klasik' && $q['answer']): ?>
            <div style="margin-top:8px;padding:10px;background:var(--bg);border-radius:8px;font-size:13px;color:var(--text-muted);">
                <strong>Cevap:</strong> <?= sanitize(mb_substr($q['answer'],0,120,'UTF-8')) ?>...
            </div>
            <?php endif; ?>
        </div>
        <div class="question-actions">
            <button class="btn btn-sm btn-secondary" onclick="openEditModal(<?= htmlspecialchars(json_encode($q), ENT_QUOTES) ?>)" title="Düzenle">✏️</button>
            <button class="btn btn-sm btn-success" onclick="openAddToExamModal(<?= $q['id'] ?>)" title="Sınava Ekle">📝</button>
            <button class="btn btn-sm btn-danger" onclick="deleteQuestion(<?= $q['id'] ?>)" title="Sil">🗑️</button>
        </div>
    </div>
    <?php endif; // true_false / diğer ?>
    <?php endforeach; ?>
    </div>
</div>
<?php endforeach; endif; ?>
<?php endif; ?>
</div>

<!-- ADD QUESTION MODAL -->
<div class="modal-overlay" id="modal-add">
    <div class="modal" style="max-width:660px;">
        <div class="modal-header">
            <span class="modal-title">➕ Yeni Soru Ekle</span>
            <button class="modal-close" onclick="closeModal('modal-add')">✕</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Ünite *</label>
                <select id="add-unit" class="form-control">
                    <option value="">Ünite seçin</option>
                    <?php foreach ($allUnits as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= sanitize($u['class_name']) ?> → <?= sanitize($u['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Soru Türü *</label>
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:10px 16px;border:1.5px solid var(--border);border-radius:8px;flex:1;min-width:130px;transition:var(--transition);" id="type-test-label">
                        <input type="radio" name="add-type" value="test" checked onchange="toggleQuestionType('test')"> 🔵 Test
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:10px 16px;border:1.5px solid var(--border);border-radius:8px;flex:1;min-width:130px;transition:var(--transition);" id="type-klasik-label">
                        <input type="radio" name="add-type" value="klasik" onchange="toggleQuestionType('klasik')"> 📝 Klasik
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:10px 16px;border:1.5px solid var(--border);border-radius:8px;flex:1;min-width:130px;transition:var(--transition);" id="type-tf-label">
                        <input type="radio" name="add-type" value="true_false" onchange="toggleQuestionType('true_false')"> ✅ Doğru/Yanlış
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Soru Başlığı / Metni *</label>
                <textarea id="add-title" class="form-control" rows="3" placeholder="Soruyu yazın..."></textarea>
            </div>
            <div id="test-options">
                <div style="display:grid;gap:8px;">
                    <?php foreach(['A','B','C','D','E'] as $opt): ?>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="width:24px;height:24px;border-radius:50%;border:1.5px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;"><?= $opt ?></span>
                        <input type="text" id="add-opt-<?= strtolower($opt) ?>" class="form-control" placeholder="<?= $opt ?> şıkkı<?= $opt==='D'||$opt==='E' ? ' (opsiyonel)' : '' ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="form-group" style="margin-top:14px;">
                    <label class="form-label">Doğru Cevap *</label>
                    <select id="add-correct" class="form-control">
                        <option value="">Doğru şıkkı seçin</option>
                        <option value="A">A</option><option value="B">B</option>
                        <option value="C">C</option><option value="D">D</option><option value="E">E</option>
                    </select>
                </div>
            </div>
            <div id="klasik-options" style="display:none;">
                <div class="form-group">
                    <label class="form-label">Cevap Anahtarı (opsiyonel)</label>
                    <textarea id="add-answer" class="form-control" rows="3" placeholder="Beklenen cevabı yazın..."></textarea>
                </div>
            </div>
            <div id="tf-options" style="display:none;">
                <div class="form-group">
                    <label class="form-label">Doğru Cevap *</label>
                    <div style="display:flex;gap:12px;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:10px 20px;border:1.5px solid var(--border);border-radius:8px;flex:1;" id="tf-dogru-label">
                            <input type="radio" name="add-tf-correct" value="A" checked onchange="highlightTf()"> ✓ Doğru
                        </label>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:10px 20px;border:1.5px solid var(--border);border-radius:8px;flex:1;" id="tf-yanlis-label">
                            <input type="radio" name="add-tf-correct" value="B" onchange="highlightTf()"> ✗ Yanlış
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Puan</label>
                <input type="number" id="add-points" class="form-control" value="10" min="1" max="100">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modal-add')">İptal</button>
            <button class="btn btn-primary" onclick="addQuestion()">💾 Kaydet</button>
        </div>
    </div>
</div>

<!-- EDIT QUESTION MODAL -->
<div class="modal-overlay" id="modal-edit">
    <div class="modal" style="max-width:660px;">
        <div class="modal-header">
            <span class="modal-title">✏️ Soruyu Düzenle</span>
            <button class="modal-close" onclick="closeModal('modal-edit')">✕</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="edit-id">
            <div class="form-group">
                <label class="form-label">Ünite *</label>
                <select id="edit-unit" class="form-control">
                    <?php foreach ($allUnits as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= sanitize($u['class_name']) ?> → <?= sanitize($u['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Soru Türü</label>
                <input type="text" id="edit-type-display" class="form-control" readonly style="background:var(--bg);cursor:default;">
                <input type="hidden" id="edit-type">
            </div>
            <div class="form-group">
                <label class="form-label">Soru Başlığı / Metni *</label>
                <textarea id="edit-title" class="form-control" rows="3"></textarea>
            </div>
            <div id="edit-test-options">
                <?php foreach(['A','B','C','D','E'] as $opt): ?>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                    <span style="width:24px;height:24px;border-radius:50%;border:1.5px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;"><?= $opt ?></span>
                    <input type="text" id="edit-opt-<?= strtolower($opt) ?>" class="form-control" placeholder="<?= $opt ?> şıkkı">
                </div>
                <?php endforeach; ?>
                <div class="form-group" style="margin-top:8px;">
                    <label class="form-label">Doğru Cevap</label>
                    <select id="edit-correct" class="form-control">
                        <option value="">Seçin</option>
                        <option value="A">A</option><option value="B">B</option>
                        <option value="C">C</option><option value="D">D</option><option value="E">E</option>
                    </select>
                </div>
            </div>
            <div id="edit-klasik-options">
                <div class="form-group">
                    <label class="form-label">Cevap Anahtarı</label>
                    <textarea id="edit-answer" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div id="edit-tf-options" style="display:none;">
                <div class="form-group">
                    <label class="form-label">Doğru Cevap</label>
                    <div style="display:flex;gap:12px;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:10px 20px;border:1.5px solid var(--border);border-radius:8px;flex:1;" id="edit-tf-dogru-label">
                            <input type="radio" name="edit-tf-correct" value="A" onchange="highlightEditTf()"> ✓ Doğru
                        </label>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:10px 20px;border:1.5px solid var(--border);border-radius:8px;flex:1;" id="edit-tf-yanlis-label">
                            <input type="radio" name="edit-tf-correct" value="B" onchange="highlightEditTf()"> ✗ Yanlış
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Puan</label>
                <input type="number" id="edit-points" class="form-control" value="10" min="1" max="100">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modal-edit')">İptal</button>
            <button class="btn btn-primary" onclick="updateQuestion()">💾 Güncelle</button>
        </div>
    </div>
</div>

<!-- ADD TO EXAM MODAL -->
<div class="modal-overlay" id="modal-exam">
    <div class="modal" style="max-width:440px;">
        <div class="modal-header">
            <span class="modal-title">📝 Sınava Ekle</span>
            <button class="modal-close" onclick="closeModal('modal-exam')">✕</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="exam-question-id">
            <p style="margin-bottom:16px;color:var(--text-muted);font-size:13px;">Soruyu hangi sınava eklemek istiyorsunuz?</p>
            <?php if (empty($allExams)): ?>
            <div class="alert alert-warning">⚠️ Henüz sınav yok. <a href="exams.php">Sınav oluşturun.</a></div>
            <?php else: ?>
            <div style="display:grid;gap:8px;" id="exam-list">
                <?php foreach ($allExams as $e): ?>
                <button class="btn btn-secondary" style="justify-content:flex-start;gap:10px;" onclick="addToExam(<?= $e['id'] ?>)">
                    📝 <?= sanitize($e['name']) ?>
                </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function toggleQuestionType(type) {
    document.getElementById('test-options').style.display    = type === 'test'       ? '' : 'none';
    document.getElementById('klasik-options').style.display  = type === 'klasik'     ? '' : 'none';
    document.getElementById('tf-options').style.display      = type === 'true_false' ? '' : 'none';
    // highlight selected radio label
    const map = { test:'type-test-label', klasik:'type-klasik-label', true_false:'type-tf-label' };
    ['type-test-label','type-klasik-label','type-tf-label'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.borderColor = (map[type] === id) ? 'var(--primary)' : 'var(--border)';
    });
    if (type === 'true_false') highlightTf();
}
function highlightTf() {
    const val = document.querySelector('input[name="add-tf-correct"]:checked')?.value;
    document.getElementById('tf-dogru-label').style.borderColor  = val === 'A' ? 'var(--primary)' : 'var(--border)';
    document.getElementById('tf-yanlis-label').style.borderColor = val === 'B' ? 'var(--primary)' : 'var(--border)';
}
function highlightEditTf() {
    const val = document.querySelector('input[name="edit-tf-correct"]:checked')?.value;
    document.getElementById('edit-tf-dogru-label').style.borderColor  = val === 'A' ? 'var(--primary)' : 'var(--border)';
    document.getElementById('edit-tf-yanlis-label').style.borderColor = val === 'B' ? 'var(--primary)' : 'var(--border)';
}
function setUnitAndOpenModal(unitId) {
    openAddModal();
    setTimeout(()=>{ document.getElementById('add-unit').value = unitId; }, 100);
}
function openAddModal() {
    <?php if (empty($allUnits)): ?>
    showToast('Önce bir ünite oluşturun!', 'warning');
    setTimeout(()=>window.location='units.php',1200);
    return;
    <?php endif; ?>
    // Reset to 'test' type
    document.querySelector('input[name="add-type"][value="test"]').checked = true;
    toggleQuestionType('test');
    // Seçili üniteyi otomatik seç
    <?php if ($unitId): ?>
    setTimeout(()=>{ document.getElementById('add-unit').value = '<?= $unitId ?>'; }, 50);
    <?php endif; ?>
    openModal('modal-add');
}
function openEditModal(q) {
    document.getElementById('edit-id').value      = q.id;
    document.getElementById('edit-unit').value    = q.unit_id;
    document.getElementById('edit-type').value    = q.type;
    const typeLabels = { test:'🔵 Test Sorusu', klasik:'📝 Klasik Soru', true_false:'✅ Doğru/Yanlış' };
    document.getElementById('edit-type-display').value = typeLabels[q.type] || q.type;
    document.getElementById('edit-title').value   = q.title;
    document.getElementById('edit-points').value  = q.points;

    document.getElementById('edit-test-options').style.display   = 'none';
    document.getElementById('edit-klasik-options').style.display = 'none';
    document.getElementById('edit-tf-options').style.display     = 'none';

    if (q.type === 'test') {
        document.getElementById('edit-test-options').style.display = '';
        ['a','b','c','d','e'].forEach(k => {
            document.getElementById('edit-opt-'+k).value = q['option_'+k] || '';
        });
        document.getElementById('edit-correct').value = q.correct_opt || '';
    } else if (q.type === 'true_false') {
        document.getElementById('edit-tf-options').style.display = '';
        const val = q.correct_opt === 'B' ? 'B' : 'A';
        document.querySelectorAll('input[name="edit-tf-correct"]').forEach(r => { r.checked = (r.value === val); });
        highlightEditTf();
    } else {
        document.getElementById('edit-klasik-options').style.display = '';
        document.getElementById('edit-answer').value = q.answer || '';
    }
    openModal('modal-edit');
}
function openAddToExamModal(qid) {
    document.getElementById('exam-question-id').value = qid;
    openModal('modal-exam');
}
async function addQuestion() {
    const unitId  = document.getElementById('add-unit').value;
    const type    = document.querySelector('input[name="add-type"]:checked').value;
    const title   = document.getElementById('add-title').value.trim();
    const points  = document.getElementById('add-points').value;
    if (!unitId) { showToast('Ünite seçin','error'); return; }
    if (!title)  { showToast('Soru metni boş olamaz','error'); return; }
    const data = { action:'add', unit_id:unitId, type, title, points };
    if (type === 'test') {
        data.option_a = document.getElementById('add-opt-a').value;
        data.option_b = document.getElementById('add-opt-b').value;
        data.option_c = document.getElementById('add-opt-c').value;
        data.option_d = document.getElementById('add-opt-d').value;
        data.option_e = document.getElementById('add-opt-e').value;
        data.correct_opt = document.getElementById('add-correct').value;
        if (!data.option_a || !data.option_b || !data.option_c) { showToast('En az A,B,C şıkkını doldurun','error'); return; }
        if (!data.correct_opt) { showToast('Doğru cevabı seçin','error'); return; }
    } else if (type === 'true_false') {
        const tfChecked = document.querySelector('input[name="add-tf-correct"]:checked');
        data.correct_opt = tfChecked ? tfChecked.value : 'A';
    } else {
        data.answer = document.getElementById('add-answer').value;
    }
    const r = await apiCall('api/question.php', data);
    if (r.error) showToast(r.error,'error');
    else { showToast('Soru eklendi!','success'); setTimeout(()=>location.reload(),800); }
}
async function updateQuestion() {
    const id     = document.getElementById('edit-id').value;
    const unitId = document.getElementById('edit-unit').value;
    const type   = document.getElementById('edit-type').value;
    const title  = document.getElementById('edit-title').value.trim();
    const points = document.getElementById('edit-points').value;
    if (!title) { showToast('Soru metni boş olamaz','error'); return; }
    const data = { action:'update', id, unit_id:unitId, type, title, points };
    if (type === 'test') {
        data.option_a    = document.getElementById('edit-opt-a').value;
        data.option_b    = document.getElementById('edit-opt-b').value;
        data.option_c    = document.getElementById('edit-opt-c').value;
        data.option_d    = document.getElementById('edit-opt-d').value;
        data.option_e    = document.getElementById('edit-opt-e').value;
        data.correct_opt = document.getElementById('edit-correct').value;
    } else if (type === 'true_false') {
        const tfChecked = document.querySelector('input[name="edit-tf-correct"]:checked');
        data.correct_opt = tfChecked ? tfChecked.value : 'A';
    } else {
        data.answer = document.getElementById('edit-answer').value;
    }
    const r = await apiCall('api/question.php', data);
    if (r.error) showToast(r.error,'error');
    else { showToast('Güncellendi!','success'); setTimeout(()=>location.reload(),800); }
}
function deleteQuestion(id) {
    confirmDelete('Bu soruyu silmek istediğinize emin misiniz?', async ()=>{
        const r = await apiCall('api/question.php', { action:'delete', id });
        if (r.error) showToast(r.error,'error');
        else { showToast('Soru silindi','success'); setTimeout(()=>location.reload(),800); }
    });
}
async function addToExam(examId) {
    const qid = document.getElementById('exam-question-id').value;
    const r = await apiCall('api/exam.php', { action:'add_question', exam_id:examId, question_id:qid });
    if (r.error) showToast(r.error,'error');
    else { closeModal('modal-exam'); showToast('Sınava eklendi!','success'); }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
