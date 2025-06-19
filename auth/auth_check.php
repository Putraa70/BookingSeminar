<?php
// Cek apakah sesi sudah dimulai, jika belum, mulai sesi
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function checkAuth(array $allowed_roles = []) {
    // Cek apakah user sudah login
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../auth/login.php");
        exit;
    }

    // Jika ada filter role, cek role user
    if (!empty($allowed_roles)) {
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
            // Role tidak sesuai, redirect ke halaman yang sesuai atau logout
            // Misal redirect ke login supaya user login ulang
            header("Location: ../auth/login.php");
            exit;
        }
    }
}
?>
