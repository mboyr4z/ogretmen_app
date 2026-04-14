<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Online Sınav — SınıfPro</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Plus Jakarta Sans',sans-serif;background:#f8fafc;color:#0f172a;min-height:100vh;}

/* GİRİŞ */
.login-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#1e3a8a,#7c3aed);padding:20px;}
.login-box{background:#fff;border-radius:20px;padding:40px 36px;width:100%;max-width:420px;box-shadow:0 8px 32px rgba(0,0,0,.15);}
.login-logo{text-align:center;margin-bottom:28px;}
.login-logo .ico{width:52px;height:52px;background:linear-gradient(135deg,#2563eb,#7c3aed);border-radius:14px;display:inline-flex;align-items:center;justify-content:center;font-size:26px;margin-bottom:10px;}
.login-logo h1{font-size:22px;font-weight:800;}
.login-logo p{color:#64748b;font-size:13px;margin-top:4px;}
.form-group{margin-bottom:16px;}
.form-label{display:block;font-weight:600;font-size:13px;margin-bottom:6px;}
.form-control{width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;transition:.2s;}
.form-control:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.12);}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:11px 20px;border-radius:8px;font-weight:700;font-size:14px;border:none;cursor:pointer;width:100%;transition:.2s;font-family:inherit;}
.btn-primary{background:#2563eb;color:#fff;}
.btn-primary:hover{background:#1d4ed8;}
.alert{padding:12px 14px;border-radius:8px;font-size:13px;margin-bottom:14px;}
.alert-error{background:#fee2e2;color:#991b1b;}
.alert-info{background:#dbeafe;color:#1e40af;}
.alert-success{background:#d1fae5;color:#065f46;}

/* SINAV */
#exam-wrap{display:none;max-width:820px;margin:0 auto;padding:20px 16px 40px;}
.exam-topbar{background:#fff;border-radius:12px;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,.08);flex-wrap:wrap;gap:10px;}
.exam-title{font-size:16px;font-weight:800;}
.timer{font-size:18px;font-weight:800;color:#dc2626;background:#fee2e2;padding:6px 14px;border-radius:8px;font-variant-numeric:tabular-nums;}
.timer.warn{animation:pulse .8s ease-in-out infinite;}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.6}}

.question-card{background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;padding:20px;margin-bottom:14px;transition:.2s;}
.question-card.answered{border-color:#059669;}
.q-header{display:flex;align-items:flex-start;gap:10px;margin-bottom:12px;}
.q-num{min-width:28px;height:28px;background:#2563eb;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;flex-shrink:0;}
.q-text{font-weight:600;font-size:14px;line-height:1.5;flex:1;}
.q-type-badge{font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;flex-shrink:0;}
.badge-test{background:#dbeafe;color:#1d4ed8;}
.badge-klasik{background:#ede9fe;color:#7c3aed;}
.q-pts{font-size:11px;color:#64748b;flex-shrink:0;}
.q-unit{font-size:11px;color:#94a3b8;margin-bottom:10px;padding-left:38px;}

/* Şıklar */
.options{display:grid;gap:7px;padding-left:38px;}
.opt-label{display:flex;align-items:center;gap:10px;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;cursor:pointer;transition:.15s;font-size:13.5px;}
.opt-label:hover{border-color:#2563eb;background:#eff6ff;}
.opt-label input[type=radio]{accent-color:#2563eb;width:15px;height:15px;flex-shrink:0;}
.opt-label.selected{border-color:#2563eb;background:#eff6ff;font-weight:600;}
.opt-circle{width:20px;height:20px;border:1.5px solid #94a3b8;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0;}

/* Klasik textarea */
.klasik-textarea{width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13.5px;font-family:inherit;resize:vertical;min-height:90px;outline:none;transition:.2s;margin-left:0;}
.klasik-textarea:focus{border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,.1);}

/* Gönder butonu */
.submit-bar{position:sticky;bottom:20px;display:flex;justify-content:center;margin-top:20px;}
.btn-submit{background:#059669;color:#fff;padding:14px 40px;font-size:15px;border-radius:10px;font-weight:800;border:none;cursor:pointer;box-shadow:0 4px 14px rgba(5,150,105,.35);transition:.2s;font-family:inherit;}
.btn-submit:hover{background:#047857;}

/* SONUÇ */
#result-wrap{display:none;min-height:100vh;display:none;align-items:center;justify-content:center;background:linear-gradient(135deg,#1e3a8a,#7c3aed);padding:20px;}
.result-box{background:#fff;border-radius:20px;padding:40px 36px;width:100%;max-width:460px;text-align:center;box-shadow:0 8px 32px rgba(0,0,0,.15);}
.result-icon{font-size:56px;margin-bottom:16px;}
.result-title{font-size:22px;font-weight:800;margin-bottom:8px;}
.result-name{font-size:14px;color:#64748b;margin-bottom:24px;}
.score-big{font-size:52px;font-weight:800;color:#2563eb;line-height:1;}
.score-label{font-size:13px;color:#64748b;margin-top:4px;}
.score-details{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:20px;}
.score-detail{background:#f8fafc;border-radius:10px;padding:12px;}
.score-detail .val{font-size:20px;font-weight:800;color:#0f172a;}
.score-detail .lbl{font-size:11px;color:#64748b;margin-top:2px;}
.pending-msg{background:#fef3c7;border:1px solid #fcd34d;border-radius:10px;padding:14px 16px;font-size:13px;color:#92400e;margin-top:16px;line-height:1.5;}

#toast{position:fixed;bottom:20px;right:20px;background:#0f172a;color:#fff;padding:10px 18px;border-radius:8px;font-size:13px;opacity:0;transition:.3s;pointer-events:none;z-index:999;}
#toast.show{opacity:1;}
</style>
</head>
<body>

<div id="toast"></div>

<!-- GİRİŞ EKRANI -->
<div class="login-wrap" id="login-wrap">
<div class="login-box">
    <div class="login-logo">
        <div class="ico">📝</div>
        <h1>Online Sınav</h1>
        <p id="exam-name-display">Sınava girmek için bilgilerinizi doldurun</p>
    </div>
    <div id="login-alert"></div>
    <div class="form-group">
        <label class="form-label">Adınız</label>
        <input type="text" id="f-name" class="form-control" placeholder="Adınız">
    </div>
    <div class="form-group">
        <label class="form-label">Soyadınız</label>
        <input type="text" id="f-surname" class="form-control" placeholder="Soyadınız">
    </div>
    <div class="form-group">
        <label class="form-label">Okul Numaranız</label>
        <input type="text" id="f-no" class="form-control" placeholder="Okul numaranız">
    </div>
    <button class="btn btn-primary" onclick="startExam()">Sınava Başla →</button>
</div>
</div>

<!-- SINAV EKRANI -->
<div id="exam-wrap">
    <div class="exam-topbar">
        <div>
            <div class="exam-title" id="et-title"></div>
            <div style="font-size:12px;color:#64748b;" id="et-student"></div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="font-size:12px;color:#64748b;" id="et-progress"></div>
            <div class="timer" id="timer">--:--</div>
        </div>
    </div>
    <div id="questions-container"></div>
    <div class="submit-bar">
        <button class="btn-submit" onclick="submitExam()">✅ Sınavı Tamamla & Gönder</button>
    </div>
</div>

<!-- SONUÇ EKRANI -->
<div id="result-wrap" style="display:none;min-height:100vh;align-items:center;justify-content:center;background:linear-gradient(135deg,#1e3a8a,#7c3aed);padding:20px;">
<div class="result-box">
    <div class="result-icon">🎓</div>
    <div class="result-title">Sınav Tamamlandı!</div>
    <div class="result-name" id="r-name"></div>
    <div id="r-score-section"></div>
</div>
</div>

<script>
const SITE_URL = '<?= defined("SITE_URL") ? SITE_URL : "" ?>';
const urlParams = new URLSearchParams(location.search);
const examCode  = urlParams.get('kod') || '';
let sessionId   = null;
let examData    = null;
let questions   = [];
let timerInterval = null;
let answers     = {};
let submitted   = false;

// ---- TOAST ----
function toast(msg, duration=3000) {
    const el = document.getElementById('toast');
    el.textContent = msg; el.classList.add('show');
    setTimeout(()=>el.classList.remove('show'), duration);
}

// ---- BAŞLA ----
async function startExam() {
    const name    = document.getElementById('f-name').value.trim();
    const surname = document.getElementById('f-surname').value.trim();
    const no      = document.getElementById('f-no').value.trim();
    const alertEl = document.getElementById('login-alert');

    if (!name || !surname || !no) {
        alertEl.innerHTML = '<div class="alert alert-error">❌ Tüm alanları doldurun.</div>'; return;
    }
    alertEl.innerHTML = '<div class="alert alert-info">⏳ Bağlanılıyor...</div>';

    const fd = new FormData();
    fd.append('action','student_start'); fd.append('code',examCode);
    fd.append('name',name); fd.append('surname',surname); fd.append('student_no',no);

    const res = await fetch('../api/online_exam.php', {method:'POST',body:fd});
    const data = await res.json();

    if (data.error) { alertEl.innerHTML = `<div class="alert alert-error">❌ ${data.error}</div>`; return; }

    sessionId = data.session_id;
    alertEl.innerHTML = '';
    loadExam(name, surname);
}

// ---- SINAVI YÜKLE ----
async function loadExam(name, surname) {
    const res  = await fetch(`../api/online_exam.php?action=get_exam&code=${examCode}`);
    const data = await res.json();
    if (data.error) { alert(data.error); return; }

    examData  = data.exam;
    questions = data.questions;

    document.getElementById('login-wrap').style.display  = 'none';
    document.getElementById('exam-wrap').style.display   = 'block';
    document.getElementById('et-title').textContent      = examData.name;
    document.getElementById('et-student').textContent    = `${name} ${surname}`;
    document.getElementById('r-name').textContent        = `${name} ${surname}`;

    renderQuestions();
    startTimer(examData.end_time, examData.duration);
}

// ---- SORULARI RENDER ET ----
function renderQuestions() {
    const container = document.getElementById('questions-container');
    container.innerHTML = '';
    updateProgress();

    questions.forEach((q, i) => {
        const card = document.createElement('div');
        card.className = 'question-card';
        card.id = `qcard-${q.id}`;

        const typeBadge = q.type === 'test'
            ? '<span class="q-type-badge badge-test">Test</span>'
            : '<span class="q-type-badge badge-klasik">Klasik</span>';

        let inner = `
            <div class="q-header">
                <div class="q-num">${i+1}</div>
                <div class="q-text">${q.title}</div>
                ${typeBadge}
                <div class="q-pts">${q.points}p</div>
            </div>
            <div class="q-unit">📚 ${q.unit_name}</div>`;

        if (q.type === 'test') {
            const opts = [
                {k:'A',v:q.option_a},{k:'B',v:q.option_b},{k:'C',v:q.option_c},
                {k:'D',v:q.option_d},{k:'E',v:q.option_e}
            ].filter(o=>o.v);
            inner += `<div class="options">`;
            opts.forEach(o => {
                inner += `
                <label class="opt-label" id="opt-${q.id}-${o.k}">
                    <input type="radio" name="q${q.id}" value="${o.k}" onchange="selectOpt(${q.id},'${o.k}')">
                    <div class="opt-circle">${o.k}</div>
                    <span>${o.v}</span>
                </label>`;
            });
            inner += `</div>`;
        } else {
            inner += `<div style="padding-left:38px;">
                <textarea class="klasik-textarea" id="ans-${q.id}" placeholder="Cevabınızı buraya yazın..."
                    oninput="saveClassic(${q.id}, this.value)" rows="4"></textarea>
            </div>`;
        }

        card.innerHTML = inner;
        container.appendChild(card);
    });
}

function updateProgress() {
    const answered = Object.keys(answers).length;
    document.getElementById('et-progress').textContent = `${answered}/${questions.length} soru cevaplandı`;
}

// ---- TEST ŞIĞI SEÇ ----
function selectOpt(qid, opt) {
    document.querySelectorAll(`[id^="opt-${qid}-"]`).forEach(el => el.classList.remove('selected'));
    document.getElementById(`opt-${qid}-${opt}`)?.classList.add('selected');
    answers[qid] = opt;
    document.getElementById(`qcard-${qid}`)?.classList.add('answered');
    updateProgress();
    saveAnswer(qid, '', opt);
}

// ---- KLASİK CEVAP ----
let klasikTimers = {};
function saveClassic(qid, val) {
    answers[qid] = val;
    document.getElementById(`qcard-${qid}`)?.classList.toggle('answered', val.trim().length > 0);
    updateProgress();
    clearTimeout(klasikTimers[qid]);
    klasikTimers[qid] = setTimeout(() => saveAnswer(qid, val, null), 1200);
}

// ---- CEVABI API'YE KAYDET ----
async function saveAnswer(qid, answer, selected) {
    if (submitted) return;
    const fd = new FormData();
    fd.append('action','save_answer');
    fd.append('session_id', sessionId);
    fd.append('question_id', qid);
    if (answer)   fd.append('answer', answer);
    if (selected) fd.append('selected_opt', selected);
    await fetch('../api/online_exam.php', {method:'POST', body:fd});
}

// ---- ZAMANLAYICI ----
function startTimer(endTime, duration) {
    function tick() {
        const now  = new Date().getTime();
        const end  = endTime ? new Date(endTime.replace(' ','T')).getTime() : now + duration*60000;
        const diff = Math.max(0, Math.floor((end - now) / 1000));
        const m = Math.floor(diff/60).toString().padStart(2,'0');
        const s = (diff%60).toString().padStart(2,'0');
        const el = document.getElementById('timer');
        el.textContent = `${m}:${s}`;
        if (diff <= 120) el.classList.add('warn'); else el.classList.remove('warn');
        if (diff <= 0 && !submitted) { clearInterval(timerInterval); autoSubmit(); }
    }
    tick();
    timerInterval = setInterval(tick, 1000);
}

// ---- OTOMATİK GÖNDER ----
async function autoSubmit() {
    toast('⏰ Süre doldu! Sınav otomatik gönderiliyor...');
    await doSubmit();
}

// ---- MANUEL GÖNDER ----
async function submitExam() {
    const unanswered = questions.length - Object.keys(answers).length;
    if (unanswered > 0) {
        if (!confirm(`${unanswered} soru cevaplanmadı. Yine de göndermek istiyor musunuz?`)) return;
    }
    await doSubmit();
}

async function doSubmit() {
    if (submitted) return;
    submitted = true;
    clearInterval(timerInterval);

    const fd = new FormData();
    fd.append('action','submit');
    fd.append('session_id', sessionId);
    const res  = await fetch('../api/online_exam.php', {method:'POST',body:fd});
    const data = await res.json();

    document.getElementById('exam-wrap').style.display = 'none';
    showResult(data);
}

// ---- SONUÇ GÖSTER ----
function showResult(data) {
    const wrap = document.getElementById('result-wrap');
    wrap.style.display = 'flex';
    const sec = document.getElementById('r-score-section');

    if (data.needs_grading) {
        sec.innerHTML = `<div class="pending-msg">
            ⏳ Sınavınız öğretmeninize ulaştı.<br>
            Klasik sorular değerlendirildikten sonra notunuzu görebileceksiniz.<br>
            Bu sayfayı kapatabilirsiniz.
        </div>`;
    } else {
        sec.innerHTML = `
            <div class="score-big">${data.test_score ?? 0}</div>
            <div class="score-label">puan</div>
            <div style="margin-top:14px;font-size:13px;color:#64748b;">Test sorularından alınan puan</div>`;
    }
}

// Sayfa yüklenince fetch yoksa
window.onload = () => {
    if (!examCode) {
        document.getElementById('login-alert').innerHTML =
            '<div class="alert alert-error">❌ Geçersiz sınav linki.</div>';
    }
};
</script>

<?php
// Sınav verilerini JSON olarak ver (GET isteği)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['api'])) {
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/db_online_helpers.php';
    $code = trim($_GET['kod'] ?? '');
    $exam = getExamByCode($code);
    if (!$exam || !isExamOpen($exam)) { echo json_encode(['error'=>'Sınav aktif değil']); exit; }
    $db = getDB();
    $qs = $db->prepare("
        SELECT q.*, u.name AS unit_name
        FROM exam_questions eq
        JOIN questions q ON q.id=eq.question_id
        JOIN units u ON u.id=q.unit_id
        WHERE eq.exam_id=?
        ORDER BY eq.order_num, eq.id
    ");
    $qs->execute([$exam['id']]);
    echo json_encode(['exam'=>$exam, 'questions'=>$qs->fetchAll()]);
    exit;
}
?>
</body>
</html>
