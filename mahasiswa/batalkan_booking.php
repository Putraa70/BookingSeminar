<?php
require_once "../auth/auth_check.php";
require_once "../config/database.php";
checkAuth(['peserta']);

$user_id = $_SESSION['user_id'] ?? null;
$id = $_GET['id'] ?? null;

if (!$id || !$user_id) {
    header("Location: index.php?msg=ID tidak valid.");
    exit;
}

// Pastikan seminar milik sendiri dan masih pending, belum diapprove dosen
$stmt = $pdo->prepare("SELECT s.*,
    (SELECT COUNT(*) FROM approvals WHERE id_seminar = s.id AND status = 'approved') AS sudah_approve
    FROM seminars s
    WHERE id = :id AND id_user = :user_id AND status = 'pending'");
$stmt->execute(['id' => $id, 'user_id' => $user_id]);
$seminar = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$seminar) {
    header("Location: index.php?msg=Seminar tidak bisa dibatalkan (sudah diproses atau bukan milik Anda).");
    exit;
}

// Sudah di-approve dosen, tidak bisa dibatalkan
if ($seminar['sudah_approve'] > 0) {
    header("Location: index.php?msg=Tidak bisa membatalkan: Pengajuan sudah disetujui dosen.");
    exit;
}

try {
    $pdo->beginTransaction();
    // Hapus approval terkait
    $pdo->prepare("DELETE FROM approvals WHERE id_seminar = :id")->execute(['id' => $id]);
    // Hapus seminar
    $pdo->prepare("DELETE FROM seminars WHERE id = :id")->execute(['id' => $id]);
    $pdo->commit();
    header("Location: index.php?msg=Pengajuan seminar berhasil dibatalkan.");
    exit;
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    header("Location: index.php?msg=Gagal membatalkan seminar: " . urlencode($e->getMessage()));
    exit;
}
?>
