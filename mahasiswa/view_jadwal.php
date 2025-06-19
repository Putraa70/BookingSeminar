<?php
require_once "../auth/auth_check.php";
require_once "../config/database.php";

checkAuth(['peserta']);

// Ambil semua seminar mahasiswa
$sql = "
SELECT s.id, s.judul, s.tanggal, s.waktu_mulai, s.waktu_selesai, s.status, u.nama AS nama_peserta
FROM seminars s
JOIN users u ON s.id_user = u.id
ORDER BY s.tanggal ASC, s.waktu_mulai ASC
";
$stmt = $pdo->query($sql);
$seminars = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Siapkan array jadwal per hari (hanya yang approved)
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

<main class="container mt-5 mb-5">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h2 class="fw-bold">Jadwal Seminar Mahasiswa</h2>
    <span class="badge bg-info fs-6"><?= count($seminars) ?> seminar terdaftar</span>
  </div>

  <div class="card mb-4 shadow-sm border-0">
    <div class="card-body p-3">
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
          <thead class="table-light">
            <tr class="align-middle text-center">
              <th>Judul Seminar</th>
              <th>Peserta</th>
              <th>Tanggal</th>
              <th>Waktu</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$seminars): ?>
              <tr>
                <td colspan="5" class="text-center text-muted">Belum ada jadwal seminar yang diajukan.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($seminars as $seminar): ?>
                <tr>
                  <td><?= htmlspecialchars($seminar['judul'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($seminar['nama_peserta'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td class="text-center"><?= htmlspecialchars($seminar['tanggal'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td class="text-center"><?= htmlspecialchars($seminar['waktu_mulai']) ?> - <?= htmlspecialchars($seminar['waktu_selesai']) ?></td>
                  <td class="text-center">
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
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <h4 class="fw-semibold mb-3 mt-5"><i class="bi bi-calendar2-week"></i> Ringkasan Jadwal Per Hari (Terisi)</h4>
  <div class="row g-4">
    <?php
      // Daftar tanggal unique terurut
      $tanggal_unik = array_unique(array_column($seminars, 'tanggal'));
      sort($tanggal_unik);

      $has_content = false;
      foreach ($tanggal_unik as $tgl):
        $jadwal_hari = $jadwal_terpakai[$tgl] ?? [];
        if (!$jadwal_hari) continue;
        $has_content = true;
    ?>
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card shadow h-100 border-0">
          <div class="card-header bg-primary text-white fw-semibold">
            <i class="bi bi-calendar-event me-2"></i><?= htmlspecialchars($tgl) ?>
          </div>
          <ul class="list-group list-group-flush">
            <?php foreach ($jadwal_hari as $jadwal): ?>
              <li class="list-group-item">
                <div class="fw-bold mb-1"><?= htmlspecialchars($jadwal['judul']) ?></div>
                <div class="small text-muted mb-1">
                  oleh <?= htmlspecialchars($jadwal['nama_peserta']) ?>
                </div>
                <div>
                  <span class="badge bg-success"><i class="bi bi-clock"></i> <?= htmlspecialchars($jadwal['waktu_mulai']) ?> - <?= htmlspecialchars($jadwal['waktu_selesai']) ?></span>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    <?php endforeach; ?>

    <?php if (!$has_content): ?>
      <div class="col-12">
        <div class="alert alert-light border text-muted text-center">Belum ada seminar yang sudah terisi jadwalnya.</div>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php include "../includes/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
