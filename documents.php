<?php
$pageTitle = 'Dökümanlar';
require_once __DIR__ . '/includes/header.php';

$db  = getDB();
$uid = $_SESSION['user_id'];

// Tüm sınıfları ve altındaki üniteleri getir
$classStmt = $db->prepare("SELECT * FROM classes WHERE user_id=? ORDER BY name ASC");
$classStmt->execute([$uid]);
$classes = $classStmt->fetchAll();

// Sınıf → üniteler haritası
$unitsByClass = [];
if ($classes) {
    $classIds = array_column($classes, 'id');
    $placeholders = implode(',', array_fill(0, count($classIds), '?'));
    $unitStmt = $db->prepare("
        SELECT * FROM units WHERE user_id=? AND class_id IN ($placeholders)
        ORDER BY class_id ASC, order_num ASC, name ASC
    ");
    $unitStmt->execute(array_merge([$uid], $classIds));
    foreach ($unitStmt->fetchAll() as $u) {
        $unitsByClass[$u['class_id']][] = $u;
    }
}

// Seçili ünite
$selectedUnitId = (int)($_GET['unit_id'] ?? 0);

// Seçili ünitenin hangi sınıfa ait olduğunu bul (sidebar için expand)
$selectedClassId = 0;
if ($selectedUnitId) {
    foreach ($unitsByClass as $cid => $units) {
        foreach ($units as $u) {
            if ($u['id'] === $selectedUnitId) { $selectedClassId = $cid; break 2; }
        }
    }
}

// Seçili ünitenin dökümanları
$docs = [];
$selUnit = null;
if ($selectedUnitId) {
    $docStmt = $db->prepare("
        SELECT * FROM unit_documents WHERE unit_id=? AND user_id=?
        ORDER BY order_num ASC
    ");
    $docStmt->execute([$selectedUnitId, $uid]);
    $docs = $docStmt->fetchAll();
    // Ünite bilgisi
    foreach ($unitsByClass as $units) {
        foreach ($units as $u) {
            if ($u['id'] === $selectedUnitId) { $selUnit = $u; break 2; }
        }
    }
}

// Sınıf adını bul
$selClassName = '';
foreach ($classes as $cls) {
    if ($cls['id'] === $selectedClassId) { $selClassName = $cls['name']; break; }
}

// Toplam ünite sayısı
$totalUnits = array_sum(array_map('count', $unitsByClass));
?>

<style>
/* ============================================================
   DÖKÜMAN SAYFASI STİLLERİ
   ============================================================ */

.doc-layout   { display:flex; gap:24px; align-items:flex-start; }

/* ---- Sol panel: Sınıf / Ünite ağacı ---- */
.doc-sidebar  { width:240px; flex-shrink:0; position:sticky; top:24px; max-height:calc(100vh - 80px); overflow-y:auto; }

.tree-card    { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; }
.tree-header  { padding:11px 14px; border-bottom:1px solid var(--border); }
.tree-header-text { font-size:11px; font-weight:700; letter-spacing:.5px; text-transform:uppercase; color:var(--text-muted); }

/* Sınıf satırı */
.tree-class {
    border-bottom: 1px solid var(--border);
}
.tree-class:last-child { border-bottom:none; }

.tree-class-btn {
    display:flex; align-items:center; gap:8px;
    width:100%; padding:10px 14px;
    border:none; background:transparent; cursor:pointer;
    font-size:13px; font-weight:600; color:var(--text);
    font-family:inherit; text-align:left;
    transition:background .15s;
}
.tree-class-btn:hover { background:var(--bg); }
.tree-class-btn.open  { color:var(--primary); }

.tree-chevron {
    margin-left:auto; font-size:10px; color:var(--text-muted);
    transition:transform .2s; display:inline-block;
}
.tree-class-btn.open .tree-chevron { transform:rotate(90deg); }

.unit-count-badge {
    font-size:10px; background:var(--bg); border:1px solid var(--border);
    color:var(--text-muted); padding:1px 6px; border-radius:10px;
}

/* Üniteler */
.tree-units { display:none; padding:4px 0; background:#fafbfc; border-top:1px solid var(--border); }
.tree-units.open { display:block; }

.tree-unit-btn {
    display:flex; align-items:center; gap:8px;
    width:100%; padding:8px 14px 8px 30px;
    border:none; background:transparent; cursor:pointer;
    font-size:13px; font-weight:500; color:var(--text-muted);
    font-family:inherit; text-align:left;
    transition:all .15s; text-decoration:none;
}
.tree-unit-btn:hover  { background:var(--primary-light); color:var(--primary); }
.tree-unit-btn.active { background:var(--primary-light); color:var(--primary); font-weight:700; }

.unit-doc-badge {
    margin-left:auto; font-size:10px; font-weight:700;
    background:var(--primary-light); color:var(--primary);
    padding:1px 6px; border-radius:10px;
    display:none;
}
.unit-doc-badge.visible { display:inline-block; }

.tree-empty {
    padding:8px 14px 8px 30px;
    font-size:12px; color:var(--text-muted); font-style:italic;
}

/* ---- Sağ panel: Dökümanlar ---- */
.doc-main { flex:1; min-width:0; }

/* Başlık kutusu */
.doc-main-header {
    display:flex; align-items:center; gap:12px;
    margin-bottom:20px;
}
.doc-breadcrumb {
    font-size:12px; color:var(--text-muted);
    display:flex; align-items:center; gap:6px;
}
.doc-breadcrumb .sep { opacity:.5; }
.doc-unit-title { font-size:18px; font-weight:800; color:var(--text); margin:2px 0 0; }

/* Upload */
.upload-zone {
    border:2px dashed var(--border); border-radius:var(--radius);
    padding:28px 24px; text-align:center; cursor:pointer;
    background:var(--surface); margin-bottom:20px; transition:all .2s;
}
.upload-zone:hover, .upload-zone.drag-over {
    border-color:var(--primary); background:var(--primary-light);
}
.upload-zone .upload-icon { font-size:32px; margin-bottom:8px; opacity:.6; }
.upload-zone p { color:var(--text-muted); font-size:13px; margin:3px 0; }
.upload-types { display:flex; gap:8px; justify-content:center; margin-top:10px; flex-wrap:wrap; }
.type-chip { padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }

/* Progress */
.upload-progress {
    display:none; background:var(--surface); border:1px solid var(--border);
    border-radius:10px; padding:12px 16px;
    align-items:center; gap:12px; margin-bottom:8px;
}
.upload-progress.visible { display:flex; }
.progress-bar-wrap { flex:1; height:6px; background:var(--border); border-radius:3px; overflow:hidden; }
.progress-bar-fill { height:100%; background:var(--primary); border-radius:3px; transition:width .2s; width:0%; }

/* Döküman listesi */
.doc-list { display:flex; flex-direction:column; }

.doc-item {
    display:flex; align-items:center; gap:12px;
    padding:13px 16px;
    background:var(--surface);
    border:1px solid var(--border); border-top:none;
    transition:background .15s;
}
.doc-item:first-child { border-radius:var(--radius) var(--radius) 0 0; border-top:1px solid var(--border); }
.doc-item:last-child  { border-radius:0 0 var(--radius) var(--radius); }
.doc-item:only-child  { border-radius:var(--radius); border-top:1px solid var(--border); }
.doc-item:hover       { background:#f8fafc; }

/* Sıra numarası */
.doc-order {
    width:26px; height:26px; border-radius:50%;
    background:var(--bg); border:1px solid var(--border);
    display:flex; align-items:center; justify-content:center;
    font-size:11px; font-weight:800; color:var(--text-muted);
    flex-shrink:0;
}

/* Dosya ikonu */
.doc-icon {
    width:34px; height:34px; border-radius:8px;
    display:flex; align-items:center; justify-content:center;
    font-size:17px; flex-shrink:0;
}
.doc-icon.pdf   { background:#fee2e2; }
.doc-icon.image { background:#d1fae5; }
.doc-icon.pptx  { background:#fef3c7; }
.doc-icon.other { background:#f1f5f9; }

/* İsim */
.doc-name-wrap { flex:1; min-width:0; }
.doc-name-display {
    font-size:14px; font-weight:600; color:var(--text);
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
    cursor:pointer; display:flex; align-items:center; gap:6px;
}
.doc-name-display:hover .edit-hint { opacity:1; }
.edit-hint { font-size:11px; color:var(--text-muted); opacity:0; transition:opacity .2s; flex-shrink:0; }
.doc-name-input {
    display:none; width:100%;
    border:1px solid var(--primary); border-radius:6px;
    padding:4px 8px; font-size:14px; font-weight:600;
    font-family:inherit; outline:none;
}
.doc-name-input.visible { display:block; }
.doc-meta { font-size:11px; color:var(--text-muted); margin-top:2px; }

/* Sıralama butonları */
.order-btns { display:flex; flex-direction:column; gap:2px; flex-shrink:0; }
.order-btn {
    width:28px; height:26px; border-radius:6px;
    border:1px solid var(--border); background:var(--surface);
    cursor:pointer; display:flex; align-items:center; justify-content:center;
    font-size:12px; transition:all .15s; color:var(--text-muted);
}
.order-btn:hover:not(:disabled) { background:var(--primary); color:#fff; border-color:var(--primary); }
.order-btn:disabled { opacity:.25; cursor:not-allowed; }

/* Badge */
.file-type-badge { font-size:10px; font-weight:700; padding:2px 6px; border-radius:4px; flex-shrink:0; text-transform:uppercase; }
.ftb-pdf   { background:#fee2e2; color:#dc2626; }
.ftb-image { background:#d1fae5; color:#059669; }
.ftb-pptx  { background:#fef3c7; color:#d97706; }
.ftb-other { background:#f1f5f9; color:#64748b; }

/* Aksiyonlar */
.doc-open-btn {
    width:30px; height:30px; border-radius:7px;
    border:1px solid var(--border); background:transparent;
    display:flex; align-items:center; justify-content:center;
    color:var(--text-muted); font-size:14px; text-decoration:none;
    transition:all .15s; flex-shrink:0;
}
.doc-open-btn:hover { background:var(--primary-light); color:var(--primary); border-color:var(--primary); }
.doc-del-btn {
    width:30px; height:30px; border-radius:7px;
    border:1px solid var(--border); background:transparent; cursor:pointer;
    display:flex; align-items:center; justify-content:center;
    color:var(--text-muted); font-size:14px;
    transition:all .15s; flex-shrink:0;
}
.doc-del-btn:hover { background:#fee2e2; color:#dc2626; border-color:#fca5a5; }

/* Boş durumlar */
.doc-empty {
    border:1px dashed var(--border); border-radius:var(--radius);
    padding:48px 20px; text-align:center;
}
</style>

<div class="topbar">
    <h2>📂 Dökümanlar</h2>
    <?php if ($selUnit): ?>
    <label for="fileInput" class="btn btn-primary btn-sm" style="cursor:pointer;">
        ＋ Dosya Yükle
    </label>
    <?php endif; ?>
</div>

<div class="content-area">

<?php if (empty($classes)): ?>
    <div class="empty-state">
        <div class="icon">🏫</div>
        <h3>Henüz sınıf yok</h3>
        <p>Döküman yüklemek için önce <a href="<?= SITE_URL ?>/classes.php">sınıf</a> ve <a href="<?= SITE_URL ?>/units.php">ünite</a> oluşturun.</p>
    </div>
<?php elseif ($totalUnits === 0): ?>
    <div class="empty-state">
        <div class="icon">📚</div>
        <h3>Henüz ünite yok</h3>
        <p>Döküman eklemek için önce <a href="<?= SITE_URL ?>/units.php">ünite oluşturun</a>.</p>
    </div>
<?php else: ?>

<div class="doc-layout">

    <!-- ========================
         SOL: Sınıf/Ünite Ağacı
         ======================== -->
    <div class="doc-sidebar">
        <div class="tree-card">
            <div class="tree-header">
                <div class="tree-header-text">📚 Sınıf &amp; Üniteler</div>
            </div>

            <?php foreach ($classes as $cls):
                $units = $unitsByClass[$cls['id']] ?? [];
                if (empty($units)) continue; // ünite yoksa sınıfı gösterme
                $isOpen = ($cls['id'] === $selectedClassId);
            ?>
            <div class="tree-class" id="tc-<?= $cls['id'] ?>">
                <button class="tree-class-btn <?= $isOpen ? 'open' : '' ?>"
                        onclick="toggleClass(<?= $cls['id'] ?>)">
                    🏫
                    <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        <?= sanitize($cls['name']) ?>
                    </span>
                    <span class="unit-count-badge"><?= count($units) ?></span>
                    <span class="tree-chevron">▶</span>
                </button>

                <div class="tree-units <?= $isOpen ? 'open' : '' ?>" id="tu-<?= $cls['id'] ?>">
                    <?php foreach ($units as $unit):
                        $isActive = ($unit['id'] === $selectedUnitId);
                    ?>
                    <a href="?unit_id=<?= $unit['id'] ?>"
                       class="tree-unit-btn <?= $isActive ? 'active' : '' ?>"
                       id="ubtn-<?= $unit['id'] ?>">
                        <span>📄</span>
                        <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;">
                            <?= sanitize($unit['name']) ?>
                        </span>
                        <span class="unit-doc-badge" id="badge-<?= $unit['id'] ?>"></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ========================
         SAĞ: Döküman Alanı
         ======================== -->
    <div class="doc-main">

        <?php if (!$selUnit): ?>
        <!-- Ünite seçilmemiş -->
        <div class="doc-empty">
            <div style="font-size:48px;margin-bottom:12px;opacity:.3;">📂</div>
            <div style="font-size:16px;font-weight:700;color:var(--text);margin-bottom:6px;">Bir ünite seçin</div>
            <div style="font-size:13px;color:var(--text-muted);">Sol panelden bir üniteye tıklayarak dökümanlarını görüntüleyin.</div>
        </div>

        <?php else: ?>

        <!-- Başlık -->
        <div class="doc-main-header">
            <div>
                <div class="doc-breadcrumb">
                    <span>🏫 <?= sanitize($selClassName) ?></span>
                    <span class="sep">›</span>
                    <span>📚 Ünite</span>
                </div>
                <div class="doc-unit-title"><?= sanitize($selUnit['name']) ?></div>
            </div>
            <span style="margin-left:auto;font-size:12px;color:var(--text-muted);background:var(--bg);padding:4px 12px;border-radius:20px;border:1px solid var(--border);" id="docCountBadge">
                <?= count($docs) ?> döküman
            </span>
        </div>

        <!-- Upload Alanı -->
        <div class="upload-zone" id="uploadZone">
            <input type="file" id="fileInput"
                   accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,.pptx,.ppt"
                   multiple style="display:none;">
            <div class="upload-icon">📎</div>
            <p style="font-weight:700;font-size:14px;color:var(--text);">Dosyaları buraya sürükle veya tıkla</p>
            <p>PDF, görsel (JPG, PNG, GIF, WEBP) veya PPTX yükleyebilirsiniz</p>
            <div class="upload-types">
                <span class="type-chip ftb-pdf">PDF</span>
                <span class="type-chip ftb-image">JPG / PNG</span>
                <span class="type-chip ftb-pptx">PPTX</span>
            </div>
            <p style="font-size:11px;margin-top:8px;color:var(--text-muted);">Maks. 50 MB · Çoklu yükleme desteklenir</p>
        </div>

        <!-- Yükleme progress -->
        <div id="uploadProgress" class="upload-progress">
            <span id="uploadFileName" style="font-size:13px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px;"></span>
            <div class="progress-bar-wrap">
                <div class="progress-bar-fill" id="progressFill"></div>
            </div>
            <span id="uploadPct" style="font-size:12px;color:var(--text-muted);flex-shrink:0;">0%</span>
        </div>

        <!-- Döküman Listesi -->
        <?php if (empty($docs)): ?>
        <div class="doc-empty" id="emptyState">
            <div style="font-size:40px;margin-bottom:10px;opacity:.3;">📄</div>
            <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:4px;">Bu ünitede henüz döküman yok</div>
            <div style="font-size:13px;color:var(--text-muted);">Yukarıdan dosya yükleyerek başlayın.</div>
        </div>
        <?php else: ?>
        <div class="doc-empty" id="emptyState" style="display:none;">
            <div style="font-size:40px;margin-bottom:10px;opacity:.3;">📄</div>
            <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:4px;">Bu ünitede henüz döküman yok</div>
            <div style="font-size:13px;color:var(--text-muted);">Yukarıdan dosya yükleyerek başlayın.</div>
        </div>
        <?php endif; ?>

        <div class="doc-list" id="docList">
            <?php foreach ($docs as $i => $doc): ?>
            <?= renderDocItem($doc, $i, count($docs)) ?>
            <?php endforeach; ?>
        </div>

        <?php endif; // selUnit ?>
    </div><!-- .doc-main -->
</div><!-- .doc-layout -->

<?php endif; ?>
</div><!-- .content-area -->

<?php
function renderDocItem(array $doc, int $idx, int $total): string {
    $icons = ['pdf'=>'📄','image'=>'🖼️','pptx'=>'📊','other'=>'📎'];
    $icon  = $icons[$doc['file_type']] ?? '📎';
    $ftb   = 'ftb-' . $doc['file_type'];
    $label = strtoupper($doc['file_type'] === 'image' ? 'IMG' : $doc['file_type']);
    $size  = formatDocSize((int)$doc['file_size']);
    $name  = htmlspecialchars($doc['display_name'], ENT_QUOTES, 'UTF-8');
    ob_start();
    ?>
    <div class="doc-item" id="doc-<?= $doc['id'] ?>" data-id="<?= $doc['id'] ?>">
        <div class="doc-order"><?= $idx+1 ?></div>
        <div class="doc-icon <?= $doc['file_type'] ?>"><?= $icon ?></div>
        <div class="doc-name-wrap">
            <div class="doc-name-display" onclick="startRename(<?= $doc['id'] ?>, this)">
                <span class="doc-name-text"><?= $name ?></span>
                <span class="edit-hint">✏️</span>
            </div>
            <input type="text" class="doc-name-input" id="input-<?= $doc['id'] ?>"
                   value="<?= $name ?>"
                   onblur="saveRename(<?= $doc['id'] ?>, this)"
                   onkeydown="handleRenameKey(event, <?= $doc['id'] ?>, this)">
            <div class="doc-meta">
                <span class="file-type-badge <?= $ftb ?>"><?= $label ?></span>
                <span style="margin-left:4px;"><?= $size ?></span>
            </div>
        </div>
        <div class="order-btns">
            <button class="order-btn" title="Yukarı taşı"
                    <?= $idx===0 ? 'disabled' : '' ?>
                    onclick="reorderDoc(<?= $doc['id'] ?>, 'up')">▲</button>
            <button class="order-btn" title="Aşağı taşı"
                    <?= $idx===$total-1 ? 'disabled' : '' ?>
                    onclick="reorderDoc(<?= $doc['id'] ?>, 'down')">▼</button>
        </div>
        <a href="<?= SITE_URL . '/' . $doc['file_path'] ?>" target="_blank"
           class="doc-open-btn" title="Dosyayı aç">🔗</a>
        <button class="doc-del-btn" title="Sil" onclick="deleteDoc(<?= $doc['id'] ?>)">🗑</button>
    </div>
    <?php
    return ob_get_clean();
}

function formatDocSize(int $bytes): string {
    if ($bytes < 1024)    return $bytes . ' B';
    if ($bytes < 1048576) return round($bytes/1024, 1) . ' KB';
    return round($bytes/1048576, 1) . ' MB';
}
?>

<script>
const UNIT_ID = <?= $selectedUnitId ?: 'null' ?>;
const API_URL  = '<?= SITE_URL ?>/api/document.php';
const SITE_URL = '<?= SITE_URL ?>';

// ============================================================
//  SOL PANEL: Sınıf aç/kapat
// ============================================================
function toggleClass(classId) {
    const btn   = document.querySelector('#tc-' + classId + ' .tree-class-btn');
    const panel = document.getElementById('tu-' + classId);
    const isOpen = panel.classList.contains('open');
    // Tüm açıkları kapat
    document.querySelectorAll('.tree-units.open').forEach(p => {
        p.classList.remove('open');
        p.previousElementSibling.classList.remove('open');
    });
    if (!isOpen) {
        panel.classList.add('open');
        btn.classList.add('open');
    }
}

// ============================================================
//  YÜKLEME
// ============================================================
const uploadZone = document.getElementById('uploadZone');
const fileInput  = document.getElementById('fileInput');

if (uploadZone) {
    uploadZone.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', e => [...e.target.files].forEach(uploadSingleFile));
    uploadZone.addEventListener('dragover', e => { e.preventDefault(); uploadZone.classList.add('drag-over'); });
    uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('drag-over'));
    uploadZone.addEventListener('drop', e => {
        e.preventDefault();
        uploadZone.classList.remove('drag-over');
        [...e.dataTransfer.files].forEach(uploadSingleFile);
    });
}

function uploadSingleFile(file) {
    if (!UNIT_ID) return;
    const progressEl = document.getElementById('uploadProgress');
    const nameEl     = document.getElementById('uploadFileName');
    const fillEl     = document.getElementById('progressFill');
    const pctEl      = document.getElementById('uploadPct');

    progressEl.classList.add('visible');
    nameEl.textContent = file.name;
    fillEl.style.width = '0%';
    pctEl.textContent  = '0%';

    const fd = new FormData();
    fd.append('action', 'upload');
    fd.append('unit_id', UNIT_ID);
    fd.append('file', file);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', API_URL);

    xhr.upload.onprogress = e => {
        if (e.lengthComputable) {
            const pct = Math.round(e.loaded / e.total * 100);
            fillEl.style.width = pct + '%';
            pctEl.textContent  = pct + '%';
        }
    };
    xhr.onload = () => {
        progressEl.classList.remove('visible');
        fileInput.value = '';
        try {
            const res = JSON.parse(xhr.responseText);
            if (res.success) {
                appendDoc(res.doc);
                showToast('✅ ' + file.name + ' yüklendi!', 'success');
            } else {
                showToast('❌ ' + (res.error || 'Yükleme hatası'), 'error');
            }
        } catch { showToast('❌ Sunucu yanıt hatası', 'error'); }
    };
    xhr.onerror = () => {
        progressEl.classList.remove('visible');
        showToast('❌ Bağlantı hatası', 'error');
    };
    xhr.send(fd);
}

// ============================================================
//  DOM'a yeni satır ekle
// ============================================================
function appendDoc(doc) {
    const list  = document.getElementById('docList');
    const empty = document.getElementById('emptyState');
    if (empty) empty.style.display = 'none';

    const total = list.children.length;
    const icons = { pdf:'📄', image:'🖼️', pptx:'📊', other:'📎' };
    const icon  = icons[doc.file_type] || '📎';
    const ftb   = 'ftb-' + doc.file_type;
    const label = (doc.file_type === 'image' ? 'IMG' : doc.file_type).toUpperCase();
    const size  = formatSize(doc.file_size);
    const name  = escHtml(doc.display_name);

    const div = document.createElement('div');
    div.className   = 'doc-item';
    div.id          = 'doc-' + doc.id;
    div.dataset.id  = doc.id;
    div.innerHTML   = `
        <div class="doc-order">${total + 1}</div>
        <div class="doc-icon ${doc.file_type}">${icon}</div>
        <div class="doc-name-wrap">
            <div class="doc-name-display" onclick="startRename(${doc.id}, this)">
                <span class="doc-name-text">${name}</span>
                <span class="edit-hint">✏️</span>
            </div>
            <input type="text" class="doc-name-input" id="input-${doc.id}"
                   value="${name}"
                   onblur="saveRename(${doc.id}, this)"
                   onkeydown="handleRenameKey(event, ${doc.id}, this)">
            <div class="doc-meta">
                <span class="file-type-badge ${ftb}">${label}</span>
                <span style="margin-left:4px;">${size}</span>
            </div>
        </div>
        <div class="order-btns">
            <button class="order-btn" title="Yukarı taşı" ${total === 0 ? 'disabled' : ''}
                    onclick="reorderDoc(${doc.id}, 'up')">▲</button>
            <button class="order-btn" title="Aşağı taşı" disabled
                    onclick="reorderDoc(${doc.id}, 'down')">▼</button>
        </div>
        <a href="${SITE_URL}/${doc.file_path}" target="_blank" class="doc-open-btn" title="Dosyayı aç">🔗</a>
        <button class="doc-del-btn" title="Sil" onclick="deleteDoc(${doc.id})">🗑</button>
    `;
    list.appendChild(div);
    refreshOrderUI();
}

// ============================================================
//  İSİM DÜZENLEME
// ============================================================
function startRename(docId, displayEl) {
    const wrap  = displayEl.closest('.doc-name-wrap');
    const input = document.getElementById('input-' + docId);
    wrap.querySelector('.doc-name-display').style.display = 'none';
    input.classList.add('visible');
    input.focus(); input.select();
}
function saveRename(docId, inputEl) {
    const newName = inputEl.value.trim();
    const wrap    = inputEl.closest('.doc-name-wrap');
    const display = wrap.querySelector('.doc-name-display');
    const span    = wrap.querySelector('.doc-name-text');

    display.style.display = '';
    inputEl.classList.remove('visible');

    if (!newName || newName === span.textContent) { inputEl.value = span.textContent; return; }

    apiFetch({ action:'rename', doc_id:docId, display_name:newName }, () => {
        span.textContent = newName;
        inputEl.value    = newName;
        showToast('✅ İsim güncellendi', 'success');
    });
}
function handleRenameKey(e, docId, inputEl) {
    if (e.key === 'Enter')  { e.preventDefault(); inputEl.blur(); }
    if (e.key === 'Escape') {
        inputEl.value = inputEl.closest('.doc-name-wrap').querySelector('.doc-name-text').textContent;
        inputEl.blur();
    }
}

// ============================================================
//  SIRALAMA
// ============================================================
function reorderDoc(docId, direction) {
    apiFetch({ action:'reorder', doc_id:docId, direction }, d => {
        if (!d.changed) return;
        const list     = document.getElementById('docList');
        const curEl    = document.getElementById('doc-' + docId);
        const neighEl  = document.getElementById('doc-' + d.neighbor_id);
        if (direction === 'up') list.insertBefore(curEl, neighEl);
        else                    list.insertBefore(neighEl, curEl);
        refreshOrderUI();
    });
}

function refreshOrderUI() {
    const items = document.querySelectorAll('#docList .doc-item');
    const total = items.length;
    document.getElementById('docCountBadge').textContent = total + ' döküman';
    // Sol paneldeki rozeti güncelle
    if (UNIT_ID) {
        const badge = document.getElementById('badge-' + UNIT_ID);
        if (badge) {
            badge.textContent = total;
            badge.classList.toggle('visible', total > 0);
        }
    }
    items.forEach((el, i) => {
        el.querySelector('.doc-order').textContent = i + 1;
        const [up, down] = el.querySelectorAll('.order-btn');
        up.disabled   = (i === 0);
        down.disabled = (i === total - 1);
    });
}

// ============================================================
//  SİL
// ============================================================
function deleteDoc(docId) {
    if (!confirm('Bu dökümanı silmek istediğinize emin misiniz?')) return;
    apiFetch({ action:'delete', doc_id:docId }, () => {
        const el = document.getElementById('doc-' + docId);
        el.style.transition = 'opacity .2s';
        el.style.opacity = '0';
        setTimeout(() => { el.remove(); refreshOrderUI(); }, 200);
        showToast('🗑 Döküman silindi', 'success');
    });
}

// ============================================================
//  YARDIMCILAR
// ============================================================
function apiFetch(data, onSuccess) {
    fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type':'application/json' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) onSuccess(d);
        else showToast('❌ ' + (d.error || 'Hata'), 'error');
    })
    .catch(() => showToast('❌ Bağlantı hatası', 'error'));
}

function formatSize(bytes) {
    if (bytes < 1024)    return bytes + ' B';
    if (bytes < 1048576) return (bytes/1024).toFixed(1) + ' KB';
    return (bytes/1048576).toFixed(1) + ' MB';
}

function escHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function showToast(msg, type) {
    const c = document.getElementById('toast-container');
    if (!c) return;
    const t = document.createElement('div');
    t.className   = 'toast ' + type;
    t.textContent = msg;
    c.appendChild(t);
    setTimeout(() => t.remove(), 3200);
}

// Sayfa yüklendiğinde mevcut döküman sayısını sol panelde göster
(function() {
    if (!UNIT_ID) return;
    const badge = document.getElementById('badge-' + UNIT_ID);
    const count = document.querySelectorAll('#docList .doc-item').length;
    if (badge && count > 0) {
        badge.textContent = count;
        badge.classList.add('visible');
    }
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
