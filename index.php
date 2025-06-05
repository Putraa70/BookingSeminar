<?php
require_once "../auth/auth_check.php";
checkAuth(['admin']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Admin - Booking Seminar</title>
  <link href="../assets/css/style.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Jika ingin gunakan Bootstrap Icons, tambahkan link ini -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body>
<?php include "../includes/navbar.php"; ?>

<main class="container mt-5">
  <div class="text-center mb-4">
    <h1 class="display-5 fw-bold">Selamat Datang, Admin <?= htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8') ?></h1>
    <p class="lead text-muted">Gunakan menu di bawah ini untuk mengelola data dosen, peserta, dan seminar.</p>
  </div>

  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
      <div class="list-group shadow-sm">
        <a href="manage_dosen.php" class="list-group-item list-group-item-action py-3 fs-5">
          <i class="bi bi-person-badge me-2"></i> Kelola Dosen
        </a>
        <a href="manage_peserta.php" class="list-group-item list-group-item-action py-3 fs-5">
          <i class="bi bi-people me-2"></i> Kelola Peserta
        </a>
        <a href="manage_seminar.php" class="list-group-item list-group-item-action py-3 fs-5">
          <i class="bi bi-calendar-check me-2"></i> Kelola Seminar
        </a>
      </div>
    </div>
  </div>
</main>

<?php include "../includes/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
