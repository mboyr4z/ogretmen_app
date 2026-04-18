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
            <button class="btn btn-danger btn-sm" onclick="openExportModal(<?= $examId ?>)">📄 PDF / Baskı</button>
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
                <?php if ($q['type']==='true_false'): ?>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-top:2px;">
                    <span class="question-title" style="margin:0;"><?= sanitize($q['title']) ?></span>
                    <span style="font-size:13px;font-weight:700;color:var(--text-muted);white-space:nowrap;">( D &nbsp;/&nbsp; Y )</span>
                    <span style="font-size:12px;padding:2px 8px;border-radius:4px;background:<?= $q['correct_opt']==='A'?'#d1fae5':'#fee2e2' ?>;color:<?= $q['correct_opt']==='A'?'#065f46':'#991b1b' ?>;font-weight:700;">
                        Cevap: <?= $q['correct_opt']==='A' ? 'Doğru' : 'Yanlış' ?>
                    </span>
                </div>
                <div style="display:flex;gap:6px;margin-top:5px;flex-wrap:wrap;">
                    <span class="badge badge-orange">✅ Doğru/Yanlış</span>
                    <span class="badge badge-green">💯 <?= $q['points'] ?> puan</span>
                    <span class="badge badge-yellow">📚 <?= sanitize($q['unit_name']) ?></span>
                </div>
                <?php else: ?>
                <div class="question-title"><?= sanitize($q['title']) ?></div>
                <div style="display:flex;gap:6px;margin-top:6px;flex-wrap:wrap;">
                    <span class="badge <?= $q['type']==='test' ? 'badge-blue' : 'badge-purple' ?>">
                        <?= $q['type']==='test' ? '🔵 Test' : '📝 Klasik' ?>
                    </span>
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
                <?php endif; // true_false ?>
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
        <button class="btn btn-sm btn-danger" onclick="event.stopPropagation();openExportModal(<?= $e['id'] ?>)">📄 PDF</button>
        <a href="exports/exam_word.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-primary" onclick="event.stopPropagation()">📘 Word</a>
    </div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
<?php endif; ?>

</div>

<!-- ADD MODAL -->
<style>
.exam-meta-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; }

