<?php
require_once "../auth/auth_check.php";
require_once "../config/database.php";

checkAuth(['admin']);

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: manage_peserta.php?msg=ID peserta tidak valid.");
    exit;
}

$error = '';
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id AND role = 'peserta'");
$stmt->execute(['id' => $id]);
$peserta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$peserta) {
    header("Location: manage_peserta.php?msg=Peserta tidak ditemukan.");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $npm = trim($_POST['npm'] ?? '');
    $prodi = trim($_POST['prodi'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password_baru = $_POST['password_baru'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    if (!$nama || !$npm || !$prodi || !$email) {
        $error = "Semua field wajib diisi kecuali password.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif ($password_baru !== $konfirmasi_password) {
        $error = "Password baru dan konfirmasi password tidak cocok.";
    } else {
        // Cek email atau npm sudah dipakai peserta lain
        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE (email = :email OR npm = :npm) AND id != :id");
        $stmtCheck->execute(['email' => $email, 'npm' => $npm, 'id' => $id]);
        if ($stmtCheck->fetch()) {
            $error = "Email atau NPM sudah digunakan peserta lain.";
        } else {
            if ($password_baru) {
                $password_hashed = password_hash($password_baru, PASSWORD_DEFAULT);
                $sql_update = "UPDATE users SET nama = :nama, npm = :npm, prodi = :prodi, email = :email, password = :password WHERE id = :id";
                $params = [
                    'nama' => $nama,
                    'npm' => $npm,
                    'prodi' => $prodi,
                    'email' => $email,
                    'password' => $password_hashed,
                    'id' => $id
                ];
            } else {
                $sql_update = "UPDATE users SET nama = :nama, npm = :npm, prodi = :prodi, email = :email WHERE id = :id";
                $params = [
                    'nama' => $nama,
                    'npm' => $npm,
                    'prodi' => $prodi,
                    'email' => $email,
                    'id' => $id
                ];
            }
            $stmtUpdate = $pdo->prepare($sql_update);
            $stmtUpdate->execute($params);

            header("Location: manage_peserta.php?msg=Data peserta berhasil diperbarui.");
            exit;
        }
    }
}

$title = "Edit Peserta";
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
    <h2 class="mb-4">Edit Data Peserta</h2>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="needs-validation" novalidate>
      <div class="mb-3">
        <label for="nama" class="form-label">Nama Lengkap</label>
        <input type="text" class="form-control" id="nama" name="nama" required value="<?= htmlspecialchars($_POST['nama'] ?? $peserta['nama']) ?>" />
        <div class="invalid-feedback">Nama wajib diisi.</div>
      </div>

      <div class="mb-3">
        <label for="npm" class="form-label">NPM</label>
        <input type="text" class="form-control" id="npm" name="npm" required value="<?= htmlspecialchars($_POST['npm'] ?? $peserta['npm']) ?>" />
        <div class="invalid-feedback">NPM wajib diisi.</div>
      </div>

      <div class="mb-3">
        <label for="prodi" class="form-label">Program Studi</label>
        <input type="text" class="form-control" id="prodi" name="prodi" required value="<?= htmlspecialchars($_POST['prodi'] ?? $peserta['prodi']) ?>" />
        <div class="invalid-feedback">Program studi wajib diisi.</div>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? $peserta['email']) ?>" />
        <div class="invalid-feedback">Email wajib diisi dan valid.</div>
      </div>

      <hr>

      <div class="mb-3">
        <label for="password_baru" class="form-label">Password Baru (kosongkan jika tidak ingin mengganti)</label>
        <input type="password" class="form-control" id="password_baru" name="password_baru" />
      </div>

      <div class="mb-3">
        <label for="konfirmasi_password" class="form-label">Konfirmasi Password Baru</label>
        <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" />
        <div class="invalid-feedback">Konfirmasi password wajib sama dengan password baru.</div>
      </div>

      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      <a href="manage_peserta.php" class="btn btn-secondary">Batal</a>
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
