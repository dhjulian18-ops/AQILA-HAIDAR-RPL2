<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../config/database.php';

$user_id = $_SESSION['user_id'];

// Filter pencarian
$search = trim($_GET['search'] ?? '');
$filter_status = $_GET['status'] ?? '';
$filter_lapangan = $_GET['lapangan'] ?? '';

$where = "WHERE b.user_id = $user_id";
$params = [];
$types = '';

if ($search !== '') {
    $where .= " AND b.nama_pemesan LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}
if ($filter_status !== '') {
    $where .= " AND b.status_pembayaran = ?";
    $params[] = $filter_status;
    $types .= 's';
}
if ($filter_lapangan !== '') {
    $where .= " AND b.jenis_lapangan = ?";
    $params[] = $filter_lapangan;
    $types .= 's';
}

$sql = "SELECT * FROM booking b $where ORDER BY b.id DESC";

if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

$success_msg = $_SESSION['success'] ?? '';
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Booking - Booking Lapangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <?php include '../includes/topbar.php'; ?>

    <div class="page-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Data Booking</h4>
                <p class="text-muted small mb-0">Kelola semua data booking lapangan Anda</p>
            </div>
            <a href="create.php" class="btn btn-success">
                <i class="bi bi-plus-circle-fill me-1"></i> Tambah Booking
            </a>
        </div>

        <?php if ($success_msg): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success_msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Filter -->
        <div class="card table-card shadow-sm mb-3">
            <div class="card-body py-2">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-sm-4">
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="Cari nama pemesan..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-sm-3">
                        <select name="lapangan" class="form-select form-select-sm">
                            <option value="">Semua Lapangan</option>
                            <?php foreach(['Badminton','Futsal','Basket','Voli'] as $l): ?>
                                <option value="<?= $l ?>" <?= $filter_lapangan === $l ? 'selected' : '' ?>><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <?php foreach(['Lunas','DP','Belum Lunas'] as $s): ?>
                                <option value="<?= $s ?>" <?= $filter_status === $s ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-2 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-success flex-fill">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="index.php" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card table-card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="40">No</th>
                                <th>Pemesan</th>
                                <th>Lapangan</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Total Bayar</th>
                                <th>Status</th>
                                <th width="130">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0):
                                $no = 1;
                                while ($row = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td class="text-muted"><?= $no++ ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($row['nama_pemesan']) ?></td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                        <?= htmlspecialchars($row['jenis_lapangan']) ?>
                                    </span>
                                </td>
                                <td><?= date('d M Y', strtotime($row['tanggal_booking'])) ?></td>
                                <td class="text-nowrap">
                                    <?= date('H:i', strtotime($row['jam_mulai'])) ?> -
                                    <?= date('H:i', strtotime($row['jam_selesai'])) ?>
                                </td>
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
                                <td>
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger"
                                            title="Hapus"
                                            onclick="confirmDelete(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['nama_pemesan'])) ?>')">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                    Tidak ada data booking.
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

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold">Konfirmasi Hapus</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-3">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-2 mb-2 d-block"></i>
                <p class="mb-0">Hapus booking <strong id="deleteTarget"></strong>?</p>
                <p class="text-muted small">Tindakan ini tidak bisa dibatalkan.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="deleteConfirmBtn" class="btn btn-sm btn-danger">Hapus</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmDelete(id, name) {
    document.getElementById('deleteTarget').textContent = name;
    document.getElementById('deleteConfirmBtn').href = 'delete.php?id=' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
</body>
</html>
