<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../config/database.php';

$user_id = $_SESSION['user_id'];
$id = (int)($_GET['id'] ?? 0);

if ($id === 0) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare("DELETE FROM booking WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $_SESSION['success'] = 'Booking berhasil dihapus!';
} else {
    $_SESSION['success'] = 'Data tidak ditemukan atau Anda tidak memiliki akses.';
}
$stmt->close();
header("Location: index.php");
exit;
