<?php
require_once "../config/database.php";

// Ambil daftar dosen pembimbing dan penguji
$stmtPembimbing = $pdo->query("SELECT id, nama FROM users WHERE role = 'dosen_pembimbing' ORDER BY nama");
$dosenPembimbing = $stmtPembimbing->fetchAll(PDO::FETCH_ASSOC);

$stmtPenguji = $pdo->query("SELECT id, nama FROM users WHERE role = 'dosen_penguji' ORDER BY nama");
$dosenPenguji = $stmtPenguji->fetchAll(PDO::FETCH_ASSOC);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $npm = trim($_POST['npm'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $id_pembimbing = $_POST['dosen_pembimbing'] ?? null;
    $id_penguji = $_POST['dosen_penguji'] ?? null;

    // Validasi sederhana
    if (!$nama || !$npm || !$email || !$password || !$password_confirm || !$id_pembimbing || !$id_penguji) {
        $error = "Semua field wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email tidak valid.";
    } elseif ($password !== $password_confirm) {
        $error = "Password dan konfirmasi password tidak cocok.";
    } else {
        // Cek email dan npm sudah terdaftar
        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE email = :email OR npm = :npm");
        $stmtCheck->execute(['email' => $email, 'npm' => $npm]);
        if ($stmtCheck->fetch()) {
            $error = "Email atau NPM sudah terdaftar.";
        }
    }

    if (!$error) {
        try {
            $pdo->beginTransaction();

            // Simpan data user mahasiswa
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmtInsertUser = $pdo->prepare("INSERT INTO users (nama, npm, email, password, role, created_at) VALUES (:nama, :npm, :email, :password, 'peserta', NOW())");
            $stmtInsertUser->execute([
                ':nama' => $nama,
                ':npm' => $npm,
                ':email' => $email,
                ':password' => $hashedPassword
            ]);
            $id_mahasiswa = $pdo->lastInsertId();

            // Simpan relasi mahasiswa_dosen
            $stmtInsertRelasi = $pdo->prepare("INSERT INTO mahasiswa_dosen (id_mahasiswa, id_dosen, jenis_dosen) VALUES (:id_mahasiswa, :id_dosen, :jenis_dosen)");
            $stmtInsertRelasi->execute([
                ':id_mahasiswa' => $id_mahasiswa,
                ':id_dosen' => $id_pembimbing,
                ':jenis_dosen' => 'pembimbing'
            ]);
            $stmtInsertRelasi->execute([
                ':id_mahasiswa' => $id_mahasiswa,
                ':id_dosen' => $id_penguji,
                ':jenis_dosen' => 'penguji'
            ]);

            $pdo->commit();
            $success = "Registrasi berhasil. Silakan login.";
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}

$title = "Registrasi Mahasiswa"; // Anda masih bisa menetapkan title untuk halaman ini
?>

<!-- Include Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
  /* Custom Background with Image */
  body {
    background: url('https://masuk-ptn.com/images/product/8a658062631aa864333b59cd45b40fa9d9ee0580.jpg') no-repeat center center fixed; 
    background-size: cover;
    font-family: 'Arial', sans-serif;
    color: white;
    height: 100vh;
    margin: 0;
  }

  .container {
    margin-top: 20px;
  }

  .card {
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    opacity: 0.9;
    background-color: rgba(255, 255, 255, 0.8); /* semi-transparent background */
  }

  .form-label {
    font-weight: bold;
    color: #333;
  }

  .btn-primary {
    width: 100%;
    background-color: #004aad;
    border-color: #004aad;
  }

  .btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
  }

  .form-control:focus {
    border-color: #004aad;
    box-shadow: 0 0 5px rgba(0, 74, 173, 0.6);
  }

  .invalid-feedback {
    font-size: 0.9rem;
  }

  .btn-link {
    color: #ffcc00; /* Change to a more visible color */
    font-weight: bold;
    text-decoration: none;
  }

  .btn-link:hover {
    color: #ff9900; /* Hover effect */
    text-decoration: underline;
  }

  .alert {
    margin-bottom: 20px;
    font-weight: bold;
  }

.text-center {
    color: #004aad;
    font-weight: bold;
  }

  /* Custom Style for "Belum Punya Akun" Text */
  .register-text {
    color:rgb(2, 0, 0); /* Blue color */
  }

</style>

<div class="container">
  <div class="card mx-auto" style="max-width: 500px;">
    <h2 class="text-center mb-4">Registrasi Mahasiswa</h2>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="needs-validation" novalidate autocomplete="off">
      <div class="mb-3">
        <label for="nama" class="form-label">Nama Lengkap</label>
        <input type="text" id="nama" name="nama" class="form-control" required value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" />
        <div class="invalid-feedback">Nama wajib diisi.</div>
      </div>

      <div class="mb-3">
        <label for="npm" class="form-label">NPM</label>
        <input type="text" id="npm" name="npm" class="form-control" required value="<?= htmlspecialchars($_POST['npm'] ?? '') ?>" />
        <div class="invalid-feedback">NPM wajib diisi.</div>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
        <div class="invalid-feedback">Email wajib diisi dan valid.</div>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-control" required />
        <div class="invalid-feedback">Password wajib diisi.</div>
      </div>

      <div class="mb-3">
        <label for="password_confirm" class="form-label">Konfirmasi Password</label>
        <input type="password" id="password_confirm" name="password_confirm" class="form-control" required />
        <div class="invalid-feedback">Konfirmasi password wajib diisi dan harus sama dengan password.</div>
      </div>

      <div class="mb-3">
        <label for="dosen_pembimbing" class="form-label">Pilih Dosen Pembimbing</label>
        <select id="dosen_pembimbing" name="dosen_pembimbing" class="form-select" required>
          <option value="">-- Pilih Dosen Pembimbing --</option>
          <?php foreach ($dosenPembimbing as $dosen): ?>
            <option value="<?= htmlspecialchars($dosen['id']) ?>" <?= (isset($_POST['dosen_pembimbing']) && $_POST['dosen_pembimbing'] == $dosen['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($dosen['nama']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Dosen pembimbing wajib dipilih.</div>
      </div>

      <div class="mb-3">
        <label for="dosen_penguji" class="form-label">Pilih Dosen Penguji</label>
        <select id="dosen_penguji" name="dosen_penguji" class="form-select" required>
          <option value="">-- Pilih Dosen Penguji --</option>
          <?php foreach ($dosenPenguji as $dosen): ?>
            <option value="<?= htmlspecialchars($dosen['id']) ?>" <?= (isset($_POST['dosen_penguji']) && $_POST['dosen_penguji'] == $dosen['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($dosen['nama']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Dosen penguji wajib dipilih.</div>
      </div>

      <button type="submit" class="btn btn-primary">Daftar</button>
    </form>

    <div class="mt-3 text-center">
      <p class="register-text">Sudah punya akun? <a href="login.php" class="btn btn-link">Login di sini</a></p>
    </div>
  </div>
</div>

<!-- Include Bootstrap JS and Popper.js for form validation -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

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
