<?php
require_once "../auth/auth_check.php"; // Memeriksa autentikasi pengguna
require_once "../config/database.php"; // Menghubungkan ke database

checkAuth(['admin']); // Memastikan hanya admin yang bisa mengakses

// Memeriksa apakah parameter 'id_seminar' ada dalam URL
if (!isset($_GET['id_seminar']) || empty($_GET['id_seminar'])) {
    echo "ID seminar tidak valid.";
    exit;
}

// Mendapatkan ID seminar dari parameter URL
$id_seminar = $_GET['id_seminar'];

// Debugging: Pastikan ID diterima
echo "ID Seminar: " . $id_seminar; // Cek apakah ID diterima

// Mengecek apakah ID seminar ada dalam database
$stmt = $pdo->prepare("SELECT * FROM seminar WHERE id_seminar = :id_seminar");
$stmt->execute(['id_seminar' => $id_seminar]);
$seminar = $stmt->fetch();

if (!$seminar) {
    // Jika seminar tidak ditemukan
    echo "Seminar dengan ID tersebut tidak ditemukan.";
    exit;
}

try {
    // Mulai transaksi
    $pdo->beginTransaction();

    // Hapus data terkait di tabel approvals (foreign key)
    $stmt1 = $pdo->prepare("DELETE FROM approvals WHERE id_seminar = :id_seminar");
    $stmt1->execute(['id_seminar' => $id_seminar]);

    // Hapus data terkait di tabel mahasiswa_dosen
    $stmt2 = $pdo->prepare("DELETE FROM mahasiswa_dosen WHERE id_seminar = :id_seminar");
    $stmt2->execute(['id_seminar' => $id_seminar]);

    // Hapus seminar dari tabel seminar
    $stmt3 = $pdo->prepare("DELETE FROM seminar WHERE id_seminar = :id_seminar");
    $stmt3->execute(['id_seminar' => $id_seminar]);

    // Commit transaksi
    $pdo->commit();

    // Redirect ke halaman manajemen seminar dengan pesan sukses
    header("Location: manage_seminar.php?msg=Seminar berhasil dihapus.");
    exit;
} catch (PDOException $e) {
    // Rollback transaksi jika terjadi error
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
    exit;
}
?>
