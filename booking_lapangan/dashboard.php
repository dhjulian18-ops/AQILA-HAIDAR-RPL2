<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}
require_once 'config/database.php';

$user_id = $_SESSION['user_id'];

// Total booking
$r1 = $conn->query("SELECT COUNT(*) as total FROM booking WHERE user_id = $user_id");
$total_booking = $r1->fetch_assoc()['total'];

// Total lunas
$r2 = $conn->query("SELECT COUNT(*) as total FROM booking WHERE user_id = $user_id AND status_pembayaran = 'Lunas'");
$total_lunas = $r2->fetch_assoc()['total'];

// Total DP / belum lunas
$r3 = $conn->query("SELECT COUNT(*) as total FROM booking WHERE user_id = $user_id AND status_pembayaran != 'Lunas'");
$total_belum = $r3->fetch_assoc()['total'];

// Total pendapatan
$r4 = $conn->query("SELECT SUM(total_bayar) as total FROM booking WHERE user_id = $user_id");
$total_pendapatan = $r4->fetch_assoc()['total'] ?? 0;

// 5 booking terbaru
$recent = $conn->query("SELECT * FROM booking WHERE user_id = $user_id ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Booking Lapangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <?php include 'includes/topbar.php'; ?>

    <div class="page-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Dashboard</h4>
                <p class="text-muted small mb-0">Selamat datang, <?= htmlspecialchars($_SESSION['user_name']) ?>!</p>
            </div>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                <i class="bi bi-calendar3 me-1"></i><?= date('d M Y') ?>
            </span>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted small mb-1">Total Booking</p>
                                <h3 class="fw-bold mb-0"><?= $total_booking ?></h3>
                            </div>
                            <div class="stat-icon bg-primary-subtle text-primary">
                                <i class="bi bi-calendar-check-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted small mb-1">Booking Lunas</p>
                                <h3 class="fw-bold mb-0"><?= $total_lunas ?></h3>
                            </div>
                            <div class="stat-icon bg-success-subtle text-success">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted small mb-1">DP / Belum Lunas</p>
                                <h3 class="fw-bold mb-0"><?= $total_belum ?></h3>
                            </div>
                            <div class="stat-icon bg-warning-subtle text-warning">
                                <i class="bi bi-clock-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted small mb-1">Total Pendapatan</p>
                                <h3 class="fw-bold mb-0 fs-5">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h3>
                            </div>
                            <div class="stat-icon bg-info-subtle text-info">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="card table-card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-success"></i>5 Booking Terbaru</h6>
                <a href="booking/index.php" class="btn btn-sm btn-outline-success">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Pemesan</th>
                                <th>Lapangan</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Total Bayar</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent->num_rows > 0): ?>
                                <?php while ($row = $recent->fetch_assoc()): ?>
                                <tr>
                                    <td class="fw-semibold"><?= htmlspecialchars($row['nama_pemesan']) ?></td>
                                    <td><?= htmlspecialchars($row['jenis_lapangan']) ?></td>
                                    <td><?= date('d M Y', strtotime($row['tanggal_booking'])) ?></td>
                                    <td><?= date('H:i', strtotime($row['jam_mulai'])) ?> - <?= date('H:i', strtotime($row['jam_selesai'])) ?></td>
                                    <td>Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                                    <td>
                                        <?php
                                        $status = $row['status_pembayaran'];
                                        $badgeClass = match($status) {
                                            'Lunas' => 'badge-lunas',
                                            'DP' => 'badge-dp',
                                            default => 'badge-belum'
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                        Belum ada data booking.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
