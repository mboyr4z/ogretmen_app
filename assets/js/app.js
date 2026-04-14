// ============================================
//  SINIFPro - Ana JavaScript
// ============================================

// ---- TOAST ----
function showToast(msg, type = 'success', duration = 3000) {
    const icons = { success:'✅', error:'❌', warning:'⚠️', info:'ℹ️' };
    const container = document.getElementById('toast-container') || (() => {
        const el = document.createElement('div');
        el.id = 'toast-container';
        document.body.appendChild(el);
        return el;
    })();
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.innerHTML = `<span>${icons[type] || '📢'}</span><span>${msg}</span>`;
    container.appendChild(t);
    setTimeout(() => { t.style.opacity='0'; t.style.transform='translateX(20px)'; t.style.transition='.3s'; setTimeout(()=>t.remove(),300); }, duration);
}

// ---- MODAL ----
function openModal(id) {
    const m = document.getElementById(id);
    if (m) { m.classList.add('active'); document.body.style.overflow = 'hidden'; }
}
function closeModal(id) {
    const m = document.getElementById(id);
    if (m) { m.classList.remove('active'); document.body.style.overflow = ''; }
}
document.addEventListener('click', e => {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('active');
        document.body.style.overflow = '';
    }
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(m => {
            m.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
});

// ---- CONFIRM DELETE ----
function confirmDelete(msg, callback) {
    if (confirm(msg || 'Silmek istediğinize emin misiniz?')) callback();
}

// ---- API FETCH ----
async function apiCall(url, data = {}, method = 'POST') {
    try {
        const formData = new FormData();
        Object.entries(data).forEach(([k,v]) => formData.append(k, v));
        const res = await fetch(url, {
            method,
            body: method !== 'GET' ? formData : undefined,
        });
        const json = await res.json();
        return json;
    } catch(e) {
        return { error: 'Bağlantı hatası: ' + e.message };
    }
}

// ---- FORM SERIALIZE ----
function serializeForm(form) {
    const data = {};
    new FormData(form).forEach((v,k) => data[k] = v);
    return data;
}

// ---- TOGGLE TEST/KLASİK FORM ----
function toggleQuestionType(type) {
    const testOpts = document.getElementById('test-options');
    const klasikOpts = document.getElementById('klasik-options');
    if (!testOpts || !klasikOpts) return;
    if (type === 'test') {
        testOpts.style.display = '';
        klasikOpts.style.display = 'none';
    } else {
        testOpts.style.display = 'none';
        klasikOpts.style.display = '';
    }
}

// ---- TABS ----
function switchTab(tabId, btn) {
    document.querySelectorAll('.tab-content').forEach(t => t.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    const el = document.getElementById(tabId);
    if (el) el.style.display = '';
    if (btn) btn.classList.add('active');
}

// ---- LIMIT BAR RENDER ----
function renderLimitBar(containerId, label, current, max) {
    const el = document.getElementById(containerId);
    if (!el) return;
    const unlimited = max === 0;
    const pct = unlimited ? 100 : Math.min(100, Math.round(current / max * 100));
    let cls = '';
    if (!unlimited) { if (pct >= 100) cls = 'full'; else if (pct >= 75) cls = 'warn'; }
    else cls = 'unlim';
    el.innerHTML = `
        <div class="limit-bar-wrap">
            <div class="limit-bar-label">
                <span>${label}</span>
                <span>${unlimited ? '∞ Sınırsız' : current + ' / ' + max}</span>
            </div>
            <div class="limit-bar-track">
                <div class="limit-bar-fill ${cls}" style="width:${pct}%"></div>
            </div>
        </div>`;
}

// ---- INIT ----
document.addEventListener('DOMContentLoaded', () => {
    // Active nav highlight
    const path = window.location.pathname.split('/').pop();
    document.querySelectorAll('.nav-item[data-page]').forEach(el => {
        if (el.dataset.page === path) el.classList.add('active');
    });

    // ---- MOBİL SIDEBAR ----
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const hamburger = document.getElementById('hamburgerBtn');

    function openSidebar() {
        sidebar?.classList.add('open');
        overlay?.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar?.classList.remove('open');
        overlay?.classList.remove('active');
        document.body.style.overflow = '';
    }

    hamburger?.addEventListener('click', openSidebar);
    overlay?.addEventListener('click', closeSidebar);

    // Sidebar'daki link tıklanınca kapat
    sidebar?.querySelectorAll('.nav-item').forEach(el => {
        el.addEventListener('click', () => {
            if (window.innerWidth <= 768) closeSidebar();
        });
    });
});
