<?php
require_once "../auth/auth_check.php";
require_once "../config/database.php";

checkAuth(['admin']);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $npm = trim($_POST['npm'] ?? '');
    $prodi = trim($_POST['prodi'] ?? '');
    $email = trim($_POST['email'] ?? '');
    // Password default diset di server, tidak dari form

    if (!$nama || !$npm || !$prodi || !$email) {
        $error = "Semua field wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } else {
        // Cek email dan npm sudah ada atau belum
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email OR npm = :npm");
        $stmt->execute(['email' => $email, 'npm' => $npm]);
        if ($stmt->fetch()) {
            $error = "Email atau NPM sudah terdaftar.";
        } else {
            // Password default "password123", hash sebelum disimpan
            $password = password_hash("password123", PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (nama, npm, prodi, email, password, role, created_at) VALUES (:nama, :npm, :prodi, :email, :password, 'peserta', NOW())");
            $stmt->execute([
                'nama' => $nama,
                'npm' => $npm,
                'prodi' => $prodi,
                'email' => $email,
                'password' => $password,
            ]);

            header("Location: manage_peserta.php?msg=Data peserta berhasil ditambahkan.");
            exit;
        }
    }
}

$title = "Tambah Peserta";
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
    <h2 class="mb-4">Tambah Peserta Baru</h2>

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
        <label for="npm" class="form-label">NPM</label>
        <input type="text" class="form-control" id="npm" name="npm" required value="<?= htmlspecialchars($_POST['npm'] ?? '') ?>" />
        <div class="invalid-feedback">NPM wajib diisi.</div>
      </div>

      <div class="mb-3">
        <label for="prodi" class="form-label">Program Studi</label>
        <input type="text" class="form-control" id="prodi" name="prodi" required value="<?= htmlspecialchars($_POST['prodi'] ?? '') ?>" />
        <div class="invalid-feedback">Program studi wajib diisi.</div>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
        <div class="invalid-feedback">Email wajib diisi dan valid.</div>
      </div>

      <p class="text-muted"><small>Password default: <strong>password123</strong></small></p>

      <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
      <a href="manage_peserta.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Batal</a>
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
