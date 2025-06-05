<?php
require_once "../auth/auth_check.php";
require_once "../config/database.php";
checkAuth(['dosen_pembimbing', 'dosen_penguji']);

$id_user = $_SESSION['user_id'];

$sql = "SELECT s.*, u.nama AS nama_peserta, a.status AS status_approval, a.komentar, a.id AS approval_id
        FROM seminars s
        JOIN users u ON s.id_user = u.id
        LEFT JOIN approvals a ON a.id_seminar = s.id AND a.id_user = :id_user
        WHERE s.status = 'pending'";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id_user' => $id_user]);
$seminars = $stmt->fetchAll(PDO::FETCH_ASSOC);

$title = "Persetujuan Seminar";
include "../includes/header.php";
include "../includes/navbar.php";
?>

<div class="container mt-5 mb-5">
  <h2>Daftar Pengajuan Seminar yang Perlu Disetujui</h2>
  <?php if (!$seminars): ?>
    <p>Tidak ada pengajuan seminar yang perlu disetujui.</p>
  <?php else: ?>
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Judul Seminar</th>
          <th>Peserta</th>
          <th>Tanggal</th>
          <th>Waktu</th>
          <th>Status Persetujuan Anda</th>
          <th>Alasan Penolakan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($seminars as $seminar): ?>
          <tr>
            <td><?= htmlspecialchars($seminar['judul']) ?></td>
            <td><?= htmlspecialchars($seminar['nama_peserta']) ?></td>
            <td><?= htmlspecialchars($seminar['tanggal']) ?></td>
            <td><?= htmlspecialchars($seminar['waktu_mulai']) ?> - <?= htmlspecialchars($seminar['waktu_selesai']) ?></td>
            <td><?= htmlspecialchars($seminar['status_approval'] ?? 'Belum disetujui') ?></td>
            <td>
              <?= ($seminar['status_approval'] === 'rejected') ? nl2br(htmlspecialchars($seminar['komentar'])) : '-' ?>
            </td>
            <td>
              <a href="approve_action.php?id=<?= $seminar['id'] ?>" class="btn btn-primary btn-sm">Detail & Approve</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php
include "../includes/footer.php";
?>
