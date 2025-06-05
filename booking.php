<?php
require_once "../auth/auth_check.php";

checkAuth(['peserta']);

$title = "Booking Seminar";
include "../includes/header.php";
include "../includes/navbar.php";
?>

<div class="container mt-5">
  <h2 class="mb-4">Booking Jadwal Seminar</h2>

  <?php if (!empty($_SESSION['flash_message'])): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($_SESSION['flash_message']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash_message']); ?>
  <?php endif; ?>

  <form action="booking_action.php" method="POST" class="needs-validation" novalidate autocomplete="off" spellcheck="false">
    <div class="mb-3">
      <label for="judul" class="form-label">Judul Seminar</label>
      <input type="text" class="form-control" id="judul" name="judul" required placeholder="Masukkan judul seminar" />
      <div class="invalid-feedback">Judul seminar wajib diisi.</div>
    </div>

    <div class="mb-3">
      <label for="tanggal" class="form-label">Tanggal Seminar</label>
      <input type="date" class="form-control" id="tanggal" name="tanggal" required />
      <div class="invalid-feedback">Tanggal seminar wajib dipilih.</div>
    </div>

    <div class="row">
      <div class="mb-3 col-md-6">
        <label for="waktu_mulai" class="form-label">Waktu Mulai</label>
        <input type="time" class="form-control" id="waktu_mulai" name="waktu_mulai" required />
        <div class="invalid-feedback">Waktu mulai wajib diisi.</div>
      </div>

      <div class="mb-3 col-md-6">
        <label for="waktu_selesai" class="form-label">Waktu Selesai</label>
        <input type="time" class="form-control" id="waktu_selesai" name="waktu_selesai" required />
        <div class="invalid-feedback">Waktu selesai wajib diisi.</div>
      </div>
    </div>

    <div class="mb-3">
      <label for="deskripsi" class="form-label">Deskripsi Seminar</label>
      <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required placeholder="Jelaskan secara singkat isi seminar"></textarea>
      <div class="invalid-feedback">Deskripsi seminar wajib diisi.</div>
    </div>

    <button type="submit" class="btn btn-primary">Ajukan Booking</button>
  </form>
</div>

<?php include "../includes/footer.php"; ?>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
