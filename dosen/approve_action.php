<?php
require_once "../auth/auth_check.php";
require_once "../config/database.php";
checkAuth(['dosen_pembimbing', 'dosen_penguji']);

$id_seminar = $_GET['id'] ?? null;
$id_user = $_SESSION['user_id'];

if (!$id_seminar) {
    header("Location: approvals.php?msg=ID seminar tidak valid.");
    exit;
}

// 1. Ambil detail seminar dan peserta
$sql = "SELECT s.*, u.nama AS nama_peserta, u.npm, u.prodi
        FROM seminars s
        JOIN users u ON s.id_user = u.id
        WHERE s.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id_seminar]);
$seminar = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$seminar) {
    header("Location: approvals.php?msg=Seminar tidak ditemukan.");
    exit;
}

// 2. Ambil approval semua dosen terkait
$sql_appr = "SELECT a.*, d.nama, d.role
             FROM approvals a
             JOIN users d ON a.id_user = d.id
             WHERE a.id_seminar = :id";
$stmt = $pdo->prepare($sql_appr);
$stmt->execute(['id' => $id_seminar]);
$approvals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mapping approval status dosen
$status_pembimbing = $status_penguji = $alasan_pembimbing = $alasan_penguji = '-';
$nama_pembimbing = $nama_penguji = '-';
foreach ($approvals as $appr) {
    if ($appr['role'] === 'dosen_pembimbing') {
        $status_pembimbing = $appr['status'] ? ucfirst($appr['status']) : 'Pending';
        $alasan_pembimbing = $appr['komentar'] ?: '-';
        $nama_pembimbing = $appr['nama'];
    }
    if ($appr['role'] === 'dosen_penguji') {
        $status_penguji = $appr['status'] ? ucfirst($appr['status']) : 'Pending';
        $alasan_penguji = $appr['komentar'] ?: '-';
        $nama_penguji = $appr['nama'];
    }
}

// 3. Ambil status approval untuk dosen yang login (jika ada)
$stmt = $pdo->prepare("SELECT * FROM approvals WHERE id_seminar = :id_seminar AND id_user = :id_user");
$stmt->execute(['id_seminar' => $id_seminar, 'id_user' => $id_user]);
$my_approval = $stmt->fetch(PDO::FETCH_ASSOC);

$feedback = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? '';
    $komentar = trim($_POST['komentar'] ?? '');

    if (!in_array($status, ['approved', 'rejected'])) {
        $feedback = "Pilih status persetujuan.";
    } elseif ($status === 'rejected' && $komentar === '') {
        $feedback = "Alasan penolakan wajib diisi jika ditolak.";
    } else {
        if ($my_approval) {
            // Update approval
            $stmt = $pdo->prepare("UPDATE approvals SET status = :status, komentar = :komentar WHERE id = :id");
            $stmt->execute([
                'status' => $status,
                'komentar' => $komentar,
                'id' => $my_approval['id']
            ]);
        } else {
            // Insert new approval
            $stmt = $pdo->prepare("INSERT INTO approvals (id_seminar, id_user, status, komentar) VALUES (:id_seminar, :id_user, :status, :komentar)");
            $stmt->execute([
                'id_seminar' => $id_seminar,
                'id_user' => $id_user,
                'status' => $status,
                'komentar' => $komentar
            ]);
        }
        header("Location: approvals.php?msg=Persetujuan berhasil disimpan.");
        exit;
    }
}

$title = "Detail & Approve Seminar";
include "../includes/header.php";
include "../includes/navbar.php";
?>

