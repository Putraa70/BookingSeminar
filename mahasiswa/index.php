<?php
require_once "../auth/auth_check.php";
require_once "../config/database.php";

checkAuth(['peserta']);
$user_id = $_SESSION['user_id'];

// Ambil data seminar mahasiswa beserta status approval dosen pembimbing dan penguji
$sql = "
SELECT s.id, s.judul, s.tanggal, s.waktu_mulai, s.waktu_selesai, s.status,
       apd.status AS status_pembimbing, apd.komentar AS komentar_pembimbing,
       apg.status AS status_penguji, apg.komentar AS komentar_penguji,
       u.nama AS nama_peserta, d.nama AS nama_dosen_pembimbing
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
JOIN users u ON s.id_user = u.id
JOIN users d ON apd.id_user = d.id
WHERE s.id_user = :id_user
ORDER BY s.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id_user' => $user_id]);
$seminars = $stmt->fetchAll(PDO::FETCH_ASSOC);

$title = "Dashboard Mahasiswa";
include "../includes/header.php";
include "../includes/navbar.php";
?>

<main class="container mt-5">
  <div class="p-5 mb-4 bg-light rounded-3 shadow-sm text-center">
    <h1 class="display-4 fw-bold">Selamat datang, <?= htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8') ?>!</h1>
    <p class="fs-5 text-muted">Kelola pengajuan seminar dan lihat status persetujuan dosen pembimbing serta dosen penguji.</p>
    <a href="booking.php" class="btn btn-primary btn-lg mt-3">Ajukan Booking Seminar</a>
    <a href="view_jadwal.php" class="btn btn-secondary btn-lg mt-3 ms-2">Lihat Jadwal Seminar</a>
  </div>

  <section>
    <h3 class="mb-3">Daftar Booking Seminar Anda</h3>

    <?php if (empty($seminars)): ?>
      <div class="alert alert-info">Anda belum memiliki pengajuan seminar.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Judul Seminar</th>
              <th>Tanggal</th>
              <th>Waktu</th>
              <th>Status Seminar</th>
              <th>Status Dosen Pembimbing</th>
              <th>Alasan Pembimbing</th>
              <th>Status Dosen Penguji</th>
              <th>Alasan Penguji</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($seminars as $seminar): ?>
              <tr>
                <td><?= htmlspecialchars($seminar['judul'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($seminar['tanggal'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($seminar['waktu_mulai']) ?> - <?= htmlspecialchars($seminar['waktu_selesai']) ?></td>
                <td>
                  <?php
                    switch ($seminar['status']) {
                      case 'pending':
                        echo '<span class="badge bg-warning text-dark">Pending</span>';
                        break;
                      case 'approved':
                        echo '<span class="badge bg-success">Disetujui</span>';
                        break;
                      case 'rejected':
                        echo '<span class="badge bg-danger">Ditolak</span>';
                        break;
                      default:
                        echo htmlspecialchars($seminar['status']);
                    }
                  ?>
                </td>
                <td>
                  <?= $seminar['status_pembimbing']
                      ? '<span class="badge ' . ($seminar['status_pembimbing'] === 'approved' ? 'bg-success' : 'bg-danger') . '">' . ucfirst($seminar['status_pembimbing']) . '</span>'
                      : '-' ?>
                </td>
                <td>
                  <?= ($seminar['status_pembimbing'] === 'rejected') ? nl2br(htmlspecialchars($seminar['komentar_pembimbing'] ?: '-')) : '-' ?>
                </td>
                <td>
                  <?= $seminar['status_penguji']
                      ? '<span class="badge ' . ($seminar['status_penguji'] === 'approved' ? 'bg-success' : 'bg-danger') . '">' . ucfirst($seminar['status_penguji']) . '</span>'
                      : '-' ?>
                </td>
                <td>
                  <?= ($seminar['status_penguji'] === 'rejected') ? nl2br(htmlspecialchars($seminar['komentar_penguji'] ?: '-')) : '-' ?>
                </td>
                <td>
                  <?php if ($seminar['status'] === 'approved' && $seminar['status_pembimbing'] === 'approved' && $seminar['status_penguji'] === 'approved'): ?>
                    <!-- Button untuk Cetak Surat Izin Seminar -->
                    <a href="cetak_surat.php?seminar_id=<?= $seminar['id'] ?>" class="btn btn-primary btn-sm">Cetak Surat Izin</a>
                  <?php else: ?>
                    <button class="btn btn-secondary btn-sm" disabled>Tunggu Persetujuan</button>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>

</main>

<?php include "../includes/footer.php"; ?>

<script src="../assets/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
