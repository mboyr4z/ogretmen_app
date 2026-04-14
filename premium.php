<?php
$pageTitle = 'Premium Üyelik';
require_once __DIR__ . '/includes/header.php';
?>

<div class="topbar">
    <h2>⭐ Premium Üyelik</h2>
</div>

<div class="content-area">

<?php if ($premium): ?>
<div class="alert alert-success" style="font-size:15px;padding:20px;">
    ⭐ Zaten Premium üyesiniz! Tüm özelliklere tam erişiminiz var.
    <?php if ($user['premium_until']): ?>
    <br><small>Bitiş tarihi: <?= date('d.m.Y', strtotime($user['premium_until'])) ?></small>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- KARŞILAŞTIRMA TABLOSU -->
<div class="grid grid-2" style="max-width:800px;margin:0 auto 40px;">
    <!-- ÜCRETSİZ -->
    <div class="card">
        <div class="card-header" style="background:var(--bg);">
            <span class="card-title">🆓 Ücretsiz Plan</span>
            <span class="badge badge-blue">Mevcut</span>
        </div>
        <div class="card-body">
            <div style="font-size:28px;font-weight:800;margin-bottom:4px;">₺0 <span style="font-size:14px;font-weight:400;color:var(--text-muted);">/ ay</span></div>
            <ul style="list-style:none;display:grid;gap:10px;margin-top:20px;">
                <li style="display:flex;align-items:center;gap:8px;"><span style="color:var(--success);">✓</span> <?= FREE_MAX_CLASSES ?> sınıf</li>
                <li style="display:flex;align-items:center;gap:8px;"><span style="color:var(--success);">✓</span> <?= FREE_MAX_UNITS ?> ünite</li>
                <li style="display:flex;align-items:center;gap:8px;"><span style="color:var(--success);">✓</span> <?= FREE_MAX_QUESTIONS ?> soru</li>
                <li style="display:flex;align-items:center;gap:8px;"><span style="color:var(--success);">✓</span> <?= FREE_MAX_EXAMS ?> sınav</li>
                <li style="display:flex;align-items:center;gap:8px;"><span style="color:var(--danger);">✗</span> <span style="color:var(--text-muted);">Sınırsız PDF/Word export</span></li>
                <li style="display:flex;align-items:center;gap:8px;"><span style="color:var(--danger);">✗</span> <span style="color:var(--text-muted);">Öncelikli destek</span></li>
            </ul>
        </div>
    </div>

    <!-- PREMİUM -->
    <div class="card" style="border-color:var(--primary);box-shadow:0 0 0 3px rgba(37,99,235,.15),var(--shadow);">
        <div class="card-header" style="background:linear-gradient(135deg,#1d4ed8,#7c3aed);color:#fff;border-radius:calc(var(--radius) - 1px) calc(var(--radius) - 1px) 0 0;">
            <span class="card-title" style="color:#fff;">⭐ Premium Plan</span>
            <span style="background:rgba(255,255,255,.2);color:#fff;padding:3px 8px;border-radius:20px;font-size:11px;font-weight:700;">ÖNERİLEN</span>
        </div>
        <div class="card-body">
            <div style="font-size:28px;font-weight:800;margin-bottom:4px;color:var(--primary);">₺49 <span style="font-size:14px;font-weight:400;color:var(--text-muted);">/ ay</span></div>
            <ul style="list-style:none;display:grid;gap:10px;margin-top:20px;">
                <li style="display:flex;align-items:center;gap:8px;"><span style="color:var(--success);">✓</span> <strong>Sınırsız</strong> sınıf</li>
                <li style="display:flex;align-items:center;gap:8px;"><span style="color:var(--success);">✓</span> <strong>Sınırsız</strong> ünite</li>
                <li style="display:flex;align-items:center;gap:8px;"><span style="color:var(--success);">✓</span> <strong>Sınırsız</strong> soru</li>
                <li style="display:flex;align-items:center;gap:8px;"><span style="color:var(--success);">✓</span> <strong>Sınırsız</strong> sınav</li>
                <li style="display:flex;align-items:center;gap:8px;"><span style="color:var(--success);">✓</span> <strong>PDF & Word</strong> export</li>
                <li style="display:flex;align-items:center;gap:8px;"><span style="color:var(--success);">✓</span> Öncelikli destek</li>
            </ul>
            <?php if (!$premium): ?>
            <button onclick="activatePremium()" class="btn btn-primary btn-block btn-lg" style="margin-top:24px;">⭐ Premium'a Geç</button>
            <p style="text-align:center;font-size:11px;color:var(--text-muted);margin-top:8px;">Demo: Butona basın, üyeliğiniz aktif olsun</p>
            <?php else: ?>
            <div class="alert alert-success" style="margin-top:16px;">✅ Aktif üyeliğiniz var</div>
            <?php endif; ?>
        </div>
    </div>
</div>

</div>

<script>
async function activatePremium() {
    const r = await apiCall('api/profile.php', { action:'activate_premium' });
    if (r.error) showToast(r.error, 'error');
    else { showToast('🎉 Premium üyeliğiniz aktif edildi!', 'success'); setTimeout(()=>location.reload(), 1200); }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
