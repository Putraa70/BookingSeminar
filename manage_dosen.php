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
include "../includes/navbar.php";
?>

<main class="container mt-5">
  <h2 class="mb-4">Daftar Dosen</h2>

  <?php if ($msg): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <a href="tambah_dosen.php" class="btn btn-success mb-3">+ Tambah Dosen</a>

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
                <a href="edit_dosen.php?id=<?= $dosen['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="hapus_dosen.php?id=<?= $dosen['id'] ?>" 
                   class="btn btn-danger btn-sm" 
                   onclick="return confirm('Yakin ingin menghapus dosen ini?');">
                  Hapus
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</main>

<?php
include "../includes/footer.php";
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
