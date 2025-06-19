<nav id="adminHeader" class="navbar navbar-expand-lg navbar-dark"
     style="background: #19376d; box-shadow: 0 2px 12px rgba(25,55,109,0.07); z-index:1060; height:60px; position:fixed; top:0; left:0; right:0; width:100%;">
  <div class="container-fluid px-3">
    <a class="navbar-brand d-flex align-items-center gap-2" href="/admin/index.php">
      <i class="bi bi-kanban-fill" style="font-size:1.6rem; color:#fdb813;"></i>
      <span class="fw-bold" style="letter-spacing:1px;">Booking Seminar
        <span class="badge rounded-pill bg-warning text-dark ms-1" style="font-size:.8em;">ADMIN</span>
      </span>
    </a>
    <div class="d-flex align-items-center ms-auto">
      <span class="me-2 text-light" style="font-size:1.05em;">
        <i class="bi bi-person-circle me-1"></i>
        <?= isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8') : "Admin"; ?>
      </span>
      <a href="../admin/index.php" class="btn btn-sm btn-outline-light ms-2">
        <i class="bi bi-house-door"></i> Dashboard
      </a>
      <a href="../auth/logout.php" class="btn btn-sm btn-outline-warning ms-2">
        <i class="bi bi-box-arrow-right"></i> Logout
      </a>
    </div>
  </div>
</nav>

<script>
document.addEventListener("DOMContentLoaded", function() {
  var adminHeader = document.getElementById('adminHeader');
  if(adminHeader){
    adminHeader.style.position = 'fixed';
    adminHeader.style.top = '0';
    adminHeader.style.left = '0';
    adminHeader.style.right = '0';
    adminHeader.style.width = '100%';
    adminHeader.style.zIndex = '1060';
  }

  // Supaya konten tidak ketutup header
  var body = document.body;
  if(body){
    body.style.marginTop = (adminHeader?.offsetHeight || 60) + 'px';
  }
});
</script>
