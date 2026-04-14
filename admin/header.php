<?php
require_once __DIR__ . '/auth.php';
requireAdminAuth();
$adminUser = getCurrentAdmin();
if (!$adminUser) {
    // Admin kaydı silindiyse oturumu temizle
    session_destroy();
    header('Location: ' . SITE_URL . '/admin/login.php');
    exit;
}
$adminInitial = mb_substr($adminUser['name'], 0, 1, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($pageTitle) ? adminSanitize($pageTitle) . ' — ' : '' ?>Admin Panel · SınıfPro</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🛡️</text></svg>">
<style>
/* ============================================
   ADMIN PANEL — ÖZEL STİL (normal app.css'den bağımsız)
   ============================================ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --admin-red:       #dc2626;
    --admin-red-dark:  #b91c1c;
    --admin-red-light: rgba(220,38,38,.1);
    --bg:              #0f172a;
    --surface:         #1e293b;
    --surface2:        #243048;
    --border:          #334155;
    --text:            #f1f5f9;
    --text-muted:      #94a3b8;
    --success:         #10b981;
    --warning:         #f59e0b;
    --danger:          #ef4444;
    --info:            #3b82f6;
    --radius:          12px;
    --shadow:          0 4px 24px rgba(0,0,0,.4);
    --sidebar-w:       240px;
}

body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: var(--bg);
    color: var(--text);
    font-size: 14px;
    line-height: 1.6;
}

/* ---- LAYOUT ---- */
.admin-layout { display: flex; min-height: 100vh; }

/* ---- SIDEBAR ---- */
.admin-sidebar {
    width: var(--sidebar-w);
    background: var(--surface);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0; left: 0; bottom: 0;
    z-index: 100;
}
.admin-sidebar-logo {
    padding: 22px 20px 18px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 12px;
}
.admin-logo-mark {
    width: 38px; height: 38px;
    background: linear-gradient(135deg, var(--admin-red), var(--admin-red-dark));
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    box-shadow: 0 4px 12px rgba(220,38,38,.4);
    flex-shrink: 0;
}
.admin-sidebar-logo span {
    font-weight: 800;
    font-size: 15px;
    color: var(--text);
    line-height: 1.2;
}
.admin-sidebar-logo small {
    display: block;
    font-size: 10px;
    font-weight: 600;
    color: var(--admin-red);
    letter-spacing: 1px;
    text-transform: uppercase;
}

.admin-sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
.admin-nav-section { margin-bottom: 24px; }
.admin-nav-section-title {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: var(--text-muted);
    padding: 0 8px;
    margin-bottom: 6px;
}
.admin-nav-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    border-radius: 9px;
    color: var(--text-muted);
    text-decoration: none;
    font-weight: 500;
    font-size: 13px;
    transition: all .2s;
    margin-bottom: 2px;
}
.admin-nav-item:hover { background: var(--surface2); color: var(--text); }
.admin-nav-item.active { background: var(--admin-red-light); color: var(--admin-red); font-weight: 700; }
.admin-nav-item .icon { font-size: 16px; width: 20px; text-align: center; flex-shrink: 0; }

.admin-sidebar-footer {
    padding: 14px 16px;
    border-top: 1px solid var(--border);
}
.admin-user-card {
    display: flex;
    align-items: center;
    gap: 10px;
}
.admin-user-avatar {
    width: 34px; height: 34px;
    background: linear-gradient(135deg, var(--admin-red), var(--admin-red-dark));
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; font-weight: 800; color: #fff;
    flex-shrink: 0;
}
.admin-user-info .name { font-size: 13px; font-weight: 700; color: var(--text); }
.admin-user-info .role { font-size: 11px; color: var(--admin-red); font-weight: 600; }

/* ---- MAIN ---- */
.admin-main {
    margin-left: var(--sidebar-w);
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}
.admin-topbar {
    padding: 20px 28px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--surface);
    position: sticky;
    top: 0;
    z-index: 10;
}
.admin-topbar h2 { font-size: 18px; font-weight: 800; color: var(--text); }
.admin-content { padding: 28px; flex: 1; }

/* ---- CARDS ---- */
.a-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
}
.a-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.a-card-title { font-size: 15px; font-weight: 700; color: var(--text); }
.a-card-body { padding: 20px; }

/* ---- STATS ---- */
.a-stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
@media(max-width:1100px) { .a-stat-grid { grid-template-columns: repeat(2,1fr); } }
@media(max-width:600px)  { .a-stat-grid { grid-template-columns: 1fr; } }