/* Toggle switch */
.toggle-row { display:flex; align-items:center; justify-content:space-between; padding:12px 0; border-bottom:1px solid var(--border); }
.toggle-row:last-child { border-bottom:none; }
.toggle-label { font-size:13px; font-weight:600; }
.toggle-label small { display:block; font-weight:400; color:var(--text-muted); font-size:11px; margin-top:1px; }
.toggle-switch { position:relative; width:44px; height:24px; flex-shrink:0; }
.toggle-switch input { opacity:0; width:0; height:0; }
.toggle-slider { position:absolute; inset:0; background:#cbd5e1; border-radius:24px; cursor:pointer; transition:.2s; }
.toggle-slider:before { content:''; position:absolute; width:18px; height:18px; left:3px; top:3px; background:#fff; border-radius:50%; transition:.2s; }
.toggle-switch input:checked + .toggle-slider { background:var(--primary,#2563eb); }
.toggle-switch input:checked + .toggle-slider:before { transform:translateX(20px); }

/* Drag list */
.section-list { display:flex; flex-direction:column; gap:6px; margin-top:6px; }
.section-item { display:flex; align-items:center; gap:10px; padding:10px 12px; background:var(--bg,#f8fafc); border:1.5px solid var(--border); border-radius:8px; cursor:grab; user-select:none; transition:.15s; }
.section-item:active { cursor:grabbing; }
.section-item.drag-over { border-color:var(--primary,#2563eb); background:#eff6ff; }
.section-item.dragging { opacity:.4; }
.drag-handle { color:var(--text-muted); font-size:16px; line-height:1; flex-shrink:0; }
.section-item-icon { font-size:18px; }
.section-item-label { font-size:13px; font-weight:600; flex:1; }
.section-item-sub { font-size:11px; color:var(--text-muted); }

/* Boyut seçici butonlar */
.size-btn-group { display:flex; gap:6px; }
.size-btn { flex:1; padding:8px 4px; border:1.5px solid var(--border); border-radius:8px; background:var(--bg); cursor:pointer; font-size:12px; font-weight:600; text-align:center; transition:.15s; color:var(--text); }
.size-btn:hover { border-color:var(--primary); }
.size-btn.active { border-color:var(--primary); background:#eff6ff; color:var(--primary); }
.size-btn .size-icon { display:block; font-size:18px; margin-bottom:2px; line-height:1; }
.size-btn .size-label { display:block; font-size:11px; color:var(--text-muted); margin-top:1px; }
.exp-section-label { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--text-muted); margin-bottom:8px; }
</style>

<div class="modal-overlay" id="modal-add">
    <div class="modal" style="max-width:520px;">
        <div class="modal-header">
            <span class="modal-title">➕ Yeni Sınav Ekle</span>
            <button class="modal-close" onclick="closeModal('modal-add')">✕</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Sınav Adı / Ders *</label>
                <input type="text" id="add-name" class="form-control" placeholder="Örn: Din Kültürü ve Ahlak Bilgisi">
            </div>
            <div class="form-group">
                <label class="form-label">Okul Adı (opsiyonel)</label>
                <input type="text" id="add-school" class="form-control" placeholder="Örn: Atatürk Anadolu Lisesi">
            </div>
            <div class="exam-meta-grid">
                <div class="form-group" style="margin:0">
                    <label class="form-label">Eğitim-Öğretim Yılı</label>
                    <input type="text" id="add-acyear" class="form-control" placeholder="Örn: 2024-2025">
                </div>
                <div class="form-group" style="margin:0">
                    <label class="form-label">Dönem</label>
                    <select id="add-period" class="form-control">
                        <option value="1">1. Dönem</option>
                        <option value="2">2. Dönem</option>
                    </select>
                </div>
                <div class="form-group" style="margin:0">
                    <label class="form-label">Yazılı No</label>
                    <select id="add-examnum" class="form-control">
                        <option value="1">1. Yazılı</option>
                        <option value="2">2. Yazılı</option>
                        <option value="3">3. Yazılı</option>
                    </select>
                </div>
                <div class="form-group" style="margin:0">
                    <label class="form-label">Sınıf (opsiyonel)</label>
                    <select id="add-class" class="form-control">
                        <option value="">Seçin</option>
                        <?php foreach ($allClasses as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= sanitize($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-top:10px;">
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
    <div class="modal" style="max-width:520px;">
        <div class="modal-header">
            <span class="modal-title">✏️ Sınavı Düzenle</span>
            <button class="modal-close" onclick="closeModal('modal-edit')">✕</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="edit-id">
            <div class="form-group">
                <label class="form-label">Sınav Adı / Ders *</label>
                <input type="text" id="edit-name" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Okul Adı</label>
                <input type="text" id="edit-school" class="form-control">
            </div>
            <div class="exam-meta-grid">
                <div class="form-group" style="margin:0"><label class="form-label">Eğitim-Öğretim Yılı</label><input type="text" id="edit-acyear" class="form-control"></div>
                <div class="form-group" style="margin:0"><label class="form-label">Dönem</label><select id="edit-period" class="form-control"><option value="1">1. Dönem</option><option value="2">2. Dönem</option></select></div>
                <div class="form-group" style="margin:0"><label class="form-label">Yazılı No</label><select id="edit-examnum" class="form-control"><option value="1">1. Yazılı</option><option value="2">2. Yazılı</option><option value="3">3. Yazılı</option></select></div>
                <div class="form-group" style="margin:0"><label class="form-label">Sınıf</label><select id="edit-class" class="form-control"><option value="">Seçin</option><?php foreach ($allClasses as $c): ?><option value="<?= $c['id'] ?>"><?= sanitize($c['name']) ?></option><?php endforeach; ?></select></div>
            </div>
            <div class="form-group" style="margin-top:10px;">
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

<!-- EXPORT OPTIONS MODAL -->
<div class="modal-overlay" id="modal-export">
    <div class="modal" style="max-width:460px;">
        <div class="modal-header">
            <span class="modal-title">📄 PDF / Baskı Seçenekleri</span>
            <button class="modal-close" onclick="closeModal('modal-export')">✕</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="export-exam-id">

            <!-- ÇERÇEVE SEÇENEKLERİ -->
            <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);margin-bottom:8px;">Çerçeve Seçenekleri</p>
            <div style="background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:4px 16px;margin-bottom:20px;">
                <div class="toggle-row">
                    <div class="toggle-label">
                        🔵 Test soruları çerçeveli
                        <small>Her test sorusu kutucuk içinde görünür</small>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="exp-test-frame">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="toggle-row">
                    <div class="toggle-label">
                        📝 Klasik sorular çerçeveli
                        <small>Her klasik soru kutucuk içinde görünür</small>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="exp-klasik-frame">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            <!-- BOYUT AYARLARI -->
            <p class="exp-section-label" style="margin-top:20px;">Boyut Ayarları</p>
            <div style="background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:14px 16px;margin-bottom:20px;display:grid;gap:14px;">

                <!-- Yazı boyutu -->
                <div>
                    <div style="font-size:12px;font-weight:600;margin-bottom:6px;">Yazı Boyutu</div>
                    <div class="size-btn-group" id="exp-font-group">
                        <button class="size-btn" data-val="9" onclick="setSizeBtn('exp-font-group',this)">
                            <span class="size-icon" style="font-size:13px;">A</span>
                            <span class="size-label">Küçük</span>
                        </button>
                        <button class="size-btn active" data-val="11" onclick="setSizeBtn('exp-font-group',this)">
                            <span class="size-icon">A</span>
                            <span class="size-label">Orta</span>
                        </button>
                        <button class="size-btn" data-val="13" onclick="setSizeBtn('exp-font-group',this)">
                            <span class="size-icon" style="font-size:22px;">A</span>
                            <span class="size-label">Büyük</span>
                        </button>
                    </div>
                </div>

                <!-- Test sütun sayısı -->
                <div>
                    <div style="font-size:12px;font-weight:600;margin-bottom:6px;">🔵 Test — Yan Yana Soru Sayısı</div>
                    <div class="size-btn-group" id="exp-test-cols-group">
                        <button class="size-btn active" data-val="1" onclick="setSizeBtn('exp-test-cols-group',this)">
                            <span class="size-icon">▮</span><span class="size-label">1</span>
                        </button>
                        <button class="size-btn" data-val="2" onclick="setSizeBtn('exp-test-cols-group',this)">
                            <span class="size-icon">▮▮</span><span class="size-label">2</span>
                        </button>
                        <button class="size-btn" data-val="3" onclick="setSizeBtn('exp-test-cols-group',this)">
                            <span class="size-icon">▮▮▮</span><span class="size-label">3</span>
                        </button>
                    </div>
                </div>

                <!-- Klasik sütun sayısı -->
                <div>
                    <div style="font-size:12px;font-weight:600;margin-bottom:6px;">📝 Klasik — Yan Yana Soru Sayısı</div>
                    <div class="size-btn-group" id="exp-klasik-cols-group">
                        <button class="size-btn active" data-val="1" onclick="setSizeBtn('exp-klasik-cols-group',this)">
                            <span class="size-icon">▮</span><span class="size-label">1</span>
                        </button>
                        <button class="size-btn" data-val="2" onclick="setSizeBtn('exp-klasik-cols-group',this)">
                            <span class="size-icon">▮▮</span><span class="size-label">2</span>
                        </button>
                    </div>
                </div>

                <!-- D/Y sütun sayısı -->
                <div>
                    <div style="font-size:12px;font-weight:600;margin-bottom:6px;">✅ Doğru/Yanlış — Yan Yana Soru Sayısı</div>
                    <div class="size-btn-group" id="exp-tf-cols-group">
                        <button class="size-btn active" data-val="1" onclick="setSizeBtn('exp-tf-cols-group',this)">
                            <span class="size-icon">▮</span><span class="size-label">1</span>
                        </button>
                        <button class="size-btn" data-val="2" onclick="setSizeBtn('exp-tf-cols-group',this)">
                            <span class="size-icon">▮▮</span><span class="size-label">2</span>
                        </button>
                        <button class="size-btn" data-val="3" onclick="setSizeBtn('exp-tf-cols-group',this)">
                            <span class="size-icon">▮▮▮</span><span class="size-label">3</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- BÖLÜM SIRASI -->
            <p class="exp-section-label">Bölüm Sırası <span style="font-weight:400;text-transform:none;font-size:11px;">— sürükleyerek değiştirin</span></p>
            <div class="section-list" id="section-sort-list">
                <div class="section-item" data-section="test" draggable="true">
                    <span class="drag-handle">⠿</span>
                    <span class="section-item-icon">🔵</span>
                    <div class="section-item-label">Test Soruları <span class="section-item-sub">A,B,C,D şıklı</span></div>
                </div>
                <div class="section-item" data-section="tf" draggable="true">
                    <span class="drag-handle">⠿</span>
                    <span class="section-item-icon">✅</span>
                    <div class="section-item-label">Doğru / Yanlış <span class="section-item-sub">□ Doğru □ Yanlış</span></div>
                </div>
                <div class="section-item" data-section="klasik" draggable="true">
                    <span class="drag-handle">⠿</span>
                    <span class="section-item-icon">📝</span>
                    <div class="section-item-label">Klasik Sorular <span class="section-item-sub">Yazılı cevap satırları</span></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modal-export')">İptal</button>
            <button class="btn btn-primary" onclick="openExportWord()">📘 Word Önizle</button>
            <button class="btn btn-danger" onclick="openExportPdf()">📄 PDF Önizle</button>
        </div>
    </div>
</div>

<script>
/* ── SINAV EKLE / DÜZENLE ─────────────────────────────── */
function openAddModal() { openModal('modal-add'); }
function openEditModal(e) {
    document.getElementById('edit-id').value       = e.id;
    document.getElementById('edit-name').value     = e.name;
    document.getElementById('edit-class').value    = e.class_id || '';
    document.getElementById('edit-desc').value     = e.description || '';
    document.getElementById('edit-duration').value = e.duration || '';
    document.getElementById('edit-school').value   = e.school_name || '';
    document.getElementById('edit-acyear').value   = e.academic_year || '';
    document.getElementById('edit-period').value   = e.period || 1;
    document.getElementById('edit-examnum').value  = e.exam_number || 1;
    openModal('modal-edit');
}
async function addExam() {
    const name    = document.getElementById('add-name').value.trim();
    const classId = document.getElementById('add-class').value;
    const dur     = document.getElementById('add-duration').value;
    const school  = document.getElementById('add-school').value;
    const acyear  = document.getElementById('add-acyear').value;
    const period  = document.getElementById('add-period').value;
    const examnum = document.getElementById('add-examnum').value;
    if (!name) { showToast('Sınav adı / ders boş olamaz','error'); return; }
    const r = await apiCall('api/exam.php', { action:'add', name, class_id:classId, duration:dur,
        school_name:school, academic_year:acyear, period, exam_number:examnum });
    if (r.error) showToast(r.error,'error');
    else { showToast('Sınav eklendi!','success'); setTimeout(()=>location.reload(),800); }
}
async function updateExam() {
    const id      = document.getElementById('edit-id').value;
    const name    = document.getElementById('edit-name').value.trim();
    const classId = document.getElementById('edit-class').value;
    const desc    = document.getElementById('edit-desc').value;
    const dur     = document.getElementById('edit-duration').value;
    const school  = document.getElementById('edit-school').value;
    const acyear  = document.getElementById('edit-acyear').value;
    const period  = document.getElementById('edit-period').value;
    const examnum = document.getElementById('edit-examnum').value;
    if (!name) { showToast('Sınav adı boş olamaz','error'); return; }
    const r = await apiCall('api/exam.php', { action:'update', id, name, class_id:classId, description:desc, duration:dur,
        school_name:school, academic_year:acyear, period, exam_number:examnum });
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

/* ── EXPORT MODAL ─────────────────────────────────────── */
function openExportModal(examId) {
    document.getElementById('export-exam-id').value = examId;
    initSectionDrag();
    openModal('modal-export');
}
function setSizeBtn(groupId, btn) {
    document.querySelectorAll('#' + groupId + ' .size-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}
function getSizeBtnVal(groupId) {
    return document.querySelector('#' + groupId + ' .size-btn.active')?.dataset.val ?? null;
}
function buildExportUrl(base) {
    const examId      = document.getElementById('export-exam-id').value;
    const testFrame   = document.getElementById('exp-test-frame').checked ? 1 : 0;
    const klasikFrame = document.getElementById('exp-klasik-frame').checked ? 1 : 0;
    const order = [...document.querySelectorAll('#section-sort-list .section-item')]
                    .map(el => el.dataset.section).join(',');
    const fs          = getSizeBtnVal('exp-font-group')        ?? 11;
    const testCols    = getSizeBtnVal('exp-test-cols-group')   ?? 1;
    const klasikCols  = getSizeBtnVal('exp-klasik-cols-group') ?? 1;
    const tfCols      = getSizeBtnVal('exp-tf-cols-group')     ?? 1;
    return `exports/${base}?id=${examId}&tf=${testFrame}&kf=${klasikFrame}&order=${order}&fs=${fs}&test_cols=${testCols}&klasik_cols=${klasikCols}&tf_cols=${tfCols}`;
}
function openExportPdf() {
    window.open(buildExportUrl('exam_pdf.php'), '_blank');
    closeModal('modal-export');
}
function openExportWord() {
    window.open(buildExportUrl('exam_word.php'), '_blank');
    closeModal('modal-export');
}

/* ── DRAG & DROP (bölüm sırası) ──────────────────────── */
function initSectionDrag() {
    const list  = document.getElementById('section-sort-list');
    let dragEl  = null;

    list.querySelectorAll('.section-item').forEach(item => {
        item.addEventListener('dragstart', e => {
            dragEl = item;
            setTimeout(() => item.classList.add('dragging'), 0);
            e.dataTransfer.effectAllowed = 'move';
        });
        item.addEventListener('dragend', () => {
            dragEl = null;
            item.classList.remove('dragging');
            list.querySelectorAll('.section-item').forEach(i => i.classList.remove('drag-over'));
        });
        item.addEventListener('dragover', e => {
            e.preventDefault();
            if (item === dragEl) return;
            list.querySelectorAll('.section-item').forEach(i => i.classList.remove('drag-over'));
            item.classList.add('drag-over');
            const rect    = item.getBoundingClientRect();
            const midY    = rect.top + rect.height / 2;
            const before  = e.clientY < midY;
            if (before) list.insertBefore(dragEl, item);
            else        list.insertBefore(dragEl, item.nextSibling);
        });
        item.addEventListener('dragleave', () => item.classList.remove('drag-over'));
        item.addEventListener('drop', e => { e.preventDefault(); item.classList.remove('drag-over'); });
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
