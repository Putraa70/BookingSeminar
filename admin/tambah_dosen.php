<?php
require_once "../auth/auth_check.php";
require_once "../config/database.php";

checkAuth(['admin']);

$error = '';
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
        // Cek email sudah ada atau belum
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $error = "Email sudah terdaftar.";
        } else {
            // Password default: password123 (hashed)
            $password = password_hash("password123", PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role, created_at) VALUES (:nama, :email, :password, :role, NOW())");
            $stmt->execute([
                'nama' => $nama,
                'email' => $email,
                'password' => $password,
                'role' => $role
            ]);

            header("Location: manage_dosen.php?msg=Data dosen berhasil ditambahkan.");
            exit;
        }
    }
}

$title = "Tambah Dosen";
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
<!-- Header & Sidebar sudah include -->

<main id="mainContent" class="content">
  <div class="container-fluid" style="max-width: 600px;">
    <h2 class="mb-4">Tambah Dosen Baru</h2>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="needs-validation" novalidate>
      <div class="mb-3">
        <label for="nama" class="form-label">Nama Lengkap</label>
        <input type="text" class="form-control" id="nama" name="nama" required value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" />
        <div class="invalid-feedback">Nama wajib diisi.</div>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
        <div class="invalid-feedback">Email wajib diisi dan valid.</div>
      </div>

      <div class="mb-3">
        <label for="role" class="form-label">Role</label>
        <select class="form-select" id="role" name="role" required>
          <option value="">-- Pilih Role --</option>
          <option value="dosen_pembimbing" <?= (($_POST['role'] ?? '') == 'dosen_pembimbing') ? 'selected' : '' ?>>Dosen Pembimbing</option>
          <option value="dosen_penguji" <?= (($_POST['role'] ?? '') == 'dosen_penguji') ? 'selected' : '' ?>>Dosen Penguji</option>
        </select>
        <div class="invalid-feedback">Role wajib dipilih.</div>
      </div>

      <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
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
