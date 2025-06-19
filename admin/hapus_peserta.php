<?php
require_once "../auth/auth_check.php";
require_once "../config/database.php";
checkAuth(['admin']);

$id = $_GET['id'] ?? null;
if ($id) {
    // 1. Cari seminar yang dimiliki peserta ini
    $stmt = $pdo->prepare("SELECT id FROM seminars WHERE id_user = :id");
    $stmt->execute(['id' => $id]);
    $seminar_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // 2. Hapus approvals untuk semua seminar milik peserta ini
    if ($seminar_ids) {
        $in = str_repeat('?,', count($seminar_ids) - 1) . '?';
        $stmt = $pdo->prepare("DELETE FROM approvals WHERE id_seminar IN ($in)");
        $stmt->execute($seminar_ids);

        // 3. Hapus seminars milik peserta ini
        $stmt = $pdo->prepare("DELETE FROM seminars WHERE id IN ($in)");
        $stmt->execute($seminar_ids);
    }

    // 4. Hapus relasi mahasiswa_dosen
    $stmt = $pdo->prepare("DELETE FROM mahasiswa_dosen WHERE id_mahasiswa = :id");
    $stmt->execute(['id' => $id]);

    // 5. Hapus user
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id AND role = 'peserta'");
    $stmt->execute(['id' => $id]);

    header("Location: manage_peserta.php?msg=Data peserta & seminar terkait berhasil dihapus.");
    exit;
} else {
    header("Location: manage_peserta.php?msg=ID peserta tidak valid.");
    exit;
}