.a-stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
}
.a-stat-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}
.a-stat-value { font-size: 26px; font-weight: 800; line-height: 1; }
.a-stat-label { font-size: 12px; color: var(--text-muted); margin-top: 4px; }

/* ---- GRID ---- */
.a-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
@media(max-width:900px) { .a-grid-2 { grid-template-columns: 1fr; } }

/* ---- TABS ---- */
.a-tabs { display: flex; gap: 4px; background: var(--surface2); padding: 4px; border-radius: 10px; margin-bottom: 24px; }
.a-tab  { padding: 8px 20px; border-radius: 8px; border: none; background: transparent; cursor: pointer; font-weight: 600; font-size: 13px; color: var(--text-muted); font-family: inherit; transition: all .2s; }
.a-tab.active { background: var(--admin-red); color: #fff; box-shadow: 0 2px 8px rgba(220,38,38,.4); }
.a-tab-content { display: none; }
.a-tab-content.active { display: block; }

/* ---- BUTTONS ---- */
.a-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px;
    border-radius: 8px;
    border: none;
    font-size: 13px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    transition: all .2s;
    text-decoration: none;
}
.a-btn-primary { background: var(--admin-red); color: #fff; }
.a-btn-primary:hover { background: var(--admin-red-dark); }
.a-btn-success { background: rgba(16,185,129,.15); color: var(--success); }
.a-btn-success:hover { background: var(--success); color: #fff; }
.a-btn-warning { background: rgba(245,158,11,.15); color: var(--warning); }
.a-btn-warning:hover { background: var(--warning); color: #fff; }
.a-btn-danger  { background: rgba(239,68,68,.15); color: var(--danger); }
.a-btn-danger:hover  { background: var(--danger); color: #fff; }
.a-btn-ghost   { background: var(--surface2); color: var(--text-muted); }
.a-btn-ghost:hover { color: var(--text); }
.a-btn-sm { padding: 5px 10px; font-size: 12px; }

/* ---- BADGES ---- */
.a-badge { display:inline-block; padding:3px 8px; border-radius:20px; font-size:11px; font-weight:700; }
.a-badge-red    { background:rgba(239,68,68,.15); color:#f87171; }
.a-badge-green  { background:rgba(16,185,129,.15); color:#34d399; }
.a-badge-yellow { background:rgba(245,158,11,.15); color:#fbbf24; }
.a-badge-blue   { background:rgba(59,130,246,.15); color:#60a5fa; }
.a-badge-gray   { background:rgba(148,163,184,.15); color:#94a3b8; }

/* ---- TABLE ---- */
.a-table-wrap { overflow-x: auto; }
.a-table { width: 100%; border-collapse: collapse; }
.a-table th {
    padding: 10px 16px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .5px;
    border-bottom: 1px solid var(--border);
    background: var(--surface2);
    white-space: nowrap;
}
.a-table td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
    font-size: 13px;
    vertical-align: middle;
}
.a-table tr:last-child td { border-bottom: none; }
.a-table tbody tr:hover td { background: var(--surface2); }
.a-table .name-cell { font-weight: 600; color: var(--text); }
.a-table .muted { color: var(--text-muted); font-size: 12px; }

/* ---- FORMS ---- */
.a-form-group { margin-bottom: 18px; }
.a-form-label { display:block; margin-bottom:6px; font-size:12px; font-weight:600; color:var(--text-muted); }
.a-form-control {
    width: 100%;
    padding: 10px 12px;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--text);
    font-size: 13px;
    font-family: inherit;
    outline: none;
    transition: border-color .2s;
}
.a-form-control:focus { border-color: var(--admin-red); box-shadow: 0 0 0 3px rgba(220,38,38,.15); }
.a-form-control::placeholder { color: #475569; }

/* ---- SEARCH / FILTER ---- */
.a-search-row { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
.a-filter-pills { display:flex; gap:6px; flex-wrap:wrap; }
.a-filter-pill {
    padding:5px 14px; border-radius:20px; font-size:12px; font-weight:600;
    border:1px solid var(--border); background:transparent;
    cursor:pointer; color:var(--text-muted); text-decoration:none;
    transition: all .2s;
}
.a-filter-pill:hover { border-color:var(--admin-red); color:var(--admin-red); }
.a-filter-pill.active { background:var(--admin-red); color:#fff; border-color:var(--admin-red); }

/* ---- ALERTS ---- */
.a-alert { padding:12px 16px; border-radius:8px; font-size:13px; margin-bottom:16px; }
.a-alert-danger { background:rgba(239,68,68,.1); color:#fca5a5; border:1px solid rgba(239,68,68,.2); }
.a-alert-warning{ background:rgba(245,158,11,.1); color:#fde68a; border:1px solid rgba(245,158,11,.2); }
.a-alert-success{ background:rgba(16,185,129,.1); color:#6ee7b7; border:1px solid rgba(16,185,129,.2); }
.a-alert-info   { background:rgba(59,130,246,.1); color:#93c5fd; border:1px solid rgba(59,130,246,.2); }

/* ---- PROGRESS BAR ---- */
.a-progress-wrap { height:6px; background:var(--surface2); border-radius:3px; overflow:hidden; }
.a-progress-bar  { height:100%; border-radius:3px; transition:width .4s; }

/* ---- MODAL ---- */
.a-modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.7); z-index: 1000;
    align-items: center; justify-content: center;
    backdrop-filter: blur(4px);
}
.a-modal-overlay.open { display: flex; }
.a-modal {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 28px;
    width: 100%;
    max-width: 440px;
    box-shadow: var(--shadow);
}
.a-modal h3 { font-size: 17px; font-weight: 800; margin-bottom: 12px; }
.a-modal p  { color: var(--text-muted); font-size: 13px; margin-bottom: 20px; }

/* ---- EMPTY STATE ---- */
.a-empty { text-align:center; padding:48px 20px; color:var(--text-muted); }
.a-empty .icon { font-size:40px; margin-bottom:12px; }
.a-empty h3 { font-size:15px; font-weight:700; color:var(--text); margin-bottom:6px; }

/* ---- TOAST ---- */
#admin-toast-container {
    position: fixed; bottom: 24px; right: 24px;
    z-index: 9999; display: flex; flex-direction: column; gap: 8px;
}
.a-toast {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 13px;
    color: var(--text);
    box-shadow: var(--shadow);
    opacity: 0;
    transform: translateX(20px);
    transition: all .3s;
    min-width: 260px;
}
.a-toast.show { opacity: 1; transform: translateX(0); }
.a-toast.toast-success { border-left: 3px solid var(--success); }
.a-toast.toast-error   { border-left: 3px solid var(--danger);  }
</style>
</head>
<body>
<div id="admin-toast-container"></div>

<div class="admin-layout">
<!-- SIDEBAR -->
<aside class="admin-sidebar">
    <div class="admin-sidebar-logo">
        <div class="admin-logo-mark">🛡️</div>
        <div>
            <span>SınıfPro <small>Admin</small></span>
        </div>
    </div>

    <nav class="admin-sidebar-nav">
        <div class="admin-nav-section">
            <div class="admin-nav-section-title">Genel Bakış</div>
            <a href="<?= SITE_URL ?>/admin/index.php" class="admin-nav-item <?= (basename($_SERVER['PHP_SELF'])==='index.php')?'active':'' ?>">
                <span class="icon">📊</span> Dashboard
            </a>
        </div>
        <div class="admin-nav-section">
            <div class="admin-nav-section-title">Yönetim</div>
            <a href="<?= SITE_URL ?>/admin/index.php?tab=users" class="admin-nav-item">
                <span class="icon">👥</span> Kullanıcılar
            </a>
            <a href="<?= SITE_URL ?>/admin/index.php?tab=premium" class="admin-nav-item">
                <span class="icon">⭐</span> Premium Yönetimi
            </a>
        </div>
        <div class="admin-nav-section">
            <div class="admin-nav-section-title">Sistem</div>
            <a href="<?= SITE_URL ?>/admin/logout.php" class="admin-nav-item" style="color:#ef4444;">
                <span class="icon">🚪</span> Çıkış Yap
            </a>
        </div>
    </nav>

    <div class="admin-sidebar-footer">
        <div class="admin-user-card">
            <div class="admin-user-avatar"><?= $adminInitial ?></div>
            <div class="admin-user-info">
                <div class="name"><?= adminSanitize($adminUser['name']) ?></div>
                <div class="role">🛡️ Yönetici</div>
            </div>
        </div>
    </div>
</aside>

<!-- MAIN CONTENT -->
<main class="admin-main">
