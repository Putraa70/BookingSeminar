<?php
require_once "../auth/auth_check.php";
require_once "../config/database.php";
require_once "../functions/cek_jadwal_bentrok.php";

checkAuth(['peserta']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_SESSION['user_id'];
    $judul = trim($_POST['judul'] ?? '');
    $tanggal = $_POST['tanggal'] ?? '';
    $waktu_mulai = $_POST['waktu_mulai'] ?? '';
    $waktu_selesai = $_POST['waktu_selesai'] ?? '';
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    if (!$judul || !$tanggal || !$waktu_mulai || !$waktu_selesai || !$deskripsi) {
        $_SESSION['flash_message'] = "Semua field wajib diisi.";
        header("Location: booking.php");
        exit;
    }

    if (cekJadwalBentrok($pdo, $tanggal, $waktu_mulai, $waktu_selesai)) {
        $_SESSION['flash_message'] = "Jadwal bentrok dengan seminar lain yang sudah disetujui. Silakan pilih waktu lain.";
        header("Location: booking.php");
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Simpan data seminar
        $sqlInsertSeminar = "INSERT INTO seminars (id_user, judul, tanggal, waktu_mulai, waktu_selesai, deskripsi, status, created_at) 
        VALUES (:id_user, :judul, :tanggal, :waktu_mulai, :waktu_selesai, :deskripsi, 'pending', NOW())";
        $stmt = $pdo->prepare($sqlInsertSeminar);
        $stmt->execute([
            ':id_user' => $id_user,
            ':judul' => $judul,
            ':tanggal' => $tanggal,
            ':waktu_mulai' => $waktu_mulai,
            ':waktu_selesai' => $waktu_selesai,
            ':deskripsi' => $deskripsi
        ]);
        $id_seminar = $pdo->lastInsertId();

        // Ambil dosen pembimbing dan penguji untuk mahasiswa ini dari tabel mahasiswa_dosen
        $sqlDosen = "SELECT id_dosen FROM mahasiswa_dosen WHERE id_mahasiswa = :id_mahasiswa";
        $stmtDosen = $pdo->prepare($sqlDosen);
        $stmtDosen->execute([':id_mahasiswa' => $id_user]);
        $dosens = $stmtDosen->fetchAll(PDO::FETCH_COLUMN);

        if (!$dosens) {
            $pdo->rollBack();
            $_SESSION['flash_message'] = "Tidak ditemukan dosen pembimbing atau penguji untuk Anda. Hubungi admin.";
            header("Location: booking.php");
            exit;
        }

        // Masukkan record approvals untuk tiap dosen
        $sqlInsertApproval = "INSERT INTO approvals (id_seminar, id_user, status, created_at) VALUES (:id_seminar, :id_user, 'pending', NOW())";
        $stmtApproval = $pdo->prepare($sqlInsertApproval);
        foreach ($dosens as $dosen_id) {
            $stmtApproval->execute([
                ':id_seminar' => $id_seminar,
                ':id_user' => $dosen_id
            ]);
        }

        $pdo->commit();

        $_SESSION['flash_message'] = "Pengajuan booking seminar berhasil dikirim dan menunggu persetujuan dosen.";
        header("Location: index.php");
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['flash_message'] = "Terjadi kesalahan: " . $e->getMessage();
        header("Location: booking.php");
        exit;
    }
} else {
    header("Location: booking.php");
    exit;
}
