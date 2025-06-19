<?php
require_once "../auth/auth_check.php";
checkAuth(['dosen_pembimbing', 'dosen_penguji']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Dosen</title>
  <link href="../assets/css/style.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<?php include "../includes/navbar.php"; ?>

<div class="container mt-5">
  <h2>Halo, <?= htmlspecialchars($_SESSION['nama']) ?></h2>
  <p>Selamat datang di halaman dosen.</p>
  <a href="approvals.php" class="btn btn-primary">Lihat Pengajuan Seminar</a>
</div>

<?php include "../includes/footer.php"; ?>
<script src="../assets/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
