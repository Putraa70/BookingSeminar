<?php
require_once "../auth/auth_check.php";
require_once "../config/database.php";

checkAuth(['admin']);

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: manage_dosen.php?msg=ID dosen tidak valid.");
    exit;
}

$error = '';
// Ambil data dosen
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id AND role IN ('dosen_pembimbing', 'dosen_penguji')");
$stmt->execute(['id' => $id]);
$dosen = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dosen) {
    header("Location: manage_dosen.php?msg=Dosen tidak ditemukan.");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? '';

    if (!$nama || !$email || !$role) {
        $error = "Semua field wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (!in_array($role, ['dosen_pembimbing', 'dosen_penguji'])) {
        $error = "Role tidak valid.";
    } else {
        // Cek email sudah dipakai dosen lain atau belum
        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
        $stmtCheck->execute(['email' => $email, 'id' => $id]);
        if ($stmtCheck->fetch()) {
            $error = "Email sudah digunakan dosen lain.";
        } else {
            $stmtUpdate = $pdo->prepare("UPDATE users SET nama = :nama, email = :email, role = :role WHERE id = :id");
            $stmtUpdate->execute([
                'nama' => $nama,
                'email' => $email,
                'role' => $role,
                'id' => $id
            ]);
            header("Location: manage_dosen.php?msg=Data dosen berhasil diperbarui.");
            exit;
        }
    }
}

$title = "Edit Dosen";
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
  <div class="container-fluid" style="max-width: 600px;">
    <h2 class="mb-4">Edit Data Dosen</h2>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="needs-validation" novalidate>
      <div class="mb-3">
        <label for="nama" class="form-label">Nama Lengkap</label>
        <input type="text" class="form-control" id="nama" name="nama" required value="<?= htmlspecialchars($_POST['nama'] ?? $dosen['nama']) ?>" />
        <div class="invalid-feedback">Nama wajib diisi.</div>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? $dosen['email']) ?>" />
        <div class="invalid-feedback">Email wajib diisi dan valid.</div>
      </div>

      <div class="mb-3">
        <label for="role" class="form-label">Role</label>
        <select class="form-select" id="role" name="role" required>
          <option value="">-- Pilih Role --</option>
          <option value="dosen_pembimbing" <?= ((($_POST['role'] ?? $dosen['role']) == 'dosen_pembimbing') ? 'selected' : '') ?>>Dosen Pembimbing</option>
          <option value="dosen_penguji" <?= ((($_POST['role'] ?? $dosen['role']) == 'dosen_penguji') ? 'selected' : '') ?>>Dosen Penguji</option>
        </select>
        <div class="invalid-feedback">Role wajib dipilih.</div>
      </div>

      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      <a href="manage_dosen.php" class="btn btn-secondary">Batal</a>
    </form>
  </div>
</main>

<?php include "../includes/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(() => {
  'use strict';
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
})();
</script>
</body>
</html>
