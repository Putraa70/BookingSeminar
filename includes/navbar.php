<?php
// Navbar dinamis sesuai role user yang login
$role = $_SESSION['role'] ?? '';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="../index.php">Booking Seminar</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if ($role === 'admin'): ?>
          <li class="nav-item"><a class="nav-link" href="../admin/index.php">Dashboard Admin</a></li>
          <li class="nav-item"><a class="nav-link" href="../admin/manage_dosen.php">Kelola Dosen</a></li>
          <li class="nav-item"><a class="nav-link" href="../admin/manage_peserta.php">Kelola Peserta</a></li>
        <?php elseif ($role === 'dosen_pembimbing' || $role === 'dosen_penguji'): ?>
          <li class="nav-item"><a class="nav-link" href="../dosen/index.php">Dashboard Dosen</a></li>
          <li class="nav-item"><a class="nav-link" href="../dosen/approvals.php">Persetujuan Seminar</a></li>
        <?php elseif ($role === 'peserta'): ?>
          <li class="nav-item"><a class="nav-link" href="../mahasiswa/index.php">Dashboard Mahasiswa</a></li>
          <li class="nav-item"><a class="nav-link" href="../mahasiswa/booking.php">Ajukan Booking</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout (<?= htmlspecialchars($_SESSION['nama'] ?? '') ?>)</a></li>
      </ul>
    </div>
  </div>
</nav>
