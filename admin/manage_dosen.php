<?php
require_once "../auth/auth_check.php";
require_once "../config/database.php";

checkAuth(['admin']);

$msg = $_GET['msg'] ?? '';

// Ambil data dosen
$sql = "SELECT id, nama, email, role FROM users WHERE role IN ('dosen_pembimbing', 'dosen_penguji') ORDER BY nama ASC";
$stmt = $pdo->query($sql);
$dosens = $stmt->fetchAll(PDO::FETCH_ASSOC);

$title = "Kelola Dosen";
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
    <h2 class="mb-4">Daftar Dosen</h2>

    <?php if ($msg): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <a href="tambah_dosen.php" class="btn btn-success mb-3">
      <i class="bi bi-plus-circle"></i> Tambah Dosen
    </a>

    <?php if (empty($dosens)): ?>
      <p class="text-muted">Belum ada data dosen yang tersedia.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Nama</th>
              <th>Email</th>
              <th>Role</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($dosens as $dosen): ?>
              <tr>
                <td><?= htmlspecialchars($dosen['nama'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($dosen['email'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= ($dosen['role'] === 'dosen_pembimbing') ? 'Dosen Pembimbing' : 'Dosen Penguji' ?></td>
                <td>
                  <a href="edit_dosen.php?id=<?= $dosen['id'] ?>" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil-square"></i> Edit
                  </a>
                  <a href="hapus_dosen.php?id=<?= $dosen['id'] ?>"
                     class="btn btn-danger btn-sm"
                     onclick="return confirm('Yakin ingin menghapus dosen ini?');">
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
