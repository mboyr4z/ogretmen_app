</main><!-- .admin-main -->
</div><!-- .admin-layout -->

<script>
function showAdminToast(msg, type) {
    const c = document.getElementById('admin-toast-container');
    const t = document.createElement('div');
    t.className = 'a-toast toast-' + (type || 'success');
    t.textContent = msg;
    c.appendChild(t);
    requestAnimationFrame(() => requestAnimationFrame(() => t.classList.add('show')));
    setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 300); }, 3000);
}
</script>
</body>
</html>
