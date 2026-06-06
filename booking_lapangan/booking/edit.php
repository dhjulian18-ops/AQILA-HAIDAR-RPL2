<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../config/database.php';

$user_id = $_SESSION['user_id'];
$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM booking WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if (!$booking) {
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"></head>
    <body class="bg-light"><div class="container text-center py-5">
    <i class="bi bi-shield-x fs-1 text-danger"></i>
    <h4 class="mt-3">Akses Ditolak</h4>
    <p class="text-muted">Data tidak ditemukan atau Anda tidak memiliki akses.</p>
    <a href="index.php" class="btn btn-success">Kembali ke Daftar Booking</a>
    </div></body></html>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Booking - Booking Lapangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <?php include '../includes/topbar.php'; ?>

    <div class="page-content">
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="index.php" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0">Edit Booking</h4>
                <p class="text-muted small mb-0">Ubah data booking yang dipilih</p>
            </div>
        </div>

        <div class="card form-card shadow-sm" style="max-width: 700px;">
            <div class="card-body p-4">
                <form method="POST" action="update.php">
                    <input type="hidden" name="id" value="<?= $booking['id'] ?>">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Pemesan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pemesan" class="form-control"
                                   value="<?= htmlspecialchars($booking['nama_pemesan']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jenis Lapangan <span class="text-danger">*</span></label>
                            <select name="jenis_lapangan" class="form-select" required>
                                <?php foreach(['Badminton','Futsal','Basket','Voli'] as $l): ?>
                                    <option value="<?= $l ?>" <?= $booking['jenis_lapangan'] === $l ? 'selected' : '' ?>><?= $l ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Booking <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_booking" class="form-control"
                                   value="<?= $booking['tanggal_booking'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_mulai" class="form-control"
                                   value="<?= $booking['jam_mulai'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_selesai" class="form-control"
                                   value="<?= $booking['jam_selesai'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Total Bayar (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="total_bayar" class="form-control"
                                   value="<?= $booking['total_bayar'] ?>" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status Pembayaran <span class="text-danger">*</span></label>
                            <select name="status_pembayaran" class="form-select" required>
                                <?php foreach(['Lunas','DP','Belum Lunas'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $booking['status_pembayaran'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 pt-2">
                            <button type="submit" class="btn btn-warning px-4">
                                <i class="bi bi-save-fill me-2"></i>Update Booking
                            </button>
                            <a href="index.php" class="btn btn-outline-secondary ms-2">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
