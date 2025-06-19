<?php
require_once "../auth/auth_check.php";
require_once "../config/database.php";

checkAuth(['admin']);

$id = $_GET['id'] ?? null;
if ($id) {
    // 1. Hapus approval yang dibuat dosen ini
    $stmt = $pdo->prepare("DELETE FROM approvals WHERE id_user = :id");
    $stmt->execute(['id' => $id]);

    // 2. Hapus relasi dosen di mahasiswa_dosen
    $stmt = $pdo->prepare("DELETE FROM mahasiswa_dosen WHERE id_dosen = :id");
    $stmt->execute(['id' => $id]);

    // 3. Jika di tabel seminars dosen dipakai sebagai pembimbing/penguji, set ke NULL (opsional/menyesuaikan constraint di DB)
    // Contoh jika ingin kosongkan relasi (tidak hapus seminar):
    // $stmt = $pdo->prepare("UPDATE seminars SET id_pembimbing = NULL WHERE id_pembimbing = :id");
    // $stmt->execute(['id' => $id]);
    // $stmt = $pdo->prepare("UPDATE seminars SET id_penguji = NULL WHERE id_penguji = :id");
    // $stmt->execute(['id' => $id]);

    // 4. Hapus user dosen
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id AND role IN ('dosen_pembimbing', 'dosen_penguji')");
    $stmt->execute(['id' => $id]);

    header("Location: manage_dosen.php?msg=Data dosen dan approval terkait berhasil dihapus.");
    exit;
} else {
    header("Location: manage_dosen.php?msg=ID dosen tidak valid.");
    exit;
}
