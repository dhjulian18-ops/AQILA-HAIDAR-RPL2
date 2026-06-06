<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Booking - Booking Lapangan</title>
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
                <h4 class="fw-bold mb-0">Tambah Booking</h4>
                <p class="text-muted small mb-0">Isi form untuk menambah data booking baru</p>
            </div>
        </div>

        <div class="card form-card shadow-sm" style="max-width: 700px;">
            <div class="card-body p-4">
                <form method="POST" action="store.php">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Pemesan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pemesan" class="form-control"
                                   placeholder="Masukkan nama pemesan" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jenis Lapangan <span class="text-danger">*</span></label>
                            <select name="jenis_lapangan" class="form-select" required>
                                <option value="">-- Pilih Jenis Lapangan --</option>
                                <option value="Badminton">Badminton</option>
                                <option value="Futsal">Futsal</option>
                                <option value="Basket">Basket</option>
                                <option value="Voli">Voli</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Booking <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_booking" class="form-control" required
                                   min="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_selesai" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Total Bayar (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="total_bayar" class="form-control"
                                   placeholder="Contoh: 150000" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status Pembayaran <span class="text-danger">*</span></label>
                            <select name="status_pembayaran" class="form-select" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Lunas">Lunas</option>
                                <option value="DP">DP</option>
                                <option value="Belum Lunas">Belum Lunas</option>
                            </select>
                        </div>
                        <div class="col-12 pt-2">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-save-fill me-2"></i>Simpan Booking
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
