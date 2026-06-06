<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: create.php");
    exit;
}

$user_id           = $_SESSION['user_id'];
$nama_pemesan      = trim($_POST['nama_pemesan'] ?? '');
$jenis_lapangan    = trim($_POST['jenis_lapangan'] ?? '');
$tanggal_booking   = $_POST['tanggal_booking'] ?? '';
$jam_mulai         = $_POST['jam_mulai'] ?? '';
$jam_selesai       = $_POST['jam_selesai'] ?? '';
$total_bayar       = (int)($_POST['total_bayar'] ?? 0);
$status_pembayaran = trim($_POST['status_pembayaran'] ?? '');

if (empty($nama_pemesan) || empty($jenis_lapangan) || empty($tanggal_booking)
    || empty($jam_mulai) || empty($jam_selesai) || $total_bayar < 0 || empty($status_pembayaran)) {
    header("Location: create.php");
    exit;
}

$stmt = $conn->prepare("INSERT INTO booking (user_id, nama_pemesan, jenis_lapangan, tanggal_booking, jam_mulai, jam_selesai, total_bayar, status_pembayaran) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssssss", $user_id, $nama_pemesan, $jenis_lapangan, $tanggal_booking, $jam_mulai, $jam_selesai, $total_bayar, $status_pembayaran);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Booking berhasil ditambahkan!';
    header("Location: index.php");
} else {
    header("Location: create.php");
}
$stmt->close();
exit;
