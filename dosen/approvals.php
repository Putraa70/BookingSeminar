<?php
require_once "../auth/auth_check.php";
require_once "../config/database.php";
checkAuth(['dosen_pembimbing', 'dosen_penguji']);

$id_user = $_SESSION['user_id'];

// 1. Seminar yang butuh persetujuan (pending, approval dosen ini masih null/pending)
$sql_pending = "SELECT s.*, u.nama AS nama_peserta, a.status AS status_approval, a.komentar, a.id AS approval_id
        FROM seminars s
        JOIN users u ON s.id_user = u.id
        LEFT JOIN approvals a ON a.id_seminar = s.id AND a.id_user = :id_user
        WHERE s.status = 'pending' AND (a.status IS NULL OR a.status = 'pending')";
$stmt = $pdo->prepare($sql_pending);
$stmt->execute([':id_user' => $id_user]);
$seminars_pending = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Riwayat pengajuan yang pernah di-approve/tolak/masih pending (oleh dosen ini)
$sql_riwayat = "SELECT s.*, u.nama AS nama_peserta, a.status AS status_approval, a.komentar, a.id AS approval_id
        FROM seminars s
        JOIN users u ON s.id_user = u.id
        JOIN approvals a ON a.id_seminar = s.id AND a.id_user = :id_user
        ORDER BY s.tanggal DESC, s.waktu_mulai DESC";
$stmt = $pdo->prepare($sql_riwayat);
$stmt->execute([':id_user' => $id_user]);
$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);

$title = "Persetujuan Seminar";
include "../includes/header.php";
include "../includes/navbar.php";
?>

<div class="container mt-5 mb-5">
  <h2>Daftar Pengajuan Seminar yang Perlu Disetujui</h2>
  <?php if (!$seminars_pending): ?>
    <p class="text-muted">Tidak ada pengajuan seminar yang perlu disetujui.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
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
          <?php foreach ($seminars_pending as $seminar): ?>
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
    </div>
  <?php endif; ?>

  <hr class="my-5">

  <h3>Riwayat Persetujuan Seminar Anda</h3>
  <?php if (!$riwayat): ?>
    <p class="text-muted">Belum ada riwayat persetujuan seminar.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead>
          <tr>
            <th>Judul Seminar</th>
            <th>Peserta</th>
            <th>Tanggal</th>
            <th>Waktu</th>
            <th>Status Persetujuan Anda</th>
            <th>Alasan Penolakan</th>
            <th>Detail</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($riwayat as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['judul']) ?></td>
              <td><?= htmlspecialchars($r['nama_peserta']) ?></td>
              <td><?= htmlspecialchars($r['tanggal']) ?></td>
              <td><?= htmlspecialchars($r['waktu_mulai']) ?> - <?= htmlspecialchars($r['waktu_selesai']) ?></td>
              <td>
                <?php
                  if ($r['status_approval'] === 'approved') {
                      echo '<span class="badge bg-success">Disetujui</span>';
                  } elseif ($r['status_approval'] === 'rejected') {
                      echo '<span class="badge bg-danger">Ditolak</span>';
                  } else {
                      echo '<span class="badge bg-secondary">Pending</span>';
                  }
                ?>
              </td>
              <td><?= ($r['status_approval'] === 'rejected') ? nl2br(htmlspecialchars($r['komentar'])) : '-' ?></td>
              <td>
                <a href="approve_action.php?id=<?= $r['id'] ?>" class="btn btn-info btn-sm">Detail</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
