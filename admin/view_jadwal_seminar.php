<?php
require_once "../auth/auth_check.php";
require_once "../config/database.php";

checkAuth(['admin', 'dosen_pembimbing', 'dosen_penguji']);

// Ambil semua jadwal seminar dengan status dan peserta
$sql = "
SELECT s.id, s.judul, s.tanggal, s.waktu_mulai, s.waktu_selesai, s.status, u.nama AS nama_peserta
FROM seminars s
JOIN users u ON s.id_user = u.id
ORDER BY s.tanggal, s.waktu_mulai
";
$stmt = $pdo->query($sql);
$seminars = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Siapkan array untuk menandai jadwal yang sudah terpakai
$jadwal_terpakai = [];
foreach ($seminars as $seminar) {
    if ($seminar['status'] === 'approved') {
        $jadwal_terpakai[$seminar['tanggal']][] = $seminar;
    }
}

$title = "Lihat Jadwal Seminar";
include "../includes/header.php";
include "../includes/navbar.php";
?>

<main class="container mt-5">
  <h2 class="mb-4">Jadwal Seminar Seluruh Mahasiswa</h2>

  <?php if (!$seminars): ?>
    <p class="text-muted">Belum ada jadwal seminar yang diajukan.</p>
  <?php else: ?>
    <div class="table-responsive mb-4">
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>Judul Seminar</th>
            <th>Peserta</th>
            <th>Tanggal</th>
            <th>Waktu</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($seminars as $seminar): ?>
            <tr>
              <td><?= htmlspecialchars($seminar['judul'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($seminar['nama_peserta'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($seminar['tanggal'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($seminar['waktu_mulai']) ?> - <?= htmlspecialchars($seminar['waktu_selesai']) ?></td>
              <td>
                <?php
                switch ($seminar['status']) {
                  case 'approved':
                    echo '<span class="badge bg-success">Disetujui</span>';
                    break;
                  case 'pending':
                    echo '<span class="badge bg-warning text-dark">Pending</span>';
                    break;
                  case 'rejected':
                    echo '<span class="badge bg-danger">Ditolak</span>';
                    break;
                  default:
                    echo htmlspecialchars($seminar['status']);
                }
                ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <h3>Ringkasan Jadwal Harian</h3>
    <?php
    // Ambil tanggal unik dari data seminar
    $tanggal_unik = array_unique(array_column($seminars, 'tanggal'));
    sort($tanggal_unik);

    if (!$tanggal_unik) {
        echo '<p class="text-muted">Tidak ada jadwal untuk ditampilkan.</p>';
    } else {
        foreach ($tanggal_unik as $tgl):
            $seminar_hari_ini = $jadwal_terpakai[$tgl] ?? [];
    ?>
    <div class="mb-4">
      <h5><?= htmlspecialchars($tgl) ?></h5>
      <?php if (empty($seminar_hari_ini)): ?>
        <p><em>Hari ini belum ada seminar yang dijadwalkan (jadwal kosong).</em></p>
      <?php else: ?>
        <ul class="list-group">
          <?php foreach ($seminar_hari_ini as $sem): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <strong><?= htmlspecialchars($sem['judul']) ?></strong><br />
                oleh <?= htmlspecialchars($sem['nama_peserta']) ?><br />
                Jam: <?= htmlspecialchars($sem['waktu_mulai']) ?> - <?= htmlspecialchars($sem['waktu_selesai']) ?>
              </div>
              <span class="badge bg-success rounded-pill">Dipakai</span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
</main>

<?php include "../includes/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php
    }
  endif;