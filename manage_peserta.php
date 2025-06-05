<?php
require_once "../auth/auth_check.php";
require_once "../config/database.php";

checkAuth(['admin']);

$msg = $_GET['msg'] ?? '';

// Ambil data peserta
$sql = "SELECT id, nama, email FROM users WHERE role = 'peserta' ORDER BY nama ASC";
$stmt = $pdo->query($sql);
$pesertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$title = "Kelola Peserta";
include "../includes/header.php";
include "../includes/navbar.php";
?>

<main class="container mt-5">
  <h2 class="mb-4">Daftar Peserta</h2>

  <?php if ($msg): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <a href="tambah_peserta.php" class="btn btn-success mb-3">+ Tambah Peserta</a>

  <?php if (empty($pesertas)): ?>
    <p class="text-muted">Belum ada data peserta.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pesertas as $peserta): ?>
            <tr>
              <td><?= htmlspecialchars($peserta['nama'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($peserta['email'], ENT_QUOTES, 'UTF-8') ?></td>
              <td>
                <a href="edit_peserta.php?id=<?= $peserta['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="hapus_peserta.php?id=<?= $peserta['id'] ?>" 
                   class="btn btn-danger btn-sm" 
                   onclick="return confirm('Yakin ingin menghapus peserta ini?');">
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
