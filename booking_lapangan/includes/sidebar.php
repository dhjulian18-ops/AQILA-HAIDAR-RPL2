<?php
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir  = basename(dirname($_SERVER['PHP_SELF']));

function isActive($page, $dir = '') {
    global $current_page, $current_dir;
    if ($dir && $current_dir === $dir) return 'active';
    if (!$dir && $current_page === $page) return 'active';
    return '';
}
?>
<nav class="sidebar d-flex flex-column">
    <div class="brand">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-trophy-fill text-warning fs-4"></i>
            <div>
                <h5>Booking Lapangan</h5>
                <p>Olahraga Management</p>
            </div>
        </div>
    </div>

    <ul class="nav flex-column py-3 flex-grow-1">
        <li class="nav-item">
            <a href="<?= str_contains($_SERVER['PHP_SELF'], '/booking/') || str_contains($_SERVER['PHP_SELF'], '/laporan/') ? '../' : '' ?>dashboard.php"
               class="nav-link <?= isActive('dashboard.php') ?>">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= str_contains($_SERVER['PHP_SELF'], '/booking/') ? '' : 'booking/' ?>index.php"
               class="nav-link <?= isActive('index.php', 'booking') ?>">
                <i class="bi bi-calendar3 me-2"></i> Data Booking
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= str_contains($_SERVER['PHP_SELF'], '/booking/') ? '' : 'booking/' ?>create.php"
               class="nav-link <?= isActive('create.php', 'booking') ?>">
                <i class="bi bi-plus-circle-fill me-2"></i> Tambah Booking
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= str_contains($_SERVER['PHP_SELF'], '/laporan/') ? '' : (str_contains($_SERVER['PHP_SELF'], '/booking/') ? '../laporan/' : 'laporan/') ?>booking_print.php"
               class="nav-link <?= isActive('booking_print.php', 'laporan') ?>">
                <i class="bi bi-printer-fill me-2"></i> Laporan
            </a>
        </li>
    </ul>

    <div class="user-info">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center"
                 style="width:36px;height:36px;flex-shrink:0;">
                <i class="bi bi-person-fill text-white"></i>
            </div>
            <div class="overflow-hidden">
                <div class="name"><?= htmlspecialchars($_SESSION['user_name']) ?></div>
                <div class="email"><?= htmlspecialchars($_SESSION['user_email']) ?></div>
            </div>
        </div>
        <a href="<?= str_contains($_SERVER['PHP_SELF'], '/booking/') || str_contains($_SERVER['PHP_SELF'], '/laporan/') ? '../' : '' ?>auth/logout.php"
           class="btn btn-sm btn-outline-light w-100 mt-2">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
        </a>
    </div>
</nav>
