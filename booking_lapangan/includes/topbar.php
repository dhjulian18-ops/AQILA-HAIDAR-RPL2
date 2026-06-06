<div class="topbar d-flex align-items-center justify-content-between">
    <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle">
        <i class="bi bi-list fs-5"></i>
    </button>
    <div class="d-none d-md-block">
        <span class="text-muted small">
            <i class="bi bi-geo-alt-fill me-1 text-success"></i>
            Admin: <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
        </span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="text-muted small d-none d-sm-block"><?= date('l, d F Y') ?></span>
        <a href="<?= str_contains($_SERVER['PHP_SELF'], '/booking/') || str_contains($_SERVER['PHP_SELF'], '/laporan/') ? '../' : '' ?>auth/logout.php"
           class="btn btn-sm btn-danger">
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }
});
</script>
