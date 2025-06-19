<?php
require_once "../auth/auth_check.php";
require_once "../config/database.php";

checkAuth(['peserta']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $user_id = $_SESSION['user_id'];

    if (!$id) {
        $_SESSION['flash_message'] = "ID booking tidak valid.";
        header("Location: index.php");
        exit;
    }

    // Cek apakah booking milik user dan status masih pending
    $stmt = $pdo->prepare("SELECT * FROM seminars WHERE id = :id AND id_user = :user_id AND status = 'pending'");
    $stmt->execute(['id' => $id, 'user_id' => $user_id]);
    $seminar = $stmt->fetch();

    if (!$seminar) {
        $_SESSION['flash_message'] = "Booking tidak ditemukan atau tidak dapat dibatalkan.";
        header("Location: index.php");
        exit;
    }

    // Hapus booking
    $stmtDel = $pdo->prepare("DELETE FROM seminars WHERE id = :id");
    $stmtDel->execute(['id' => $id]);

    $_SESSION['flash_message'] = "Booking seminar berhasil dibatalkan.";
    header("Location: index.php");
    exit;
} else {
    header("Location: index.php");
    exit;
}