<div class="container mt-5 mb-5" style="max-width:700px;">
  <h2>Detail Seminar</h2>
  <div class="card mb-3">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-4">Judul Seminar</dt>
        <dd class="col-sm-8"><?= htmlspecialchars($seminar['judul'] ?? '-') ?></dd>

        <dt class="col-sm-4">Peserta</dt>
        <dd class="col-sm-8">
          <?= htmlspecialchars($seminar['nama_peserta'] ?? '-') ?>
          (<?= htmlspecialchars($seminar['npm'] ?? '-') ?> / <?= htmlspecialchars($seminar['prodi'] ?? '-') ?>)
        </dd>

        <dt class="col-sm-4">Tanggal</dt>
        <dd class="col-sm-8"><?= htmlspecialchars($seminar['tanggal'] ?? '-') ?></dd>

        <dt class="col-sm-4">Waktu</dt>
        <dd class="col-sm-8"><?= htmlspecialchars($seminar['waktu_mulai'] ?? '-') ?> - <?= htmlspecialchars($seminar['waktu_selesai'] ?? '-') ?></dd>
      </dl>
    </div>
  </div>

  <h5>Status Approval Dosen</h5>
  <table class="table table-sm mb-4">
    <thead>
      <tr>
        <th>Dosen Pembimbing</th>
        <th>Status</th>
        <th>Alasan Penolakan</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><?= htmlspecialchars($nama_pembimbing ?? '-') ?></td>
        <td>
          <?php
            if ($status_pembimbing === 'Approved') {
                echo '<span class="badge bg-success">Disetujui</span>';
            } elseif ($status_pembimbing === 'Rejected') {
                echo '<span class="badge bg-danger">Ditolak</span>';
            } else {
                echo '<span class="badge bg-secondary">Pending</span>';
            }
          ?>
        </td>
        <td><?= $status_pembimbing === 'Rejected' ? nl2br(htmlspecialchars($alasan_pembimbing ?? '-')) : '-' ?></td>
      </tr>
      <tr>
        <th>Dosen Penguji</th>
        <th>Status</th>
        <th>Alasan Penolakan</th>
      </tr>
      <tr>
        <td><?= htmlspecialchars($nama_penguji ?? '-') ?></td>
        <td>
          <?php
            if ($status_penguji === 'Approved') {
                echo '<span class="badge bg-success">Disetujui</span>';
            } elseif ($status_penguji === 'Rejected') {
                echo '<span class="badge bg-danger">Ditolak</span>';
            } else {
                echo '<span class="badge bg-secondary">Pending</span>';
            }
          ?>
        </td>
        <td><?= $status_penguji === 'Rejected' ? nl2br(htmlspecialchars($alasan_penguji ?? '-')) : '-' ?></td>
      </tr>
    </tbody>
  </table>

  <hr>

  <?php
$has_action = ($my_approval && in_array($my_approval['status'], ['approved','rejected']));
?>
<?php if ($has_action): ?>
  <div class="alert alert-info">
    Anda sudah memberikan persetujuan: <b><?= htmlspecialchars(ucfirst($my_approval['status'])) ?></b><br>
    <?php if ($my_approval['status'] === 'rejected'): ?>
      <b>Alasan Penolakan:</b> <?= nl2br(htmlspecialchars($my_approval['komentar'] ?? '-')) ?>
    <?php endif; ?>
  </div>
  <a href="approvals.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
<?php else: ?>
  <?php if ($feedback): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($feedback) ?></div>
  <?php endif; ?>
  <form method="POST" action="">
    <div class="mb-3">
      <label class="form-label">Status Persetujuan</label>
      <select class="form-select" name="status" required onchange="document.getElementById('alasanTolak').style.display = this.value=='rejected' ? 'block' : 'none';">
        <option value="">-- Pilih --</option>
        <option value="approved">Setujui</option>
        <option value="rejected">Tolak</option>
      </select>
    </div>
    <div class="mb-3" id="alasanTolak" style="display:none;">
      <label for="komentar" class="form-label">Alasan Penolakan</label>
      <textarea class="form-control" name="komentar" id="komentar" rows="2" placeholder="Alasan penolakan"></textarea>
    </div>
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan Persetujuan</button>
    <a href="approvals.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
  </form>
<?php endif; ?>

</div>

<?php include "../includes/footer.php"; ?>
