<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$user_id           = $_SESSION['user_id'];
$id                = (int)($_POST['id'] ?? 0);
$nama_pemesan      = trim($_POST['nama_pemesan'] ?? '');
$jenis_lapangan    = trim($_POST['jenis_lapangan'] ?? '');
$tanggal_booking   = $_POST['tanggal_booking'] ?? '';
$jam_mulai         = $_POST['jam_mulai'] ?? '';
$jam_selesai       = $_POST['jam_selesai'] ?? '';
$total_bayar       = (int)($_POST['total_bayar'] ?? 0);
$status_pembayaran = trim($_POST['status_pembayaran'] ?? '');

if ($id === 0 || empty($nama_pemesan) || empty($jenis_lapangan) || empty($tanggal_booking)
    || empty($jam_mulai) || empty($jam_selesai) || empty($status_pembayaran)) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare("UPDATE booking SET nama_pemesan=?, jenis_lapangan=?, tanggal_booking=?, jam_mulai=?, jam_selesai=?, total_bayar=?, status_pembayaran=?, updated_at=NOW() WHERE id=? AND user_id=?");
$stmt->bind_param("sssssisii", $nama_pemesan, $jenis_lapangan, $tanggal_booking, $jam_mulai, $jam_selesai, $total_bayar, $status_pembayaran, $id, $user_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $_SESSION['success'] = 'Booking berhasil diperbarui!';
} else {
    $_SESSION['success'] = 'Tidak ada perubahan atau akses ditolak.';
}
$stmt->close();
header("Location: index.php");
exit;
