<?php
session_start();
require_once "../config/database.php";

// Cegah akses login jika sudah login
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: ../admin/index.php");
            break;
        case 'dosen_pembimbing':
        case 'dosen_penguji':
            header("Location: ../dosen/index.php");
            break;
        case 'peserta':
            header("Location: ../mahasiswa/index.php");
            break;
    }
    exit;
}

$error = '';

// Proses login jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = "Email dan password wajib diisi.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nama'] = $user['nama'];

            // Arahkan user sesuai role
            switch ($user['role']) {
                case 'admin':
                    header("Location: ../admin/index.php");
                    break;
                case 'dosen_pembimbing':
                case 'dosen_penguji':
                    header("Location: ../dosen/index.php");
                    break;
                case 'peserta':
                    header("Location: ../mahasiswa/index.php");
                    break;
                default:
                    session_destroy();
                    $error = "Role tidak dikenal. Hubungi admin.";
            }
            exit;
        } else {
            $error = "Email atau password salah!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login Sistem Seminar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .login-container {
      display: flex;
      height: 100vh;
      align-items: center;
      justify-content: center;
      background-color: #f0f2f5;
      position: relative;
    }
    .login-image {
      background-image: url('https://masuk-ptn.com/images/product/8a658062631aa864333b59cd45b40fa9d9ee0580.jpg');
      background-size: cover;
      background-position: center;
      height: 100%;
      flex: 1;
      border-radius: 8px 0 0 8px;
      filter: blur(8px);
    }
    .login-form {
      max-width: 600px;
      padding: 60px;
      border-radius: 15px;
      background-color: rgba(255, 255, 255, 0.9);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
      position: absolute;
      z-index: 1;
    }
    .login-form h2 {
      margin-bottom: 30px;
      font-size: 30px;
      font-weight: bold;
      color: #333;
    }
    .form-control {
      border-radius: 20px;
    }
    .btn-primary {
      border-radius: 20px;
      padding: 12px 20px;
    }
    .alert {
      border-radius: 20px;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="login-image"></div>

    <div class="login-form">
      <h2 class="text-center">Login Sistem Seminar</h2>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="" class="needs-validation" novalidate>
        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
          <div class="invalid-feedback">Email wajib diisi dan format harus benar.</div>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" required />
          <div class="invalid-feedback">Password wajib diisi.</div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>

      <p class="mt-3 text-center">
        Belum punya akun? <a href="register.php">Daftar di sini</a>
      </p>
    </div>
  </div>

  <script>
    // Validasi Bootstrap
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
