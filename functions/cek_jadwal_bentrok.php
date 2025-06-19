<?php
require_once "../config/database.php";

/**
 * Fungsi untuk cek jadwal bentrok di tabel seminars.
 * @param PDO $pdo Koneksi database
 * @param string $tanggal Tanggal seminar (Y-m-d)
 * @param string $waktu_mulai Jam mulai seminar (H:i:s)
 * @param string $waktu_selesai Jam selesai seminar (H:i:s)
 * @return bool true jika bentrok, false jika tidak bentrok
 */
function cekJadwalBentrok($pdo, $tanggal, $waktu_mulai, $waktu_selesai) {
    $sql = "SELECT COUNT(*) FROM seminars
            WHERE tanggal = :tanggal
            AND status = 'approved'
            AND (
                (:waktu_mulai BETWEEN waktu_mulai AND waktu_selesai)
                OR (:waktu_selesai BETWEEN waktu_mulai AND waktu_selesai)
                OR (waktu_mulai BETWEEN :waktu_mulai AND :waktu_selesai)
            )";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':tanggal' => $tanggal,
        ':waktu_mulai' => $waktu_mulai,
        ':waktu_selesai' => $waktu_selesai
    ]);
    $count = $stmt->fetchColumn();
    return $count > 0;
}
