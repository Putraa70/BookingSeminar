<?php
require_once "../auth/auth_check.php";
require_once "../config/database.php";

checkAuth(['admin']);

$msg = $_GET['msg'] ?? '';

// Query mengambil data seminar beserta nama peserta dan approval dosen pembimbing & penguji
$sql = "
SELECT 
    s.id, s.judul, s.tanggal, s.waktu_mulai, s.waktu_selesai, s.status, u.nama AS nama_peserta,
    apd.status AS status_pembimbing, apd.komentar AS komentar_pembimbing,
    apg.status AS status_penguji, apg.komentar AS komentar_penguji
FROM seminars s
JOIN users u ON s.id_user = u.id
LEFT JOIN approvals apd ON apd.id_seminar = s.id AND apd.id_user = (
    SELECT id FROM users WHERE role = 'dosen_pembimbing' LIMIT 1
)
LEFT JOIN approvals apg ON apg.id_seminar = s.id AND apg.id_user = (
    SELECT id FROM users WHERE role = 'dosen_penguji' LIMIT 1
)
ORDER BY s.tanggal DESC, s.waktu_mulai DESC
";

$stmt = $pdo->query($sql);
$seminars = $stmt->fetchAll(PDO::FETCH_ASSOC);

$title = "Kelola Seminar";
include "../includes/header.php";
include "../includes/admin_header.php";
include "../includes/admin_sidebar.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($title) ?></title>
  <link href="../assets/css/style.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body>
<!-- Header dan Sidebar sudah di-include -->

<main id="mainContent" class="content">
  <div class="container-fluid">
    <h2 class="mb-4">Daftar Seminar</h2>

    <?php if ($msg): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <a href="tambah_seminar.php" class="btn btn-success mb-3">
      <i class="bi bi-plus-circle"></i> Tambah Seminar
    </a>

    <?php if (empty($seminars)): ?>
      <p class="text-muted">Belum ada data seminar.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Judul</th>
              <th>Peserta</th>
              <th>Tanggal</th>
              <th>Waktu</th>
              <th>Status</th>
              <th>Dosen Pembimbing</th>
              <th>Alasan Pembimbing</th>
              <th>Dosen Penguji</th>
              <th>Alasan Penguji</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($seminars as $seminar): ?>
              <tr>
                <td><?= htmlspecialchars($seminar['judul'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($seminar['nama_peserta'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($seminar['tanggal'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($seminar['waktu_mulai']) ?> - <?= htmlspecialchars($seminar['waktu_selesai']) ?></td>
                <td><?= htmlspecialchars(ucfirst($seminar['status']), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= $seminar['status_pembimbing'] ? ucfirst(htmlspecialchars($seminar['status_pembimbing'])) : '-' ?></td>
                <td><?= ($seminar['status_pembimbing'] === 'rejected') ? nl2br(htmlspecialchars($seminar['komentar_pembimbing'] ?: '-')) : '-' ?></td>
                <td><?= $seminar['status_penguji'] ? ucfirst(htmlspecialchars($seminar['status_penguji'])) : '-' ?></td>
                <td><?= ($seminar['status_penguji'] === 'rejected') ? nl2br(htmlspecialchars($seminar['komentar_penguji'] ?: '-')) : '-' ?></td>
                <td>
                  <a href="edit_seminar.php?id=<?= $seminar['id'] ?>" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil-square"></i> Edit
                  </a>
                  <a href="hapus_seminar.php?id=<?= $seminar['id'] ?>" class="btn btn-danger btn-sm"
                     onclick="return confirm('Yakin ingin menghapus seminar ini?');">
                    <i class="bi bi-trash"></i> Hapus
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php include "../includes/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const sidebar = document.getElementById('sidebarMenu');
const mainContent = document.getElementById('mainContent');
const sidebarToggle = document.getElementById('sidebarToggle');
function closeSidebar() {
  sidebar.classList.remove('open');
  mainContent && mainContent.classList.add('sidebar-collapsed');
}
function openSidebar() {
  sidebar.classList.add('open');
  mainContent && mainContent.classList.remove('sidebar-collapsed');
}
sidebarToggle?.addEventListener('click', function() {
  if (sidebar.classList.contains('open')) {
    closeSidebar();
  } else {
    openSidebar();
  }
});
window.addEventListener('resize', function() {
  if (window.innerWidth > 900) {
    sidebar.classList.remove('open');
    mainContent && mainContent.classList.remove('sidebar-collapsed');
  }
});
</script>
</body>
</html>
