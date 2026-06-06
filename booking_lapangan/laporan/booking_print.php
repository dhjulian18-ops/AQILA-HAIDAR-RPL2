<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../config/database.php';

$user_id = $_SESSION['user_id'];

// Filter tanggal
$dari   = $_GET['dari'] ?? '';
$sampai = $_GET['sampai'] ?? '';

$where = "WHERE user_id = $user_id";
if ($dari)   $where .= " AND tanggal_booking >= '$dari'";
if ($sampai) $where .= " AND tanggal_booking <= '$sampai'";

$result  = $conn->query("SELECT * FROM booking $where ORDER BY tanggal_booking ASC, jam_mulai ASC");

// Summary
$r_total = $conn->query("SELECT COUNT(*) as c, SUM(total_bayar) as s FROM booking $where");
$summary = $r_total->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Booking - <?= htmlspecialchars($_SESSION['user_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
            body { font-size: 12px; }
        }
    </style>
</head>
<body>

<div class="no-print">
<?php include '../includes/sidebar.php'; ?>
</div>

<div class="main-content">
    <div class="no-print">
        <?php include '../includes/topbar.php'; ?>
    </div>

    <div class="page-content">
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <div>
                <h4 class="fw-bold mb-0">Laporan Booking</h4>
                <p class="text-muted small mb-0">Cetak laporan data booking Anda</p>
            </div>
            <button onclick="window.print()" class="btn btn-success">
                <i class="bi bi-printer-fill me-2"></i>Cetak Laporan
            </button>
        </div>

        <!-- Filter Tanggal -->
        <div class="card shadow-sm mb-4 no-print" style="border-radius:12px;">
            <div class="card-body py-2">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-sm-4">
                        <label class="form-label small fw-semibold mb-1">Dari Tanggal</label>
                        <input type="date" name="dari" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($dari) ?>">
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label small fw-semibold mb-1">Sampai Tanggal</label>
                        <input type="date" name="sampai" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($sampai) ?>">
                    </div>
                    <div class="col-sm-4 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="bi bi-funnel-fill me-1"></i>Filter
                        </button>
                        <a href="booking_print.php" class="btn btn-sm btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Print Header -->
        <div class="print-header text-center mb-4" style="border-bottom: 2px solid #198754; padding-bottom: 12px;">
            <h4 class="fw-bold text-success mb-1">LAPORAN BOOKING LAPANGAN OLAHRAGA</h4>
            <p class="mb-0">Admin: <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong></p>
            <p class="mb-0 text-muted small">
                Tanggal Cetak: <?= date('d F Y H:i') ?>
                <?php if ($dari || $sampai): ?>
                 &nbsp;|&nbsp; Periode: <?= $dari ? date('d M Y', strtotime($dari)) : '-' ?> s/d <?= $sampai ? date('d M Y', strtotime($sampai)) : '-' ?>
                <?php endif; ?>
            </p>
        </div>

        <!-- Summary -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6">
                <div class="card border-0 bg-success-subtle">
                    <div class="card-body py-2 text-center">
                        <p class="text-muted small mb-1">Total Booking</p>
                        <h5 class="fw-bold text-success mb-0"><?= $summary['c'] ?> Booking</h5>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card border-0 bg-info-subtle">
                    <div class="card-body py-2 text-center">
                        <p class="text-muted small mb-1">Total Pendapatan</p>
                        <h5 class="fw-bold text-info mb-0">Rp <?= number_format($summary['s'] ?? 0, 0, ',', '.') ?></h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Laporan -->
        <div class="card shadow-sm" style="border-radius:12px; overflow:hidden;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0" style="font-size:0.875rem;">
                        <thead class="table-success">
                            <tr>
                                <th width="40">No</th>
                                <th>Nama Pemesan</th>
                                <th>Jenis Lapangan</th>
                                <th>Tanggal Booking</th>
                                <th>Jam Mulai</th>
                                <th>Jam Selesai</th>
                                <th>Total Bayar</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0):
                                $no = 1;
                                while ($row = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_pemesan']) ?></td>
                                <td><?= htmlspecialchars($row['jenis_lapangan']) ?></td>
                                <td><?= date('d M Y', strtotime($row['tanggal_booking'])) ?></td>
                                <td><?= date('H:i', strtotime($row['jam_mulai'])) ?></td>
                                <td><?= date('H:i', strtotime($row['jam_selesai'])) ?></td>
                                <td>Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                                <td>
                                    <?php
                                    $s = $row['status_pembayaran'];
                                    $cls = match($s) { 'Lunas' => 'success', 'DP' => 'warning', default => 'danger' };
                                    ?>
                                    <span class="badge bg-<?= $cls ?>"><?= htmlspecialchars($s) ?></span>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-3 text-muted">Tidak ada data booking.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-light fw-semibold">
                            <tr>
                                <td colspan="6" class="text-end">Total Pendapatan:</td>
                                <td colspan="2">Rp <?= number_format($summary['s'] ?? 0, 0, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 text-muted small">
            <p>--- Akhir Laporan ---</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
