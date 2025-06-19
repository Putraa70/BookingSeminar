<!-- admin_sidebar.php -->
<style>
:root {
  --header-height: 60px;
  --sidebar-bg: #19376d;
  --sidebar-hover: #295cc2;
  --sidebar-active: #fdb813;
  --sidebar-font: #fff;
  --sidebar-icon: #fdb813;
  --main-bg: #f4f6fb;
}
body {
  background-color: var(--main-bg);
}
.sidebar {
  position: fixed;
  top: var(--header-height);
  left: 0;
  height: calc(100vh - var(--header-height));
  width: 235px;
  background: var(--sidebar-bg);
  color: var(--sidebar-font);
  z-index: 1050;
  padding-top: 24px;
  transition: all .25s;
  box-shadow: 2px 0 12px rgba(25, 55, 109, 0.07);
}
.sidebar.collapsed {
  width: 0;
  overflow: hidden;
  padding: 0;
  transition: all .25s;
}
.sidebar .nav-link {
  color: var(--sidebar-font);
  border-radius: 8px;
  font-weight: 500;
  margin-bottom: 6px;
  padding: 14px 18px;
  transition: background .2s, color .2s;
  font-size: 1.08rem;
  display: flex;
  align-items: center;
  gap: 10px;
}
.sidebar .nav-link i {
  color: var(--sidebar-icon);
  font-size: 1.3em;
  transition: color .2s;
}
.sidebar .nav-link.active,
.sidebar .nav-link:hover {
  background: var(--sidebar-hover);
  color: var(--sidebar-active);
}
.sidebar .nav-link.active i,
.sidebar .nav-link:hover i {
  color: var(--sidebar-active);
}
.sidebar .admin-badge {
  background: var(--sidebar-active);
  color: #1a2442;
  font-size: 1rem;
  font-weight: 700;
  padding: 4px 16px;
  border-radius: 8px;
  margin-bottom: 12px;
  display: inline-block;
}
.sidebar .admin-icon {
  font-size: 2.6rem;
  margin-bottom: 3px;
  color: var(--sidebar-active);
}
.hamburger-btn {
  display: none;
  position: fixed;
  top: calc(var(--header-height) + 10px);
  left: 16px;
  z-index: 1100;
  background: var(--sidebar-bg);
  color: var(--sidebar-active);
  border: none;
  font-size: 2rem;
  border-radius: 7px;
  padding: 5px 10px;
  box-shadow: 0 3px 10px rgba(25, 55, 109, 0.08);
}
.content {
  margin-left: 235px;
  padding: 70px 20px 20px 20px;
  transition: margin-left .25s;
}
.sidebar.collapsed + .content,
.content.sidebar-collapsed {
  margin-left: 0;
}
@media (max-width: 900px) {
  .sidebar {
    width: 0;
    overflow: hidden;
    padding: 0;
    transition: all .2s;
    top: var(--header-height);
    height: calc(100vh - var(--header-height));
  }
  .sidebar.open {
    width: 220px;
    padding-top: 24px;
    overflow: auto;
  }
  .hamburger-btn {
    display: block;
  }
  .content {
    margin-left: 0 !important;
    padding: 80px 8px 20px 8px;
  }
}
</style>

<!-- Sidebar Admin -->
<div class="sidebar" id="sidebarMenu">
  <div class="text-center mb-4">
    <span class="admin-badge mt-2">Admin Panel</span>
  </div>
  <ul class="nav flex-column mb-auto px-2">
    <li>
      <a href="manage_dosen.php" class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'manage_dosen.php' ? ' active' : '' ?>">
        <i class="bi bi-person-badge"></i> Kelola Dosen
      </a>
    </li>
    <li>
      <a href="manage_peserta.php" class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'manage_peserta.php' ? ' active' : '' ?>">
        <i class="bi bi-people"></i> Kelola Peserta
      </a>
    </li>
    <li>
      <a href="manage_seminar.php" class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'manage_seminar.php' ? ' active' : '' ?>">
        <i class="bi bi-calendar-check"></i> Kelola Seminar
      </a>
    </li>
  </ul>
</div>

<!-- Hamburger Button (mobile) -->
<button id="sidebarToggle" class="hamburger-btn" type="button">
  <i class="bi bi-list"></i>
</button>

<script>
const sidebar = document.getElementById('sidebarMenu');
const mainContent = document.getElementById('mainContent');
const sidebarToggle = document.getElementById('sidebarToggle');

function closeSidebar() {
  sidebar.classList.remove('open');
  mainContent && mainContent.classList.add('sidebar-collapsed');
}
function openSidebar() {
  sidebar.classList.add('open');
  mainContent && mainContent.classList.remove('sidebar-collapsed');
}
sidebarToggle?.addEventListener('click', function() {
  if (sidebar.classList.contains('open')) {
    closeSidebar();
  } else {
    openSidebar();
  }
});
window.addEventListener('resize', function() {
  if (window.innerWidth > 900) {
    sidebar.classList.remove('open');
    mainContent && mainContent.classList.remove('sidebar-collapsed');
  }
});
</script>

<!-- NOTE:
Untuk icon bi-... pastikan sudah include Bootstrap Icons di layout utama kamu:
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
-->
