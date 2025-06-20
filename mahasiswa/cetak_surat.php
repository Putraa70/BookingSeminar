<?php
require_once "../auth/auth_check.php";
require_once "../config/database.php";

// Pastikan mahasiswa yang mengakses adalah peserta yang relevan
checkAuth(['peserta']);

// Ambil seminar_id dari parameter URL
$seminar_id = isset($_GET['seminar_id']) ? $_GET['seminar_id'] : null;
if (!$seminar_id) {
    // Jika seminar_id tidak ada, redirect ke dashboard mahasiswa
    header('Location: dashboard.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data seminar berdasarkan seminar_id
$sql = "
SELECT s.id, s.judul, s.tanggal, s.waktu_mulai, s.waktu_selesai, s.status,
       apd.status AS status_pembimbing, apd.komentar AS komentar_pembimbing,
       apg.status AS status_penguji, apg.komentar AS komentar_penguji,
       u.nama AS nama_peserta, d.nama AS nama_dosen_pembimbing,
       pg.nama AS nama_dosen_penguji
FROM seminars s
LEFT JOIN (
    SELECT a.*, u.role FROM approvals a
    JOIN users u ON a.id_user = u.id
    WHERE u.role = 'dosen_pembimbing'
) apd ON apd.id_seminar = s.id
LEFT JOIN (
    SELECT a.*, u.role FROM approvals a
    JOIN users u ON a.id_user = u.id
    WHERE u.role = 'dosen_penguji'
) apg ON apg.id_seminar = s.id
LEFT JOIN users u ON s.id_user = u.id
LEFT JOIN users d ON apd.id_user = d.id
LEFT JOIN users pg ON apg.id_user = pg.id
WHERE s.id = :seminar_id
AND s.id_user = :user_id
";

$stmt = $pdo->prepare($sql);
$stmt->execute([':seminar_id' => $seminar_id, ':user_id' => $user_id]);
$seminar = $stmt->fetch(PDO::FETCH_ASSOC);

// Pastikan seminar ditemukan
if (!$seminar) {
    // Jika seminar tidak ditemukan, redirect ke dashboard mahasiswa
    header('Location: dashboard.php');
    exit;
}

$title = "Surat Izin Seminar";
include "../includes/header.php";
include "../includes/navbar.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Izin Seminar</title>
    <!-- Link ke file CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        @media print {
  body * {
    visibility: hidden;  /* Sembunyikan semua elemen */
  }
  #printableArea, #printableArea * {
    visibility: visible;  /* Hanya tampilkan area yang dicetak */
  }
  #printableArea {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    font-family: Arial, sans-serif;
    padding: 20px;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
  }
  table td {
    padding: 8px;
    border: 1px solid #ddd;
  }
  h3 {
    text-align: center;
    font-size: 24px;
  }
  p {
    text-align: justify;
    font-size: 16px;
  }
  .btn {
    display: none;  /* Sembunyikan tombol saat mencetak */
  }

  /* Menambahkan styling untuk teks yang di sebelah kanan */
  .right-align {
    text-align: right;
  }
}

    </style>
</head>
<body>
    <main class="container mt-5">
        <div class="p-5 mb-4 bg-light rounded-3 shadow-sm text-center">
            <h1 class="display-4 fw-bold">Surat Izin Seminar</h1>
        </div>

        <section>
            <!-- Area yang akan dicetak -->
            <div id="printableArea">
                <h3>Surat Izin Seminar</h3>
                <p>Dengan hormat,</p>
                <p>Berikut adalah surat izin pelaksanaan seminar:</p>
                <table>
                    <tr>
                        <td><strong>Judul Seminar</strong></td>
                        <td>: <?= htmlspecialchars($seminar['judul'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Peserta</strong></td>
                        <td>: <?= htmlspecialchars($seminar['nama_peserta'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal</strong></td>
                        <td>: <?= htmlspecialchars($seminar['tanggal'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Waktu</strong></td>
                        <td>: <?= htmlspecialchars($seminar['waktu_mulai']) ?> - <?= htmlspecialchars($seminar['waktu_selesai']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Dosen Pembimbing</strong></td>
                        <td>: <?= htmlspecialchars($seminar['nama_dosen_pembimbing'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Status Pembimbing</strong></td>
                        <td>: <?= $seminar['status_pembimbing'] === 'approved' ? 'Disetujui' : 'Ditolak' ?></td>
                    </tr>
                    <tr>
                        <td><strong>Alasan Pembimbing</strong></td>
                        <td>: <?= nl2br(htmlspecialchars($seminar['komentar_pembimbing'] ?: '-')) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Dosen Penguji</strong></td>
                        <td>: <?= htmlspecialchars($seminar['nama_dosen_penguji'] ?: '-', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Status Penguji</strong></td>
                        <td>: <?= $seminar['status_penguji'] === 'approved' ? 'Disetujui' : 'Ditolak' ?></td>
                    </tr>
                    <tr>
                        <td><strong>Alasan Penguji</strong></td>
                        <td>: <?= nl2br(htmlspecialchars($seminar['komentar_penguji'] ?: '-')) ?></td>
                    </tr>
                </table>
                <p>Dimohon kepada penjaga gedung untuk memberikan izin dan fasilitas yang diperlukan selama pelaksanaan seminar berlangsung.</p>
                <p>Terima kasih atas perhatian dan kerjasamanya.</p>
                <p>Jakarta, <?= date('d F Y') ?></p>
                <p>Koordinator Seminar</p>
            </div>
        </section>

        <div class="text-center mt-4">
            <!-- Tombol untuk mencetak surat -->
            <button type="submit" class="btn btn-primary">Cetak Surat</button>
        </div>
    </main>

    <?php include "../includes/footer.php"; ?>
</body>
</html>
